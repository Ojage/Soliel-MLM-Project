<?php
if (!defined('OK_LOADME')) {
    die('o o p s !');
}

$pgdatatoken = $payrow['pgdatatoken'];
$pgdatatokenarr = get_optionvals($pgdatatoken);
$paytoken = $payrow['paytoken'];

// ---

$manualpayonarr = array(0, 1);
$manualpayon_cek = radiobox_opt($manualpayonarr, $payrow['manualpayon']);
$manualpay4usr_cek = checkbox_opt($payrow['manualpay4usr']);

$perfectmoneyonarr = array(0, 1);
$perfectmoneyon_cek = radiobox_opt($perfectmoneyonarr, $pgdatatokenarr['perfectmoneyon']);
$perfectmoney4usr_cek = checkbox_opt($pgdatatokenarr['perfectmoney4usr']);
$perfectmoneycfg = get_optarr($pgdatatokenarr['perfectmoneycfg']);

$payfastonarr = array(0, 1);
$payfaston_cek = radiobox_opt($payfastonarr, $pgdatatokenarr['payfaston']);
$payfast4usr_cek = checkbox_opt($pgdatatokenarr['payfast4usr']);
$payfastcfg = get_optarr($pgdatatokenarr['payfastcfg']);

$ispfsandbox = $payfastcfg['payfastsbox'];
$payfastsboxarr = array(0, 1);
$payfastsbox_cek = radiobox_opt($payfastsboxarr, $ispfsandbox);

$paystackonarr = array(0, 1);
$paystackon_cek = radiobox_opt($paystackonarr, $pgdatatokenarr['paystackon']);
$paystack4usr_cek = checkbox_opt($pgdatatokenarr['paystack4usr']);
$paystackcfg = get_optarr($pgdatatokenarr['paystackcfg']);

$coinpaymentsonarr = array(0, 1);
$coinpaymentson_cek = radiobox_opt($coinpaymentsonarr, $pgdatatokenarr['coinpaymentson']);
$coinpayments4usr_cek = checkbox_opt($pgdatatokenarr['coinpayments4usr']);
$coinpaymentscfg = get_optarr($pgdatatokenarr['coinpaymentscfg']);

$paypalonarr = array(0, 1);
$paypalon_cek = radiobox_opt($paypalonarr, $pgdatatokenarr['paypalon']);
$paypal4usr_cek = checkbox_opt($pgdatatokenarr['paypal4usr']);
$paypalcfg = get_optarr($pgdatatokenarr['paypalcfg']);

$isppsandbox = $paypalcfg['paypalsbox'];
$paypalsboxarr = array(0, 1);
$paypalsbox_cek = radiobox_opt($paypalsboxarr, $isppsandbox);

$stripeonarr = array(0, 1);
$stripeon_cek = radiobox_opt($stripeonarr, $pgdatatokenarr['stripeon']);
$stripecfg = get_optarr($pgdatatokenarr['stripecfg']);

$stripeoptcoarr = array(1, 2);
$stripeoptco_cek = radiobox_opt($stripeoptcoarr, $stripecfg['stripeoptco']);

$ewalletonarr = array(0, 1);
$ewalleton_cek = radiobox_opt($ewalletonarr, $pgdatatokenarr['ewalleton']);
$ewalletcfg = get_optarr($pgdatatokenarr['ewalletcfg']);

$testpayonarr = array(0, 1);
$testpayon_cek = radiobox_opt($testpayonarr, $payrow['testpayon']);
$testpay4usr_cek = checkbox_opt($payrow['testpay4usr']);

if (isset($FORM['dosubmit']) and $FORM['dosubmit'] == '1') {

    extract($FORM);

    $perfectmoneyarr = array('perfectmoneyacc' => $perfectmoneyacc, 'perfectmoneyname' => $perfectmoneyname, 'perfectmoneypass' => $perfectmoneypass, 'perfectmoneyfee' => $perfectmoneyfee);
    $perfectmoneycfg = put_optarr($pgdatatokenarr['perfectmoneycfg'], $perfectmoneyarr);
    $pgdatatoken = put_optionvals($pgdatatoken, 'perfectmoneyon', intval($perfectmoneyon));
    $pgdatatoken = put_optionvals($pgdatatoken, 'perfectmoney4usr', intval($perfectmoney4usr));
    $pgdatatoken = put_optionvals($pgdatatoken, 'perfectmoneycfg', $perfectmoneycfg);

    $payfastarr = array('payfastmercid' => $payfastmercid, 'payfastkey' => $payfastkey, 'payfastfee' => $payfastfee, 'payfastsbox' => $payfastsbox);
    $payfastcfg = put_optarr($pgdatatokenarr['payfastcfg'], $payfastarr);
    $pgdatatoken = put_optionvals($pgdatatoken, 'payfaston', intval($payfaston));
    $pgdatatoken = put_optionvals($pgdatatoken, 'payfast4usr', intval($payfast4usr));
    $pgdatatoken = put_optionvals($pgdatatoken, 'payfastcfg', $payfastcfg);

    $paystackarr = array('paystackpub' => $paystackpub, 'paystackpin' => $paystackpin, 'paystackfee' => $paystackfee);
    $paystackcfg = put_optarr($pgdatatokenarr['paystackcfg'], $paystackarr);
    $pgdatatoken = put_optionvals($pgdatatoken, 'paystackon', intval($paystackon));
    $pgdatatoken = put_optionvals($pgdatatoken, 'paystack4usr', intval($paystack4usr));
    $pgdatatoken = put_optionvals($pgdatatoken, 'paystackcfg', $paystackcfg);

    $paypalarr = array('paypalacc' => $paypalacc, 'paypalsbox' => $paypalsbox, 'paypalfee' => $paypalfee);
    $paypalcfg = put_optarr($pgdatatokenarr['paypalcfg'], $paypalarr);
    $pgdatatoken = put_optionvals($pgdatatoken, 'paypalon', intval($paypalon));
    $pgdatatoken = put_optionvals($pgdatatoken, 'paypal4usr', intval($paypal4usr));
    $pgdatatoken = put_optionvals($pgdatatoken, 'paypalcfg', $paypalcfg);

    $coinpaymentsarr = array('coinpaymentsmercid' => $coinpaymentsmercid, 'coinpaymentsipnkey' => $coinpaymentsipnkey, 'coinpaymentsfee' => $coinpaymentsfee);
    $coinpaymentscfg = put_optarr($pgdatatokenarr['coinpaymentscfg'], $coinpaymentsarr);
    $pgdatatoken = put_optionvals($pgdatatoken, 'coinpaymentson', intval($coinpaymentson));
    $pgdatatoken = put_optionvals($pgdatatoken, 'coinpayments4usr', intval($coinpayments4usr));
    $pgdatatoken = put_optionvals($pgdatatoken, 'coinpaymentscfg', $coinpaymentscfg);

    $stripearr = array('stripename' => $stripename, 'stripepass' => $stripepass, 'stripeacc' => $stripeacc, 'stripefee' => $stripefee, 'stripeoptco' => $stripeoptco);
    $stripecfg = put_optarr($pgdatatokenarr['stripecfg'], $stripearr);
    $pgdatatoken = put_optionvals($pgdatatoken, 'stripeon', intval($stripeon));
    $pgdatatoken = put_optionvals($pgdatatoken, 'stripe4usr', 0);
    $pgdatatoken = put_optionvals($pgdatatoken, 'stripecfg', $stripecfg);

    $ewalletarr = array('ewalletlabel' => $ewalletlabel, 'ewalletfee' => $ewalletfee);
    $ewalletcfg = put_optarr($pgdatatokenarr['ewalletcfg'], $ewalletarr);
    $pgdatatoken = put_optionvals($pgdatatoken, 'ewalleton', intval($ewalleton));
    $pgdatatoken = put_optionvals($pgdatatoken, 'ewalletcfg', $ewalletcfg);

    $data = array(
        'pgdatatoken' => $pgdatatoken,
        'manualpayon' => intval($manualpayon),
        'manualpaybtn' => $manualpaybtn,
        'manualpayfee' => $manualpayfee,
        'manualpayname' => mystriptag($manualpayname),
        'manualpayipn' => base64_encode($manualpayipn),
        'manualpay4usr' => intval($manualpay4usr),
        'testpayon' => intval($testpayon),
        'testpayfee' => $testpayfee,
        'testpaylabel' => $testpaylabel,
        'testpay4usr' => intval($testpay4usr),
        'paytoken' => $paytoken,
    );

    $condition = " AND paygid = '{$didId}' ";
    $sql = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_paygates WHERE 1 " . $condition . "");
    if (count($sql) > 0) {
        if (!defined('ISDEMOMODE')) {
            $update = $db->update(DB_TBLPREFIX . '_paygates', $data, array('paygid' => $didId));
            if ($update) {
                $_SESSION['dotoaster'] = "toastr.success('Payment options updated successfully!', 'Success');";
            } else {
                $_SESSION['dotoaster'] = "toastr.warning({$LANG['g_nomajorchanges']}, 'Info');";
            }
        } else {
            $_SESSION['dotoaster'] = "toastr.warning({$LANG['g_nomajorchanges']}, 'Demo Mode');";
        }
    } else {
        $insert = $db->insert(DB_TBLPREFIX . '_paygates', $data);
        if ($insert) {
            $_SESSION['dotoaster'] = "toastr.success('Payment options added successfully!', 'Success');";
        } else {
            $_SESSION['dotoaster'] = "toastr.error('Payment options not added <strong>Please try again!</strong>', 'Warning');";
        }
    }
    //header('location: index.php?hal=' . $hal);
    redirpageto('index.php?hal=' . $hal);
    exit;
}

$ispfsandboxstr = ($ispfsandbox == 1) ? "<span class='badge badge-transparent float-right text-small text-warning'><i class='fa fa-fw fa-exclamation'></i></span>" : '';
$isppsandboxstr = ($isppsandbox == 1) ? "<span class='badge badge-transparent float-right text-small text-warning'><i class='fa fa-fw fa-exclamation'></i></span>" : '';
$iconstatuspaystr = ($pgdatatokenarr['payfaston'] == 1 || $pgdatatokenarr['stripeon'] == 1 || $pgdatatokenarr['perfectmoneyon'] == 1 || $pgdatatokenarr['paystackon'] == 1 || $pgdatatokenarr['ewalleton'] == 1 || $pgdatatokenarr['paypalon'] == 1 || $pgdatatokenarr['coinpaymentson'] == 1 || $payrow['manualpayon'] == 1) ? "<i class='fa fa-check text-success' data-toggle='tooltip' title='Payment Option is Available'></i>" : "<i class='fa fa-times text-danger' data-toggle='tooltip' title='Payment Option is Unavailable'></i>";
?>

<div class="section-header">
    <h1><i class="fa fa-fw fa-money-bill-wave"></i> <?php echo myvalidate($LANG['a_payment']); ?></h1>
</div>

<div class="section-body">
    <div class="row">
        <div class="col-md-4">	
            <div class="card">
                <div class="card-header">
                    <h4>Gateway<?php echo myvalidate($reglicmarker); ?></h4>
                    <div class="card-header-action">
                        <?php echo myvalidate($iconstatuspaystr); ?>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="nav nav-pills flex-column" id="myTab4" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="config-cash" data-toggle="tab" href="#paycash" role="tab" aria-controls="cash" aria-selected="true">Cash and Bank</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="config-coinpayments" data-toggle="tab" href="#paycoinpayments" role="tab" aria-controls="coinpayments" aria-selected="false">Coinpayments</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="config-perfectmoney" data-toggle="tab" href="#payperfectmoney" role="tab" aria-controls="perfectmoney" aria-selected="false">Perfectmoney</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="config-payfast" data-toggle="tab" href="#paypayfast" role="tab" aria-controls="payfast" aria-selected="false">Payfast<?php echo myvalidate($ispfsandboxstr); ?></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="config-paypal" data-toggle="tab" href="#paypaypal" role="tab" aria-controls="paypal" aria-selected="false">Paypal<?php echo myvalidate($isppsandboxstr); ?></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="config-paystack" data-toggle="tab" href="#paypaystack" role="tab" aria-controls="paystack" aria-selected="false">Paystack</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="config-stripe" data-toggle="tab" href="#paystripe" role="tab" aria-controls="stripe" aria-selected="false">Stripe</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="config-test" data-toggle="tab" href="#paytest" role="tab" aria-controls="test" aria-selected="false">System Test</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-8">	
            <div class="card">

                <form method="post" action="index.php" enctype="multipart/form-data" id="payform">
                    <input type="hidden" name="hal" value="paymentopt">

                    <div class="card-header">
                        <h4>Options</h4>
                    </div>

                    <div class="card-body">
                        <div class="tab-content no-padding" id="myTab2Content">

                            <div class="tab-pane fade show active" id="paycash" role="tabpanel" aria-labelledby="config-cash">
                                <p class="text-muted">Cash, bank transfer, and other offline or manual payment methods. Use the following tags to display dynamic contents:</p>
                                <ul>
                                    <li><strong>[[currencysym]]</strong> = Currency symbol (<?php echo myvalidate($bpprow['currencysym']); ?>).</li>
                                    <li><strong>[[currencycode]]</strong> = Currency code (<?php echo myvalidate($bpprow['currencycode']); ?>).</li>
                                    <li><strong>[[feeamount]]</strong> = Payment processing fee.</li>
                                    <li><strong>[[amount]]</strong> = Registration amount.</li>
                                    <li><strong>[[totamount]]</strong> = Total amount need to pay.</li>
                                    <li><strong>[[payplan]]</strong> = Membership name.</li>
                                </ul>

                                <div class="form-group">
                                    <label for="manualpayname">Payment Name</label>
                                    <input type="text" name="manualpayname" id="manualpayname" class="form-control" value="<?php echo isset($payrow['manualpayname']) ? $payrow['manualpayname'] : 'Cash or Bank Transfer'; ?>" placeholder="Cash or Bank Transfer">
                                </div>
                                <div class="form-group">
                                    <label for="manualpayipn">Payment Instructions</label>
                                    <textarea class="form-control rowsize-md" name="manualpayipn" id="summernotemini" placeholder="Enter the payment instructions here."><?php echo isset($payrow['manualpayipn']) ? base64_decode($payrow['manualpayipn']) : ''; ?></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="manualpayfee">Processing Fee</label>
                                    <input type="text" name="manualpayfee" id="manualpayfee" class="form-control" value="<?php echo isset($payrow['manualpayfee']) ? $payrow['manualpayfee'] : '0'; ?>" placeholder="Additional fee">
                                </div>

                                <div class="form-group">
                                    <label for="selectgroup-pills">Gateway Status</label>
                                    <div class="selectgroup selectgroup-pills">
                                        <label class="selectgroup-item">
                                            <input type="radio" name="manualpayon" value="0" class="selectgroup-input"<?php echo myvalidate($manualpayon_cek[0]); ?>>
                                            <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-fw fa-times-circle"></i> Disable</span>
                                        </label>
                                        <label class="selectgroup-item">
                                            <input type="radio" name="manualpayon" value="1" class="selectgroup-input"<?php echo myvalidate($manualpayon_cek[1]); ?>>
                                            <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-fw fa-check-circle"></i> Enable</span>
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="control-label">Member Gateway Status</div>
                                    <label class="custom-switch mt-2">
                                        <input type="checkbox" name="manualpay4usr" value="1" class="custom-switch-input"<?php echo myvalidate($manualpay4usr_cek); ?>>
                                        <span class="custom-switch-indicator"></span>
                                        <span class="custom-switch-description">Allow member to use this payment gateway option</span>
                                    </label>
                                </div>

                            </div>

                            <div class="tab-pane fade" id="paycoinpayments" role="tabpanel" aria-labelledby="config-coinpayments">
                                <p class="text-muted">Use this gateway option to accept payment using Coinpayments.</p>
                                <p class="text-muted text-small"><em>In order to make this payment method working properly, make sure your current currency <strong> <?php echo myvalidate($bpprow['currencycode']); ?></strong> is supported by this payment option.</em></p>

                                <div class="form-group">
                                    <label for="coinpaymentsmercid">Merchant ID</label>
                                    <input type="text" name="coinpaymentsmercid" id="coinpaymentsmercid" class="form-control" value="<?php echo isset($coinpaymentscfg['coinpaymentsmercid']) ? $coinpaymentscfg['coinpaymentsmercid'] : ''; ?>" placeholder="Coinpayments Merchant ID">
                                </div>

                                <div class="form-group">
                                    <label for="coinpaymentsipnkey">IPN Secret</label>
                                    <input type="password" name="coinpaymentsipnkey" id="coinpaymentsipnkey" class="form-control" value="<?php echo isset($coinpaymentscfg['coinpaymentsipnkey']) ? $coinpaymentscfg['coinpaymentsipnkey'] : ''; ?>" placeholder="Coinpayments IPN Secret">
                                </div>

                                <div class="form-group">
                                    <label for="coinpaymentsfee">Gateway Fee</label>
                                    <input type="text" name="coinpaymentsfee" id="coinpaymentsfee" class="form-control" value="<?php echo isset($coinpaymentscfg['coinpaymentsfee']) ? $coinpaymentscfg['coinpaymentsfee'] : '0'; ?>" placeholder="Additional fee">
                                </div>

                                <div class="form-group">
                                    <label for="selectgroup-pills">Gateway Status</label>
                                    <div class="selectgroup selectgroup-pills">
                                        <label class="selectgroup-item">
                                            <input type="radio" name="coinpaymentson" value="0" class="selectgroup-input"<?php echo myvalidate($coinpaymentson_cek[0]); ?>>
                                            <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-fw fa-times-circle"></i> Disable</span>
                                        </label>
                                        <label class="selectgroup-item">
                                            <input type="radio" name="coinpaymentson" value="1" class="selectgroup-input"<?php echo myvalidate($coinpaymentson_cek[1]); ?>>
                                            <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-fw fa-check-circle"></i> Enable</span>
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="control-label">Member Gateway Status</div>
                                    <label class="custom-switch mt-2">
                                        <input type="checkbox" name="coinpayments4usr" value="1" class="custom-switch-input"<?php echo myvalidate($coinpayments4usr_cek); ?>>
                                        <span class="custom-switch-indicator"></span>
                                        <span class="custom-switch-description">Allow member to use this payment gateway option</span>
                                    </label>
                                </div>

                            </div>

                            <div class="tab-pane fade" id="payperfectmoney" role="tabpanel" aria-labelledby="config-perfectmoney">
                                <p class="text-muted">Use this gateway option to accept payment using Perfectmoney.</p>
                                <p class="text-muted text-small"><em>In order to make this payment method working properly, make sure your current currency <strong> <?php echo myvalidate($bpprow['currencycode']); ?></strong> is supported by this payment option.</em></p>

                                <div class="form-group">
                                    <label for="perfectmoneyacc">Perfectmoney Account</label>
                                    <input type="text" name="perfectmoneyacc" id="perfectmoneyacc" class="form-control" value="<?php echo isset($perfectmoneycfg['perfectmoneyacc']) ? $perfectmoneycfg['perfectmoneyacc'] : ''; ?>" placeholder="Perfectmoney Account">
                                </div>

                                <div class="form-group">
                                    <label for="perfectmoneyname">Display Name</label>
                                    <input type="text" name="perfectmoneyname" id="perfectmoneyname" class="form-control" value="<?php echo isset($perfectmoneycfg['perfectmoneyname']) ? $perfectmoneycfg['perfectmoneyname'] : ''; ?>" placeholder="Name Displayed in the Payment Page">
                                </div>

                                <div class="form-group">
                                    <label for="perfectmoneypass">Alternate Passphrase</label>
                                    <input type="password" name="perfectmoneypass" id="perfectmoneypass" class="form-control" value="<?php echo isset($perfectmoneycfg['perfectmoneypass']) ? $perfectmoneycfg['perfectmoneypass'] : ''; ?>" placeholder="Perfectmoney Alternate Passphrase">
                                </div>

                                <div class="form-group">
                                    <label for="perfectmoneyfee">Gateway Fee</label>
                                    <input type="text" name="perfectmoneyfee" id="perfectmoneyfee" class="form-control" value="<?php echo isset($perfectmoneycfg['perfectmoneyfee']) ? $perfectmoneycfg['perfectmoneyfee'] : '0'; ?>" placeholder="Additional fee">
                                </div>

                                <div class="form-group">
                                    <label for="selectgroup-pills">Gateway Status</label>
                                    <div class="selectgroup selectgroup-pills">
                                        <label class="selectgroup-item">
                                            <input type="radio" name="perfectmoneyon" value="0" class="selectgroup-input"<?php echo myvalidate($perfectmoneyon_cek[0]); ?>>
                                            <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-fw fa-times-circle"></i> Disable</span>
                                        </label>
                                        <label class="selectgroup-item">
                                            <input type="radio" name="perfectmoneyon" value="1" class="selectgroup-input"<?php echo myvalidate($perfectmoneyon_cek[1]); ?>>
                                            <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-fw fa-check-circle"></i> Enable</span>
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="control-label">Member Gateway Status</div>
                                    <label class="custom-switch mt-2">
                                        <input type="checkbox" name="perfectmoney4usr" value="1" class="custom-switch-input"<?php echo myvalidate($perfectmoney4usr_cek); ?>>
                                        <span class="custom-switch-indicator"></span>
                                        <span class="custom-switch-description">Allow member to use this payment gateway option</span>
                                    </label>
                                </div>

                            </div>

                            <div class="tab-pane fade" id="paypayfast" role="tabpanel" aria-labelledby="config-payfast">
                                <p class="text-muted">Use this gateway option to accept payment using Payfast.</p>
                                <p class="text-muted text-small"><em>In order to make this payment method working properly, make sure your current <a href="index.php?hal=payplancfg">currency setting</a> is in <strong>ZAR</strong>.</em></p>

                                <div class="form-group">
                                    <label for="payfastmercid">Account Merchant ID</label>
                                    <input type="text" name="payfastmercid" id="payfastmercid" class="form-control" value="<?php echo isset($payfastcfg['payfastmercid']) ? $payfastcfg['payfastmercid'] : ''; ?>" placeholder="Payfast Account Merchant ID">
                                </div>

                                <div class="form-group">
                                    <label for="payfastkey">Account Merchant Key</label>
                                    <input type="password" name="payfastkey" id="payfastkey" class="form-control" value="<?php echo isset($payfastcfg['payfastkey']) ? $payfastcfg['payfastkey'] : ''; ?>" placeholder="Payfast Account Merchant Key">
                                </div>

                                <div class="form-group">
                                    <label for="payfastfee">Gateway Fee</label>
                                    <input type="text" name="payfastfee" id="payfastfee" class="form-control" value="<?php echo isset($payfastcfg['payfastfee']) ? $payfastcfg['payfastfee'] : '0'; ?>" placeholder="Additional fee">
                                </div>
                                <div class="form-group">
                                    <label for="selectgroup-pills">Payfast Sandbox Status</label>
                                    <div class="selectgroup selectgroup-pills">
                                        <label class="selectgroup-item">
                                            <input type="radio" name="payfastsbox" value="0" class="selectgroup-input"<?php echo myvalidate($payfastsbox_cek[0]); ?>>
                                            <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-fw fa-stop-circle"></i> Disable</span>
                                        </label>
                                        <label class="selectgroup-item">
                                            <input type="radio" name="payfastsbox" value="1" class="selectgroup-input"<?php echo myvalidate($payfastsbox_cek[1]); ?>>
                                            <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-fw fa-play-circle"></i> Enable</span>
                                        </label>
                                    </div>
                                </div>


                                <div class="form-group">
                                    <label for="selectgroup-pills">Gateway Status</label>
                                    <div class="selectgroup selectgroup-pills">
                                        <label class="selectgroup-item">
                                            <input type="radio" name="payfaston" value="0" class="selectgroup-input"<?php echo myvalidate($payfaston_cek[0]); ?>>
                                            <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-fw fa-times-circle"></i> Disable</span>
                                        </label>
                                        <label class="selectgroup-item">
                                            <input type="radio" name="payfaston" value="1" class="selectgroup-input"<?php echo myvalidate($payfaston_cek[1]); ?>>
                                            <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-fw fa-check-circle"></i> Enable</span>
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="control-label">Member Gateway Status</div>
                                    <label class="custom-switch mt-2">
                                        <input type="checkbox" name="payfast4usr" value="1" class="custom-switch-input"<?php echo myvalidate($payfast4usr_cek); ?>>
                                        <span class="custom-switch-indicator"></span>
                                        <span class="custom-switch-description">Allow member to use this payment gateway option</span>
                                    </label>
                                </div>

                            </div>

                            <div class="tab-pane fade" id="paypaypal" role="tabpanel" aria-labelledby="config-paypal">
                                <p class="text-muted">Use this gateway option to accept payment using Paypal.</p>
                                <p class="text-muted text-small"><em>In order to make this payment method working properly, make sure your current currency <strong> <?php echo myvalidate($bpprow['currencycode']); ?></strong> is supported by this payment option.</em></p> 

                                <div class="form-group">
                                    <label for="paypalacc">Paypal Account</label>
                                    <input type="text" name="paypalacc" id="paypalacc" class="form-control" value="<?php echo isset($paypalcfg['paypalacc']) ? $paypalcfg['paypalacc'] : ''; ?>" placeholder="Paypal Email Address">
                                </div>

                                <div class="form-group">
                                    <label for="paypalfee">Gateway Fee</label>
                                    <input type="text" name="paypalfee" id="paypalfee" class="form-control" value="<?php echo isset($paypalcfg['paypalfee']) ? $paypalcfg['paypalfee'] : '0'; ?>" placeholder="Additional fee">
                                </div>

                                <div class="form-group">
                                    <label for="selectgroup-pills">Paypal Sandbox Status</label>
                                    <div class="selectgroup selectgroup-pills">
                                        <label class="selectgroup-item">
                                            <input type="radio" name="paypalsbox" value="0" class="selectgroup-input"<?php echo myvalidate($paypalsbox_cek[0]); ?>>
                                            <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-fw fa-stop-circle"></i> Disable</span>
                                        </label>
                                        <label class="selectgroup-item">
                                            <input type="radio" name="paypalsbox" value="1" class="selectgroup-input"<?php echo myvalidate($paypalsbox_cek[1]); ?>>
                                            <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-fw fa-play-circle"></i> Enable</span>
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="selectgroup-pills">Gateway Status</label>
                                    <div class="selectgroup selectgroup-pills">
                                        <label class="selectgroup-item">
                                            <input type="radio" name="paypalon" value="0" class="selectgroup-input"<?php echo myvalidate($paypalon_cek[0]); ?>>
                                            <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-fw fa-times-circle"></i> Disable</span>
                                        </label>
                                        <label class="selectgroup-item">
                                            <input type="radio" name="paypalon" value="1" class="selectgroup-input"<?php echo myvalidate($paypalon_cek[1]); ?>>
                                            <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-fw fa-check-circle"></i> Enable</span>
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="control-label">Member Gateway Status</div>
                                    <label class="custom-switch mt-2">
                                        <input type="checkbox" name="paypal4usr" value="1" class="custom-switch-input"<?php echo myvalidate($paypal4usr_cek); ?>>
                                        <span class="custom-switch-indicator"></span>
                                        <span class="custom-switch-description">Allow member to use this payment gateway option</span>
                                    </label>
                                </div>

                            </div>

                            <div class="tab-pane fade" id="paypaystack" role="tabpanel" aria-labelledby="config-paystack">
                                <p class="text-muted">Use this gateway option to accept payment using Paystack.</p>
                                <p class="text-muted text-small"><em>In order to make this payment method working properly, make sure your current currency <strong> <?php echo myvalidate($bpprow['currencycode']); ?></strong> is supported by this payment option.</em></p>

                                <div class="form-group">
                                    <label for="paystackpub">Account Public Key</label>
                                    <input type="text" name="paystackpub" id="paystackpub" class="form-control" value="<?php echo isset($paystackcfg['paystackpub']) ? $paystackcfg['paystackpub'] : ''; ?>" placeholder="Paystack Account Public Key">
                                </div>

                                <div class="form-group">
                                    <label for="paystackpin">Account Secret Key</label>
                                    <input type="password" name="paystackpin" id="paystackpin" class="form-control" value="<?php echo isset($paystackcfg['paystackpin']) ? $paystackcfg['paystackpin'] : ''; ?>" placeholder="Paystack Account Secret Key">
                                </div>

                                <div class="form-group">
                                    <label for="paystackfee">Gateway Fee</label>
                                    <input type="text" name="paystackfee" id="paystackfee" class="form-control" value="<?php echo isset($paystackcfg['paystackfee']) ? $paystackcfg['paystackfee'] : '0'; ?>" placeholder="Additional fee">
                                </div>

                                <div class="form-group">
                                    <label for="selectgroup-pills">Gateway Status</label>
                                    <div class="selectgroup selectgroup-pills">
                                        <label class="selectgroup-item">
                                            <input type="radio" name="paystackon" value="0" class="selectgroup-input"<?php echo myvalidate($paystackon_cek[0]); ?>>
                                            <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-fw fa-times-circle"></i> Disable</span>
                                        </label>
                                        <label class="selectgroup-item">
                                            <input type="radio" name="paystackon" value="1" class="selectgroup-input"<?php echo myvalidate($paystackon_cek[1]); ?>>
                                            <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-fw fa-check-circle"></i> Enable</span>
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="control-label">Member Gateway Status</div>
                                    <label class="custom-switch mt-2">
                                        <input type="checkbox" name="paystack4usr" value="1" class="custom-switch-input"<?php echo myvalidate($paystack4usr_cek); ?>>
                                        <span class="custom-switch-indicator"></span>
                                        <span class="custom-switch-description">Allow member to use this payment gateway option</span>
                                    </label>
                                </div>

                            </div>

                            <div class="tab-pane fade" id="paystripe" role="tabpanel" aria-labelledby="config-stripe">
                                <p class="text-muted">Use this gateway option to accept payment using Stripe.</p>
                                <p class="text-muted text-small"><em>In order to make this payment method working properly, make sure your current currency <strong> <?php echo myvalidate($bpprow['currencycode']); ?></strong> is supported by this payment option.</em></p>

                                <div class="form-group">
                                    <label for="stripename">Display Name</label>
                                    <input type="text" name="stripename" id="stripename" class="form-control" value="<?php echo isset($stripecfg['stripename']) ? $stripecfg['stripename'] : ''; ?>" placeholder="Name Displayed in the Payment Page">
                                </div>

                                <div class="form-group">
                                    <label for="stripeacc">Publishable Key</label>
                                    <input type="text" name="stripeacc" id="stripeacc" class="form-control" value="<?php echo isset($stripecfg['stripeacc']) ? $stripecfg['stripeacc'] : ''; ?>" placeholder="Stripe Publishable Key">
                                </div>

                                <div class="form-group">
                                    <label for="stripepass">Secret Key</label>
                                    <input type="password" name="stripepass" id="stripepass" class="form-control" value="<?php echo isset($stripecfg['stripepass']) ? $stripecfg['stripepass'] : ''; ?>" placeholder="Stripe Secret Key">
                                </div>

                                <div class="form-group">
                                    <label for="stripefee">Gateway Fee</label>
                                    <input type="text" name="stripefee" id="stripefee" class="form-control" value="<?php echo isset($stripecfg['stripefee']) ? $stripecfg['stripefee'] : '0'; ?>" placeholder="Additional fee">
                                </div>

                                <!--div class="form-group">
                                    <label for="selectgroup-pills">Checkout Option</label>
                                    <div class="selectgroup selectgroup-pills">
                                        <label class="selectgroup-item">
                                            <input type="radio" name="stripeoptco" value="1" class="selectgroup-input"<?php echo myvalidate($stripeoptco_cek[0]); ?>>
                                            <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-fw fa-arrow-right"></i> Updated</span>
                                        </label>
                                        <label class="selectgroup-item">
                                            <input type="radio" name="stripeoptco" value="2" class="selectgroup-input"<?php echo myvalidate($stripeoptco_cek[1]); ?>>
                                            <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-fw fa-arrow-right"></i> Legacy</span>
                                        </label>
                                    </div>
                                </div-->
                                <input type="hidden" name="stripeoptco" value="1">

                                <div class="form-group">
                                    <label for="selectgroup-pills">Gateway Status</label>
                                    <div class="selectgroup selectgroup-pills">
                                        <label class="selectgroup-item">
                                            <input type="radio" name="stripeon" value="0" class="selectgroup-input"<?php echo myvalidate($stripeon_cek[0]); ?>>
                                            <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-fw fa-times-circle"></i> Disable</span>
                                        </label>
                                        <label class="selectgroup-item">
                                            <input type="radio" name="stripeon" value="1" class="selectgroup-input"<?php echo myvalidate($stripeon_cek[1]); ?>>
                                            <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-fw fa-check-circle"></i> Enable</span>
                                        </label>
                                    </div>
                                </div>

                            </div>

                            <div class="tab-pane fade" id="paytest" role="tabpanel" aria-labelledby="config-test">
                                <p class="text-muted">Use this gateway option for testing and to simulate member payment.</p>

                                <div class="form-group">
                                    <label for="testpaylabel">Payment Name</label>
                                    <input type="text" name="testpaylabel" id="testpaylabel" class="form-control" value="<?php echo isset($payrow['testpaylabel']) ? $payrow['testpaylabel'] : 'Test Payment'; ?>" placeholder="Gateway Name">
                                </div>

                                <div class="form-group">
                                    <label for="testpayfee">Gateway Fee</label>
                                    <input type="text" name="testpayfee" id="testpayfee" class="form-control" value="<?php echo isset($payrow['testpayfee']) ? $payrow['testpayfee'] : '0'; ?>" placeholder="Additional fee">
                                </div>

                                <div class="form-group">
                                    <label for="selectgroup-pills">Gateway Status (Debug Mode)</label>
                                    <div class="selectgroup selectgroup-pills">
                                        <label class="selectgroup-item">
                                            <input type="radio" name="testpayon" value="0" class="selectgroup-input"<?php echo myvalidate($testpayon_cek[0]); ?>>
                                            <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-fw fa-times-circle"></i> Disable</span>
                                        </label>
                                        <label class="selectgroup-item">
                                            <input type="radio" name="testpayon" value="1" class="selectgroup-input"<?php echo myvalidate($testpayon_cek[1]); ?>>
                                            <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-fw fa-check-circle"></i> Enable</span>
                                        </label>
                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>

                    <div class="card-footer bg-whitesmoke text-md-right">
                        <button type="reset" name="reset" value="reset" id="reset" class="btn btn-warning">
                            <i class="fa fa-fw fa-undo"></i> Reset
                        </button>
                        <button type="submit" name="submit" value="submit" id="submit" class="btn btn-primary">
                            <i class="fa fa-fw fa-plus-circle"></i> Save Changes
                        </button>
                        <input type="hidden" name="dosubmit" value="1">
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>
