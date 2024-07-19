<?php

$subdomain = 'icpro12'; //Поддомен нужного аккаунта
$link = 'https://' . $subdomain . '.amocrm.ru/oauth2/access_token'; //Формируем URL для запроса

/** Соберем данные для запроса */
$data = [
    'client_id' => '676904c5-eee1-4fb6-9369-d3574c775247',
    'client_secret' => 'LldAD4LMFhGNcHZPdPqcKbyOFJNXOITpmK3p0Gx3GB8DAPfBi3z8dWLQzk7qz23e',
    'grant_type' => 'authorization_code',
    'code' => 'def502002dd15bd867238fffedd9d4fc5ecd14a89a799183fbcdcc37348ae9643f7c2ab4a803e43743d36dfd6b825834f9fd8cc6cc6150ea4cd218f7305fd8cff7f91a9d55b78fb1270f541e6b02bf61bc152882354073925c6c5e46bc897974dd29ffce09c2b3baf4f51db9a53bdeb67ac94f92ee2bc9961eeae917028e2359d8ae92927b399f865927fe8b1ca38bdd9a77b5505954cdd9dabe2da8a5cc65607ecaa0c428973ee23b5b93e3861a686a3fb57550fae8ed56e233caa9329eab16a2bdabb0ff29648340280099eefeab835086acd507bf1d621479cb79ebf12a08e3e5cf567a4a1e46b78018b35f573c794ed57fb0bd42fb28ebd6a1efb7a5a8b16388727860b0568c1c7d8e16e713af57380d698abf226f2798e19bbda926d9bb2a82d93676997b509791f090fb47fc8df6a15d65d49bfe84ef1db970d3acce981f2500d066393f10b0dba6d7e5f6e584f3dd0b6da357beaec385a14bad1435cb939e9bbff58bf14fb308f466a3722e052685d1d0a0462e9ce13dafe3cae6c4fd843ed545c8186eb878ca8af3b91a2f26858a4050e6f9dd418aca90eb4978b15543ff6190a599551295f02d79c4850cb4c4065e21812569a510ca952fcf96774fd1efb0efaf9a76a9fa8305271998d34d648b945deed101cb3415d6913aacc9b7ce42f74310ba5f0f372cd8c022b8b246c19b73bc20521e1ca61b35',
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