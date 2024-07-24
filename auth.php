<?php

$subdomain = 'icpro12'; //Поддомен нужного аккаунта
$link = 'https://' . $subdomain . '.amocrm.ru/oauth2/access_token'; //Формируем URL для запроса

/** Соберем данные для запроса */
$data = [
    'client_id' => '676904c5-eee1-4fb6-9369-d3574c775247',
    'client_secret' => 'LldAD4LMFhGNcHZPdPqcKbyOFJNXOITpmK3p0Gx3GB8DAPfBi3z8dWLQzk7qz23e',
    'grant_type' => 'authorization_code',
    'code' => 'def50200849319991bd9ac77dadf013dc365e4f5b09e2b6ec15082cdbd00ac8108cb91a99af1f718fdcf354d4820555c009a36c74dd8e09ac4f958ba0b900dc6e355e2ae3300dde49af99b28aec6d089e97e4d02076e1ae4e1d4c0cbc9cf847d8ef590a90db64cbe40cc98441254400af28bc494d8567f7da5659f54df4e54a18dca57ddda92402ecce2a15864523266e9114b076e744c4c9792792122ce56f0dd0600d02bb1e03fed5eb02f04a4194d2458e97e3687a8dde4eeeff4c52692fa161625abdc11334caf5f2f2c87d4048f38d01b2383e48e86f8ca72ca501152e9b4adb44f980a6eb25d9c03afad95c39b2e8cbbef8ac4dcf816d7ba9413482288fd46b7c167d70428402316cfbaea6d4843d5139703c8ac77bddea0fb9881b9dd5b7905115ea2170c0dca5aac4b89888c41081237627e157500ff8d0fa6b9b70d90e570a796c5209024c5b7020c981b5e767f44de3e4b19cb3719097f2fc067e150d4f2c96f95d8aba87ec8950e374b9220e8f3f081e095720bfd9d2d25621dcfb822e4624e3e18f0baa281e30c8fdffe89cf12fe04f08678ca5087e4d6a402faec5c92537bfc674307d2e51b776d9e98c0c2a9a8e6d87eb98d283f7e5b548c25116fc9ad4c089a50f5d34b210b6d8a6e39db5d93d372697b050e7e11299edfcba93364d6378dd87ba52c08df78a2e5092f697765d1bc810c5e2218',
    'redirect_uri' => 'https://b2c7-90-156-160-11.ngrok-free.app/',
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