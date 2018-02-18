<?php
/**
 * Created by PhpStorm.
 * User: johnriordan
 * Date: 17/02/2018
 * Time: 19:34
 */

include './includes/credentials/credentials.php';
include_once './classes/MbInterfaceClass.php';

if(isset($_POST['purchase_contract']))
{
    $mbConnection           = new classes\MbInterfaceClass();
    $mbConnection->setSourceCredentials($sourceCreds);
    $mbConnection->setUserCredentials($userCreds);

    $soapRequestArray['PageSize']                   = 50;
    $soapRequestArray['CurrentPageIndex']           = 0;
    $soapRequestArray['XMLDetail']                  = 'Full';
    $soapRequestArray['Test']                       = 'true';                     //set this to false when deploying to production
    $soapRequestArray['ClientID']                   = $_POST['ClientID'];     //the client ID from the MB record
    $soapRequestArray['ContractID']                 = $_POST['ContractID'];    // example 320
    $soapRequestArray['LocationID']                 = $_POST['LocationID'];
    $soapRequestArray['Amount']                     = $_POST['Amount'];
    $soapRequestArray['FirstPaymentOccurs']         = $_POST['FirstPaymentOccurs'];

    if(!isset($_POST['savedcc']))
    {
        $soapRequestArray['CreditCardNumber']           = $_POST['CreditCardNumber'];
        $soapRequestArray['CreditCardExpMonth']         = $_POST['CreditCardExpMonth'];
        $soapRequestArray['CreditCardExpYear']          = $_POST['CreditCardExpYear'];
        $soapRequestArray['CustomerName']               = $_POST['CustomerName'];
        $soapRequestArray['CustomerAddress']            = $_POST['CustomerAddress'];
        $soapRequestArray['CustomerCity']               = $_POST['CustomerCity'];
//    $soapRequestArray['CustomerState']              = $_POST['CustomerState'];
        $soapRequestArray['CustomerPostcode']           = $_POST['CustomerPostcode'];

        $result                 = $mbConnection->purchaseContractWitNewCard($soapRequestArray);
    }
    else
    {
        $result                 = $mbConnection->purchaseContractWitSavedCard($soapRequestArray);
    }

    if($result['status'] == '200')
    {
        $postMessage    = "Contract successfully purchased";
    }
    else
    {
        $postMessage    = "Failed: " . $result->body;
    }
}

?>

<!DOCTYPE html>
<html>
<head>

    <style>

        .cell{
            width: 200px;
        }

        .cellSmall{
            width: 100px;
        }
    </style>

    <script src="//code.jquery.com/jquery-latest.js"></script>


</head>
<body>

<form action="PurchaseContract.php" method="post">
    <table>
        <tr>
            <td>XML Detail</td>
            <td><input class='cell' type="text" name="XMLDetail" value="Full"></td>
        </tr>
        <tr>
            <td>Client ID</td>
            <td><input class='cell' type="text" name="ClientID" value="100015062"></td>
        </tr>
        <tr>
            <td>Contract ID</td>
            <td><input class='cell' type="text" name="ContractID" value="320"></td>
        </tr>
        <tr>
            <td>Use Saved Credit Card?</td>
            <td>
                <input type="checkbox" id="switch" name="savedcc">
            </td>
        </tr>
        <tr class="hider">
            <td>Credit Card Number</td>
            <td>
                <input class='cell inv' type="text" name="CreditCardNumber" id="CreditCardNumber" value="4111111111111111">
            </td>
        </tr>
        <tr class="hider">
            <td>Expiry</td>
            <td><input class='cellSmall inv' type="text" name="CreditCardExpMonth" id="CreditCardExpMonth" value="12"><input class='cellSmall inv' type="text" name="CreditCardExpYear" id="CreditCardExpYear" value="2019"></td>
        </tr>
        <tr class="hider">
            <td>Name</td>
            <td><input class='cell inv' type="text" name="CustomerName" id="CustomerName" value="Test"></td>
        </tr>
        <tr class="hider">
            <td>Address</td>
            <td><input class='cell inv' type="text" name="CustomerAddress" id="CustomerAddress" value="Test"></td>
        </tr>
        <tr class="hider">
            <td>City</td>
            <td><input class='cell inv' type="text" name="CustomerCity" id="CustomerCity" value="Test"></td>
        </tr>
        <tr class="hider">
            <td>Postcode</td>
            <td><input class='cell inv' type="text" name="CustomerPostcode" id="CustomerPostcode" value="Test"></td>
        </tr>
        <tr>
            <td>First Payment Occurs</td>
            <td><input class='cell' type="text" name="FirstPaymentOccurs" value="Instant"></td>
        </tr>
        <tr>
            <td>Amount</td>
            <td><input class='cell' type="text" name="Amount" value="1000"></td>
        </tr>
        <tr>
            <td>LocationID</td>
            <td><input class='cell' type="text" name="LocationID" value="1"></td>
        </tr>
        <tr>
            <td><input type="hidden" name="purchase_contract"></td>
            <td><input type="submit" value="Submit"></td>
        </tr>
        <tr>
            <td colspan="2">
            <?php
                if(isset($postMessage))
                {
                    echo $postMessage;
                }
            ?>
            </td>
        </tr>
    </table>
</form>

</body>

<script type="text/javascript">

$(document).ready(function()
{
    $('#switch').click(function()
    {
        console.log($(this).val());

        if($(this).is(':checked'))
        {
            $('.inv').each(function (i, obj)
            {
                $(obj).removeAttr('name');
            });

            $('.hider').hide();
        }
        else
        {
            $('.inv').each(function (i, obj)
            {
                console.log($(obj).attr('id'));

                $(obj).attr('name', $(obj).id);
            });
            $('.hider').show();
        }
    });
});

</script>
</html>

