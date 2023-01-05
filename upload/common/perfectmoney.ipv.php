<?php

include_once('../common/init.loader.php');

//check if request was made with the right data
if ($_POST['PAYMENT_BATCH_NUM'] < 1) {
    die("Transaction batch not found");
}

$pgdatatokenarr = get_optionvals($payrow['pgdatatoken']);
$perfectmoneycfg = get_optarr($pgdatatokenarr['perfectmoneycfg']);

//$perfectmoneycfg['perfectmoneyacc']
//$perfectmoneycfg['perfectmoneyname']
//$perfectmoneycfg['perfectmoneypass']
//PAYMENT_ID:PAYEE_ACCOUNT:PAYMENT_AMOUNT:PAYMENT_UNITS:PAYMENT_BATCH_NUM:PAYER_ACCOUNT:AlternateMerchantPassphraseHash:TIMESTAMPGMT

$althash = strtoupper(md5($perfectmoneycfg['perfectmoneypass']));
$v2hash = "{$_POST['PAYMENT_ID']}:{$perfectmoneycfg['perfectmoneyacc']}:{$_POST['PAYMENT_AMOUNT']}:{$_POST['PAYMENT_UNITS']}:{$_POST['PAYMENT_BATCH_NUM']}:{$_POST['PAYER_ACCOUNT']}:{$althash}:{$_POST['TIMESTAMPGMT']}";
$v2_hash = strtoupper(md5($v2hash));

if ($_POST['V2_HASH'] == $v2_hash || ($perfectmoneycfg['perfectmoneypass'] == '' && $_POST['PAYMENT_AMOUNT'] > 0)) {
    //echo "success";
    //Perform necessary action
    include_once('../common/sandbox.php');
    $FORM['sb_type'] = 'payreg';
    //doipnbox($tx_and_$mpid, $paymentamount, $paymentgateway, $txid_referrence, $redirurl, $ipnreturn = '', $skipamount = 0, $addtoken = '') {
    doipnbox($_POST['PAYMENT_ID'], $_POST['PAYMENT_AMOUNT'], 'PerfectMoney', $_POST['PAYMENT_BATCH_NUM'], '', 'OK', 0, '');
} else {
    //echo "Transaction was unsuccessful";
}
