<?php

$subdomain = 'icpro12'; //Поддомен нужного аккаунта
$link = 'https://' . $subdomain . '.amocrm.ru/oauth2/access_token'; //Формируем URL для запроса

/** Соберем данные для запроса */
$data = [
    'client_id' => '676904c5-eee1-4fb6-9369-d3574c775247',
    'client_secret' => 'LldAD4LMFhGNcHZPdPqcKbyOFJNXOITpmK3p0Gx3GB8DAPfBi3z8dWLQzk7qz23e',
    'grant_type' => 'authorization_code',
    'code' => 'def502005f850d53eb9b71c002908f60af582ad9532dc4f19bb09ab969401671b3dd74fac2712ea9653e26d50d9ed07e9300c998b3c2dd488477d55b8d9fafb23a2fdaf8365e2eaafcb8f206ea93a392d09d5c78b5df562b254805b84d47e90eff9940e8d6a1c1aacee908db2e85fa14f948f2e699d6f1dab349693f06d8f9e975628a090102295e760b7364f1de1b86c41c6f86b17c2779c7bab9013747a2824e7c70216583c7eb97d07d297283c5196fde3c394f413a1cd869f2410916f27c93ae9b27d05ec2cced87aa1106ad2fa1ec3eac73574d4b1fdb0493a43ec97ff7fe24c858cb73423b32112c08f804739413ac97a71f819c413657f6e7744ea674ec4424674798c76d0d6886b8c8c4287fed53172ff62b45d931a5a785de2c27a9ba1eecc2815dd6b7fd9432d13b2ddfbd92bb34767abd65cb149793521eb54a3917bd5bca0b487ad09a137782dadcd33326b7e6319dd0a5ad90b4c5a72fc6a9cd6163a8e0babc1222590019335e11a9f337b65a1e02280579d7fe0b41ec165abf4da94ad0e366e3aeb846d6f3b28e0454a079b81625965c0255c9478bb9bafdfb2ade427e664dbbf69812bea2fb058d1f7ad8a69a281716e46349632fcc9a960e6d130cd19c81af42bff36b22f04cc5879e420b29b16287478a96b5c82cfef03bb6116933f0d5a056a3c22e63174f39450b45cc1ac65f7dbf1013',
    'redirect_uri' => 'https://38cc-84-54-78-134.ngrok-free.app/',
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