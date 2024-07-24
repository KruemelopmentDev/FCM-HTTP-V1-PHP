<?php
function sendFCMNotification($title, $message, $topic, $ttl, $priority)
{
    $serviceAccountData = json_decode(file_get_contents("/pathToYourServices.json"), true);

    $jwt = createJWT($serviceAccountData['private_key'], $serviceAccountData['client_email']);
    if (isset($jwt["error"])) {
        return $jwt;
    }
    $accessToken = fetchAccessToken($jwt);
    if (isset($accessToken["error"])) {
        return $accessToken;
    }

    $notificationData = [
        "message" => [
            "topic" => $topic,
            "data" => ["title" => $title, "text" => $message],
            "notification" => ["title" => $title, "body" => $message],
            "android" => ["priority" => $priority, "ttl" => $ttl . "s"]
        ]
    ];

    return sendFCMMessage($accessToken["token"], $notificationData);
}

function createJWT($privateKey, $serviceAccountEmail)
{
    $timestamp = time();
    $header = base64url_encode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
    $payload = base64url_encode(json_encode([
        'iss' => $serviceAccountEmail,
        'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
        'aud' => 'https://oauth2.googleapis.com/token',
        'exp' => $timestamp + 3600,
        'iat' => $timestamp
    ]));
    $signature = "";
    $signResult = openssl_sign($header . "." . $payload, $signature, openssl_pkey_get_private($privateKey), OPENSSL_ALGO_SHA256);
    if (!$signResult) {
        $error = openssl_error_string();
        return ["error" => "OpenSSL Signing Error", "error_description" => $error, "where" => "openssl_sign"];
    }
    return $header . "." . $payload . "." . base64url_encode($signature);
}

function fetchAccessToken($jwt)
{
    $ch = curl_init('https://oauth2.googleapis.com/token');
    curl_setopt_array($ch, [
        CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POSTFIELDS => http_build_query([
            "grant_type" => "urn:ietf:params:oauth:grant-type:jwt-bearer",
            "assertion" => $jwt
        ])
    ]);
    $response = json_decode(curl_exec($ch), true);
    curl_close($ch);

    if (isset($response["error"])) {
        return ["error" => $response["error"], "error_description" => $response["error_description"], "where" => "Curl Token Request"];
    }
    return ["token" => $response['access_token'], "expiresIn" => $response['expires_in']];
}

function sendFCMMessage($accessToken, $notificationData)
{
    $ch = curl_init("https://fcm.googleapis.com/v1/projects/{project-id}/messages:send");
    curl_setopt_array($ch, [
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $accessToken
        ],
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POSTFIELDS => json_encode($notificationData)
    ]);

    $response = json_decode(curl_exec($ch), true);
    curl_close($ch);

    if (isset($response["error"])) {
        return ["error" => $response["error"], "error_description" => $response["error"]["message"], "where" => "Curl FCM Request"];
    }
    return ["message_id" => $response["name"]];
}

function base64url_encode($data)
{
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}
