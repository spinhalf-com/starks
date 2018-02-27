<?php
namespace Classes;

/**
 * Created by PhpStorm.
 * User: johnriordan
 * Date: 17/02/2018
 * Time: 18:25
 */
interface MbInterfaceClassContract
{
    public function setUserCredentials($userCredentials);
    public function setSourceCredentials($sourceCredentials);
    public function getClientByEmail($email);
    public function validateContractPurchase($array);
    public function purchaseContractWitNewCard($array);
    public function purchaseContractWitSavedCard($array);
}