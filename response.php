<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
#transaction_id, cpay_ref, merchant_id, response_code, response_desc and hash
include_once 'lib/f.php';
$postdata = file_get_contents("php://input");

parse_str($postdata);
/* echo $cpay_ref;
  echo '<br/>';
  echo $transaction_id;
  echo '<br/>';
  echo $merchant_id;
  echo '<br/>';
  echo $response_code;
  echo '<br/>';
  echo $response_desc;
  echo '<br/>';
  echo $hash; //transaction_id, cpay_ref, merchant_id, response_code and secret key
  echo '<br/>'; */
$secretkey = 'EF9BA77720C83BE8579AC23C668FA1E2';
$cHash = $transaction_id . $cpay_ref . $merchant_id . $secretkey;
$cxHash = hash('sha256', $cHash);

$res = file_get_contents("https://staging.nibss-plc.com.ng/CentralPayPlus/merchantAcctTransQueryJSON?transaction_id=$transaction_id&cpay_ref=$cpay_ref&merchant_id=$merchant_id&hash=$cxHash");
#$res = file_get_contents("https://staging.nibss-plc.com.ng/CentralPayPlus/merchantAcctTransQuery?transaction_id=$transaction_id&cpay_ref=$cpayRef&merchant_id=$merchant_id&hash=$hash");
if ($res) {
    $response = $res;
    #echo '{"success": ' . json_encode($sql) . '}';  
} else {
    $response = array(
        'response' => 'Empty Response',
        'rcode' => 'Try Again');
    #echo '{"failure":{"text":'. Failed .'}}';
}
#header('Content-Type: application/json'); 
#echo json_encode($response);
#echo $response;
$alex = json_decode($response, true);

$MerchantId = $alex['MerchantId'];
$CustomerId = $alex['CustomerId'];
$CPAYRef = $alex['CPAYRef'];
$BankCode = $alex['BankCode'];
$ResponseCode = $alex['ResponseCode'];
$ResponseDesc = $alex['ResponseDesc'];
$TransDate = $alex['TransDate'];
$Amount = $alex['Amount'];
$TransactionId = $alex['TransactionId'];
$Currency = $alex['Currency'];
$Hash = $alex['Hash'];
/*
  echo $MerchantId;
  echo '<br/>';
  echo $CustomerId;
  echo '<br/>';
  echo $CPAYRef;
  echo '<br/>';
  echo $BankCode;
  echo '<br/>';
  echo $ResponseCode;
  echo '<br/>';
  echo $ResponseDesc;
  echo '<br/>';
  echo $TransDate;
  echo '<br/>';
  echo $Amount;
  echo '<br/>';
  echo $TransactionId;
  echo '<br/>';
  echo $Currency;
  echo '<br/>';
  echo $Hash; //transaction_id, cpay_ref, merchant_id, response_code and secret key
  echo '<br/>';
 * 
 */

$log_res = log_nibbs_response($MerchantId, $CustomerId, $CPAYRef, $BankCode, $ResponseCode, $ResponseDesc, $TransDate, $Amount, $TransactionId, $Currency, $Hash);
?>

<style>
    section{
        padding: 5px 5px 5px 5px;
        border: 0px solid red;
    }
    section h1{
        font-family: Arial;
        font-size: 1em;
    }
    td {
        font-family: Arial;
        font-size: 0.8em;
        padding: 5px 5px 5px 0px;
    }

    .myButton {
        -moz-box-shadow:inset 0px 1px 0px 0px #cf866c;
        -webkit-box-shadow:inset 0px 1px 0px 0px #cf866c;
        box-shadow:inset 0px 1px 0px 0px #cf866c;
        background:-webkit-gradient(linear, left top, left bottom, color-stop(0.05, #d0451b), color-stop(1, #bc3315));
        background:-moz-linear-gradient(top, #d0451b 5%, #bc3315 100%);
        background:-webkit-linear-gradient(top, #d0451b 5%, #bc3315 100%);
        background:-o-linear-gradient(top, #d0451b 5%, #bc3315 100%);
        background:-ms-linear-gradient(top, #d0451b 5%, #bc3315 100%);
        background:linear-gradient(to bottom, #d0451b 5%, #bc3315 100%);
        filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#d0451b', endColorstr='#bc3315',GradientType=0);
        background-color:#d0451b;
        -moz-border-radius:3px;
        -webkit-border-radius:3px;
        border-radius:3px;
        border:1px solid #942911;
        display:inline-block;
        cursor:pointer;
        color:#ffffff;
        font-family:Arial;
        font-size:13px;
        padding:6px 24px;
        text-decoration:none;
        text-shadow:0px 1px 0px #854629;
    }
    .myButton:hover {
        background:-webkit-gradient(linear, left top, left bottom, color-stop(0.05, #bc3315), color-stop(1, #d0451b));
        background:-moz-linear-gradient(top, #bc3315 5%, #d0451b 100%);
        background:-webkit-linear-gradient(top, #bc3315 5%, #d0451b 100%);
        background:-o-linear-gradient(top, #bc3315 5%, #d0451b 100%);
        background:-ms-linear-gradient(top, #bc3315 5%, #d0451b 100%);
        background:linear-gradient(to bottom, #bc3315 5%, #d0451b 100%);
        filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#bc3315', endColorstr='#d0451b',GradientType=0);
        background-color:#bc3315;
    }
    .myButton:active {
        position:relative;
        top:1px;
    }
    .logo{
        height: 121px;
    }
</style>

<section>
    <img src = "lib/vaspay-logo.png" class="logo"/>
    <h1>Transaction Report</h1>
    <table>
        <tr><td>CPAY Ref:</td><td><?php echo $CPAYRef; ?></td></tr>
        <tr><td>Bank</td><td><?php echo get_bank_name($BankCode); ?></td></tr>
        <tr><td>Response Code</td><td><?php echo $ResponseCode ?></td></tr>
        <tr><td>Response Description</td><td><?php echo $ResponseDesc ?></td></tr>
        <tr><td>Amount</td><td><?php echo ' NGN' . number_format($Amount, 2); ?></td></tr>
    </table>
    <br>
</section>

