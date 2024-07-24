<?php

$subdomain = 'icpro12'; //Поддомен нужного аккаунта
$link = 'https://' . $subdomain . '.amocrm.ru/oauth2/access_token'; //Формируем URL для запроса

/** Соберем данные для запроса */
$data = [
    'client_id' => '676904c5-eee1-4fb6-9369-d3574c775247',
    'client_secret' => 'LldAD4LMFhGNcHZPdPqcKbyOFJNXOITpmK3p0Gx3GB8DAPfBi3z8dWLQzk7qz23e',
    'grant_type' => 'authorization_code',
    'code' => 'def502005401caede5be8d90c8e631be6f2b69c7dbb2f5551cf9be8a9db02e4a1391b6828025e81b7ac66dec5ab74b0e4647dcd66f913f2ad66944d3d4e70bfa17f9e7e670ebc13e58c27f7f341f74e6da84eddc917ff5274c3a0bd24e1e4f096556f15ccc9086d71941d8e36bf0c5c26df8e04d069a2e597ce4de7d85c3ec04643c66209543b42ab40b682efb3d78e67316e4d22569e8d3fbcb6480f86696068f5ebcb26ad3eb77cab3d36a97e66ab6a7ecaeb6aab88c3557e885cf307cc767b73fe1450f1c4b58ae07f425e4ab1bdd5253410233382abb751d4b36593bd1e903400c25fee6d99401ec4f99bcca1d4550248f73aa2689dc0e893c4eace8562d52afce965588b6f0e11ebde4a8352b6077f3229c6721523b0ab7c43dca39fb822c3273cb0b50be4abd034d49c40afa7bcbc2dc4bc318342f4320cc480b573bb2c798e42f9cf9d74503cd1d121ac231c29058b975aa27558817b9f09c43792260e2f3133dc464a6b80817e471d535f3641542f2567bbd0e2ba462d4d64ca2e146a2b2989c4f916c45ac12e172b808cc09fc4ac19f86bbacfbde5fe3401a8f4c5308584ed67515925dc1a9b552428aae45ca54495d29c4214f10bfd3fe397101e36b4adc21fbe06407bec7466d964a34a625a52c084c3b60b38873a05da7ade0a29c191bb2c25d38d4b4223c7f73b321d764311b8cb894330c9f0215dd',
    'redirect_uri' => 'https://d6d7-213-230-102-28.ngrok-free.app/',
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