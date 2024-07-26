<?php

$subdomain = 'icpro12'; //Поддомен нужного аккаунта
$link = 'https://' . $subdomain . '.amocrm.ru/oauth2/access_token'; //Формируем URL для запроса

/** Соберем данные для запроса */
$data = [
    'client_id' => '676904c5-eee1-4fb6-9369-d3574c775247',
    'client_secret' => 'LldAD4LMFhGNcHZPdPqcKbyOFJNXOITpmK3p0Gx3GB8DAPfBi3z8dWLQzk7qz23e',
    'grant_type' => 'authorization_code',
    'code' => 'def502003fdf9ff7232301bba16bd6d11ae898dfd8c6d661b4cd96571bf372024d407cf780fc3e34b88be5425bfdbae165e4adb346f8be22f3c0b7f4a23fdd361274caf7d9ff76a4f2de8d58f84f4506aab2501652b789fd9ab570adddf5313de099092bbe71d82f11fb19e732b5ceee48f5fb0288c232ef5b46e9e73aedd3c70a9365bea2adefb141473e71893ecd6ad2d5bee8a01fe6573d3b3a6eebb55d7127ce6c8a41025da0662b3a97c355798ac97f09fd4c1914c55f557e228b95af41a23b94e28874fd095dee5bd5952873e15c81f9304ea43a16df00a89a8fa2581d1daf513e7bf20755a566203cf7ed99f577656fcb804d1c2123a2a86ada3620a150e8249e6fa586a8334ff515702e77adcb548142573994b75d754a996185eab821c728b23f0a24d475569820b59fec64d6bf16e18bc23418bd9d34883afba6e9bb1c2bd79cb7d9e82b60aac59b0033da0c13508cfbb65e31275881235d50f26ada7daa1d000291b2b6298af868bb24b78d290d21e3b4154e801103955970d57a9ef0f7cc7d09fbee76676825354fe88bc53792c7d7dce8ee5e8678433e98a285eceda71b54928e34f514d0c0a3ea56eed6b3134813418b9a4cecd7176ebb330d13fb4a27339d597f5061f70684672a1e0f23a2b5e4baed6c7420445d011cfea4381c7e290d007e65caf23dbf753e43c8a6d0d32529d6ae5c6d903c',
    'redirect_uri' => 'https://2dfc-185-213-230-4.ngrok-free.app/',
];

/**
 * Нам необходимо инициировать запрос к серверу.
 * Воспользуемся библиотекой cURL (поставляется в составе PHP).
 * Вы также можете использовать и кроссплатформенную программу cURL, если вы не программируете на PHP.
// */
$curl = curl_init(); //Сохраняем дескриптор сеанса cURL
/** Устанавливаем необходимые опции для сеанса cURL  */
curl_setopt($curl,CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-oAuth-client/1.0');
curl_setopt($curl,CURLOPT_URL, $link);
curl_setopt($curl,CURLOPT_HTTPHEADER,['Content-Type:application/json']);
curl_setopt($curl,CURLOPT_HEADER, false);
curl_setopt($curl,CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($curl,CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($curl,CURLOPT_SSL_VERIFYPEER, 1);
curl_setopt($curl,CURLOPT_SSL_VERIFYHOST, 2);
$out = curl_exec($curl); //Инициируем запрос к API и сохраняем ответ в переменную
$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);
/** Теперь мы можем обработать ответ, полученный от сервера. Это пример. Вы можете обработать данные своим способом. */
$code = (int)$code;
var_dump($out);
$errors = [
    400 => 'Bad request',
    401 => 'Unauthorized',
    403 => 'Forbidden',
    404 => 'Not found',
    500 => 'Internal server error',
    502 => 'Bad gateway',
    503 => 'Service unavailable',
];

try
{
    /** Если код ответа не успешный - возвращаем сообщение об ошибке  */
    if ($code < 200 || $code > 204) {
        throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undefined error', $code);
    }
}
catch(\Exception $e)
{
    die('Ошибка: ' . $e->getMessage() . PHP_EOL . 'Код ошибки: ' . $e->getCode());
}

/**
 * Данные получаем в формате JSON, поэтому, для получения читаемых данных,
 * нам придётся перевести ответ в формат, понятный PHP
 */
$response = json_decode($out, true);

$access_token = $response['access_token']; //Access токен
$refresh_token = $response['refresh_token']; //Refresh токен
$token_type = $response['token_type']; //Тип токена
$expires_in = $response['expires_in']; //Через сколько действие токена истекает

echo "\n refresh token: " . $refresh_token;
echo "\n access token: " . $access_token;