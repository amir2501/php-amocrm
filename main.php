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
    'access_token' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6ImZkNzI3NzkzOTRhMzU3MDFmZTAxNWNkYmJiZmEzYTc1YjlmYWZmZDYyMmRhMTBkOWI1MmY0NWQyYzM1ODIwZmQ0NWI3ZTMzZTM2ZTMwMGU1In0.eyJhdWQiOiI2NzY5MDRjNS1lZWUxLTRmYjYtOTM2OS1kMzU3NGM3NzUyNDciLCJqdGkiOiJmZDcyNzc5Mzk0YTM1NzAxZmUwMTVjZGJiYmZhM2E3NWI5ZmFmZmQ2MjJkYTEwZDliNTJmNDVkMmMzNTgyMGZkNDViN2UzM2UzNmUzMDBlNSIsImlhdCI6MTcyMTcyMDkwNCwibmJmIjoxNzIxNzIwOTA0LCJleHAiOjE3MjE4MDczMDQsInN1YiI6IjkwNzAxMTQiLCJncmFudF90eXBlIjoiIiwiYWNjb3VudF9pZCI6MzA3NjIyMzAsImJhc2VfZG9tYWluIjoiYW1vY3JtLnJ1IiwidmVyc2lvbiI6Miwic2NvcGVzIjpbInB1c2hfbm90aWZpY2F0aW9ucyIsImZpbGVzIiwiY3JtIiwiZmlsZXNfZGVsZXRlIiwibm90aWZpY2F0aW9ucyJdLCJoYXNoX3V1aWQiOiJkODcyMDg0Mi02YzYxLTQzNTUtOGMxMi04ODJhMThhMmNkZDgifQ.lQOILkI9vse7GilRYYK2B1jlO10KuZHJQsSxxQ5I3IA1acSH3nZ7LwFrsPMN2Xi2QEPf9vEa0x28tWCpqitW_oq307gtBV_Vw4FMRgLL5UWoFAvkUDyG8yJDFY9oNz0_6t12RuoGK317o7btNii8k1rNUZdqgoTAp-B4YHgEqqVMOJlFwtyC-sqvwQBt-allYWaT7jOxh_oY_2snUUoJm9bbtUyrFyTQUBKJMyc2XHNvvupGDlsA9kYslrZ7ECiO6yMFinujba5vxYFokuQUbrS3Sc1keJlmryJptBmsmdQw9_Fo8FvNf9md_7sbFetQfEy4HCV6GUybDaxgADbSmA',
    'refresh_token' => 'def5020066d2c7eae01025d1b3f1e7c3324641f650022e3e9a76e327ef8b8a9db722e787958132e00bc3d31f0a5d07d556a718fb0d93c195103c4cc43945f9c79b1a31d3fae34f4515abb936bf58efc8dc49b8dce4e837d52055200214117ac416452c797f89a33e7ceaf0a3968d143531fc376fe626eb26f0e9108a34c9bba325343742844be1de3434d81a4984d9f9a88965069e176c6291721034f386a6d5f5d75e00f2d945769a618034448487a3f723f664209307805049a332357b6acac7ba8364122cd0a730e03361d57638a8fef064d491f3090569fdcdcc12c4a4dca5c95dc1b06498f0343ca4d14ca601ec99832b806e9d75ed9c37f0a0af5223cf140ea44e51a19790a1dcefd9dbe9b53001d42ac98ad8207868f61e34d9fb0ca8a334ff0899a4ef43b1a73ebed573f8bb9090c8b26e95a55f66f108ae43724433c8d2f558053b7b646fcad10810d465dcedebc934f76cc741d0fb862fed3a9b8474f85943441cd8e9a7ad22ea6885d4c63b7c798e628112a5c74ed834eadcdfc233a47cee0d15e822b95ba49dc724a664ab7f5624d2fc5c0e776e6a74e0213e79f482307d54e0a0998fa93a1adf279369316eadaada060d1760c5ffee154547e8c946a9bbc8b3f9b60a3b42c0fbc0bc1b943c62e521f1d2bebbce75a29b8375b9a886a7f55ed8ff8d96538ef1d2',
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