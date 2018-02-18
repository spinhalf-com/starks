<?php

    session_start();

    function p($p, $exit = 1)
    {
        echo '<pre>';
        print_r($p);
        echo '</pre>';
        if ($exit == 1)
        {
            exit;
        }
    }

    if (!empty($_POST))
    {

        if (!empty($_SESSION['userdata']['ValidateLoginResult']['Client']['ID']))
        {
            $ID = $_SESSION['userdata']['ValidateLoginResult']['Client']['ID'];

            $expiry = $_POST['expiry'];

            $expiryArr = explode('/', $expiry);

            $client = array();

            $client['ID'] = $ID;
            $client['ClientCreditCard']['CardNumber'] = $_POST['cardnumber'];
            $client['ClientCreditCard']['CardHolder'] = $_POST['cardname'];
            $client['ClientCreditCard']['Address'] = $_POST['address'];
            $client['ClientCreditCard']['City'] = $_POST['city'];
            $client['ClientCreditCard']['State'] = $_POST['county'];
            $client['ClientCreditCard']['PostalCode'] = $_POST['postcode'];
            $client['ClientCreditCard']['ExpMonth'] = $expiryArr[0];
            $client['ClientCreditCard']['ExpYear'] = $expiryArr[1];


            $clients = array(
                'Client' => $client,
            );

            //p($clients);

            require_once("includes/clientService.php");
            $sourcename = 'BrandoMedia';
            $password = 'H9GHythoV+iUYCynr6beegSdhqg=';
            $siteID = '427862';
            $creds = new SourceCredentials($sourcename, $password, array($siteID));
            $clientService = new MBClientService(true);
            $clientService->SetDefaultCredentials($creds);

            $response = $clientService->AddOrUpdateClients($clients);

            $status = $response->AddOrUpdateClientsResult->Status;

            if ($status == 'Success')
            {
                $data['status'] = TRUE;
                $data['message'] = '';
            }
            else
            {
                $message = $response->AddOrUpdateClientsResult->Message;
                $data['status'] = FALSE;
                $data['message'] = $message;
            }
        }
        else
        {

            $data['status'] = FALSE;
            $data['message'] = 'Login to your account !';
        }
    }
    else
    {
        $data['status'] = FALSE;
        $data['message'] = 'Something goes wrong !';
    }

    echo json_encode($data);

    