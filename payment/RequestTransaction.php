<?php

    $json = file_get_contents('php://input');
    
    $result = json_decode($json);

    // Set your merchant code (Note: Server key for sandbox and production mode are different)
    $merchantCode = "D5393"; 
    // Set your merchant key (Note: Server key for sandbox and production mode are different)
    $merchantKey = "bd9cdd0853a015722959816d0aaf6c31";
    
    $paymentAmount = $result->{'paymentAmount'}; 
    $merchantOrderId = $result->{'merchantOrderId'};
    
    $signature = md5($merchantCode . $merchantOrderId . $paymentAmount . $merchantKey);
  
    $itemsParam = array(
        'merchantCode' => $merchantCode,
        'merchantKey' => $merchantKey,
        'merchantOrderId' => $merchantOrderId,
        'signature' => $signature
    );

    class emp{}
    
    $params = array_merge((array)$result,$itemsParam);

    $params_string = json_encode($params);
    
    //$url = 'https://sandbox.duitku.com/webapi/api/merchant/v2/inquiry'; // Sandbox
    $url = 'https://passport.duitku.com/webapi/api/merchant/v2/inquiry'; // Production
    $ch = curl_init();
    
    
    
                            //write log
    file_put_contents('duitku_request.txt', "*** Logs Request Transaksi ***\r\n", FILE_APPEND | LOCK_EX);
	file_put_contents('duitku_request.txt', "\r\n***************************\r\n\r\n", FILE_APPEND | LOCK_EX);
	file_put_contents('duitku_request.txt', "*** " . date("Y-m-d H:i:s") . " ***\r\n", FILE_APPEND | LOCK_EX);
	file_put_contents('duitku_request.txt', "request : " . $params_string . " \r\n", FILE_APPEND | LOCK_EX);
	file_put_contents('duitku_request.txt', "\r\n***************************\r\n\r\n", FILE_APPEND | LOCK_EX);

    
    
    
    
    
    
    

    curl_setopt($ch, CURLOPT_URL, $url); 
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params_string);                                                                  
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
        'Content-Type: application/json',                                                                                
        'Content-Length: ' . strlen($params_string))                                                                       
    );   
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

    //execute post
    $request = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if($httpCode == 200)
    {
            echo $request ;
    }
    else{
            $response = new emp();
			$response->statusMessage = "Server Error . $httpCode ";
			$response->error = $httpCode;
			die(json_encode($response)); 

    }
			
?>