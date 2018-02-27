<?php
/**
 * Created by PhpStorm.
 * User: johnriordan
 * Date: 26/02/2018
 * Time: 13:39
 */

include './includes/credentials/credentials.php';
include_once './classes/MbInterfaceClass.php';

if(isset($_POST['esearch_form']))
{
    $mbConnection           = new classes\MbInterfaceClass();
    $mbConnection->setSourceCredentials($sourceCreds);
    $mbConnection->setUserCredentials($userCreds);

    $soapRequestArray['PageSize']                   = 50;
    $soapRequestArray['CurrentPageIndex']           = 0;
    $soapRequestArray['XMLDetail']                  = 'Full';
    $soapRequestArray['Test']                       = 'true';                     //set this to false when deploying to production
    $soapRequestArray['ClientIDs'][]                = $_POST['id'];     //the client ID from the MB record

    $result                 = $mbConnection->getClientByID($soapRequestArray);

//    die(json_encode($result));

    if($result['status'] == '200')
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

<form action="SearchForm.php" method="post">
    <table>
        <tr>
            <td>ClientID</td>
            <td><input class='cell' type="text" name="id" value="100015651"></td>
        </tr>

        <tr>
            <td><input type="hidden" name="esearch_form"></td>
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

