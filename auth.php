<?php

$subdomain = 'icpro12'; //Поддомен нужного аккаунта
$link = 'https://' . $subdomain . '.amocrm.ru/oauth2/access_token'; //Формируем URL для запроса

/** Соберем данные для запроса */
$data = [
    'client_id' => '676904c5-eee1-4fb6-9369-d3574c775247',
    'client_secret' => 'LldAD4LMFhGNcHZPdPqcKbyOFJNXOITpmK3p0Gx3GB8DAPfBi3z8dWLQzk7qz23e',
    'grant_type' => 'authorization_code',
    'code' => 'def50200994a0f5e49610590f2eac47e90a93bfef8eb1936ec06379d601a6f1fd67b10026cdc1c316e5fa7dad8b85977eff2bba17e9d41ee6884f8ffe6a224040656dc43de51c03ff7d4fb7e4c0adfd761a7469073fa39a8c85d2bc914decd02f00c398076f1a9fc3a48d0ad284e08b290966239b3ab3dd7389b04df77d19a13fd179962ef3113aca888f7a3cf245a35efa661099dda24270774cd0254640687127e9a06b0f7ce3c767375a6088d96d4c18d0c69a2c8d5241d59ff2e4ca7a3a192a77e1852bb333d10d8f4b07b5ec77eec5193703101a33b93c35258e77643bf518d0c33ccc613b34f2fdb278dce77ad4f183189dbaa9796c46af8d4eaafd22b1e253525599261dbffc21a6519c706bb41e5b22c322ffd0e82620bd97ead7fb2ca37f230941f22bda12a6e39ad74db77c63f34fb8d46f019f491b5bb9198e48dfc4b1acac74d72da8b8c3abeb2547f1da2c4795082023e50a6cd385d406e4de7601b53aac8c92048fd2b0b38bdda8e39e8741c7a0e2891e6780f7762d0149089dfc9f51fbec9dd6b9e0b5b38fe1f4ca4bf4792f4afb1694373ecbe565f2556c2b4f76e9c33f6331772cbcda31756ed5b8a1a465fd7a76925cc08c068a485735479fbc8410c209367b87ca06ba78555cb9fcee25fac27030f3e08fc7fe4d93189dc967984f700ef772bc78711915db84138f3629efff8b47dcc855c',
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