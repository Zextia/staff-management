<?php

require_once 'config.php';

if (isset($_GET['code']) && trim($_GET['code']) != '') {
    echo '<pre>';
    $code = $_GET['code'];

    // Get Access token

    $url = "https://graph.facebook.com/oauth/access_token?"
            . "client_id=" . FB_APP_ID . "&"
            . "client_secret=" . FB_APP_SECRET . "&"
            . "redirect_uri=" . FB_REDIRECT_URL . "&"
            . "code=$code";

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $data = curl_exec($ch);
    curl_close($ch);

    // Find access token

    $b = explode('&', $data);
    echo $data;
    echo '<br />';
    echo '<br />';
    echo $b[0];
    echo '<br />';
    echo '<br />';
    echo $b[1];
    $at = explode('=', $b[0]);

    echo '<br />';
    echo '<br />';
    echo $at[0];
    echo '<br />';
    echo '<br />';
    echo $at[1];
    echo '<br />';
    echo '<br />';

    // Get email of user
    $url = "https://graph.facebook.com/me?fields=email&"
            . "access_token={$at[1]}";

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $data = curl_exec($ch);
    curl_close($ch);


    $json = json_decode($data);

    //print_r($json);
    require_once 'classes/class.db.php';
    require_once 'classes/class.user.php';
    require_once 'classes/class.utility.php';

    $db = new database();
    $user = new user($db);
    $user->fblogin($json->email);
    die;
} else {

    $url = "https://graph.facebook.com/oauth/authorize?"
            . "client_id=" . FB_APP_ID . "&"
            . "redirect_uri=" . FB_REDIRECT_URL . "&"
            . "scope=email";

//echo $url;
    header("Location:$url");
    die;
}