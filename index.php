<?php

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Models\CustomFieldsValues\ValueModels\BaseCustomFieldValueModel;
use AmoCRM\Models\CustomFieldsValues\MultitextCustomFieldValuesModel;
use League\OAuth2\Client\Token\AccessToken;
use AmoCRM\Collections\CustomFieldsValuesCollection;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\MultitextCustomFieldValueCollection;

use AmoCRM\Collections\TagsCollection;
use AmoCRM\Models\TagModel;
use AmoCRM\Models\CustomFieldsValues\MultiselectCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\ValueModels\MultiselectCustomFieldValueModel;
use AmoCRM\Models\NoteType\CommonNote;
use AmoCRM\Models\NoteType;

require 'vendor/autoload.php';

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

// Parse the URL-encoded form data
parse_str($input, $data);
file_put_contents('webhook.log', "Decoded Data: " . print_r($data, true) . "\n", FILE_APPEND);

header('Content-Type: application/json');
echo json_encode(['status' => 'success']);
flush();

if ($data && isset($data['contacts']['update'])) {
    foreach ($data['contacts']['update'] as $contact) {
        if (isset($contact['id'])) {
            $contactId = $contact['id'];
            $phoneNumbers = getContactPhoneNumbers($apiClient, $contactId);

            updateContactPhoneNumbers($apiClient, $contactId, $phoneNumbers);

            $is_wrong = checkPhoneNumbers($phoneNumbers);

            $contactDetails = $apiClient->contacts()->getOne($contactId);

            manageWrongPhoneNumberTag($contactId, $contactDetails, $apiClient, $is_wrong);


            file_put_contents('webhook.log', "Phone Numbers: " . implode(', ', $phoneNumbers) . "\n", FILE_APPEND);
        } else {
            file_put_contents('webhook.log', "Missing id in contact data: " . print_r($contact, true) . "\n", FILE_APPEND);
        }
    }
}

if (isset($data['contacts']['add'])) {
    foreach ($data['contacts']['add'] as $contact) {
        if (isset($contact['id'])) {
            $contactId = $contact['id'];
            $phoneNumbers = getContactPhoneNumbers($apiClient, $contactId);

            updateContactPhoneNumbers($apiClient, $contactId, $phoneNumbers);

            $is_wrong = checkPhoneNumbers($phoneNumbers);

            $contactDetails = $apiClient->contacts()->getOne($contactId);

            manageWrongPhoneNumberTag($contactId, $contactDetails, $apiClient, $is_wrong);

            file_put_contents('webhook.log', "Phone Numbers: " . implode(', ', $phoneNumbers) . "\n", FILE_APPEND);
        } else {
            file_put_contents('webhook.log', "Missing id in contact data: " . print_r($contact, true) . "\n", FILE_APPEND);
        }
    }
}


$is_correct = false;

function correctPhoneNumber($phoneNumber)
{
    global $is_correct;

    // Log the initial state of $is_correct
    file_put_contents('log.log', "Initial Status: $is_correct\n", FILE_APPEND);

    $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);

    // Log the cleaned phone number
    file_put_contents('log.log', "Cleaned Phone Number: $phoneNumber\n", FILE_APPEND);

    if (strlen($phoneNumber) == 9) {
        $is_correct = true;
        file_put_contents('log.log', "Status Inside If: $is_correct\n", FILE_APPEND);
        return "998{$phoneNumber}";
    } elseif (strlen($phoneNumber) == 12) {
        $is_correct = true;
        file_put_contents('log.log', "Status Inside ElseIf: $is_correct\n", FILE_APPEND);
        return "{$phoneNumber}";
    } else {
        $is_correct = false;
        file_put_contents('log.log', "Status Inside Else: $is_correct\n", FILE_APPEND);
        return $phoneNumber;
    }
}


function checkPhoneNumbers($phoneNumbers)
{
    foreach ($phoneNumbers as $phoneNumber) {
        $cleanedPhoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);
        if (strlen($cleanedPhoneNumber) != 12 && strlen($cleanedPhoneNumber) != 9) {
            return false;
        }
    }
    return true;
}


function Cheking_phone_numbers($phoneNumber)
{

    if (strlen($phoneNumber) == 12) {
        return true;
    } else {
        return false;
    }
}


file_put_contents('log.log', "Real status global:" . "Is Correct: " . ($is_correct ? 'true' : 'false') . "\n", FILE_APPEND);

function getContactPhoneNumbers($apiClient, $contactId)
{
    $phoneNumbers = [];
    try {
        $contact = $apiClient->contacts()->getOne($contactId);
        $customFields = $contact->getCustomFieldsValues();

        if ($customFields) {
            $phoneField = $customFields->getBy('fieldCode', 'PHONE');
            if ($phoneField) {
                foreach ($phoneField->getValues() as $value) {
                    $phoneNumbers[] = correctPhoneNumber($value->getValue());
                }

                file_put_contents('log.log', 'Phone number array' . json_encode($phoneNumbers) . PHP_EOL, FILE_APPEND);
            }
        }
    } catch (AmoCRMApiException $e) {
        file_put_contents('log.log', 'Error occurred: ' . $e->getMessage() . PHP_EOL, FILE_APPEND);
    }

    return $phoneNumbers;
}

function updateContactPhoneNumbers($apiClient, $contactId, $newPhoneNumbers)
{
    try {
        // Retrieve the contact details
        $contact = $apiClient->contacts()->getOne($contactId);

        // Get the custom fields values
        $customFields = $contact->getCustomFieldsValues();
        $phoneField = null;

        if ($customFields) {
            $phoneField = $customFields->getBy('fieldCode', 'PHONE');
        }

        if (!$phoneField) {
            $phoneField = new MultitextCustomFieldValuesModel();
            $phoneField->setFieldCode('PHONE');
            if (!$customFields) {
                $customFields = new CustomFieldsValuesCollection();
            }
            $customFields->add($phoneField);
        }

        // Create a new array of phone field values
        $phoneFieldValues = $phoneField->getValues();
        $existingPhoneNumbers = [];

        if ($phoneFieldValues instanceof MultitextCustomFieldValueCollection) {
            foreach ($phoneFieldValues as $value) {
                $existingPhoneNumbers[] = $value->getValue();
            }
        }

        $updatedPhoneFieldValues = new MultitextCustomFieldValueCollection();

        foreach ($newPhoneNumbers as $index => $phoneNumber) {
            $correctedPhoneNumber = correctPhoneNumber($phoneNumber);
            $is_correct = Cheking_phone_numbers($correctedPhoneNumber);

            if ($is_correct) {
                $is_correct = 2;
            } else {
                $is_correct = 1;
            }

            file_put_contents('log.log', "----------------------------------" . PHP_EOL, FILE_APPEND);
            file_put_contents('log.log', 'Is it really correct: ' . $is_correct . PHP_EOL, FILE_APPEND);
            file_put_contents('log.log', "----------------------------------" . PHP_EOL, FILE_APPEND);


            if (isset($existingPhoneNumbers[$index])) {
                // Update existing phone numbers
                $updatedPhoneFieldValues->add(
                    (new BaseCustomFieldValueModel())->setValue($correctedPhoneNumber)
                );
            }
        }

        // Set the updated phone values to the phone field
        $phoneField->setValues($updatedPhoneFieldValues);
        $contact->setCustomFieldsValues($customFields);

        // Update the contact
        $apiClient->contacts()->updateOne($contact);

        echo "Contact phone numbers updated successfully.";
        file_put_contents('log.log', "++++++++++++++++++++++++++++++++++++++++" . PHP_EOL, FILE_APPEND);


    } catch (AmoCRMApiException $e) {
        file_put_contents('log.log', 'Error occurred: ' . $e->getMessage() . PHP_EOL, FILE_APPEND);
        echo 'Error occurred: ' . $e->getMessage();
    }
}


function manageWrongPhoneNumberTag($contactId, $contactDetails, $apiClient, $isWrongNumber)
{
    $contactTags = $contactDetails->getTags() ?: new TagsCollection();


    try {




        $tagExists = false;
        foreach ($contactTags as $tag) {
            if ($tag->getName() === 'Wrong Phone number') {
                $tagExists = true;
                break;
            }
        }

        if ($isWrongNumber) {

            if ($tagExists) {
                foreach ($contactTags as $key => $tag) {
                    if ($tag->getName() === 'Wrong Phone number') {
                        $contactTags->offsetUnset($key);
                        file_put_contents('webhook.log', 'Tag "Wrong Phone number" removed from contact' . PHP_EOL, FILE_APPEND);
                        break;
                    }
                }
                $contactDetails->setTags($contactTags);

                try {
                    $apiClient->contacts()->updateOne($contactDetails);
                } catch (AmoCRMApiException $e) {
                    file_put_contents('log.log', 'Error occurred: ' . $e->getMessage() . PHP_EOL, FILE_APPEND);
                }
            }
        } else {
            // Add the tag if it doesn't exist
            if (!$tagExists) {
                $tag = new TagModel();
                $tag->setName('Wrong Phone number');
                $contactTags->add($tag);
                $contactDetails->setTags($contactTags);

                try {
                    $apiClient->contacts()->updateOne($contactDetails);
                    file_put_contents('webhook.log', 'Tag "Wrong Phone number" added to contact' . PHP_EOL, FILE_APPEND);
                } catch (AmoCRMApiException $e) {
                    file_put_contents('error.log', 'Error occurred: ' . $e->getMessage() . PHP_EOL, FILE_APPEND);
                }
            }
        }
    } catch (AmoCRMApiException $e) {
        file_put_contents('error.log', 'Error occurred: ' . $e->getMessage() . PHP_EOL, FILE_APPEND);
    }
}
