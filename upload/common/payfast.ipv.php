<?php

//check if request was made with the right data
if (!isset($_POST['m_payment_id'])) {
    die("Transaction payment id not found");
}

// Tell PayFast that this page is reachable by triggering a header 200
header('HTTP/1.0 200 OK');
flush();

//define('SANDBOX_MODE', true);
//$pfHost = SANDBOX_MODE ? 'sandbox.payfast.co.za' : 'www.payfast.co.za';
// Posted variables from ITN
$pfData = $_POST;

// Strip any slashes in data
foreach ($pfData as $key => $val) {
    $pfData[$key] = stripslashes($val);
}

//$pfDatastr = implode(', ', $pfData);
//file_put_contents('printlog.log', "[" . date('Y-m-d H:i:s') . "][" . basename(__FILE__, '.php') . "] {$pfDatastr}" . PHP_EOL, FILE_APPEND | LOCK_EX);

if ($pfData['payment_status'] == 'COMPLETE' && $pfData['signature'] != '' && $pfData['pf_payment_id'] > 0) {
    //echo "success";
    //Perform necessary action
    include_once('../common/sandbox.php');
    $FORM['sb_type'] = 'payreg';
    //doipnbox($tx_and_$mpid, $paymentamount, $paymentgateway, $txid_referrence, $redirurl, $ipnreturn = '', $skipamount = 0, $addtoken = '') {
    doipnbox($_POST['m_payment_id'], $pfData['amount_gross'], 'Payfast', $pfData['pf_payment_id'], '', 'OK', 0, '');
} else {
    //echo "Transaction was unsuccessful";
}
