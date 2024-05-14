<?php
function login($email, $password) {
    $url = "https://aigency.dev/api/v1/login/";
    $data = http_build_query(['email' => $email, 'password' => $password]);
    $options = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => $data,
        ],
    ];
    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    if ($result === FALSE) {
        return ["error" => "Failed to login"];
    }
    return json_decode($result, true);
}

function ai_team_list($access_token) {
    $url = "https://aigency.dev/api/v1/ai-team-list/?access_token=$access_token";
    $result = file_get_contents($url);
    if ($result === FALSE) {
        return ["error" => "Failed to fetch AI team list"];
    }
    return json_decode($result, true);
}

function new_chat($access_token, $ai_id) {
    $url = "https://aigency.dev/api/v1/newChat";
    $data = http_build_query(['access_token' => $access_token, 'ai_id' => $ai_id]);
    $options = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => $data,
        ],
    ];
    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    if ($result === FALSE) {
        return ["error" => "Failed to start new chat"];
    }
    return json_decode($result, true);
}

function send_message($access_token, $chat_id, $message) {
    $url = "https://aigency.dev/api/v1/sendMessage";
    $data = http_build_query(['access_token' => $access_token, 'chat_id' => $chat_id, 'message' => $message]);
    $options = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => $data,
        ],
    ];
    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    if ($result === FALSE) {
        return ["error" => "Failed to send message"];
    }
    $response = json_decode($result, true);
    if (isset($response['error'])) {
        return ["error" => $response['error']];
    }
    if (isset($response['answer']['message'])) {
        return ["message" => $response['answer']['message']];
    }
    return ["error" => "No message in response"];
}

function reset_chat($access_token, $chat_id) {
    $url = "https://aigency.dev/api/v1/resetChat";
    $data = http_build_query(['access_token' => $access_token, 'chat_id' => $chat_id]);
    $options = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => $data,
        ],
    ];
    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    if ($result === FALSE) {
        return ["error" => "Failed to reset chat"];
    }
    return json_decode($result, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    if ($action === 'login') {
        $email = $_POST['email'];
        $password = $_POST['password'];
        echo json_encode(login($email, $password));
    } elseif ($action === 'ai_team_list') {
        $access_token = $_POST['access_token'];
        echo json_encode(ai_team_list($access_token));
    } elseif ($action === 'newChat') {
        $access_token = $_POST['access_token'];
        $ai_id = $_POST['ai_id'];
        echo json_encode(new_chat($access_token, $ai_id));
    } elseif ($action === 'sendMessage') {
        $access_token = $_POST['access_token'];
        $chat_id = $_POST['chat_id'];
        $message = $_POST['message'];
        echo json_encode(send_message($access_token, $chat_id, $message));
    } elseif ($action === 'resetChat') {
        $access_token = $_POST['access_token'];
        $chat_id = $_POST['chat_id'];
        echo json_encode(reset_chat($access_token, $chat_id));
    }
}
?>
