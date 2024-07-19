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
    'access_token' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjYxNTA1NmE4NmMzNjMzMGM0ZmRhYzBlZWQyYjExN2U1ZjBkNDM1ZjA1ZDk5YjQ2YTU4YTMyNDhmZGRlODZiZmY0MzIxYzJhZWY0NTEwMWUzIn0.eyJhdWQiOiI2NzY5MDRjNS1lZWUxLTRmYjYtOTM2OS1kMzU3NGM3NzUyNDciLCJqdGkiOiI2MTUwNTZhODZjMzYzMzBjNGZkYWMwZWVkMmIxMTdlNWYwZDQzNWYwNWQ5OWI0NmE1OGEzMjQ4ZmRkZTg2YmZmNDMyMWMyYWVmNDUxMDFlMyIsImlhdCI6MTcyMTMxMTk0OCwibmJmIjoxNzIxMzExOTQ4LCJleHAiOjE3MjEzOTgzNDgsInN1YiI6IjkwNzAxMTQiLCJncmFudF90eXBlIjoiIiwiYWNjb3VudF9pZCI6MzA3NjIyMzAsImJhc2VfZG9tYWluIjoiYW1vY3JtLnJ1IiwidmVyc2lvbiI6Miwic2NvcGVzIjpbInB1c2hfbm90aWZpY2F0aW9ucyIsImZpbGVzIiwiY3JtIiwiZmlsZXNfZGVsZXRlIiwibm90aWZpY2F0aW9ucyJdLCJoYXNoX3V1aWQiOiJiNGRkYzBhOC1hZTQwLTQ1NzUtYjcwYS0xYzc3NDcyNzg3MjkifQ.r9XJKe-vZLTjKwRGwbuswbeVVd_7sdvxA4V3bDmtjuB5kbSftcg3YmpF0xEivAPtduLJt-HY7ynf2qn_689s48OqVWBuzcpxoJK4m6wmE5QI5LKbaRMZasOt2K40ee16XxRbRxV0KN39D7pcjGqiW-fAJl6laujWoWsGbOK6vs2I5SgHVIVzHcIttpRi-eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6ImI3NjJlM2E0NjdiNzU3YTY3MTBmYzIzMDRkZWU2ZTUwODRjZDkzNDc1ZjhlMGQ4MzI3YjM0MWMzODU2YjJiY2Y3NTJkOWVmNGRjMTJkODYxIn0.eyJhdWQiOiI2NzY5MDRjNS1lZWUxLTRmYjYtOTM2OS1kMzU3NGM3NzUyNDciLCJqdGkiOiJiNzYyZTNhNDY3Yjc1N2E2NzEwZmMyMzA0ZGVlNmU1MDg0Y2Q5MzQ3NWY4ZTBkODMyN2IzNDFjMzg1NmIyYmNmNzUyZDllZjRkYzEyZDg2MSIsImlhdCI6MTcyMTM2MjU5OSwibmJmIjoxNzIxMzYyNTk5LCJleHAiOjE3MjE0NDg5OTksInN1YiI6IjkwNzAxMTQiLCJncmFudF90eXBlIjoiIiwiYWNjb3VudF9pZCI6MzA3NjIyMzAsImJhc2VfZG9tYWluIjoiYW1vY3JtLnJ1IiwidmVyc2lvbiI6Miwic2NvcGVzIjpbInB1c2hfbm90aWZpY2F0aW9ucyIsImZpbGVzIiwiY3JtIiwiZmlsZXNfZGVsZXRlIiwibm90aWZpY2F0aW9ucyJdLCJoYXNoX3V1aWQiOiI1Mjg1NjgwOC1iNzhhLTQ2OGYtODIwZS0yNGQ2YjcwOGFhMmQifQ.etxtp5sjGAj2WpZS3BOauCa7tHbODSrhIY1sBJdCW60tOSddz-lMYdEwwGZ6deVvexuuIP9JaZrKt7VYQkRE6BeEDl7nnUk4s8Pdxz2AoJNS4oXMbv3zWolJrdA_UORvKKhN5K3Xoxm2l_78x-aYjtgYicCHPcxHHPTrKDKyfGlsEvT64EIYDHXMZv4PuEvwXneNoTNr5cIQ31PhO-e6tk0MbgP3JlZKMjZZWNtD-cv9RgZtGV_1FwpfGyvnPOkf7mJ1TDDXc5rsQz50y2K4MU1QFCxJIXw-yS7uwD6sp6gi2PPELVEhL859jfOQCeeDztioHlXjvCTqg1bVmzgoFA-EApEmkX6OuYy2hnfFZBJT_Koz7Nu4rBLWL5TZE7XLYTojpxxWuDsUbw.eyJhdWQiOiI2NzY5MDRjNS1lZWUxLTRmYjYtOTM2OS1kMzU3NGM3NzUyNDciLCJqdGkiOiI4YzEwZWE0MmY4M2VkMThjNjViNDNjNGQ4MDY2Y2Y3NjM4NzQyMjlhNmViZjIxMTQ1OWFiN2Y5YTNjMWFkNTMxYjY3OTQxNTYyYTEzYjI4MyIsImlhdCI6MTcyMTI4NzEwNCwibmJmIjoxNzIxMjg3MTA0LCJleHAiOjE3MjEzNzM1MDQsInN1YiI6IjkwNzAxMTQiLCJncmFudF90eXBlIjoiIiwiYWNjb3VudF9pZCI6MzA3NjIyMzAsImJhc2VfZG9tYWluIjoiYW1vY3JtLnJ1IiwidmVyc2lvbiI6Miwic2NvcGVzIjpbInB1c2hfbm90aWZpY2F0aW9ucyIsImZpbGVzIiwiY3JtIiwiZmlsZXNfZGVsZXRlIiwibm90aWZpY2F0aW9ucyJdLCJoYXNoX3V1aWQiOiJiZDg4ODUwYi04ODIwLTQxYmQtYWRmYi0zYWM3YzJlMjNmMTYifQ.NXS7z7kuTUCtTiL6dcjEkxHVCRWpPUL4aJFczkA_8w_KJ0roG8aubSRS05RjZvzyTGfES6tcitCoQ4XAG4sV5LDBgv02KwuAIdq1WCHuwPy2qIie4uQJPGI9679BCkqZRjQT9rLC43UtWPD55IetarD0DL_g2hniMy3PfP3nzr2Ml6ZpMVGQ83DgzTUHqQ24s5z2kXNt4PpWqb6pNj7UaL7yFtnJl9nrJZkLyZRa2oAkFZzEZ-0lY3WUZGXQbf0C4h4TsnqSOMBMta1VlQm1jmvyWL-VlgariCC7h4KgZFYsNMRnSa9b0fVE6QhN9UXH_qdXJPyvQvOB-SOhek4vSg',
    'refresh_token' => 'def50200c48746844b6c2279ddc99d4fe28d44351fbe1411a0b104d8ef412c72af7c29b47f299e35c38dbd923bd880777765638f09d39a774a3452af3f2ce3a7bdf0cadb0bf13cd2ef866e13b74064b472f0a47902ced8cc9b87f770f2b08b98c4ece2eab60db4250663451faba69dd05b3881eb54f9cf6d9b81e78a35a34700fc541815bca77ab6399fc5d5ed874e583ac0099ce0dea1bfc9190ef9923a57c170624b3ab7dec3f374cf16fbe20b8c55d742c2414f07e599977b86b5c872959cc7bd382d5fb2077f253aab45251653ac6fdefd3865b36b4ed21d1a3a180f0a7328e8d6f3042e1ce814c49eca4c8607ac2920eb81c3ac897d70ae1b8a8a7d9e6ab04f20ecbf701d6b055f9d26cb7130d95236ec4aaea356285330e4a638ef4615ebd3d29c57c6fd0a85884727dfdad8ad6b53818e4fc9da8264813ac623203ff7ddd9e5cbcc7ba6cc6e1cc41e16f0284c8259ceacbc7d1479311f82004348535c93de6f22b54c01aebb930553a70016f7289d4ff1b27c7b4fe4d3ef484c247c28027a703621a6efd6c80f0c183d351b36baca7384c51c4fc73b95553c3d70c25e3198694d99079bd69efe4641ef821f3f1b645f2bbcc32814e73817581447a654306187b1abdb33a816ce1823ffd054dd48e8378f4ffa64102139cb1010059f08a6abd2f598dd4c76135e447250',
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




