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
    'access_token' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjhjMTBlYTQyZjgzZWQxOGM2NWI0M2M0ZDgwNjZjZjc2Mzg3NDIyOWE2ZWJmMjExNDU5YWI3ZjlhM2MxYWQ1MzFiNjc5NDE1NjJhMTNiMjgzIn0.eyJhdWQiOiI2NzY5MDRjNS1lZWUxLTRmYjYtOTM2OS1kMzU3NGM3NzUyNDciLCJqdGkiOiI4YzEwZWE0MmY4M2VkMThjNjViNDNjNGQ4MDY2Y2Y3NjM4NzQyMjlhNmViZjIxMTQ1OWFiN2Y5YTNjMWFkNTMxYjY3OTQxNTYyYTEzYjI4MyIsImlhdCI6MTcyMTI4NzEwNCwibmJmIjoxNzIxMjg3MTA0LCJleHAiOjE3MjEzNzM1MDQsInN1YiI6IjkwNzAxMTQiLCJncmFudF90eXBlIjoiIiwiYWNjb3VudF9pZCI6MzA3NjIyMzAsImJhc2VfZG9tYWluIjoiYW1vY3JtLnJ1IiwidmVyc2lvbiI6Miwic2NvcGVzIjpbInB1c2hfbm90aWZpY2F0aW9ucyIsImZpbGVzIiwiY3JtIiwiZmlsZXNfZGVsZXRlIiwibm90aWZpY2F0aW9ucyJdLCJoYXNoX3V1aWQiOiJiZDg4ODUwYi04ODIwLTQxYmQtYWRmYi0zYWM3YzJlMjNmMTYifQ.NXS7z7kuTUCtTiL6dcjEkxHVCRWpPUL4aJFczkA_8w_KJ0roG8aubSRS05RjZvzyTGfES6tcitCoQ4XAG4sV5LDBgv02KwuAIdq1WCHuwPy2qIie4uQJPGI9679BCkqZRjQT9rLC43UtWPD55IetarD0DL_g2hniMy3PfP3nzr2Ml6ZpMVGQ83DgzTUHqQ24s5z2kXNt4PpWqb6pNj7UaL7yFtnJl9nrJZkLyZRa2oAkFZzEZ-0lY3WUZGXQbf0C4h4TsnqSOMBMta1VlQm1jmvyWL-VlgariCC7h4KgZFYsNMRnSa9b0fVE6QhN9UXH_qdXJPyvQvOB-SOhek4vSg',
    'refresh_token' => 'def502006e9ee60c4b26358ce7a50f27fbe03c6f61501a69fcf3f2f02d18d16b703eccf14183a9bdc1f80b5ef294e5493720f67246af6c9e7d2c49151954df48304eac1684fe5c8403350e922144fcbe62c6b2ab58803cfa42cc821f08303861e1a428a89caab25bd43b8ecba34e02152cfde53e4a6c91936647f70c0ceb200d835fa60d6144e5b49fc97931257c67afaef6668eea83ecfc4a0dc1dc1f4f00901bee61aa9b34c33d7b6150b1c1f5338c77a62292370a190ef8119dbf4660b4c03c6978f46d9249bfb54e551fc52b3606e12615252cc90653ed253211974d0c7bf28730b4ad6bdce9bff2069668d19743880af781c62f253defcb88ecdaad555b28881dee8f89ee45dcc99c38bd08b11ec53efe58f16f5f2c4c17ad5ae0367c982583356943c71ecac72205fde580f24c53b91cc627e3b4e55f8279a10305cb8ca5f8b65c565e8832b7cc8f3f2b931cab14bcc99da1434e100a6456d10543318eda3df843a7b1d3e1997ac822590e8006c82798b22c4d939bfdff5688da0f5fd0efc4f379d46d54585253e2b40675ac0a910a211dcd9fbf053efbafd44f663d50fccc32e546165957911685550a8fbd2c3b6a9fc1afb9d374ccc6524ff35f6186ca8cc6d729f83e655c73ffcda8b1ae122f7cd9ed39076ad0df40dc20cbc2c08bf80d0332fe37df284e1efdf634',
    'expires' => '86400',
    'baseDomain' => 'icpro12.amocrm.ru',
];
$accessToken = new AccessToken($accessTokenArray);
$apiClient->setAccessToken($accessToken);

$apiClient->setAccountBaseDomain($accessTokenArray['baseDomain']);

try {
    $newAccessToken = $apiClient->getOAuthClient()->getAccessTokenByRefreshToken($accessToken);
    echo "<h1> New Access Token: </h1>" . $newAccessToken->getToken() . PHP_EOL ;
    echo '<h1> New Refresh Token: </h1> ' . $newAccessToken->getRefreshToken() . PHP_EOL;
    echo '<h1> Expires in: </h1> ' . $newAccessToken->getExpires() . PHP_EOL;

} catch (AmoCRMApiException $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
}