<?php

require_once 'vendor/autoload.php';

use AmoCRM\Client\AmoCRMApiClient;
use League\OAuth2\Client\Token\AccessToken;
use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Filters\ContactsFilter;
use AmoCRM\Models\ContactModel;

$clientId = '676904c5-eee1-4fb6-9369-d3574c775247';
$clientSecret = 'LldAD4LMFhGNcHZPdPqcKbyOFJNXOITpmK3p0Gx3GB8DAPfBi3z8dWLQzk7qz23e';
$redirectUri = 'https://b2c7-90-156-160-11.ngrok-free.app/';
$apiClient = new AmoCRMApiClient($clientId, $clientSecret, $redirectUri);

$accessTokenArray = [
    'access_token' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjYxNTA1NmE4NmMzNjMzMGM0ZmRhYzBlZWQyYjExN2U1ZjBkNDM1ZjA1ZDk5YjQ2YTU4YTMyNDhmZGRlODZiZmY0MzIxYzJhZWY0NTEwMWUzIn0.eyJhdWQiOiI2NzY5MDRjNS1lZWUxLTRmYjYtOTM2OS1kMzU3NGM3NzUyNDciLCJqdGkiOiI2MTUwNTZhODZjMzYzMzBjNGZkYWMwZWVkMmIxMTdlNWYwZDQzNWYwNWQ5OWI0NmE1OGEzMjQ4ZmRkZTg2YmZmNDMyMWMyYWVmNDUxMDFlMyIsImlhdCI6MTcyMTMxMTk0OCwibmJmIjoxNzIxMzExOTQ4LCJleHAiOjE3MjEzOTgzNDgsInN1YiI6IjkwNzAxMTQiLCJncmFudF90eXBlIjoiIiwiYWNjb3VudF9pZCI6MzA3NjIyMzAsImJhc2VfZG9tYWluIjoiYW1vY3JtLnJ1IiwidmVyc2lvbiI6Miwic2NvcGVzIjpbInB1c2hfbm90aWZpY2F0aW9ucyIsImZpbGVzIiwiY3JtIiwiZmlsZXNfZGVsZXRlIiwibm90aWZpY2F0aW9ucyJdLCJoYXNoX3V1aWQiOiJiNGRkYzBhOC1hZTQwLTQ1NzUtYjcwYS0xYzc3NDcyNzg3MjkifQ.r9XJKe-vZLTjKwRGwbuswbeVVd_7sdvxA4V3bDmtjuB5kbSftcg3YmpF0xEivAPtduLJt-HY7ynf2qn_689s48OqVWBuzcpxoJK4m6wmE5QI5LKbaRMZasOt2K40ee16XxRbRxV0KN39D7pcjGqiW-fAJl6laujWoWsGbOK6vs2I5SgHVIVzHcIttpRi-VJP4dxLait7FFMGFD4zsIQxqZem58Pt7XjUxuTRmcuOxNWFdOLMGPHALWTqyDkUTzVGwHwUaOUmKVMPxhtmm4sydKrlA1Wfgs-EApEmkX6OuYy2hnfFZBJT_Koz7Nu4rBLWL5TZE7XLYTojpxxWuDsUbw.eyJhdWQiOiI2NzY5MDRjNS1lZWUxLTRmYjYtOTM2OS1kMzU3NGM3NzUyNDciLCJqdGkiOiI4YzEwZWE0MmY4M2VkMThjNjViNDNjNGQ4MDY2Y2Y3NjM4NzQyMjlhNmViZjIxMTQ1OWFiN2Y5YTNjMWFkNTMxYjY3OTQxNTYyYTEzYjI4MyIsImlhdCI6MTcyMTI4NzEwNCwibmJmIjoxNzIxMjg3MTA0LCJleHAiOjE3MjEzNzM1MDQsInN1YiI6IjkwNzAxMTQiLCJncmFudF90eXBlIjoiIiwiYWNjb3VudF9pZCI6MzA3NjIyMzAsImJhc2VfZG9tYWluIjoiYW1vY3JtLnJ1IiwidmVyc2lvbiI6Miwic2NvcGVzIjpbInB1c2hfbm90aWZpY2F0aW9ucyIsImZpbGVzIiwiY3JtIiwiZmlsZXNfZGVsZXRlIiwibm90aWZpY2F0aW9ucyJdLCJoYXNoX3V1aWQiOiJiZDg4ODUwYi04ODIwLTQxYmQtYWRmYi0zYWM3YzJlMjNmMTYifQ.NXS7z7kuTUCtTiL6dcjEkxHVCRWpPUL4aJFczkA_8w_KJ0roG8aubSRS05RjZvzyTGfES6tcitCoQ4XAG4sV5LDBgv02KwuAIdq1WCHuwPy2qIie4uQJPGI9679BCkqZRjQT9rLC43UtWPD55IetarD0DL_g2hniMy3PfP3nzr2Ml6ZpMVGQ83DgzTUHqQ24s5z2kXNt4PpWqb6pNj7UaL7yFtnJl9nrJZkLyZRa2oAkFZzEZ-0lY3WUZGXQbf0C4h4TsnqSOMBMta1VlQm1jmvyWL-VlgariCC7h4KgZFYsNMRnSa9b0fVE6QhN9UXH_qdXJPyvQvOB-SOhek4vSg',
    'refresh_token' => 'def50200898c5bac430be12b30784f613fb94edd4284fe4a431617f4b757150ab5f2bdfda25d58bf8a49413e9d70a11b812778616967c5d9210b0e845c85fae03979b2b2f8e22ac96ec1111a07e5925b415b31070f4c72b43c71073719c442d3e332f7114ffa6ab9d589345642b18f83e4312350e2c5e32baab4893c5dc193e41f59b4606d0320bfecf483190d99f54304c98aca704278c7abf61da66aab5fa2056e2f1fcdc068a34ee86c6e41066dcbf1eccbeb973d6b8f281d717b5dddf4a6ee66c941e311fdc099c77ac7cecaad3a6d7f29bd66c9a03c6d4fb5a301d22d60b65480cc34efe4d4d79a7c27a92a964435629824e87aacf7eb91b211e24644b7d1ebc524fca653002dcc7e09f2584ec4de71a24649f3273b908b436cfb24d506d3a3864028b97b65441a9f4a42f97ad9af63e32170122ad4ba9b888472f9b497bde0a1aad71b2eede6e556d9ae2368a9647b34293382863e08d25c88ff9708d8920ae2cda645f5dc5d235dacd6f4e2528b878b0c25d0c2d4096ccd2411e736d280974c88f91e059deaa8cc56b709e0bd91ae2edc3abf6dc938026e4365de197e194df7f5fe2d07ae74ffdb8b04e43fd3ff4e42ce104880bf4ae609de4af97468e5852f415ed198b959a12be132eacbdd74787812a0652e583e9f6d2d750326b78ff95fc2371ec058beda862832',
    'expires' => '86400',
    'baseDomain' => 'icpro12.amocrm.ru',
];
$accessToken = new AccessToken($accessTokenArray);
$apiClient->setAccessToken($accessToken);

$apiClient->setAccountBaseDomain($accessTokenArray['baseDomain']);

$phone_arr = [];

try {
    $contactsService = $apiClient->contacts();
    $contactsFilter = new ContactsFilter();
    $contactsCollection = $contactsService->get($contactsFilter);

    foreach ($contactsCollection as $contact) {
        /** @var ContactModel $contact */
        echo 'Contact Name: ' . $contact->getName() . PHP_EOL;

        $customFields = $contact->getCustomFieldsValues();
        $phoneField = $customFields->getBy('fieldCode', 'PHONE');
        if ($phoneField) {
            $phones = $phoneField->getValues();

            echo '<h1> Phone array : </h1>' . $phones . '<br>';
            foreach ($phones as $phone) {
                $phone_arr[] = $phone->getValue() ;
            }
        }

    }
    echo '<h1> Phone number array : </h1>' .  implode(" ,  \n ", $phone_arr) . '<br>';

    function Correct_user_phone_number($arr)
    {
        return array_map(function ($n) {
            $n = preg_replace('/[\s+\-\(\)]/', '', $n);

            if (strlen($n) >= 13 || strlen($n) < 9) {
                return "Invalid phone number: $n \n";
            } else if (strlen($n) == 9) {
                return 'Updated phone number: 998' . $n . "\n";
            } else {
                return "Correct phone number: $n \n";
            }


        }, $arr);


    }
    $result = Correct_user_phone_number($phone_arr);

    echo implode(" ,  \n ", $result);

} catch (AmoCRMApiException $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
}




