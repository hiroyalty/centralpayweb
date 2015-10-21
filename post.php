<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$transaction_id = $_REQUEST['trans_id'];
$req = $_REQUEST['req'];
$res = $_REQUEST['res'];
$desc = "(DESCRIPTION =(ADDRESS = (PROTOCOL = TCP)(HOST = 397970-vm2.db1.localhost.co.uk)(PORT = 1521))(CONNECT_DATA =(SERVER = DEDICATED)(SERVICE_NAME = mydb.397970-vm2.db1.localhost.co.uk)))";
try {
    $conn = oci_connect('cpay', 'Cpay411_', "$desc");

    $stid = oci_parse($conn, "insert into cpay_transactions(trans_id,req,res) "
            . "values('$transaction_id','$req','$res')");
    oci_execute($stid);
} catch (Exception $e) {
    echo $e->getMessage();
}
?>


