<?php

include_once('../common/init.loader.php');

$pgdatatokenarr = get_optionvals($payrow['pgdatatoken']);
$stripecfg = get_optarr($pgdatatokenarr['stripecfg']);

//$stripecfg['stripeacc']
//$stripecfg['stripename']
//$stripecfg['stripepass']
//PAYMENT_ID:PAYEE_ACCOUNT:PAYMENT_AMOUNT:PAYMENT_UNITS:PAYMENT_BATCH_NUM:PAYER_ACCOUNT:AlternateMerchantPassphraseHash:TIMESTAMPGMT
$sitelogoonstrp = $cfgrow['site_url'] . str_replace('..', '', $site_logo);

if ($_SESSION['stripetxmpid'] != '') {
    require_once('../assets/fellow/stripe-php/init.php');
    \Stripe\Stripe::setApiKey($stripecfg['stripepass']);

    header('Content-Type: application/json');
    $checkout_session = \Stripe\Checkout\Session::create([
                'customer_email' => $_SESSION['stripembrem'],
                'client_reference_id' => $_SESSION['stripetxmpid'],
                'payment_method_types' => ['card'],
                'line_items' => [[
                'price_data' => [
                    'currency' => $bpprow['currencycode'],
                    'unit_amount' => $_SESSION['stripetot00'],
                    'product_data' => [
                        'name' => $_SESSION['stripepname'],
                        'images' => ["{$sitelogoonstrp}"],
                    ],
                ],
                'quantity' => 1,
                    ]],
                'mode' => 'payment',
                'success_url' => $cfgrow['site_url'] . '/common/stripe.ipv.php?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => $cfgrow['site_url'] . '/' . MBRFOLDER_NAME . '/index.php?hal=planpay&act=cancelpay',
    ]);

    echo json_encode(['id' => $checkout_session->id]);
}