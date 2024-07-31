<?php


require 'vendor/autoload.php';

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Exceptions\AmoCRMApiException;
use League\OAuth2\Client\Token\AccessToken;

use AmoCRM\Collections\TagsCollection;
use AmoCRM\Models\TagModel;

use AmoCRM\Models\NoteType\CommonNote;

use AmoCRM\Collections\NotesCollection;
use AmoCRM\AmoCRM\Models\NoteType;
use AmoCRM\Collections\CustomFieldsValuesCollection;
use AmoCRM\Models\CustomFieldsValues\MultiselectCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\ValueModels\MultiselectCustomFieldValueModel;
use AmoCRM\EntitiesServices\Notes;


// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$clientId = $_ENV['CLIENT_ID'];
$clientSecret = $_ENV['CLIENT_SECRET'];
$redirectUri = $_ENV['REDIRECT_URI'];
$accessTokenData = [
    'access_token' => $_ENV['ACCESS_TOKEN'],
    'refresh_token' => $_ENV['REFRESH_TOKEN'],
    'expires' => '86400',
    'baseDomain' => $_ENV['BASE_DOMAIN'],
];

$apiClient = new AmoCRMApiClient($clientId, $clientSecret, $redirectUri);
$accessToken = new AccessToken($accessTokenData);
$apiClient->setAccessToken($accessToken)
    ->setAccountBaseDomain($accessToken->getValues()['baseDomain']);

$input = file_get_contents('php://input');

file_put_contents('webhook.log', "Raw POST Data: " . $input . "\n", FILE_APPEND);

parse_str($input, $data);

file_put_contents('webhook.log', "Decoded Data: " . print_r($data, true) . "\n", FILE_APPEND);

if (isset($data['contacts']['update'])) {
    foreach ($data['contacts']['update'] as $contact) {
        $phoneNumber = 'Not provided';

        foreach ($contact['custom_fields'] as $field) {
            if ($field['code'] == 'PHONE' && !empty($field['values'])) {
                $phoneNumber = $field['values'][0]['value'];
            }
        }

        try {
            $contactId = $contact['id'];
            $contactDetails = $apiClient->contacts()->getOne($contactId);

            $contactName = $contactDetails->getName();

            // Log contact ID, name, and phone number for debugging
            file_put_contents('webhook.log', "Contact ID: $contactId\n", FILE_APPEND);
            file_put_contents('webhook.log', "Contact Name: $contactName\n", FILE_APPEND);
            file_put_contents('webhook.log', "Phone Number: $phoneNumber\n", FILE_APPEND);
            //
            echo "Contact ID: $contactId\n";
            echo "Contact Name: $contactName\n";
            echo "Phone Number: $phoneNumber\n";

            $phoneNumber = correctPhoneNumber($phoneNumber);

            file_put_contents('webhook.log', "Updated Phone Number: $phoneNumber\n", FILE_APPEND);
            file_put_contents('webhook.log', "____________________________________________________________\n", FILE_APPEND);

            if (strlen($phoneNumber) == 12) {
                try {
                    $contactTags = $contactDetails->getTags();
                    if ($contactTags) {
                        foreach ($contactTags as $key => $tag) {
                            if ($tag->getName() === 'Wrong Phone number') {
                                $contactTags->offsetUnset($key);
                                file_put_contents('webhook.log', 'Tag "Wrong Phone number" removed from contact' . PHP_EOL, FILE_APPEND);
                                break;
                            }
                        }
                        $contactDetails->setTags($contactTags);
                        $apiClient->contacts()->updateOne($contactDetails);
                    }

                    // Update phone number
                    $customFields = $contactDetails->getCustomFieldsValues();
                    if ($customFields) {
                        $phoneField = $customFields->getBy('fieldCode', 'PHONE');
                        if ($phoneField) {
                            $phoneValues = $phoneField->getValues();
                            if ($phoneValues) {
                                $phoneValues[0]->setValue($phoneNumber);
                                $phoneField->setValues($phoneValues);
                            } else {
                                $phoneField->addValue(
                                    (new MultiselectCustomFieldValueModel())->setValue($phoneNumber)
                                );
                            }
                        } else {
                            $customFields->add(
                                (new MultiselectCustomFieldValuesModel())
                                    ->setFieldCode('PHONE')
                                    ->setValues(
                                        [(new MultiselectCustomFieldValueModel())->setValue($phoneNumber)]
                                    )
                            );
                        }
                    } else {
                        $contactDetails->setCustomFieldsValues(
                            (new CustomFieldsValuesCollection())
                                ->add(
                                    (new MultiselectCustomFieldValuesModel())
                                        ->setFieldCode('PHONE')
                                        ->setValues(
                                            [(new MultiselectCustomFieldValueModel())->setValue($phoneNumber)]
                                        )
                                )
                        );
                    }


                    $notesService = $apiClient->notes(NoteType::CONTACT);
                    $notes = $notesService->get($contactDetails);
                    $noteExists = false;

                    foreach ($notes as $note) {
                        if ($note instanceof CommonNote && strpos($note->getText(), 'Phone number updated to:') !== false) {
                            $note->setText("Phone number updated to: $phoneNumber");
                            $notesService->updateOne($note);
                            $noteExists = true;
                            break;
                        }
                    }

                    if (!$noteExists) {
                        // Create new note
                        $note = new CommonNote();
                        $note->setEntityId($contactDetails->getId())
                            ->setText("Phone number updated to: $phoneNumber");

                        $notesService->addOne($note);
                    }
                    $apiClient->contacts()->updateOne($contactDetails);

                } catch (AmoCRMApiException $e) {
                    file_put_contents('log.log', 'Error occurred: ' . $e->getMessage() . PHP_EOL, FILE_APPEND);
                }

                file_put_contents('webhook.log', "Phone Number: $phoneNumber is correct\n", FILE_APPEND);

                header('Content-Type: application/json');
                echo json_encode(['status' => 'success']);
            } else {
                $tagsCollection = new TagsCollection();
                $tag = new TagModel();
                $tag->setName('Wrong Phone number');
                $tagsCollection->add($tag);

                try {
                    $tag = (new TagModel())->setName('Wrong Phone number');
                    $tagsCollection = new TagsCollection();
                    $tagsCollection->add($tag);

                    $contactTags = $contactDetails->getTags() ?: new TagsCollection();
                    $contactTags->add($tag);
                    $contactDetails->setTags($contactTags);

                    $apiClient->contacts()->updateOne($contactDetails);

                    file_put_contents('webhook.log', 'Tag "Wrong Phone number" added to contact' . PHP_EOL, FILE_APPEND);
                } catch (AmoCRMApiException $e) {
                    file_put_contents('log.log', 'Error occurred: ' . $e->getMessage() . PHP_EOL, FILE_APPEND);
                }

                file_put_contents('webhook.log', "Phone Number: $phoneNumber\n not correct", FILE_APPEND);

                header('Content-Type: application/json');
                echo json_encode(['status' => 'success']);
            }


        } catch (AmoCRMApiException $e) {
            file_put_contents('webhook.log', "Error: " . $e->getMessage() . "\n", FILE_APPEND);
        }
    }
} else {
    file_put_contents('webhook.log', "Expected data structure not found\n", FILE_APPEND);
}

header('Content-Type: application/json');
echo json_encode(['status' => 'success']);

function correctPhoneNumber($phoneNumber)
{
    $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);

    if (strlen($phoneNumber) >= 13 || strlen($phoneNumber) < 9) {
        return " $phoneNumber ";
    } elseif (strlen($phoneNumber) == 9) {
//        обновлен номер телефона:
        return "998{$phoneNumber}";
    } else {
        return $phoneNumber;
    }
}
