<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * htmlspecialchars
 * ADD RECORD.
 *
  $paramMerchantId = isset($_REQUEST['merchant_id']) ? $_REQUEST['merchant_id'] : "NIBSS0000000033";
  $recvtim = isset($_REQUEST['recvtime']) ? $_REQUEST['recvtime'] : date('Y-m-d h:i:s');
  $paramBankCode = isset($_REQUEST['bank_code']) ? $_REQUEST['bank_code'] : "057";
  $paramCustomerId = isset($_REQUEST['customer_id']) ? $_REQUEST['customer_id'] : "hakintoye@nibss-plc.com.ng";
  $paramProdesc = isset($_REQUEST['description']) ? $_REQUEST['description'] : "Gucci Men Shoe";
  $amount = isset($_REQUEST['amount']) ? $_REQUEST['amount'] : "5000";
  $paramCurrency = isset($_REQUEST['currency']) ? $_REQUEST['currency'] : "566";
  $paramTransId = isset($_REQUEST['transaction_id']) ? $_REQUEST['transaction_id'] : "123456789";
 * 
 */
include_once 'lib/f.php';

#$paramMerchantId = $_REQUEST['merchant_id']; // Getting parameter with names
$paramMerchantId = "NIBSS0000000033";
$paramBankCode = $_REQUEST['bank_code']; // Getting parameter with names
$paramCustomerId = $_REQUEST['customer_id'];
$paramProdesc = $_REQUEST['description'];
$amount = $_REQUEST['amount'];
#$paramCurrency = $_POST['currency'];
$paramCurrency = '566';
$paramTransId = $_REQUEST['transaction_id'];
$secretkey = 'EF9BA77720C83BE8579AC23C668FA1E2';
#$paramResURL = $_POST['response_url'];
$paramResURL = "http://83.138.190.170/centralpay/response.php";

#$pHash = $paramMerchantId.$paramBankId.$paramCustomertId.$paramProdesc.$amount.$paramCurrency.$paramTransId.$paramResURL.$secretkey;
#$paramHash = hash('sha256', $pHash);
#$paramResURL = isset($_REQUEST['response_url']) ? $_REQUEST['response_url'] : "http://83.138.190.170/centralpay/confirm.php";
#$secretkey = 'EF9BA77720C83BE8579AC23C668FA1E2';
#$paramHash = isset($_REQUEST['hash']);
$pHash = $paramMerchantId . $paramBankCode . $paramCustomerId . $paramProdesc . $amount . $paramCurrency . $paramTransId . $paramResURL . $secretkey;
$paramHash = hash('sha256', $pHash);
#echo $paramHash;
#NIBSS0000000033057hakintoye@nibss-plc.com.ngGucci Men Shoe5000566123456789http://localhost/centralpay/shop/callbackEF9BA77720C83BE8579AC23C668FA1E2
$str_to_insert = ".";
$pos = 2;
$newstr = substr_replace($amount, $str_to_insert, $pos, 0);

$ret_request = make_nibbs_request($paramMerchantId,$paramBankCode,$paramCustomerId,$paramProdesc,$amount,$paramCurrency,$paramTransId,$paramResURL,$paramHash);
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
<br>
<section>
    <img src="lib/vaspay-logo.png" class="logo"/>
    <h1>Please confirm your payment</h1>
    <table>
        <tr><td>Description</td><td><?PHP echo $paramProdesc ?></td></tr>
        <tr><td>Amount</td><td><?PHP echo ' NGN' . number_format($amount,2); ?></td></tr>
    </table>
    <br>
    <form name="cpay" action=" https://staging.nibss-plc.com.ng/CentralPayPlus/payAcctMerchant">
        <input type="hidden" name="merchant_id" value="<?PHP echo ($paramMerchantId); ?>" />
        <input type="hidden" name="bank_code" value="<?PHP echo ($paramBankCode); ?>" />
        <input type="hidden" name="customer_id" value="<?PHP echo ($paramCustomerId); ?>" />
        <input type="hidden" name="description" value="<?PHP echo ($paramProdesc); ?>" /> 
        <input type="hidden" name="amount" value="<?PHP echo ($amount); ?>" />
        <input type="hidden" name="currency" value="<?PHP echo ($paramCurrency); ?>" />
        <input type="hidden" name="transaction_id" value="<?PHP echo ($paramTransId); ?>" />
        <input type="hidden" name="response_url" value="<?PHP echo ($paramResURL); ?>" /> 
        <input type="hidden" name="hash" value="<?PHP echo ($paramHash); ?>" />
        <input type="submit" class ="myButton" value="Confirm" />
    
    </form>
</section>
<?php
/* <form name="cpay" action=" https://staging.nibss-plc.com.ng/CentralPayPlus/payAcctMerchant "> 
  <input type="hidden" name="merchant_id" value="00000001" />
  <input type="hidden" name="bank_code" value="014" />
  <input type="hidden" name="customer_id" value="hakintoye@nibss-plc.com.ng" />
  <input type="hidden" name="description" value="Gucci Men Shoe" />
  <input type="hidden" name="amount" value="5000" />
  <input type="hidden" name="currency" value="566" />
  <input type="hidden" name="transaction_id" value="123456789" />
  <input type="hidden" name="response_url" value="http://localhost/response" />
  <input type="hidden" name="hash" value="865700674EFABC934720187465969784736" />
  </form> */
?>