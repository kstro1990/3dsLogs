<?php

    $login ='login';
    $trankey = 'key';
    $numCard = 'tdc';
    //4110770018334703
    $auth = new stdClass();
    $auth->login = $login;
    $auth->nonce = 'abc123toma';
    $auth->seed = date('c');
    $auth->tranKey = base64_encode(hash('sha256', $auth->nonce . $auth->seed . $trankey , true));
    $auth->nonce = base64_encode($auth->nonce);
    //intrumento
    $card = new stdClass();
    $card->number = $numCard;
    $card->expirationMonth = "11";
    $card->expirationYear = "22";
    $card->cvv="123";
    $instrument = new stdClass();
    $instrument->card = $card;
    //monto de el pago
    $amount = new stdClass();
    $amount->currency = "USD";
    $amount->total = 100;
    //monto de la trx
    $payment = new stdClass();
    $payment->reference = uniqid();
    $payment->description = 'A payment collect example';
    $payment->amount = $amount;
    //obj json
    $request = new stdClass();
    $request->auth = $auth;
    $request->instrument = $instrument;
    $request->payment = $payment;
    //
    $denco= json_encode($request);
    $url = 'https://secure.placetopay.ec/rest/gateway/information';
    $ch = curl_init($url);
    $jsonDataEncoded = json_encode($request);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $denco);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=UTF-8', 'User-Agent: cUrl Testing'));
    $result = curl_exec($ch);
    $respuesta = json_decode($result);
    //echo($respuesta->threeDS);
    $file = fopen("information_".date("Y-m-d").".txt","a") or die("problema al creal el archivo");
    fwrite($file, $result .PHP_EOL);
    fwrite($file, "...........------------......................------------...........".PHP_EOL);
    fclose($file);
    /*
        if ($respuesta->threeDS) {
            # code...
        }
    */
    $request->returnUrl = "https://www.placetopay.com";
    $denco2= json_encode($request);
    $url = 'https://secure.placetopay.ec/rest/gateway/mpi/lookup';
    $ch = curl_init($url);
    $jsonDataEncoded = json_encode($request);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $denco2);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=UTF-8', 'User-Agent: cUrl Testing'));
    $resultMPI = curl_exec($ch);
    $respuestaMPI = json_decode($resultMPI);
    // guardar en session $resultMPI->identifier
    if ($respuestaMPI->status->status == "OK" ) {
        echo "esta ok_";
       if ($respuestaMPI->data->enrolled == "Y") {
           echo "_ esta enrrolado";
            $file = fopen("MPI_lookup_".date("Y-m-d").".txt","a") or die("problema al creal el archivo");
            fwrite($file, "ENROLLED".PHP_EOL);
            fwrite($file,"Login : ".$login.PHP_EOL);
            fwrite($file, $respuestaMPI->data->identifier .PHP_EOL);
            fwrite($file, $respuestaMPI->data->redirectUrl .PHP_EOL);
            fwrite($file, $resultMPI .PHP_EOL);
            fwrite($file, "...........------------......................------------...........".PHP_EOL);
            fclose($file);
       }
    }else{
        $file = fopen("MPI_lookup_ERROR_".date("Y-m-d").".txt","a") or die("problema al creal el archivo");
        fwrite($file, "Login : ".$login.PHP_EOL);
        fwrite($file, $resultMPI .PHP_EOL);
        fwrite($file, "...........------------......................------------...........".PHP_EOL);
        fclose($file);
        echo "no hace nada_";
    }
    /*
        $file = fopen("MPI_lookup_".date("Y-m-d").".txt","a") or die("problema al creal el archivo");
        fwrite($file, $resultMPI .PHP_EOL);
        fwrite($file, "...........------------......................------------...........".PHP_EOL);
        fclose($file);
    */

?>
