<?php
require_once 'lib/f.php';
$exil = list_real_banks();
#print_r($exil);
$TransId = isset($_REQUEST['TransId']) ? $_REQUEST['TransId'] : time();
#$TransId = time();
?>
<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <title>MYPAY</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="nuhtml.css">
    </head>
    <body>
        <div class="container">

            <form id="signup" name="bpay" method="post" action="confirm.php" >

        <div class="header">
        
            <h3>Enter Your Details</h3>
            
            <p>Kindly fill Out to make Payments</p>
            
        </div>
        
        <div class="sep"></div>

        <div class="inputs">
            
            <input type="hidden" name="merchant_id" value="NIBSS0000000033" />
            
            
            <select name="bank_code" autofocus>
            <?php foreach ($exil as $exi) { ?>
            <option value="<?php echo $exi['bankCode'] ?>"><?php echo $exi['bankName'] ?></option>
            <?php } ?>
            </select>
            
            <input type="text" name="customer_id" maxlength="200" placeholder="Customer Id" />
            
            <input type="text" name="description" maxlength="250" placeholder="Product Description"/>
            
            <input type="text" name="amount" placeholder="Enter Amount in Kobo" />
            
            <select name="currency">
                <option value="566" selected="selected">Naira</option>
                <option value="840">Dollar</option>
                <option value="826">Pound</option>
            </select>
            
            <input type="hidden" name="transaction_id" value="<?php echo $TransId; ?>" />
            
            <input type="hidden" name="response_url" value="http://localhost/centralpay/response.php" /> 
            
            <input type="hidden" name="hash" />
            
            <div class="checkboxy">
                <input name="cecky" id="checky" value="1" type="checkbox" /><label class="terms">I accept the terms of use</label>
            </div>
            
            <!--<a id="submit" >C H E C K O U T</a> -->
            <input type="submit" id="submit" value="C H E C K O U T" />
        </div>

    </form>

</div>
​
    </body>
</html>
<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
