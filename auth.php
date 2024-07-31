<?php

$subdomain = 'icpro12'; //Поддомен нужного аккаунта
$link = 'https://' . $subdomain . '.amocrm.ru/oauth2/access_token'; //Формируем URL для запроса

/** Соберем данные для запроса */
$data = [
    'client_id' => '676904c5-eee1-4fb6-9369-d3574c775247',
    'client_secret' => 'LldAD4LMFhGNcHZPdPqcKbyOFJNXOITpmK3p0Gx3GB8DAPfBi3z8dWLQzk7qz23e',
    'grant_type' => 'authorization_code',
    'code' => 'def502007e3bc5303bb89feadef833c4b575ba038055907a9df76479df378473a012b8b7f2422ba9777b46194c7ecb243df41ff07bc762360bb1ac18926e967e71f91dd779c4fecae5607b52eb939c2eb84f561f4c07f71b832eb123683e61ee6b16d17b3ac1ccaebc4d2a62599586e35fb755fff37b0233c6ea6ed17d4e047bdd159a31b3b10b70f28c32f0102fd59fd8a6438e34e6badd4894edec5a005597a4d1f67ef8617dd0f3ed5db0c3e7162d0d289b36932e41bcb82559f3afe404522784eaf093fbeb5cfbfb6f6ba45e09e3bef2069426fcfb718ac70917dd20f8a28560e8f06c259352fae82917ab753778cf81c928ce43bf4b809cf3f44b590f83548a93b390f6d9a64084d0d8051f1755327d8c03d6805382dbdd0b8558682e87ce8bfc20830311e1224d67efcd56124fcd4b634ebaee5835b821f7c14eeed477926c9798f056812f121971340247cf5b8487dcb7f8d865eaffc567ad5e3f33c48d553666e6b159819bf068762003e64a8e6b1084fe72805de7b9a96239c8e3b18582ccb322a4b9c48e978dad195e8f42e868f43cfff6a194ff3b41ac46af2c43af187672fed74b9fa9a6ede8033ff69c32135dd2b9f1bc04f49faac9d1c1203dd24db5cfa6e6426f468bb35927582669122fa8410c087aac09c56e3804ea396f05ffac48cfb2a472e7706205673c048c696d6c5e68615af435e230',
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