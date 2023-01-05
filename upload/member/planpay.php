<?php
if (!defined('OK_LOADME')) {
    die('o o p s !');
}

// check if already registered to the payplan
if ($mbrstr['idmbr'] != $mbrstr['id']) {
    // not registered
    redirpageto('index.php?hal=planreg');
    exit;
}

$bpprow = ppdbplan($mbrstr['mppid']);
$pgdatatokenarr = get_optionvals($payrow['pgdatatoken']);

$pgdatatoken = $mbrstr['pgdatatoken'];
$pgmbrtokenarr = get_optionvals($pgdatatoken);

$perfectmoneycfg = get_optarr($pgdatatokenarr['perfectmoneycfg']);
$mbrperfectmoneycfg = get_optarr($pgmbrtokenarr['perfectmoneycfg']);

$stripecfg = get_optarr($pgdatatokenarr['stripecfg']);
$mbrstripecfg = get_optarr($pgmbrtokenarr['stripecfg']);

$payfastcfg = get_optarr($pgdatatokenarr['payfastcfg']);
$mbrpayfastcfg = get_optarr($pgmbrtokenarr['payfastcfg']);

$paystackcfg = get_optarr($pgdatatokenarr['paystackcfg']);
$mbrpaystackcfg = get_optarr($pgmbrtokenarr['paystackcfg']);

$coinpaymentscfg = get_optarr($pgdatatokenarr['coinpaymentscfg']);
$mbrcoinpaymentscfg = get_optarr($pgmbrtokenarr['coinpaymentscfg']);

$paypalcfg = get_optarr($pgdatatokenarr['paypalcfg']);
$mbrpaypalcfg = get_optarr($pgmbrtokenarr['paypalcfg']);

$ewalletcfg = get_optarr($pgdatatokenarr['ewalletcfg']);
$mbrewalletcfg = get_optarr($pgmbrtokenarr['ewalletcfg']);

// get transaction details
$unpaidtxid = get_unpaidtxid($mbrstr);
if ($unpaidtxid > 0) {
    $txidstr = $unpaidtxid;
    $payforstr = "<i class='fas fa-file-invoice fa-fw'></i>";
} else {
    $condition = ' AND txtoken LIKE "%|REG:' . $mbrstr['mpid'] . '|%" ';
    $row = $db->getAllRecords(DB_TBLPREFIX . '_transactions', '*', $condition);
    $trxstr = array();
    foreach ($row as $value) {
        $trxstr = array_merge($trxstr, $value);
    }
    $txidstr = $trxstr['txid'];
    $payforstr = 'REGISTERED';

    if ($trxstr['txstatus'] == 1) {
        redirpageto('index.php?hal=dashboard');
        exit;
    }
}
// get transaction details
$trxstr = get_txinfo($txidstr);
$mbr_fee = (strpos($trxstr['txtoken'], '|RENEW:') !== false) ? $mbrstr['renew_fee'] : $mbrstr['reg_fee'];
$paynow_fee = ($trxstr['txamount'] > 0) ? $trxstr['txamount'] : $mbr_fee;
// -----

$txmpid = $txidstr . '-' . $mbrstr['mpid'];
$paybatch = strtoupper($mbrstr['mpid'] . date("m-DH-dis"));

$regfee = $totcoinpayments = $totstripe = $totperfectmoney = $totpaypal = $totpayfast = $totpaystack = $totewallet = $totmanualpay = $tottestpay = $paynow_fee;

$isppsandbox = $paypalcfg['paypalsbox'];

$ispayg = 0;
$paygatearr = array('coinpayments', 'perfectmoney', 'paypal', 'stripe', 'payfast', 'paystack', 'ewallet', 'manualpay', 'testpay');
foreach ($paygatearr as $key => $value) {
    if ($payrow[$value . 'on'] == 1) {
        if ($payrow[$value . 'fee'] > 0) {
            ${'fee' . $value} = getamount($payrow[$value . 'fee'], $regfee);
            ${'tot' . $value} = $regfee + ${'fee' . $value};
        } else {
            ${'fee' . $value} = 0;
        }
        $ispayg++;
    }
    if ($pgdatatokenarr[$value . 'on'] == 1) {
        $valdatatoken = get_optarr($pgdatatokenarr[$value . 'cfg']);
        if ($valdatatoken[$value . 'fee'] > 0) {
            ${'fee' . $value} = getamount($valdatatoken[$value . 'fee'], $regfee);
            ${'tot' . $value} = $regfee + ${'fee' . $value};
        } else {
            ${'fee' . $value} = 0;
        }
        $ispayg++;
    }
}

if ($ispayg <= 1) {
    $colmdclass = "col-md-12";
} elseif ($ispayg <= 2) {
    $colmdclass = "col-md-6";
} else {
    $colmdclass = "col-md-4";
}

$tagsarr = array("[[currencysym]]" => $bpprow['currencysym'], "[[currencycode]]" => $bpprow['currencycode'], "[[feeamount]]" => $feemanualpay, "[[amount]]" => $regfee, "[[totamount]]" => $totmanualpay, "[[payplan]]" => $bpprow['ppname']);
$manualpayipn = base64_decode($payrow['manualpayipn']);
$manualpayipn = strtr($manualpayipn, $tagsarr);
$manualpayipn64 = base64_encode($manualpayipn . '<hr /><button type="button" class="btn btn-warning btn-lg mt-2" onclick="location.href = \'index.php?hal=feedback&isconfirm=' . base64_encode($txmpid) . '&pby=manualpayipn\'">Confirm Payment</button>');
?>

<div class="section-header">
    <h1><i class="fa fa-fw fa-money-check"></i> <?php echo myvalidate($LANG['m_planpay']); ?></h1>
</div>

<div class="section-body">
    <div class="row">
        <div class="col-md-12">
            <article class="article article-style-b">
                <div class="article-header">
                    <div class="article-image" data-background="<?php echo myvalidate($planimg); ?>">
                    </div>
                    <div class="article-badge">
                        <span class="article-badge-item bg-danger">
                            <?php echo myvalidate($bpprow['currencysym'] . $regfee . ' ' . $bpprow['currencycode']); ?>
                        </span>
                        <?php
                        if ($sprstr['mpstatus'] == 1) {
                            ?>
                            <span class="article-badge-item bg-warning">
                                Sponsored by <?php echo myvalidate($sprstr['username']); ?>
                            </span>
                            <?php
                        }
                        ?>
                    </div>
                </div>
                <div class="article-details">
                    <div class="article-title">
                        <h4><?php echo myvalidate($bpprow['ppname']); ?></h4>
                    </div>
                    <p><?php echo myvalidate($bpprow['planinfo']); ?></p>
                    <div class="article-cta">
                        <span class="badge badge-secondary">
                            <?php echo myvalidate($payforstr); ?>
                        </span>
                        <span class="badge badge-danger">
                            <?php echo myvalidate($LANG['g_unpaid']); ?>
                        </span>
                    </div>
                </div>
            </article>

        </div>
    </div>

    <h2 class="section-title"><?php echo myvalidate($LANG['m_payoption']); ?></h2>
    <p class="section-lead"><?php echo myvalidate($LANG['m_payinfo']); ?></p>

    <div class="row">
        <?php
        if ($pgdatatokenarr['coinpaymentson'] == 1) {
            ?>
            <div class="<?php echo myvalidate($colmdclass); ?>">
                <div class="card card-primary">
                    <div class="card-body text-center">
                        <?php echo myvalidate($avalpaygateicon_array['coinpaymentsmercid']); ?>
                        <h4>Coinpayments</h4>
                        <div class="mt-4"><?php echo myvalidate($LANG['g_amount']); ?>: <?php echo myvalidate($bpprow['currencysym'] . $regfee); ?></div>
                        <div><code><?php echo myvalidate($LANG['g_servicefee']); ?>: <?php echo myvalidate($bpprow['currencysym'] . $feecoinpayments); ?></code></div>
                        <h6>Total: <?php echo myvalidate($bpprow['currencysym'] . $totcoinpayments . ' ' . $bpprow['currencycode']); ?></h6>
                        <form method="post" action="https://www.coinpayments.net/index.php" id="dopayform">
                            <input type="hidden" name="cmd" value="_pay_simple"> <!-- or _pay -->
                            <input type="hidden" name="reset" value="1">
                            <input type="hidden" name="merchant" value="<?php echo myvalidate($coinpaymentscfg['coinpaymentsmercid']); ?>">
                            <input type="hidden" name="item_name" value="<?php echo myvalidate($bpprow['ppname']); ?>">
                            <input type="hidden" name="item_number" value="<?php echo myvalidate($mbrstr['username']); ?>">
                            <input type="hidden" name="invoice" value="<?php echo myvalidate($txmpid); ?>">
                            <input type="hidden" name="currency" value="<?php echo myvalidate($bpprow['currencycode']); ?>">
                            <input type="hidden" name="amountf" value="<?php echo myvalidate($totcoinpayments); ?>">
                            <input type="hidden" name="quantity" value="1">
                            <input type="hidden" name="allow_quantity" value="1">
                            <input type="hidden" name="want_shipping" value="0">
                            <input type="hidden" name="success_url" value="<?php echo myvalidate($cfgrow['site_url']) . '/' . MBRFOLDER_NAME . '/ipnhub.php?hal=dashboard'; ?>">
                            <input type="hidden" name="cancel_url" value="<?php echo myvalidate($cfgrow['site_url']) . '/' . MBRFOLDER_NAME . '/index.php?hal=planpay&act=cancelpay'; ?>">
                            <input type="hidden" name="ipn_url" value="<?php echo myvalidate($cfgrow['site_url']) . '/common/sandbox.php'; ?>">
                            <input type="hidden" name="allow_extra" value="1">

                            <button type="submit" name="dopay" value="1" id="dopay" class="btn btn-primary btn-lg mt-4">
                                <?php echo myvalidate($LANG['m_makepayment']); ?>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <?php
        }
        if ($pgdatatokenarr['perfectmoneyon'] == 1) {
            ?>
            <div class="<?php echo myvalidate($colmdclass); ?>">
                <div class="card card-primary">
                    <div class="card-body text-center">
                        <?php echo myvalidate($avalpaygateicon_array['perfectmoneyacc']); ?>
                        <h4>Perfectmoney</h4>
                        <div class="mt-4"><?php echo myvalidate($LANG['g_amount']); ?>: <?php echo myvalidate($bpprow['currencysym'] . $regfee); ?></div>
                        <div><code><?php echo myvalidate($LANG['g_servicefee']); ?>: <?php echo myvalidate($bpprow['currencysym'] . $feeperfectmoney); ?></code></div>
                        <h6>Total: <?php echo myvalidate($bpprow['currencysym'] . $totperfectmoney . ' ' . $bpprow['currencycode']); ?></h6>

                        <form method="post" action="https://perfectmoney.is/api/step1.asp" id="dopayform">
                            <p>
                                <input type="hidden" name="PAYEE_ACCOUNT" value="<?php echo myvalidate($perfectmoneycfg['perfectmoneyacc']); ?>">
                                <input type="hidden" name="PAYEE_NAME" value="<?php echo myvalidate($perfectmoneycfg['perfectmoneyname']); ?>">
                                <input type="hidden" name="PAYMENT_AMOUNT" value="<?php echo myvalidate($totperfectmoney); ?>">
                                <input type="hidden" name="PAYMENT_UNITS" value="<?php echo myvalidate($bpprow['currencycode']); ?>">
                                <input type="hidden" name="STATUS_URL"
                                       value="<?php echo myvalidate($cfgrow['site_url']) . '/common/perfectmoney.ipv.php'; ?>">
                                <input type="hidden" name="PAYMENT_URL"
                                       value="<?php echo myvalidate($cfgrow['site_url']) . '/' . MBRFOLDER_NAME . '/ipnhub.php?hal=dashboard'; ?>">
                                <input type="hidden" name="NOPAYMENT_URL"
                                       value="<?php echo myvalidate($cfgrow['site_url']) . '/' . MBRFOLDER_NAME . '/index.php?hal=planpay&act=cancelpay'; ?>">
                                <input type="hidden" name="PAYMENT_ID" value="<?php echo myvalidate($txmpid); ?>">
                                <!--input type="hidden" name="BAGGAGE_FIELDS" value="PAYMENT_ID"-->
                                <button type="submit" name="dopay" value="1" id="dopay" class="btn btn-primary btn-lg mt-4">
                                    <?php echo myvalidate($LANG['m_makepayment']); ?>
                                </button>
                            </p>
                        </form>

                    </div>
                </div>
            </div>
            <?php
        }
        if ($pgdatatokenarr['payfaston'] == 1) {

            $posturl = ($payfastcfg['payfastsbox'] == 1) ? "https://sandbox.payfast.co.za?/eng/process" : "https://www.payfast.co.za/eng/process";
            ?>
            <div class="<?php echo myvalidate($colmdclass); ?>">
                <div class="card card-primary">
                    <div class="card-body text-center">
                        <?php echo myvalidate($avalpaygateicon_array['payfastmercid']); ?>
                        <h4>Payfast</h4>
                        <div class="mt-4"><?php echo myvalidate($LANG['g_amount']); ?>: <?php echo myvalidate($bpprow['currencysym'] . $regfee); ?></div>
                        <div><code><?php echo myvalidate($LANG['g_servicefee']); ?>: <?php echo myvalidate($bpprow['currencysym'] . $feepayfast); ?></code></div>
                        <h6>Total: <?php echo myvalidate($bpprow['currencysym'] . $totpayfast . ' ' . $bpprow['currencycode']); ?></h6>
                        <form method="post" action="<?php echo myvalidate($posturl); ?>" id="dopayform">
                            <input type="hidden" name="merchant_id" value="<?php echo myvalidate($payfastcfg['payfastmercid']); ?>">
                            <input type="hidden" name="merchant_key" value="<?php echo myvalidate($payfastcfg['payfastkey']); ?>">
                            <input type="hidden" name="amount" value="<?php echo myvalidate($totpayfast); ?>">
                            <input type="hidden" name="item_name" value="<?php echo myvalidate($bpprow['ppname']); ?>">
                            <input type="hidden" name="item_description" value="<?php echo myvalidate($mbrstr['username']); ?>">
                            <input type="hidden" name="return_url" value="<?php echo myvalidate($cfgrow['site_url']) . '/' . MBRFOLDER_NAME . '/ipnhub.php?hal=dashboard'; ?>">
                            <input type="hidden" name="cancel_url" value="<?php echo myvalidate($cfgrow['site_url']) . '/' . MBRFOLDER_NAME . '/index.php?hal=planpay&act=cancelpay'; ?>">
                            <input type="hidden" name="notify_url" value="<?php echo myvalidate($cfgrow['site_url']) . '/common/payfast.ipv.php'; ?>">
                            <input type="hidden" name="name_first" value="<?php echo myvalidate($mbrstr['firstname']); ?>">
                            <input type="hidden" name="name_last" value="<?php echo myvalidate($mbrstr['lastname']); ?>">
                            <input type="hidden" name="email_address" value="<?php echo myvalidate($mbrstr['email']); ?>">
                            <input type="hidden" name="m_payment_id" value="<?php echo myvalidate($txmpid); ?>">
                            <button type="submit" name="dopay" value="1" id="dopay" class="btn btn-primary btn-lg mt-4">
                                <?php echo myvalidate($LANG['m_makepayment']); ?>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <?php
        }
        if ($pgdatatokenarr['paypalon'] == 1) {

            $posturl = ($isppsandbox == 1) ? "https://ipnpb.sandbox.paypal.com/cgi-bin/webscr" : "https://ipnpb.paypal.com/cgi-bin/webscr";
            ?>
            <div class="<?php echo myvalidate($colmdclass); ?>">
                <div class="card card-primary">
                    <div class="card-body text-center">
                        <?php echo myvalidate($avalpaygateicon_array['paypalacc']); ?>
                        <h4>Paypal</h4>
                        <div class="mt-4"><?php echo myvalidate($LANG['g_amount']); ?>: <?php echo myvalidate($bpprow['currencysym'] . $regfee); ?></div>
                        <div><code><?php echo myvalidate($LANG['g_servicefee']); ?>: <?php echo myvalidate($bpprow['currencysym'] . $feepaypal); ?></code></div>
                        <h6>Total: <?php echo myvalidate($bpprow['currencysym'] . $totpaypal . ' ' . $bpprow['currencycode']); ?></h6>
                        <form method="post" action="<?php echo myvalidate($posturl); ?>" id="dopayform">
                            <input type="hidden" name="cmd" value="_xclick">
                            <input type="hidden" name="amount" value="<?php echo myvalidate($totpaypal); ?>">
                            <input type="hidden" name="business" value="<?php echo myvalidate($paypalcfg['paypalacc']); ?>">
                            <input type="hidden" name="notify_url" value="<?php echo myvalidate($cfgrow['site_url']) . '/common/sandbox.php'; ?>">
                            <input type="hidden" name="return" value="<?php echo myvalidate($cfgrow['site_url']) . '/' . MBRFOLDER_NAME . '/ipnhub.php?hal=dashboard'; ?>">
                            <input type="hidden" name="cancel_return" value="<?php echo myvalidate($cfgrow['site_url']) . '/' . MBRFOLDER_NAME . '/index.php?hal=planpay&act=cancelpay'; ?>">
                            <input type="hidden" name="currency_code" value="<?php echo myvalidate($bpprow['currencycode']); ?>">
                            <input type="hidden" name="item_name" value="<?php echo myvalidate($bpprow['ppname']); ?>">
                            <input type="hidden" name="quantity" value="1">
                            <input type="hidden" name="rm" value="2">
                            <input type="hidden" name="no_shipping" value="1">
                            <input type="hidden" name="no_note" value="1">
                            <input type="hidden" name="custom" value="<?php echo myvalidate($txmpid); ?>">

                            <button type="submit" name="dopay" value="1" id="dopay" class="btn btn-primary btn-lg mt-4">
                                <?php echo myvalidate($LANG['m_makepayment']); ?>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <?php
        }
        if ($pgdatatokenarr['paystackon'] == 1) {
            $paystackpin64 = base64_encode($paystackcfg['paystackpin']);
            $paystackhash = md5($paystackpin64 . $txmpid . $paynow_fee);
            ?>
            <div class="<?php echo myvalidate($colmdclass); ?>">
                <div class="card card-primary">
                    <div class="card-body text-center">
                        <?php echo myvalidate($avalpaygateicon_array['paystackpub']); ?>
                        <h4>Paystack</h4>
                        <div class="mt-4"><?php echo myvalidate($LANG['g_amount']); ?>: <?php echo myvalidate($bpprow['currencysym'] . $regfee); ?></div>
                        <div><code><?php echo myvalidate($LANG['g_servicefee']); ?>: <?php echo myvalidate($bpprow['currencysym'] . $feepaystack); ?></code></div>
                        <h6>Total: <?php echo myvalidate($bpprow['currencysym'] . $totpaystack . ' ' . $bpprow['currencycode']); ?></h6>
                        <button type="button" class="btn btn-primary btn-lg mt-4" onclick="payWithPaystack()">
                            <?php echo myvalidate($LANG['m_makepayment']); ?>
                        </button>
                    </div>
                </div>
            </div>
            <script src="https://js.paystack.co/v1/inline.js"></script>
            <script>
                            function payWithPaystack() {

                                var handler = PaystackPop.setup({
                                    key: '<?php echo myvalidate($paystackcfg['paystackpub']); ?>', //put your public key here
                                    email: '<?php echo myvalidate($mbrstr['email']); ?>', //put your customer's email here
                                    amount: <?php echo myvalidate(100 * $totpaystack); ?>, //amount the customer is supposed to pay
                                    currency: '<?php echo myvalidate($bpprow['currencycode']); ?>', //Use GHS for Ghana Cedis or USD for US Dollars
                                    metadata: {
                                        custom_fields: [
                                            {
                                                display_name: "Mobile Number",
                                                variable_name: "mobile_number",
                                                value: "<?php echo myvalidate($mbrpaystackcfg['paystackpub']); ?>" //customer's mobile number
                                            }
                                        ]
                                    },
                                    callback: function (response) {
                                        //after the transaction have been completed
                                        //make post call  to the server with to verify payment
                                        //using transaction reference as post data
                                        $.post("../common/paystack.ipv.php", {reference: response.reference, refpin: '<?php echo myvalidate($paystackpin64); ?>', txmpid: '<?php echo myvalidate($txmpid); ?>', reg_fee: '<?php echo myvalidate($paynow_fee); ?>', reg_hash: '<?php echo myvalidate($paystackhash); ?>'}, function (status) {
                                            if (status == "success") {
                                                //successful transaction
                                                alert('Transaction was successful');
                                                window.location.href = 'index.php?hal=dashboard';
                                            } else {
                                                //transaction failed
                                                alert(response);
                                            }
                                        });
                                    },
                                    onClose: function () {
                                        //when the user close the payment modal
                                        alert('Transaction interrupted and cancelled!');
                                    }
                                });
                                handler.openIframe(); //open the paystack's payment modal
                            }
            </script>
            <?php
        }
        if ($pgdatatokenarr['stripeon'] == 1) {
            $totstripe00 = $totstripe * 100;
            $_SESSION['stripetot00'] = $totstripe00;
            $_SESSION['stripembrem'] = $mbrstr['email'];
            $_SESSION['stripepname'] = $stripecfg['stripename'];
            $_SESSION['stripetxmpid'] = $txmpid;
            ?>
            <div class="<?php echo myvalidate($colmdclass); ?>">
                <div class="card card-primary">
                    <div class="card-body text-center">
                        <?php echo myvalidate($avalpaygateicon_array['stripeacc']); ?>
                        <h4>Stripe</h4>
                        <div class="mt-4"><?php echo myvalidate($LANG['g_amount']); ?>: <?php echo myvalidate($bpprow['currencysym'] . $regfee); ?></div>
                        <div><code><?php echo myvalidate($LANG['g_servicefee']); ?>: <?php echo myvalidate($bpprow['currencysym'] . $feestripe); ?></code></div>
                        <h6>Total: <?php echo myvalidate($bpprow['currencysym'] . $totstripe . ' ' . $bpprow['currencycode']); ?></h6>

                        <?php
                        if ($stripecfg['stripeoptco'] == 1) {
                            ?>


                            <script src="https://js.stripe.com/v3/"></script>
                            <button type="submit" id="stripe-checkout-button" class="btn btn-primary btn-lg mt-4">
                                <?php echo myvalidate($LANG['m_makepayment']); ?>
                            </button>
                            <script type="text/javascript">
                                // Create an instance of the Stripe object with your publishable API key
                                var stripe = Stripe('<?php echo myvalidate($stripecfg['stripeacc']); ?>');
                                var checkoutButton = document.getElementById('stripe-checkout-button');

                                checkoutButton.addEventListener('click', function () {
                                    // Create a new Checkout Session using the server-side endpoint you
                                    // created in step 3.
                                    fetch('../common/stripe.ses.php', {
                                        method: 'POST',
                                    })
                                            .then(function (response) {
                                                return response.json();
                                            })
                                            .then(function (session) {
                                                return stripe.redirectToCheckout({sessionId: session.id});
                                            })
                                            .then(function (result) {
                                                // If `redirectToCheckout` fails due to a browser or network
                                                // error, you should display the localized error message to your
                                                // customer using `error.message`.
                                                if (result.error) {
                                                    alert(result.error.message);
                                                }
                                            })
                                            .catch(function (error) {
                                                console.error('Error:', error);
                                            });
                                });
                            </script>

                            <?php
                        }
                        if ($stripecfg['stripeoptco'] == 2) {
                            ?>

                            <form method="post" action="../common/stripe.ipv.php" id="dopayform">

                                <noscript>You must <a href="http://www.enable-javascript.com" target="_blank">enable JavaScript</a> in your web browser in order to process the payment.</noscript>

                                <input
                                    id="btnstripe"
                                    type="submit"
                                    class="btn btn-primary btn-lg mt-4" value="<?php echo myvalidate($LANG['m_makepayment']); ?>"
                                    data-key="<?php echo myvalidate($stripecfg['stripeacc']); ?>"
                                    data-email="<?php echo myvalidate($mbrstr['email']); ?>"
                                    data-name="<?php echo myvalidate($stripecfg['stripename']); ?>"
                                    data-amount="<?php echo myvalidate($totstripe00); ?>"
                                    data-currency="<?php echo myvalidate($bpprow['currencycode']); ?>"
                                    data-description="<?php echo myvalidate($bpprow['ppname']); ?>"
                                    data-bitcoin="false"
                                    data-locale="auto"
                                    data-image="<?php echo myvalidate($site_logo); ?>"
                                    />

                                <script src="https://checkout.stripe.com/checkout.js"></script>
                                <script>
                                $(document).ready(function () {
                                    $('#btnstripe').on('click', function (event) {
                                        event.preventDefault();

                                        var $button = $(this),
                                                $form = $button.parents('form');
                                        var opts = $.extend({}, $button.data(), {
                                            token: function (result) {
                                                $form.append($('<input>').attr({type: 'hidden', name: 'stripeToken', value: result.id})).submit();
                                            }
                                        });

                                        StripeCheckout.open(opts);
                                    });
                                });
                                </script>

                                <input type="hidden" name="description" value="<?php echo myvalidate($stripecfg['stripename']); ?>">
                                <input type="hidden" name="amount" value="<?php echo myvalidate($totstripe00); ?>">
                                <input type="hidden" name="currency" value="<?php echo myvalidate($bpprow['currencycode']); ?>">
                                <input type="hidden" name="stripe_txmpid" value="<?php echo myvalidate($txmpid); ?>">

                            </form>
                            <?php
                        }
                        ?>

                    </div>
                </div>
            </div>
            <?php
        }
        if ($payrow['manualpayon'] == 1) {
            ?>
            <div class="<?php echo myvalidate($colmdclass); ?>">
                <div class="card card-primary">
                    <div class="card-body text-center">
                        <?php echo myvalidate($avalpaygateicon_array['manualpayipn']); ?>
                        <h4><?php echo myvalidate($payrow['manualpayname']); ?></h4>
                        <div class="mt-4"><?php echo myvalidate($LANG['g_amount']); ?>: <?php echo myvalidate($bpprow['currencysym'] . $regfee); ?></div>
                        <div><code><?php echo myvalidate($LANG['g_servicefee']); ?>: <?php echo myvalidate($bpprow['currencysym'] . $feemanualpay); ?></code></div>
                        <h6>Total: <?php echo myvalidate($bpprow['currencysym'] . $totmanualpay . ' ' . $bpprow['currencycode']); ?></h6>
                        <button type="button" class="openPopup btn btn-primary btn-lg mt-4" data-encbase64="<?php echo myvalidate($manualpayipn64); ?>" data-poptitle="<?php echo myvalidate($avalpaygateicon_array['manualpayipn'] . ' ' . $payrow['manualpayname']); ?>">
                            <?php echo myvalidate($LANG['m_makepayment']); ?>
                        </button>
                    </div>
                </div>
            </div>
            <?php
        }
        if ($payrow['testpayon'] == 1) {
            ?>
            <div class="<?php echo myvalidate($colmdclass); ?>">
                <div class="card card-danger">
                    <div class="card-body text-center">
                        <i class="fa fa-cog fa-fw"></i>
                        <h4><?php echo myvalidate($payrow['testpaylabel']); ?></h4>
                        <div class="mt-4"><?php echo myvalidate($LANG['g_amount']); ?>: <?php echo myvalidate($bpprow['currencysym'] . $regfee); ?></div>
                        <div><code><?php echo myvalidate($LANG['g_servicefee']); ?>: <?php echo myvalidate($bpprow['currencysym'] . $feetestpay); ?></code></div>
                        <h6>Total: <?php echo myvalidate($bpprow['currencysym'] . $tottestpay . ' ' . $bpprow['currencycode']); ?></h6>
                        <div class="mt-4"><?php echo myvalidate($LANG['m_testpayinfo']); ?></div>
                        <form method="post" action="../common/sandbox.php" id="dopayform">
                            <input type="hidden" name="sb_type" value="payreg">
                            <input type="hidden" name="sb_txmpid" value="<?php echo myvalidate($txmpid); ?>">
                            <input type="hidden" name="sb_amount" value="<?php echo myvalidate($tottestpay); ?>">
                            <input type="hidden" name="sb_batch" value="<?php echo myvalidate($paybatch); ?>">
                            <input type="hidden" name="sb_label" value="<?php echo myvalidate($payrow['testpaylabel']); ?>">
                            <input type="hidden" name="sb_success" value="<?php echo myvalidate($cfgrow['site_url']) . '/' . MBRFOLDER_NAME . '/ipnhub.php?hal=dashboard'; ?>">
                            <button type="submit" name="dopay" value="1" id="dopay" class="btn btn-danger btn-lg mt-4">
                                <?php echo myvalidate($LANG['m_makepayment']); ?>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <?php
        }
        ?>
    </div>
</div>
