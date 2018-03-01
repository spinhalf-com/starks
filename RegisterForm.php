<?php
/**
 * Created by PhpStorm.
 * User: johnriordan
 * Date: 24/02/2018
 * Time: 11:37
 */

include './includes/credentials/credentials.php';
include_once './classes/MbInterfaceClass.php';

if(isset($_POST['register_form']))
{
    $mbConnection           = new classes\MbInterfaceClass();
    $mbConnection->setSourceCredentials($sourceCreds);
    $mbConnection->setUserCredentials($userCreds);
//    $mbConnection->setConsumerCredentials($consumerCreds);

    $soapRequestArray['PageSize']                   = 50;
    $soapRequestArray['CurrentPageIndex']           = 0;
    $soapRequestArray['XMLDetail']                  = 'Full';
    $soapRequestArray['Test']                       = 'false';                     //set this to false when deploying to production
    $soapRequestArray['FirstName']                  = $_POST['FirstName'];
    $soapRequestArray['LastName']                   = $_POST['LastName'];
    $soapRequestArray['BirthDate']                  = $_POST['BirthDate'];
    $soapRequestArray['Email']                      = $_POST['Email'];
    $soapRequestArray['Username']                   = $_POST['Email'];
    $soapRequestArray['Password']                   = $_POST['Password'];
    $soapRequestArray['AddressLine1']               = $_POST['AddressLine1'];
    $soapRequestArray['City']                       = $_POST['City'];
    $soapRequestArray['State']                      = $_POST['State'];
    $soapRequestArray['PostalCode']                 = $_POST['PostalCode'];
    $soapRequestArray['MobilePhone']                = $_POST['MobilePhone'];
    $soapRequestArray['ReferredBy']                 = $_POST['ReferredBy'];


//    die(json_encode(($mbConnection->getClientFields())));
    $result                                         = $mbConnection->addClient($soapRequestArray);

//    die(json_encode($result));

    if($result['status'] == 'Success')
    {
        $postMessage    = "Success: " . json_encode($result['body']);
    }
    else
    {
        $postMessage    = "Failed: " . json_encode($result['body']);
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

<form action="RegisterForm.php" method="post">
    <table>
        <tr>
            <td>First Name</td>
            <td><input class='cell' type="text" name="FirstName" value="john"></td>
        </tr>
        <tr>
            <td>Last Name</td>
            <td><input class='cell' type="text" name="LastName" value="riordan"></td>
        </tr>
        <tr>
            <td>Birth Date</td>
            <td><input class='cell' type="text" name="BirthDate" value="1998-02-28"></td>
        </tr>
        <tr>
            <td>Email</td>
            <td><input class='cell' type="text" name="Email" value="jr@m3u.com"></td>
        </tr>
        <tr>
            <td>Password</td>
            <td><input class='cell' type="text" name="Password" value="Password1"></td>
        </tr>

        <tr>
            <td>AddressLine1</td>
            <td><input class='cell' type="text" name="AddressLine1" value="1 New Street"></td>
        </tr>

        <tr>
            <td>City</td>
            <td><input class='cell' type="text" name="City" value="New Towm"></td>
        </tr>

        <tr>
            <td>State</td>
            <td><input class='cell' type="text" name="State" value="New County"></td>
        </tr>

        <tr>
            <td>PostalCode</td>
            <td><input class='cell' type="text" name="PostalCode" value="NT1 1NT"></td>
        </tr>

        <tr>
            <td>MobilePhone</td>
            <td><input class='cell' type="text" name="MobilePhone" value="07777 777777"></td>
        </tr>

        <tr>
            <td>ReferredBy</td>
            <td><input class='cell' type="text" name="ReferredBy" value="New Person"></td>
        </tr>

        <tr>
            <td><input type="hidden" name="register_form"></td>
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

