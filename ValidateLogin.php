<?php
/**
 * Created by PhpStorm.
 * User: johnriordan
 * Date: 24/02/2018
 * Time: 09:07
 */

include './includes/credentials/credentials.php';
include_once './classes/MbInterfaceClass.php';

if(isset($_POST['login_form']))
{
    $mbConnection                                   = new classes\MbInterfaceClass();

    $mbConnection->setSourceCredentials($sourceCreds);
    $mbConnection->setUserCredentials($userCreds);

    $soapRequestArray['Username']                   = $_POST['email'];
    $soapRequestArray['Password']                   = $_POST['password'];

    $result                 = $mbConnection->consumerLogin($soapRequestArray);

//    die(json_encode($result));

    if($result['ValidateLoginResult']['Status'] == 'Success')
    {
        $postMessage    = "Success: " . $result['ValidateLoginResult']['Client']['ID'];
    }
    else
    {
        $postMessage    = "Failed: " . $result['ValidateLoginResult']['Status'] . " : " . $result['ValidateLoginResult']['Message'];
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

<form action="ValidateLogin.php" method="post">
    <table>
        <tr>
            <td>Email</td>
            <td><input class='cell' type="text" name="email" value="jr@m3u.com"></td>
        </tr>
        <tr>
            <td>Password</td>
            <td><input class='cell' type="text" name="password" value=""></td>
        </tr>

        <tr>
            <td><input type="hidden" name="login_form"></td>
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

</html>

