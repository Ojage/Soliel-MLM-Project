<?php

//check if request was made with the right data
if (!$_SERVER['REQUEST_METHOD'] == 'POST' || !isset($_POST['reference'])) {
    die("Transaction reference not found");
}

//set reference to a variable @ref
$reference = $_POST['reference'];
$paystackpin = $_POST['refpin'];
$paystackhash = md5($paystackpin.$_POST['txmpid'].$_POST['reg_fee']);

//The parameter after verify/ is the transaction reference to be verified
$url = 'https://api.paystack.co/transaction/verify/' . $reference;
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt(
        $ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . base64_decode($paystackpin)]
);
//send request
$request = curl_exec($ch);
//close connection
curl_close($ch);

//declare an array that will contain the result 
$result = array();
if ($request) {
    $result = json_decode($request, true);
}

if (array_key_exists('data', $result) && array_key_exists('status', $result['data']) && ($result['data']['status'] === 'success') && $paystackhash == $_POST['reg_hash']) {
    //echo "success";
    //Perform necessary action
    include_once('../common/sandbox.php');
    $FORM['sb_type'] = 'payreg';
    //doipnbox($tx_and_$mpid, $paymentamount, $paymentgateway, $txid_referrence, $redirurl, $ipnreturn = '', $skipamount = 0, $addtoken = '') {
    doipnbox($_POST['txmpid'], $_POST['reg_fee'], 'PayStack', $reference, '', 'OK', 0, '');
} else {
    //echo "Transaction was unsuccessful";
}
