<?php

$subdomain = 'icpro12'; //Поддомен нужного аккаунта
$link = 'https://' . $subdomain . '.amocrm.ru/oauth2/access_token'; //Формируем URL для запроса

/** Соберем данные для запроса */
$data = [
    'client_id' => '676904c5-eee1-4fb6-9369-d3574c775247',
    'client_secret' => 'LldAD4LMFhGNcHZPdPqcKbyOFJNXOITpmK3p0Gx3GB8DAPfBi3z8dWLQzk7qz23e',
    'grant_type' => 'authorization_code',
    'code' => 'def50200febfa9f3ad1aac99bae90eaca7e4c79fb417acc26d8bccd1a8eef81f8187a062d542ceb8b08f73e4bbe4dc501cc126ff6dd1dc5b486271db96f1905c69db412acffa520b69d9fe40497720b1aee4c25b0f2b4081c21a0086711ea485968b0bca4e891f2ee321970287ba60e0ff3d0a1f428918b8e94ceabaf01c907c9b4c37d18c57fda9c28a0b022f0acde2f52daad13947de69e7721cda5aecd4f6045a61fddd047f0a103745ad96a36945a67af26be8329fd89cab5e15f34538afc61d478f70600cc4d6080adb7251504487ff9187fc56f14120b718af1ef9a710471c0a284d149657ccec54dd59752e88a9e089d29e8fb0c15e40f9fc939009d9411ec7045428b0c894ac34dc5cf7bf1d18da87d9229d6ca49f26236c140661cf3473e33b65ea11d42ba73b135d0109d39174f3ca6caa9d697153e8eaf3ec2a9cec635f6efa3ea89f17654982292c9419390512d388e5ac461a0a99638b1e50435cc15338dd863711ce9a00ab60bfdb81da53311098d790661a5c02f2eb6adc346f3a43c49de277653c0e4d33cde31d77377a1936cd8501771d159d73c2383209fd086447c099d596c8eca9aefc1841ac5226e541a61c83537f67ce7565e69d68f926cd9a9d90b918135654f640886450b72e242ba760316fae2d5d6f98dda37b4e2bd9c80f39ee30e1641fd515e7bd0d5b156e8a8fd82c328f0f38',
    'redirect_uri' => 'https://80f3-213-230-112-3.ngrok-free.app/',
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