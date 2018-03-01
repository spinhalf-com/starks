<?php namespace Classes;
/**
 * Created by PhpStorm.
 * User: johnriordan
 * Date: 17/02/2018
 * Time: 18:25
 */
require './vendor/autoload.php';
require 'MbClassValidator.php';

use Classes\MbInterfaceClassContract;
use Classes\MbClassValidator;
use DevinCrossman\Mindbody\MB_API;
use Carbon\Carbon;

class MbInterfaceClass
{
    public $sourceCredentials;
    public $userCredentials;
    public $mbClassValidator;
    public $consumerCredentials;

    public function setSourceCredentials($sourceCredentials)
    {
        $this->sourceCredentials        = $sourceCredentials;
    }

    public function setUserCredentials($userCredentials)
    {
        $this->userCredentials          = $userCredentials;
    }

    public function setConsumerCredentials($consumerCredentials)
    {
        $this->consumerCredentials      = $consumerCredentials;
    }

    protected function getMbObject()
    {
        $mb                     = new MB_API($this->sourceCredentials, $this->userCredentials);

        return $mb;
    }

    public function consumerLogin()
    {
        $this->mb->ValidateLogin($this->consumerCredentials);

        return $this->mb;
    }

    public function getClientByID($soapRequestArray)
    {
        $mb                         = $this->getMbObject();

        try
        {
            $getClientRequest           = $mb->GetClients($soapRequestArray);

            return ['status' => 200, 'body' => $getClientRequest];
        }
        catch (SoapFault $s)
        {
            return ['status' => 400, 'body' => $s->getMessage()];
        }
        catch (Exception $e)
        {
            return ['status' => 400, 'body' => $e->getMessage()];
        }
    }

    public function getClientByEmail($soapRequestArray)
    {
        $mb                         = $this->getMbObject();

        try
        {
            $getClientRequest           = $mb->GetClients($soapRequestArray);

            if($getClientRequest['GetClientsResult']['ResultCount'] > 0)
            {
                if (array_key_exists('ID', $getClientRequest['GetClientsResult']['Clients']['Client']))
                {
                    $id = ($getClientRequest['GetClientsResult']['Clients']['Client']['ID']);
                }
                else
                {
                    $id = false;
                }
            }
            else
            {
                $id = false;
            }

            return ['status' => $getClientRequest['GetClientsResult']['Status'], 'body' => $getClientRequest, 'clientId' => $id];
        }
        catch (SoapFault $s)
        {
            return ['status' => 400, 'body' => $s->getMessage()];
        }
        catch (Exception $e)
        {
            return ['status' => 400, 'body' => $e->getMessage()];
        }
    }

    public function getClientFields()
    {
        $mb                             = $this->getMbObject();

        return ($mb->GetRequiredClientFields());
    }

    public function addClient($soapRequestArray)
    {
        $mb                             = $this->getMbObject();

        $checkArray['PageSize']                   = 50;
        $checkArray['CurrentPageIndex']           = 0;
        $checkArray['XMLDetail']                  = 'Full';
        $checkArray['SearchText']                 = $soapRequestArray['Email'];

        $existingClient                 = $this->getClientByEmail($checkArray);

//        die(json_encode($existingClient));

        $clientArray['FirstName']       = $soapRequestArray['FirstName'];
        $clientArray['LastName']        = $soapRequestArray['LastName'];
        $clientArray['BirthDate']       = $soapRequestArray['BirthDate'];
        $clientArray['Email']           = $soapRequestArray['Email'];
        $clientArray['Username']        = $soapRequestArray['Email'];
        $clientArray['Password']        = $soapRequestArray['Password'];
        $clientArray['Status']          = 'Inactive';

        if($existingClient['body']['GetClientsResult']['ResultCount'] > 0)
        {
            if($existingClient['body']['GetClientsResult']['ResultCount'] == 1)
            {
                $clientArray['ID'] = $existingClient['body']['GetClientsResult']['Clients']['Client']['ID'];
            }
            else
            {
                $clientArray['ID'] = $existingClient['body']['GetClientsResult']['Clients']['Client'][0]['ID'];
            }
        }

        try
        {
            $addClientRequest           = $mb->AddOrUpdateClients(
            [
                'Test'                  => false,
                'PageSize'              => $soapRequestArray['PageSize'],
                'XMLDetail'             => $soapRequestArray['XMLDetail'],
                'CurrentPageIndex'      => $soapRequestArray['CurrentPageIndex'],
                'Clients' =>
                [
                    'Client'    => $clientArray
                ]
            ]
        );

//        $result                         = $mb->getXMLResponse();
//        $request                        = $mb->getXMLRequest();

        return ['status' => $addClientRequest['AddOrUpdateClientsResult']['Status'], 'body' => ($addClientRequest['AddOrUpdateClientsResult']['Clients']['Client'])];

        }
        catch (SoapFault $s)
        {
            return ['status' => 400, 'body' => $s->getMessage()];
        }
        catch (Exception $e)
        {
            return ['status' => 400, 'body' => $e->getMessage()];
        }
    }

    public function validateContractPurchase($array)
    {
        return MbClassValidator::validateContractPurchaseData($array);
    }

    public function getAcceptedCardTypes()
    {
        $mb                         = $this->getMbObject();

        $cardTypes                  = $mb->GetAcceptedCardType();

        return $cardTypes;
    }

    public function getContracts($locationId)
    {
        $mb                         = $this->getMbObject();

        $contracts                  = $mb->GetContracts(
        [
            'XMLDetail' => 'Full',
            'PageSize' => 50,
            'CurrentPageIndex' => 0,
            'SoldOnline' => true,
            'LocationID' => $locationId
        ]);

        return $contracts;
    }

    public function purchaseContractWithNewCard($soapRequestArray)
    {
        $valid                          = $this->validateContractPurchase($soapRequestArray);

//        $clientId                       = $this->getClientByEmail($soapRequestArray['Email']);

        if( TRUE === $valid )
        {
            try
            {
                $mb                         = $this->getMbObject();

                $purchaseContractRequest    = $mb->PurchaseContracts(
                [
                    'Test'                  => true,
                    'PageSize'              => $soapRequestArray['PageSize'],
                    'ClientID'              => $soapRequestArray['ClientID'],
                    'XMLDetail'             => $soapRequestArray['XMLDetail'],
                    'LocationID'            => $soapRequestArray['LocationID'],
                    'ContractID'            => $soapRequestArray['ContractID'],
                    'FirstPaymentOccurs'    => $soapRequestArray['FirstPaymentOccurs'],

                    'PaymentInfo' => new \SoapVar(
                    [
                        'Amount'                => $soapRequestArray['Amount'],
                        'CreditCardNumber'      => $soapRequestArray['CreditCardNumber'],
                        'ExpYear'               => $soapRequestArray['CreditCardExpYear'],
                        'ExpMonth'              => $soapRequestArray['CreditCardExpMonth'],
                        'CustomerName'          => $soapRequestArray['CustomerName'],
                        'BillingAddress'        => $soapRequestArray['CustomerAddress'],
                        'BillingPostalCode'     => $soapRequestArray['CustomerPostcode'],
                        'FirstPaymentOccurs'    => $soapRequestArray['FirstPaymentOccurs'] == 'Instant'? 'Instant' : Carbon::parse($soapRequestArray['FirstPaymentOccurs'])->toDateString()
                    ],
                    SOAP_ENC_ARRAY,
                    'CreditCardInfo',
                    'http://clients.mindbodyonline.com/api/0_5_1'
                    )
                ]);

                return ['status' => 200, 'body' => $purchaseContractRequest];
            }
            catch (SoapFault $s)
            {
                return ['status' => 400, 'body' => $s->getMessage()];
            }
            catch (Exception $e)
            {
                return ['status' => 400, 'body' => $e->getMessage()];
            }
        }
        else
        {
            return "Failed to validate Contract Purchase data: " . $valid;
        }
    }

    public function purchaseContractWithSavedCard($soapRequestArray)
    {
        if(TRUE === $this->validateContractPurchase($soapRequestArray, true))
        {
            try
            {
                $mb                     = $this->getMbObject();

                $purchaseContractRequest    = $mb->PurchaseContracts(
                [
                    'Test'                  => $soapRequestArray['PageSize'],
                    'ClientID'              => $soapRequestArray['ClientID'],
                    'XMLDetail'             => $soapRequestArray['XMLDetail'],
                    'LocationID'            => $soapRequestArray['LocationID'],
                    'ContractID'            => $soapRequestArray['ContractID'],
                    'FirstPaymentOccurs'    => $soapRequestArray['FirstPaymentOccurs'],

                    'PaymentInfo' => new \SoapVar(
                    [
                        'Amount'                => $soapRequestArray['Amount'],
                    ],
                    SOAP_ENC_ARRAY,
                    'StoredCardInfo',
                    'http://clients.mindbodyonline.com/api/0_5_1'
                    )
                ]);
                return ['status' => 200, 'body' => simplexml_load_string($mb->getXMLResponse())];
            }
            catch (SoapFault $s)
            {
                return ['status' => 400, 'body' => $s->getMessage()];
            }
            catch (Exception $e)
            {
                return ['status' => 400, 'body' => $e->getMessage()];
            }
        }
        else
        {
            return "Failed to validate Contract Purchase data";
        }
    }

    public function makeUserName($fname, $lname)
    {
        $nonce                    = rand(100000, 999999);

        return $fname . $lname . $nonce;
    }
}