<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/***********************
 * Used for processing data from the JAVA RESTFUL WEBSERVICE
 * CENTRAL PAY APPLICATION.
 */

require_once 'oracle_dbase.php';

function get_bank_name($code){
    /*
     * <bankList><bank>
     * <bankCode>214</bankCode><bankName>First City Monument Bank Plc</bankName></bank>
     * <bank><bankCode>058</bankCode><bankName>Guaranty Trust Bank Plc</bankName></bank>
     * <bank><bankCode>057</bankCode><bankName>Zenith Bank Plc</bankName></bank>
     * <bank><bankCode>044</bankCode><bankName>Access Bank Plc</bankName></bank>
     * <bank><bankCode>076</bankCode><bankName>Skye Bank Plc</bankName></bank>
     * <bank><bankCode>221</bankCode><bankName>Stanbic IBTC Bank Plc</bankName></bank>
     * <bank><bankCode>032</bankCode><bankName>Union Bank of Nigeria Plc</bankName></bank>
     * </bankList>
     */
     $ret = 'Uknown Bank Name';
    switch ($code) {
       
        case '214':
            $ret = 'First City Monument Bank Plc';
            break;
         case '057':
            $ret = 'Zenith Bank Plc';
            break;
        case '058':
            $ret = 'Guaranty Trust Bank Plc';
            break;
        case '044':
            $ret = 'Access Bank Plc';
            break;
         case '076':
            $ret = 'Skye Bank Plc';
            break;
        case '221':
            $ret = 'Stanbic IBTC Bank Plc';
            break;
         case '032':
            $ret = 'Union Bank of Nigeria Plc';
            break;
        default:
            break;
    }
    return $ret;
}

function callUrl($url) {
    try {
# try pushing request to url;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPGET, 1); // Make sure GET method it used
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // Return the result
        curl_setopt($ch, CURLOPT_COOKIEJAR, '/tmp/cookies.txt');
        curl_setopt($ch, CURLOPT_COOKIEFILE, '/tmp/cookies.txt');
        $res = curl_exec($ch); // Run the request
    } catch (Exception $ex) {

        $res = 'Error Calling URL';
    }
    return $res;
}

function crypto_rand_secure($min, $max) {
    $range = $max - $min;
    if ($range < 0)
        return $min; // not so random...
    $log = log($range, 2);
    $bytes = (int) ($log / 8) + 1; // length in bytes
    $bits = (int) $log + 1; // length in bits
    $filter = (int) (1 << $bits) - 1; // set all lower bits to 1
    do {
        $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
        $rnd = $rnd & $filter; // discard irrelevant bits
    } while ($rnd >= $range);
    return $min + $rnd;
}

function getToken($length) {
    $token = "";
    $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
    $codeAlphabet.= "0123456789";
    for ($i = 0; $i < $length; $i++) {
        $token .= $codeAlphabet[crypto_rand_secure(0, strlen($codeAlphabet))];
    }
    return $token;
}

function sendSMS($src, $dest, $body) {
    $id = "samson_ude@yahoo.com";
    $pw = "eut235A33"; # ensure that you use the approved password on v2nmobile.

    $url = "http://v2nmobile.co.uk/api/httpsms.php?u=" .
            urlencode($id) . "&p=" . urlencode($pw)
            . "&r=" . urlencode($dest)
            . "&s="
            . urlencode($src)
            . "&m="
            . urlencode($body) . "&t=1";
    callUrl($url);
}

function log_action($msg, $logFile) {
    #$date_time = date("Y-m-d h:i:s");
    #$logpath = '/var/www/html/nsl/';
    #$logFile = "call.log";
    //$log = "$date_time >> $msg";
    $fp = fopen($logFile, 'a+');
    fputs($fp, $msg);
    fclose($fp);
    return TRUE;
}

function make_nibbs_request($merchantid, $bank_code, $customer_id, $description, $amount, $currency, $transaction_id, $response_url, $hash) {
    #$length = 15;
    #$transId = getToken($length);
    $log_req = "insert into NIBSS_REQUEST (merchant_id,bank_code,customer_id,product_description,amount,currency,transaction_id,response_url,hash) values ('$merchantid','$bank_code','$customer_id','$description','$amount','$currency','$transaction_id','$response_url','$hash')";
    $sql = db_execute($log_req);
    return $sql;
}

function log_nibbs_response($merchant_id, $CustomerId, $CPAYRef, $BankCode, $ResponseCode, $ResponseDesc, $TransDate, $Amount, $TransactionId, $Currency, $Hash) {
    $log_res = "insert into NIBSS_RESPONSE (MERCHANT_ID,CUSTOMERID,CPAYREF,BANKCODE,RESPONSECODE,RESPONSEDESC,TRANSDATE,AMOUNT,TRANSACTIONID,CURRENCY,HASH) values ('$merchant_id','$CustomerId','$CPAYRef','$BankCode','$ResponseCode','$ResponseDesc','$TransDate','$Amount','$TransactionId','$Currency','$Hash')";
    $sql = db_execute($log_res);
    return $sql;
}

function get_transaction_response($transID) {
    $trans = "select * from NIBSS_RESPONSE where TRANSACTIONID = $transID";
    $sql = db_query($trans);
    return $sql;
}

function process_values($vals) {
    if (is_array($vals)) {
        if (empty($vals)) {
            #return $key. '-' . '0';
            return NULL;
        } else {
            foreach ($vals as $v => $val) {
                #echo $key. ' - '. $v. ' : '. $val;
                return $val;
            }
        }
    } else {
        #echo $key. ' - '. $value;
        return $vals;
    }
}

function post_to_url($url, $data) {
    $fields = '';
    foreach ($data as $key => $value) {
        $fields .= $key . '=' . $value . '&';
    }
    rtrim($fields, '&');
    $post = curl_init();

    curl_setopt($post, CURLOPT_URL, $url);
    curl_setopt($post, CURLOPT_POST, count($data));
    curl_setopt($post, CURLOPT_POSTFIELDS, $fields);
    curl_setopt($post, CURLOPT_RETURNTRANSFER, 1);

    $result = curl_exec($post);

    curl_close($post);
    return $result;
}

function crazy_posting($url, $fields) {

//url-ify the data for the POST
    foreach ($fields as $key => $value) {
        $fields_string .= $key . '=' . $value . '&';
    }
    rtrim($fields_string, '&');

//open connection
    $ch = curl_init();

//set the url, number of POST vars, POST data
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, count($fields));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);

//execute post
    $result = curl_exec($ch);

//close connection
    curl_close($ch);
    return $result;
}

function list_real_banks() {
    #$eee = file_get_contents("https://83.138.190.170/CentralPay/listActiveBanks/NIBSS0000000033/FGFGDFHGD345689497TWFDBLSOUEZ_");
    $eee = file_get_contents("https://localhost/CentralPay/listActiveBanks/NIBSS0000000033/FGFGDFHGD345689497TWFDBLSOUEZ_");

#echo $eee;
    $eed = json_decode($eee, TRUE);
#print_r ($eed);
#echo '<br>';
    foreach ($eed as $vgtv) {
        foreach ($vgtv as $vgt) {
            foreach ($vgt as $key) {
                foreach ($key as $ke) {
                    #echo 'Bank Name'. $ke['bankName'] . ':  Bank Code'. $ke['bankCode'];
                    #echo '<br>';
                }
            }
        }
    }
    return $key;
}

function list_Banks() {
    $namespace = "http://web.nibss.com/";
    $url = "https://staging.nibss-plc.com.ng/CentralPayWebservice/CentralPayOperations";
// The value for the SOAPAction: header
    $action = "listActiveBanks";
    $para = 'NIBSS0000000033';
// Get the SOAP data into a string, I am using HEREDOC syntax
// but how you do this is irrelevant, the point is just get the
// body of the request into a string
    $xml = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:web="http://web.nibss.com/">
   <soapenv:Header/>
   <soapenv:Body>
      <web:listActiveBanks>
         <!--Optional:-->
         <arg0>' . $para . '</arg0>
      </web:listActiveBanks>
   </soapenv:Body>
</soapenv:Envelope>';

// The HTTP headers for the request (based on image above)
    $headers = array(
        'Content-Type: text/xml; charset=utf-8',
        'Content-Length: ' . strlen($xml),
        'SOAPAction: ' . $action
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);

// Send the request and check the response
    if (($result = curl_exec($ch)) === FALSE) {
        die('cURL error: ' . curl_error($ch) . "<br />\n");
    } else {
        #echo "Success!<br />\n";
        #echo $result;
    }
    curl_close($ch);
#echo $result;
    $fileContents = $result;
#echo $fileContents;
    $all_data = array();
    $exil = array();
    $sPattern = "/<return>(.*?)<\/return>/s";
    preg_match($sPattern, $result, $aMatch);
    $data = $aMatch[1];
    $old = array('<![CDATA[', ']]>');
    $new = array('', '');
    $finals = str_replace($old, $new, $data);
    $final = html_entity_decode($finals);
#echo $final;
    $simpleXml = simplexml_load_string($final);
#echo $simpleXml->CPAYActiveBankResponse->merchantID;
    $json = json_encode($simpleXml);
    $all_data = json_decode($json, true);
    if ($all_data['responseCode'] == '000') {
        $exil = $all_data['bankList']['bank'];
        #print_r ($all_data);
        return($exil);
    }
}


