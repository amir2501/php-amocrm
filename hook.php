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
use AmoCRM\Models\CustomFieldsValues\ValueCollections\MultiselectCustomFieldValueCollection;

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


header('Content-Type: application/json');
echo json_encode(['status' => 'success']);
flush();

if ($data && isset($data['contacts']['update'])) {
    foreach ($data['contacts']['update'] as $contact) {
        if (isset($contact['id'])) {

            $contactId = $contact['id'];

            handle_post($apiClient, $contactId);


        } else {
            file_put_contents('error.log', "Missing id in contact data: " . print_r($contact, true) . "\n", FILE_APPEND);
        }
    }
    file_put_contents('log.log', "----------------------------------" . PHP_EOL, FILE_APPEND);

}


if (isset($data['contacts']['add'])) {
    foreach ($data['contacts']['add'] as $contact) {

        if (isset($contact['id'])) {

            $contactId = $contact['id'];

            handle_post($apiClient, $contactId);
        } else {
            file_put_contents('error.log', "Missing id in contact data: " . print_r($contact, true) . "\n", FILE_APPEND);
        }

    }
}


function handle_post($apiClient, $contactId)
{
    $phoneNumbers = [];

    $contact = $apiClient->contacts()->getOne($contactId);
    $customFields = $contact->getCustomFieldsValues();


    if ($customFields) {
        $phoneField = $customFields->getBy('fieldCode', 'PHONE');
        if ($phoneField) {

            $is_wrong = true;


            foreach ($phoneField->getValues() as $value) {
                $phoneNumbers[] = correctPhoneNumber($value->getValue());


                if ($is_wrong) {
                    $current_phone_number = correctPhoneNumber($value->getValue());

                    strlen($current_phone_number) == 12 ? $is_wrong = false : $is_wrong = true;
                }


            }


            file_put_contents('log.log', 'Phone number array' . json_encode($phoneNumbers) . PHP_EOL, FILE_APPEND);


            try {
                // Fetch the contact details


                // Find the PHONE field or create it if it doesn't exist
                $phoneField->setFieldCode('PHONE');

                // Create a new collection of phone values
                $phoneValues = new MultiselectCustomFieldValueCollection();

                // Add each phone number to the collection
                foreach ($phoneNumbers as $phoneNumber) {
                    $phoneValues->add(
                        (new MultiselectCustomFieldValueModel())->setValue($phoneNumber)
                    );
                }


                $contactTags = $contact->getTags() ?: new TagsCollection();


                try {
                    $tagExists = false;
                    foreach ($contactTags as $tag) {
                        if ($tag->getName() === 'Wrong Phone number') {
                            $tagExists = true;
                            break;
                        }
                    }

                    if (!$is_wrong) {

                        if ($tagExists) {
                            foreach ($contactTags as $key => $tag) {
                                if ($tag->getName() === 'Wrong Phone number') {
                                    $contactTags->offsetUnset($key);
                                    file_put_contents('log.log', 'Tag "Wrong Phone number" removed from contact' . PHP_EOL, FILE_APPEND);
                                    break;
                                }
                            }
                            $contact->setTags($contactTags);
                        }
                    } else {
                        // Add the tag if it doesn't exist
                        if (!$tagExists) {
                            $tag = new TagModel();
                            $tag->setName('Wrong Phone number');
                            $contactTags->add($tag);
                            $contact->setTags($contactTags);


                        }
                    }
                } catch (AmoCRMApiException $e) {
                    file_put_contents('error.log', 'Error occurred: ' . $e->getMessage() . PHP_EOL, FILE_APPEND);
                }

                // Set the new phone values to the phone field
                $phoneField->setValues($phoneValues);

                // Add or update the phone field in the custom fields collection
                if (!$customFields->getBy('fieldCode', 'PHONE')) {
                    $customFields->add($phoneField);
                }

                // Set the custom fields back to the contact details
                $contact->setCustomFieldsValues($customFields);

                // Update the contact
                $apiClient->contacts()->updateOne($contact);

                file_put_contents('log.log', "Phone numbers updated successfully for contact ID: $contactId\n", FILE_APPEND);

            } catch (AmoCRMApiException $e) {
                file_put_contents('log.log', 'Error occurred: ' . $e->getMessage() . PHP_EOL, FILE_APPEND);
                echo 'Error: ' . $e->getMessage() . PHP_EOL;
            }

        }
    }


    file_put_contents('log.log', "Id: " . $contactId . "\n", FILE_APPEND);
    file_put_contents('log.log', "custom fields: " . $customFields . "\n", FILE_APPEND);

}

function correctPhoneNumber($phoneNumber)
{
    $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);

    if (strlen($phoneNumber) >= 13 || strlen($phoneNumber) < 9) {
        return "$phoneNumber";
    } elseif (strlen($phoneNumber) == 9) {
//        обновлен номер телефона:
        return "998{$phoneNumber}";
    } else {
        return "$phoneNumber";
    }
}
