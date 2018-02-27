<?php
/**
 * Created by PhpStorm.
 * User: johnriordan
 * Date: 27/02/2018
 * Time: 15:40
 */


include './includes/credentials/credentials.php';
include_once './classes/MbInterfaceClass.php';

    $mbConnection           = new classes\MbInterfaceClass();
    $mbConnection->setSourceCredentials($sourceCreds);
    $mbConnection->setUserCredentials($userCreds);
    $mbConnection->setConsumerCredentials($consumerCreds);

    echo json_encode($mbConnection->getAcceptedCardTypes());