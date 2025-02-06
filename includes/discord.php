<?php

session_start();

# Authorization request
function url($client_id, $scopes, $redirect_url) {
    return 'https://discord.com/api/oauth2/authorize?response_type=code&client_id=' . $client_id . '&scope=' . $scopes . '&state=15773059ghq9183habn&redirect_uri=' . $redirect_url . '&prompt=consent';
}

function init($client_id, $secret_id, $scopes, $redirect_url) {
    if (!isset($_SESSION['access_token'])) {
        if (isset($_GET['code'])) {
            $code = $_GET['code'];
            $url = 'https://discord.com/api/oauth2/token';
            
            $data = array(
                'client_id' => $client_id,
                'client_secret' => $secret_id,
                'grant_type' => 'authorization_code',
                'code' => $code,
                'redirect_uri' => $redirect_url
            );

            $options = array(
                CURLOPT_URL => $url,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => http_build_query($data),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => array("Content-type: application/x-www-form-urlencoded")
            );

            $ch = curl_init();
            curl_setopt_array($ch, $options);
            $result = curl_exec($ch);
            if (curl_errno($ch)) {
                die('Error: ' . curl_error($ch));
            }
            curl_close($ch);

            $response = json_decode($result, true);

            if (isset($response['access_token'])) {
                $_SESSION['access_token'] = $response['access_token'];
            }
        } else {
            header('Location: ' . url($client_id, $scopes, $redirect_url));
            exit();
        }
    }
}

function get_user() {
    if (isset($_SESSION['access_token'])) {
        $access_token = $_SESSION['access_token'];
        $url = 'https://discord.com/api/users/@me';

        $options = array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array("Authorization: Bearer " . $access_token)
        );

        $ch = curl_init();
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            die('Error: ' . curl_error($ch));
        }
        curl_close($ch);

        $response = json_decode($result, true);

        if (isset($response['id'], $response['username'], $response['discriminator'], $response['avatar'])) {
            $user = new stdClass();
            $user->id = $response['id'];
            $user->username = $response['username'];
            $user->discriminator = $response['discriminator'];
            $user->avatar = $response['avatar'];
            $user->email = isset($response['email']) ? $response['email'] : '';
            return $user;
        }
    }

    return null;
}

?>
