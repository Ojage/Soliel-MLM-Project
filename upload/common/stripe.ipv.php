<?php

include_once('../common/init.loader.php');

$pgdatatokenarr = get_optionvals($payrow['pgdatatoken']);
$stripecfg = get_optarr($pgdatatokenarr['stripecfg']);

//$stripecfg['stripeacc']
//$stripecfg['stripename']
//$stripecfg['stripepass']
//PAYMENT_ID:PAYEE_ACCOUNT:PAYMENT_AMOUNT:PAYMENT_UNITS:PAYMENT_BATCH_NUM:PAYER_ACCOUNT:AlternateMerchantPassphraseHash:TIMESTAMPGMT

if ($FORM['stripe_txmpid'] != '') {
    try {
        require_once('../assets/fellow/stripe-php/init.php');
        \Stripe\Stripe::setApiKey($stripecfg['stripepass']);

        $charge = \Stripe\Charge::create(array(
                    "amount" => $_POST['amount'],
                    "currency" => $_POST['currency'],
                    "card" => $_POST['stripeToken'],
                    "description" => $_POST['description']
        ));
        $paidbatch = $charge->balance_transaction;
        $amountreal = $_POST['amount'] / 100;
        //Perform necessary action
        include_once('../common/sandbox.php');
        $FORM['sb_type'] = 'payreg';
        //doipnbox($tx_and_$mpid, $paymentamount, $paymentgateway, $txid_referrence, $redirurl, $ipnreturn = '', $skipamount = 0, $addtoken = '')
        doipnbox($_POST['stripe_txmpid'], $amountreal, 'Stripe', $paidbatch, '', 'OK', 0, '');
    } catch (Exception $e) {
        // Something else happened, completely unrelated to Stripe
    }
    redirpageto($cfgrow['site_url'] . '/' . MBRFOLDER_NAME . '/ipnhub.php?hal=dashboard');
} elseif ($FORM['session_id'] != '') {
    try {
        require_once('../assets/fellow/stripe-php/init.php');
        \Stripe\Stripe::setApiKey($stripecfg['stripepass']);

        // Fetch the Checkout Session to display the JSON result on the success page
        try {
            $checkout_session = \Stripe\Checkout\Session::retrieve($FORM['session_id']);
        } catch (Exception $e) {
            $api_error = $e->getMessage();
        }

        if (empty($api_error) && $checkout_session) {
            // Check whether the charge is successful
            if ($checkout_session->payment_status == 'paid') {

                // Transaction details
                $transactionID = $checkout_session->payment_intent;
                $paidAmount = $checkout_session->amount_subtotal;
                $paidAmount = $paidAmount / 100;
                $client_reference_id = $checkout_session->client_reference_id;

                //Perform necessary action
                include_once('../common/sandbox.php');
                $FORM['sb_type'] = 'payreg';
                //doipnbox($tx_and_$mpid, $paymentamount, $paymentgateway, $txid_referrence, $redirurl, $ipnreturn = '', $skipamount = 0, $addtoken = '') {
                doipnbox($client_reference_id, $paidAmount, 'Stripe', $transactionID, '', 'OK', 0, '');
            } else {
                $statusMsg = "Transaction has been failed!";
            }
        } else {
            $statusMsg = "Transaction has been failed! $api_error";
        }
        if ($statusMsg != '') {
            redirpageto($cfgrow['site_url'] . '/' . MBRFOLDER_NAME . '/index.php?hal=planpay&msg=' . $statusMsg);
            exit();
        }
    } catch (Exception $e) {
        // Something else happened, completely unrelated to Stripe
    }
    redirpageto($cfgrow['site_url'] . '/' . MBRFOLDER_NAME . '/ipnhub.php?hal=dashboard');
}
