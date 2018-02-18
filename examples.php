<?php
/**
 * Created by PhpStorm.
 * User: johnriordan
 * Date: 11/02/2018
 * Time: 11:50
 */

require './includes/credentials/credentials.php';
require './vendor/autoload.php';

$service                = MindbodyAPI\MindbodyClient::service("ClientService");
$credentials            = $service::credentials($souceCreds['sourcename'], $souceCreds['password'], $souceCreds['siteID']);

$userCredentials        = $service::userCredentials(
    $userCreds['username'],
    $userCreds['userpass'],
    $userCreds['siteID']
);


$buyContractXml = '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
   <soap:Body>
      <PurchaseContracts xmlns="http://clients.mindbodyonline.com/api/0_5_1">
         <Request>
            <SourceCredentials>
               <SourceName>{SourceName}</SourceName>
               <Password>***</Password>
               <SiteIDs>
                 <int>-99</int>
               </SiteIDs>
            </SourceCredentials>
            <XMLDetail>Full</XMLDetail>
            <PageSize>50</PageSize>
            <CurrentPageIndex>0</CurrentPageIndex>
            <Test>false</Test>
            <LocationID>1</LocationID>
            <ClientID>100013403</ClientID>
            <ContractID>320</ContractID>
            <FirstPaymentOccurs>Instant</FirstPaymentOccurs>
            <PaymentInfo xsi:type="CreditCardInfo">
               <CreditCardNumber>4111111111111111</CreditCardNumber>
               <ExpMonth>12</ExpMonth>
               <ExpYear>2019</ExpYear>
               <BillingName>Clay Smith</BillingName>
               <BillingAddress>123 Grant Dr</BillingAddress>
               <BillingCity>Santa Cruz</BillingCity>
               <BillingState>CA</BillingState>
               <BillingPostalCode>93405</BillingPostalCode>
            </PaymentInfo>
            <ClientSignature>iVBORw0KGgoAAAANSUhEUgAAAA8AAAAH
            CAIAAABY54BwAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7
            DAcdvqGQAAAA6SURBVChTY/z//z8D0YAJShMHEKq3b7+zfWJ6ujWj9cTtE62tJ965MzF94h
            2oJBTQxyXEAFJUMzAAAJLsEdvM1XlfAAAAAElFTkSuQmCC</ClientSignature>
            <PromotionCode>SALE50</PromotionCode>
         </Request>
      </PurchaseContracts>
   </soap:Body>
</soap:Envelope>';


$validate_login = $mb->ValidateLogin(array(
    'Username' => $login_email,
    'Password' => $login_password,
));

if(!empty($validate_login['ValidateLoginResult']['GUID'])) {
    $client_id = $validate_login['ValidateLoginResult']['Client']['ID'];
    $options = array(
        'Test' => 'true',
        'ClientID' => $client_id,
        'CartItems'=>array(
            'CartItem'=>array(
                'Quantity'=>1,
                'Item' => new SoapVar(
                    array(
                        'ID'=> 352,
                        'DiscountPercentage'=> 0,
                        'SellOnline'=> 'true'
                    ),
                    SOAP_ENC_ARRAY,
                    'Package',
                    'http://clients.mindbodyonline.com/api/0_5'
                ),
                'DiscountAmount' => 0,
            )
        ),
        'Payments' => array(
            'PaymentInfo' => new SoapVar(
                array(
                    'CreditCardNumber'=>'4111111111111111',
                    'ExpYear'=>'2020',
                    'ExpMonth'=>'06',
                    'Amount'=>'560',
                    'BillingAddress'=>'Address',
                    'BillingPostalCode'=>'BS6 1UN'
                ),
                SOAP_ENC_ARRAY,
                'CreditCardInfo',
                'http://clients.mindbodyonline.com/api/0_5'
            )
        )
    );
    $data = $mb->CheckoutShoppingCart($options);
    starks_dump($data);
}
