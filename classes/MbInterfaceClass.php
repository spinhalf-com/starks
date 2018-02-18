<?php namespace Classes;
/**
 * Created by PhpStorm.
 * User: johnriordan
 * Date: 17/02/2018
 * Time: 18:25
 */
require './vendor/autoload.php';
require 'MbInterfaceClassContract.php';
require 'MbClassValidator.php';

use Classes\MbInterfaceClassContract;
use Classes\MbClassValidator;
use DevinCrossman\Mindbody\MB_API;
use Carbon\Carbon;

class MbInterfaceClass //implements MbInterfaceClassContract
{
    public $sourceCredentials;
    public $userCredentials;
    public $mbClassValidator;

    public function setSourceCredentials($sourceCredentials)
    {
        $this->sourceCredentials    = $sourceCredentials;
    }

    public function setUserCredentials($userCredentials)
    {
        $this->userCredentials      = $userCredentials;
    }

    protected function getMbObject()
    {
        $mb = new MB_API(
        [
            "SourceName"        => $this->sourceCredentials['sourcename'],
            "Password"          => $this->sourceCredentials['sourcepass'],
            "SiteIDs"           => [-99]
        ]);

        $validateLogin = $mb->ValidateLogin(
        [
            'Username'          => $this->userCredentials['username'],
            'Password'          => $this->userCredentials['userpass']
        ]);

        return $mb;
    }

    public function validateContractPurchase($array)
    {
        return MbClassValidator::validateContractPurchaseData($array);
    }

    public function purchaseContractWitNewCard($soapRequestArray)
    {
        $valid                          = $this->validateContractPurchase($soapRequestArray);

        if( TRUE === $valid )
        {
            try
            {
                $mb                         = $this->getMbObject();

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
            return "Failed to validate Contract Purchase data: " . $valid;
        }
    }

    public function purchaseContractWitSavedCard($soapRequestArray)
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
}