<?php
require 'vendor/autoload.php';

use AmoCRM\Client\AmoCRMApiClient;
use League\OAuth2\Client\Token\AccessToken;
use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Models\ContactModel;
use AmoCRM\Filters\ContactsFilter;

use AmoCRM\Models\CustomFieldsValues\MultitextCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\MultitextCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueModels\MultitextCustomFieldValueModel;

$clientId = '676904c5-eee1-4fb6-9369-d3574c775247';
$clientSecret = 'LldAD4LMFhGNcHZPdPqcKbyOFJNXOITpmK3p0Gx3GB8DAPfBi3z8dWLQzk7qz23e';
$redirectUri = 'https://d6d7-213-230-102-28.ngrok-free.app/';
$apiClient = new AmoCRMApiClient($clientId, $clientSecret, $redirectUri);

$accessTokenArray = [
    'access_token' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6ImJkN2QzZjc2MDJlMTVmYjFiODM0MDFiY2M4NTUzMzg5M2FlNjM0ODVmYzI3ZDdiMGU3ZDBlOGM1YzAxYjc4NGUwN2JkMTlhYTg5YTk4OTg1In0.eyJhdWQiOiI2NzY5MDRjNS1lZWUxLTRmYjYtOTM2OS1kMzU3NGM3NzUyNDciLCJqdGkiOiJiZDdkM2Y3NjAyZTE1ZmIxYjgzNDAxYmNjODU1MzM4OTNhZTYzNDg1ZmMyN2Q3YjBlN2QwZThjNWMwMWI3ODRlMDdiZDE5YWE4OWE5ODk4NSIsImlhdCI6MTcyMTgyMDIzMSwibmJmIjoxNzIxODIwMjMxLCJleHAiOjE3MjE5MDY2MzEsInN1YiI6IjkwNzAxMTQiLCJncmFudF90eXBlIjoiIiwiYWNjb3VudF9pZCI6MzA3NjIyMzAsImJhc2VfZG9tYWluIjoiYW1vY3JtLnJ1IiwidmVyc2lvbiI6Miwic2NvcGVzIjpbInB1c2hfbm90aWZpY2F0aW9ucyIsImZpbGVzIiwiY3JtIiwiZmlsZXNfZGVsZXRlIiwibm90aWZpY2F0aW9ucyJdLCJoYXNoX3V1aWQiOiI2OGNlNDA3MS00ZDRlLTQ5MGUtYjA2Yi1mMmMzZmZmM2Q2YWMifQ.ESenffPMstd4_-BCDYqppiXEUuzsvywMaDj03RDeX2XjC0Bmwo_nWjQWnVZ6N63UZUd4JNr0nfZRwTBhBU8NPIH7MKcSDWl1onutQjBQjDmPRn9dQmHAib_UVuE58de4Xe2QtFSbjd7UFSFAS7UmVpJ0Uh1GbPHdLmFTKMhGQdDRO-eUHq8W2LQu3lFEOWbhtpkK8x8Oh0Vpto_vYZYiV1dtKSF9wR1kUObsVfls54p-RAAUgPi_TtEHGTC54uBzMkvkMPA9JFY31P3c-pmBUklVNN0o8rejE3WonGSDanmYpVUeajVIyiPJUEcl5tv7UBc78Ov-ApwY2KPgxjXgKw',
    'refresh_token' => 'def502001c1e651bff8baba118cc37d1d1295874e025f2c3c38c5cd01ecedd3dee1aa638e51db07e38b56b38b319371471ea3466de946bbc631820115cd10f675e00242fb743f99f8c17d9a3b7b79698afdf87967fce4820971b153c7a32054cbb048999306efe80d2eb2da814c7bf74db8d9c912613482cec3e8120f7a7070b53cdcff2ced35f1f8eb39f9b9f61e0d6c5c5fdf315a680b6d2c8a909a646b21b123c3c2a7c18325a79f4cd0775d93e7b4e7b05e6c13f08ef25e085907cda9d76a0529e11edfbace37e19eb1dda083d2d7527b88840d9f797f1bc354d28bdaf7aa1cd3c22e2319275911aeb826be3801dc69099f1fcf765e7442bd2070d4bd254c798348ac51dbd2635bef4ca73eccadc864bb267b7ef967bada25ef7644042cb46fb2eee80327fd8fba3852e01dc1035288183648a5f44e2d78baeeedd3a8bc3dfd1d221f21a908365d95dc4c1c5e064a3914d8c35d9b70fc09ea0c87ce0d144a52f55865055d3a57978eae0af941627a46cb981e65016035ccc73738bc57d316fcb57515a87186656374832529c5c8d9eadd5601248ac0348710ff607a796eb93addcbd78fceaf8ad09ba5db1285c2fd35603cf26e75933fedd2840520f1e9c4a3bc28efe9219447a033cf37c1682625b38aabac375681770024c8a50de8d7da79a1bd193dc9f3e15cd8c4545',
    'expires' => '86400',
    'baseDomain' => 'icpro12.amocrm.ru',
];
$accessToken = new AccessToken($accessTokenArray);
$apiClient->setAccessToken($accessToken);
$apiClient->setAccountBaseDomain($accessTokenArray['baseDomain']);
try {
    $contactsService = $apiClient->contacts();
    $contactsFilter = new ContactsFilter();
    $contactsCollection = $contactsService->get($contactsFilter);

    foreach ($contactsCollection as $contact) {
        /** @var ContactModel $contact */
        echo 'Contact Name: ' . $contact->getName() . "<br>";
        echo "ID: " . $contact->getId() . "<br>";
        $contactId = $contact->getId();
        $contact = $apiClient->contacts()->getOne($contactId);

        $customFields = $contact->getCustomFieldsValues();
        $phoneField = $customFields->getBy('fieldCode', 'PHONE');

        if ($phoneField) {
            $phones = $phoneField->getValues();

            foreach ($phones as $phoneValue) {
                $phoneNumber = $phoneValue->getValue();
                $correctedPhoneNumber = correctPhoneNumber($phoneNumber);

                if ($correctedPhoneNumber !== $phoneNumber) {
                    $phoneValue->setValue($correctedPhoneNumber);
                    $contact->setCustomFieldsValues($customFields);

                    try {
                        $apiClient->contacts()->updateOne($contact);
                        echo "Contact ID {$contactId} updated successfully with phone number {$correctedPhoneNumber}.<br>";
                    } catch (AmoCRMApiException $e) {
                        echo "Error updating contact ID {$contactId}: " . $e->getMessage() . "<br>";
                    }
                }
            }
        } else {
            echo "No phone field found for contact ID {$contactId}.<br>";
        }
    }
} catch (AmoCRMApiException $e) {
    echo "Error fetching contacts: " . $e->getMessage();
}

function correctPhoneNumber($phoneNumber)
{
    $phoneNumber = preg_replace('/[^0-9]/', '',  $phoneNumber);

    if (strlen($phoneNumber) >= 13 || strlen($phoneNumber) < 9) {
        return "неправильный номер телефона: $phoneNumber ";
    } elseif (strlen($phoneNumber) == 9) {
//        обновлен номер телефона:
        return " 998{$phoneNumber}";
    } else {
        return $phoneNumber;
    }
}

echo "<h1>Main.php</h1>";
echo "test";