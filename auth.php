<?php

$subdomain = 'icpro12'; //Поддомен нужного аккаунта
$link = 'https://' . $subdomain . '.amocrm.ru/oauth2/access_token'; //Формируем URL для запроса

/** Соберем данные для запроса */
$data = [
    'client_id' => '676904c5-eee1-4fb6-9369-d3574c775247',
    'client_secret' => 'LldAD4LMFhGNcHZPdPqcKbyOFJNXOITpmK3p0Gx3GB8DAPfBi3z8dWLQzk7qz23e',
    'grant_type' => 'authorization_code',
    'code' => 'def5020051bfb659f4512cd6d5a2caa715aa027577380e1218fe14d110f7652eda6a4ecd01c0410576d486d33a8644c708c76810c78b9f4a83fcb7f202c0b0522b3bc40620d89df4ae17e3398310ef4795620865354b7f27d54c1fe2bd421aaf95ac9020a9ff9b717045c36dfb1f4366d0378e95fce5e164bb2ea42d02b564c8e433f2f934f933dfd3f575c9f26515c8aac3ac94800592688cc3b0f8c7ea564f3ea496b1c230b91e1d37e51d6354d0b0de57bdf84ccf0dc0f149b99c0ebb0d98c3c98b6d353bc50c124a04b2f3af9c9549efdb8ef672c91f1e55b9b91f9a7613e800d7c39f08687b7a398575012595922641d6bb74c6c1ef3e3a999701f9925cb92a15afcc03881e113153bb4083e94ddd569da642495f74bd33c6cab397d1cf140787c9452d58e1833cd705c0c0167fbec5766f08df8e02c617f46195342c2fb9030932011bfbe7d55a5593cecbf78038e078364cf8edd5285146a2a7eace1574c786cdbe6660a500b415f9debc90cb300568b30f8edc631a5c456bab2c869b8cbde82ae703703356ca9452608c1d87b6bc22386f93a70f4166b5c9934db7568017f8320cfeb9cf8ff3c30a3a1f5fabee73523840809d2ae764635afec8c202cd810df9cdcad81392c552dff12e955a3d02ad2aeac8788033f0eef102f639034f99475c2c9f3c1c5b794e020b20fd53477b16707b9d2a957a890d',
    'redirect_uri' => 'https://f776-185-213-229-3.ngrok-free.app/',
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