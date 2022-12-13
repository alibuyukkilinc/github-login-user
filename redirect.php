<?php

include 'Github';

$Github = new Github();
$continiue = true;

if(isset($_GET['code'])){

    $uri = $Github->getAuthorizeURL($_GET['code']);
    $access_token = $Github->getAccessToken($uri,$_GET['code']);
    if(!isset($access_token->error)){
        $data = $Github->getAuthenticatedUser($access_token);
        if(isset($data->email)){
            #LOGİN IS SUCCESSFULL you make users data save or if user saved in your data make user login
            echo "Login Success!";
            print_r($data);
        } else {
            $continiue = false;
        }
    } else {
        $continiue = false;
    }
} else {
    $continiue = false;
}

if($continiue == false){
    echo "Login Failed!";
}

?>