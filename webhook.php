<?php


require 'vendor/autoload.php';

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Exceptions\AmoCRMApiException;
use League\OAuth2\Client\Token\AccessToken;

use AmoCRM\Collections\TagsCollection;
use AmoCRM\Models\TagModel;


use AmoCRM\Collections\CustomFieldsValuesCollection;
use AmoCRM\Models\CustomFieldsValues\MultiselectCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\ValueModels\MultiselectCustomFieldValueModel;

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use Dotenv\Dotenv;

// Load .env file
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();


//$clientId = '676904c5-eee1-4fb6-9369-d3574c775247';
//$clientSecret = 'LldAD4LMFhGNcHZPdPqcKbyOFJNXOITpmK3p0Gx3GB8DAPfBi3z8dWLQzk7qz23e';
//$redirectUri = 'https://2dfc-185-213-230-4.ngrok-free.app/';
//$apiClient = new AmoCRMApiClient($clientId, $clientSecret, $redirectUri);



$clientId = $_ENV['CLIENT_ID'];
$clientSecret = $_ENV['CLIENT_SECRET'];
$redirectUri = $_ENV['REDIRECT_URI'];
$accessTokenData = [
    'access_token' => $_ENV['ACCESS_TOKEN'],
    'refresh_token' => $_ENV['REFRESH_TOKEN'],
    'expires' => '86400',
    'baseDomain' => $_ENV['BASE_DOMAIN'],
];

//$accessTokenArray = [
//    'access_token' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6ImJkN2QzZjc2MDJlMTVmYjFiODM0MDFiY2M4NTUzMzg5M2FlNjM0ODVmYzI3ZDdiMGU3ZDBlOGM1YzAxYjc4NGUwN2JkMTlhYTg5YTk4OTg1In0.eyJhdWQiOiI2NzY5MDRjNS1lZWUxLTRmYjYtOTM2OS1kMzU3NGM3NzUyNDciLCJqdGkiOiJiZDdkM2Y3NjAyZTE1ZmIxYjgzNDAxYmNjODU1MzM4OTNhZTYzNDg1ZmMyN2Q3YjBlN2QwZThjNWMwMWI3ODRlMDdiZDE5YWE4OWE5ODk4NSIsImlhdCI6MTcyMTgyMDIzMSwibmJmIjoxNzIxODIwMjMxLCJleHAiOjE3MjE5MDY2MzEsInN1YiI6IjkwNzAxMTQiLCJncmFudF90eXBlIjoiIiwiYWNjb3VudF9pZCI6MzA3NjIyMzAsImJhc2VfZG9tYWluIjoiYW1vY3JtLnJ1IiwidmVyc2lvbiI6Miwic2NvcGVzIjpbInB1c2hfbm90aWZpY2F0aW9ucyIsImZpbGVzIiwiY3JtIiwiZmlsZXNfZGVsZXRlIiwibm90aWZpY2F0aW9ucyJdLCJoYXNoX3V1aWQiOiI2OGNlNDA3MS00ZDRlLTQ5MGUtYjA2Yi1mMmMzZmZmM2Q2YWMifQ.ESenffPMstd4_-BCDYqppiXEUuzsvywMaDj03RDeX2XjC0Bmwo_nWjQWnVZ6N63UZUd4JNr0nfZRwTBhBU8NPIH7MKcSDWl1onutQjBQjDmPRn9dQmHAib_UVuE58de4Xe2QtFSbjd7UFSFAS7UmVpJ0Uh1GbPHdLmFTKMhGQdDRO-eUHq8W2LQu3lFEOWbhtpkK8x8Oh0Vpto_vYZYiV1dtKSF9wR1kUObsVfls54p-RAAUgPi_TtEHGTC54uBzMkvkMPA9JFY31P3c-pmBUklVNN0o8rejE3WonGSDanmYpVUeajVIyiPJUEcl5tv7UBc78Ov-ApwY2KPgxjXgKw',
//    'refresh_token' => 'def502001c1e651bff8baba118cc37d1d1295874e025f2c3c38c5cd01ecedd3dee1aa638e51db07e38b56b38b319371471ea3466de946bbc631820115cd10f675e00242fb743f99f8c17d9a3b7b79698afdf87967fce4820971b153c7a32054cbb048999306efe80d2eb2da814c7bf74db8d9c912613482cec3e8120f7a7070b53cdcff2ced35f1f8eb39f9b9f61e0d6c5c5fdf315a680b6d2c8a909a646b21b123c3c2a7c18325a79f4cd0775d93e7b4e7b05e6c13f08ef25e085907cda9d76a0529e11edfbace37e19eb1dda083d2d7527b88840d9f797f1bc354d28bdaf7aa1cd3c22e2319275911aeb826be3801dc69099f1fcf765e7442bd2070d4bd254c798348ac51dbd2635bef4ca73eccadc864bb267b7ef967bada25ef7644042cb46fb2eee80327fd8fba3852e01dc1035288183648a5f44e2d78baeeedd3a8bc3dfd1d221f21a908365d95dc4c1c5e064a3914d8c35d9b70fc09ea0c87ce0d144a52f55865055d3a57978eae0af941627a46cb981e65016035ccc73738bc57d316fcb57515a87186656374832529c5c8d9eadd5601248ac0348710ff607a796eb93addcbd78fceaf8ad09ba5db1285c2fd35603cf26e75933fedd2840520f1e9c4a3bc28efe9219447a033cf37c1682625b38aabac375681770024c8a50de8d7da79a1bd193dc9f3e15cd8c4545',
//    'expires' => '86400',
//    'baseDomain' => 'icpro12.amocrm.ru',
//];

//
//$accessTokenData = [
//    'access_token' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6ImJkN2QzZjc2MDJlMTVmYjFiODM0MDFiY2M4NTUzMzg5M2FlNjM0ODVmYzI3ZDdiMGU3ZDBlOGM1YzAxYjc4NGUwN2JkMTlhYTg5YTk4OTg1In0.eyJhdWQiOiI2NzY5MDRjNS1lZWUxLTRmYjYtOTM2OS1kMzU3NGM3NzUyNDciLCJqdGkiOiJiZDdkM2Y3NjAyZTE1ZmIxYjgzNDAxYmNjODU1MzM4OTNhZTYzNDg1ZmMyN2Q3YjBlN2QwZThjNWMwMWI3ODRlMDdiZDE5YWE4OWE5ODk4NSIsImlhdCI6MTcyMTgyMDIzMSwibmJmIjoxNzIxODIwMjMxLCJleHAiOjE3MjE5MDY2MzEsInN1YiI6IjkwNzAxMTQiLCJncmFudF90eXBlIjoiIiwiYWNjb3VudF9pZCI6MzA3NjIyMzAsImJhc2VfZG9tYWluIjoiYW1vY3JtLnJ1IiwidmVyc2lvbiI6Miwic2NvcGVzIjpbInB1c2hfbm90aWZpY2F0aW9ucyIsImZpbGVzIiwiY3JtIiwiZmlsZXNfZGVsZXRlIiwibm90aWZpY2F0aW9ucyJdLCJoYXNoX3V1aWQiOiI2OGNlNDA3MS00ZDRlLTQ5MGUtYjA2Yi1mMmMzZmZmM2Q2YWMifQ.ESenffPMstd4_-BCDYqppiXEUuzsvywMaDj03RDeX2XjC0Bmwo_nWjQWnVZ6N63UZUd4JNr0nfZRwTBhBU8NPIH7MKcSDWl1onutQjBQjDmPRn9dQmHAib_UVuE58de4Xe2QtFSbjd7UFSFAS7UmVpJ0Uh1GbPHdLmFTKMhGQdDRO-eUHq8W2LQu3lFEOWbhtpkK8x8Oh0Vpto_vYZYiV1dtKSF9wR1kUObsVfls54p-RAAUgPi_TtEHGTC54uBzMkvkMPA9JFY31P3c-pmBUklVNN0o8rejE3WonGSDanmYpVUeajVIyiPJUEcl5tv7UBc78Ov-ApwY2KPgxjXgKw',
//    'refresh_token' => 'def502001c1e651bff8baba118cc37d1d1295874e025f2c3c38c5cd01ecedd3dee1aa638e51db07e38b56b38b319371471ea3466de946bbc631820115cd10f675e00242fb743f99f8c17d9a3b7b79698afdf87967fce4820971b153c7a32054cbb048999306efe80d2eb2da814c7bf74db8d9c912613482cec3e8120f7a7070b53cdcff2ced35f1f8eb39f9b9f61e0d6c5c5fdf315a680b6d2c8a909a646b21b123c3c2a7c18325a79f4cd0775d93e7b4e7b05e6c13f08ef25e085907cda9d76a0529e11edfbace37e19eb1dda083d2d7527b88840d9f797f1bc354d28bdaf7aa1cd3c22e2319275911aeb826be3801dc69099f1fcf765e7442bd2070d4bd254c798348ac51dbd2635bef4ca73eccadc864bb267b7ef967bada25ef7644042cb46fb2eee80327fd8fba3852e01dc1035288183648a5f44e2d78baeeedd3a8bc3dfd1d221f21a908365d95dc4c1c5e064a3914d8c35d9b70fc09ea0c87ce0d144a52f55865055d3a57978eae0af941627a46cb981e65016035ccc73738bc57d316fcb57515a87186656374832529c5c8d9eadd5601248ac0348710ff607a796eb93addcbd78fceaf8ad09ba5db1285c2fd35603cf26e75933fedd2840520f1e9c4a3bc28efe9219447a033cf37c1682625b38aabac375681770024c8a50de8d7da79a1bd193dc9f3e15cd8c4545',
//    'expires' => '86400',
//    'baseDomain' => 'icpro12.amocrm.ru',
//];

$apiClient = new AmoCRMApiClient($clientId, $clientSecret, $redirectUri);
$accessToken = new AccessToken($accessTokenData);
$apiClient->setAccessToken($accessToken)
    ->setAccountBaseDomain($accessToken->getValues()['baseDomain']);

$input = file_get_contents('php://input');

// Log the raw POST data
file_put_contents('webhook.log', "Raw POST Data: " . $input . "\n", FILE_APPEND);

// Decode the URL-encoded data
parse_str($input, $data);

// Log the decoded data
file_put_contents('webhook.log', "Decoded Data: " . print_r($data, true) . "\n", FILE_APPEND);

if (isset($data['contacts']['update'])) {
    foreach ($data['contacts']['update'] as $contact) {
        // Initialize a variable to store the phone number
        $phoneNumber = 'Not provided';

        // Check if the phone field has changed and retrieve the phone number
        foreach ($contact['custom_fields'] as $field) {
            if ($field['code'] == 'PHONE' && !empty($field['values'])) {
                // Assuming the phone number is in the first value
                $phoneNumber = $field['values'][0]['value'];
                break; // Exit the loop once the phone number is found
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
                                // Assuming we want to update the first phone number in the list
                                $phoneValues[0]->setValue($phoneNumber);
                                $phoneField->setValues($phoneValues);
                            } else {
                                // If there are no phone values, add a new one
                                $phoneField->addValue(
                                    (new MultiselectCustomFieldValueModel())->setValue($phoneNumber)
                                );
                            }
                        } else {
                            // If there is no phone field, create a new one
                            $customFields->add(
                                (new MultiselectCustomFieldValuesModel())
                                    ->setFieldCode('PHONE')
                                    ->setValues(
                                        [(new MultiselectCustomFieldValueModel())->setValue($phoneNumber)]
                                    )
                            );
                        }
                    } else {
                        // If there are no custom fields, create a new phone field
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

                    $apiClient->contacts()->updateOne($contactDetails);

                } catch (AmoCRMApiException $e) {
                    file_put_contents('log.log', 'Error occurred: ' . $e->getMessage() . PHP_EOL, FILE_APPEND);
                }

                file_put_contents('webhook.log', "Phone Number: $phoneNumber is correct\n", FILE_APPEND);

                // Respond to amoCRM to acknowledge receipt
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

                    // Add the tag to the contact
                    $contactTags = $contactDetails->getTags() ?: new TagsCollection();
                    $contactTags->add($tag);
                    $contactDetails->setTags($contactTags);

                    // Update the contact
                    $apiClient->contacts()->updateOne($contactDetails);

                    // Log the tag addition
                    file_put_contents('webhook.log', 'Tag "Wrong Phone number" added to contact' . PHP_EOL, FILE_APPEND);
                } catch (AmoCRMApiException $e) {
                    file_put_contents('log.log', 'Error occurred: ' . $e->getMessage() . PHP_EOL, FILE_APPEND);
                }

                file_put_contents('webhook.log', "Phone Number: $phoneNumber\n not correct", FILE_APPEND);

                // Respond to amoCRM to acknowledge receipt
                header('Content-Type: application/json');
                echo json_encode(['status' => 'success']);
            }


        } catch (AmoCRMApiException $e) {
            file_put_contents('webhook.log', "Error: " . $e->getMessage() . "\n", FILE_APPEND);
        }
    }
} else {
    // Log if the expected data structure is not found
    file_put_contents('webhook.log', "Expected data structure not found\n", FILE_APPEND);
}

// Respond to amoCRM to acknowledge receipt
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
