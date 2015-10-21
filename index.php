<?php
session_start();
/**********************************
 * Tried to use this REstful webservices Stack, however it
 * will not based based on the limitation placed on 
 * us by the implementing organisation.
 * I am to Post values directly with the forms.
 */
#require_once 'lib/oracle_dbase.php';
require_once 'lib/f.php';

ini_set("display_errors",1);
require 'Slim/Slim.php';
//if($_POST)
//{
//    echo "asdfasdf";
//    exit;
//}
\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim();

$app->get('/shop', 'makePayment');
$app->get('/shop/callback', 'callBac');
$app->get('/paymentstatus', 'payStatus');
#$app->get('/users/:id',    'getUser');
#$app->get('/addagent', 'addAgent');
#$app->put('/users/:id', 'updateUser');
#$app->delete('/users/:id',    'deleteUser');


$app->run();

function makePayment()  {
    global $app;
    $req = $app->request(); // Getting parameter with names
    $paramMerchantId = $req->params('merchant_id'); // Getting parameter with names
    $paramProductId = $req->params('product_id'); // Getting parameter with names
    $paramProdesc = $req->params('product_description');
    $amount = $req->params('amount');
    $paramCurrency = $req->params('currency');
    $paramTransId = time();
    $paramResURL = $req->params('response_url');
    $secretkey = 'EF9BA77720C83BE8579AC23C668FA1E2';
    #$paramHash = $req->params('hash');
    #$uniqueId= time();
    $pHash = "$paramMerchantId.$paramProductId.$paramProdesc.$amount.$paramCurrency.$paramTransId.$paramResURL.$secretkey";
    $paramHash = hash('sha256', $pHash);
    $url = "https://staging.nibss-plc.com.ng/CentralPayPlus/pay";
    #post_to_url("http://83.138.190.170/centralpay/shop/", $_POST);
    #curl -i -X POST -H 'Content-Type: application/json' -d '{"name": "New Wine", "year": "2009"}' http://localhost/cellar/api/wines
    $fields = array(
            'merchant_id' => urlencode($paramMerchantId),
            'product_id' => urlencode($paramProductId),
            'product_description' => urlencode($paramProdesc),
            'amount' => urlencode($amount),
            'currency' => urlencode($paramCurrency),
            'transaction_id' => urlencode($paramTransId),           
            'response_url' => urlencode($paramResURL),
            'hash' => urlencode($paramHash)
);
  $rex = crazy_posting($url, $fields);
  echo $rex;
}

function callBac()  {
    global $app;
    $req = $app->request();
    $cpayRef = $req->get('cpay_ref');
    $transaction_id = $req->get('transaction_id');
    $merchant_id = $req->get('merchant_id');
    $response_code = $req->get('response_code');
    $response_desc = $req->get('response_desc');
    $hash = $req->get('hash');
    #transaction_id, cpay_ref, merchant_id, response_code, response_desc and hash
    $res = file_get_contents("https://staging.nibss-plc.com.ng/CentralPayPlus/merchantAcctTransQueryJSON?transaction_id=$transaction_id&cpay_ref=$cpayRef&merchant_id=$merchant_id&hash=$hash");
    #$res = file_get_contents("https://staging.nibss-plc.com.ng/CentralPayPlus/merchantAcctTransQuery?transaction_id=$transaction_id&cpay_ref=$cpayRef&merchant_id=$merchant_id&hash=$hash");
    if($res)    {
        $response = $res;
        #echo '{"success": ' . json_encode($sql) . '}';  
    } else {
        $response = array(
            'response' => 'Empty Response',
            'rcode' => 'Try Again' );
        #echo '{"failure":{"text":'. Failed .'}}';
    }
    header('Content-Type: application/json'); 
    echo json_encode($res);
}

function payStatus()  {
    global $app;
    $req = $app->request();
    $transaction_id = $req->get('transaction_id');
    $res = get_transaction_response($transaction_id);
    header('Content-Type: application/json'); 
    echo json_encode($res);
    
}
/*$desc = "(DESCRIPTION =(ADDRESS = (PROTOCOL = TCP)(HOST = 397970-vm2.db1.vas2nets.co.uk)(PORT = 1521))(CONNECT_DATA =(SERVER = DEDICATED)(SERVICE_NAME = mydb.397970-vm2.db1.vas2nets.co.uk)))";
try {
    $conn = oci_connect('cpay', 'Cpay411_', "$desc");

    $stid = oci_parse($conn, 'select table_name from user_tables');
    oci_execute($stid);

    echo "<table>\n";
    while (($row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS)) != false) {
        echo "<tr>\n";
        foreach ($row as $item) {
            echo "  <td>" . ($item !== null ? htmlentities($item, ENT_QUOTES) : "&nbsp;") . "</td>\n";
        }
        echo "</tr>\n";
    }
    echo "</table>\n";
} catch (Exception $e) {
    echo $e->getMessage();
}*/
?>
