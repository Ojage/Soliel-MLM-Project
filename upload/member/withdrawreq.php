<?php
if (!defined('OK_LOADME')) {
    die('o o p s !');
}

$pgdatatokenarr = get_optionvals($payrow['pgdatatoken']);

$mbrpaystr = get_pgmbrtoken($mbrstr);
$wdrwfeearr = get_withdrawfee();
$fval = $wdrwfeearr['fee'];
$fcapval = $wdrwfeearr['cap'];

if ($mbrstr['mbrstatus'] != 1 || $mbrstr['mpstatus'] != 1) {
    $cfgtoken['diswithdraw'] = '1';
}

if (isset($FORM['dosubmit']) && $FORM['dosubmit'] == '1' && $cfgtoken['diswithdraw'] != '1') {

    extract($FORM);

    if ($txamount <= 0 || ($cfgtoken['minwalletwdr'] > 0 && $txamount < $cfgtoken['minwalletwdr']) || ($cfgtoken['maxwalletwdr'] > 0 && $txamount > $cfgtoken['maxwalletwdr'])) {
        $_SESSION['dotoaster'] = "toastr.warning('{$LANG['m_errwithdrawal']} <strong>{$LANG['m_errwithdrawalinvalid']}</strong>', 'Error');";
    } else if ($txpaytype != '' && $txamount > 0 && $txamount <= $mbrstr['ewallet']) {
        $redirto = $_SESSION['redirto'];
        $_SESSION['redirto'] = '';

        $insert = do_withdrawreq($mbrstr, $txamount, $txpaytype);

        if ($insert) {
            $_SESSION['dotoaster'] = "toastr.success('{$LANG['m_okwithdrawal']}', 'Success');";
        } else {
            $_SESSION['dotoaster'] = "toastr.error('{$LANG['m_errwithdrawal']} <strong>{$LANG['m_errwithdrawaltry']}</strong>', 'Warning');";
        }
    } else if ($txpaytype == '') {
        $_SESSION['dotoaster'] = "toastr.warning('{$LANG['m_errwithdrawal']} <strong>{$LANG['m_errwithdrawalnouser']}</strong>', 'Error');";
    } else {
        $_SESSION['dotoaster'] = "toastr.error('{$LANG['m_errwithdrawal']} <strong>{$LANG['m_errwithdrawalnofund']}</strong>', 'Error');";
    }

    redirpageto('index.php?hal=withdrawreq');
    exit;
}

if ($mbrstr['ewallet'] < 0) {
    $balanceclor = ' text-danger';
} elseif ($mbrstr['ewallet'] > 0) {
    $balanceclor = ' text-info';
}

$btnwidrdis = ($mbrstr['ewallet'] <= 0) ? " disabled" : '';

$condition = " AND txtoken LIKE '%|WIDR:%' AND txfromid = '0' AND txtoid = '{$mbrstr['id']}'";
$withdrawlist = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_transactions WHERE 1 " . $condition . " LIMIT 12");

$waitwithdrawlist = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_transactions WHERE 1 " . $condition . " AND txstatus != '1'");
if ((!defined('ISDEMOMODE') && count($waitwithdrawlist) > 0) || $cfgtoken['diswithdraw'] == '1') {
    $formstr = $formendstr = '';
    $wdrbtnstr = '<button type="button" class="btn btn-primary"' . $btnwidrdis . ' disabled><i class="fa fa-fw fa-donate"></i> ' . $LANG['g_waiting'] . '</button>';
} else {
    $formstr = '<form method="post" action="index.php">';
    $formendstr = '</form>';
    $wdrbtnstr = '<button type="submit" name="submit" value="withdraw" id="submit" class="btn btn-primary"' . $btnwidrdis . '><i class="fa fa-fw fa-donate"></i> ' . $LANG['g_withdraw'] . '</button>';
}

$minwdrstr = ($cfgtoken['minwalletwdr'] > 0) ? "min='{$cfgtoken['minwalletwdr']}'" : "min='0'";
$maxwdrstr = ($cfgtoken['maxwalletwdr'] > 0) ? "max='{$cfgtoken['maxwalletwdr']}'" : '';

$withdrawstatusinfo = sprintf($LANG['g_withdrawstatusinfo'], $LANG['g_withdrawiswait'], $LANG['g_withdrawislook'], $LANG['g_withdrawispaid']);
?>

<div class="section-header">
    <h1><i class="fa fa-fw fa-hand-holding-usd"></i> <?php echo myvalidate($LANG['g_withdrawreq']); ?></h1>
</div>

<div class="section-body">

    <?php echo myvalidate($formstr); ?>
    <input type="hidden" name="hal" value="withdrawreq">
    <div class="card card-primary">
        <div class="card-header">
            <h4>
                <?php echo myvalidate($LANG['g_balance']); ?> <span class="<?php echo myvalidate($balanceclor); ?>"><?php echo myvalidate($bpprow['currencysym'] . $mbrstr['ewallet']); ?></span> <?php echo myvalidate($bpprow['currencycode']); ?>
            </h4>
        </div>
        <div class="card-body">
            <div class="row">

                <div class="col-md-6 float-md-right">
                    <?php echo myvalidate($withdrawstatusinfo); ?>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><?php echo myvalidate($LANG['g_account']); ?></span>
                            </div>
                            <select name='txpaytype' class="custom-select" id="inputGroupSelect05" required="">
                                <option value="" disabled="" selected>-</option>
                                <?php
                                if ($pgdatatokenarr['payfast4usr'] == 1) {
                                    ?>
                                    <option value="payfastmercid">Payfast (<?php echo myvalidate($mbrpaystr['payfastmercid']) ? $mbrpaystr['payfastmercid'] : '...'; ?>)</option>
                                    <?php
                                }
                                if ($pgdatatokenarr['paypal4usr'] == 1) {
                                    ?>
                                    <option value="paypalacc">Paypal (<?php echo myvalidate($mbrpaystr['paypalacc']) ? $mbrpaystr['paypalacc'] : '...'; ?>)</option>
                                    <?php
                                }
                                if ($pgdatatokenarr['stripe4usr'] == 1) {
                                    ?>
                                    <option value="stripeacc">Stripe (<?php echo myvalidate($mbrpaystr['stripeacc']) ? $mbrpaystr['stripeacc'] : '...'; ?>)</option>
                                    <?php
                                }

                                if ($pgdatatokenarr['paystack4usr'] == 1) {
                                    ?>
                                    <option value="paystackpub">Paystack (<?php echo myvalidate($mbrpaystr['paystackpub']) ? $mbrpaystr['paystackpub'] : '...'; ?>)</option>
                                    <?php
                                }
                                if ($pgdatatokenarr['perfectmoney4usr'] == 1) {
                                    ?>
                                    <option value="perfectmoneyacc">Perfectmoney (<?php echo myvalidate($mbrpaystr['perfectmoneyacc']) ? $mbrpaystr['perfectmoneyacc'] : '...'; ?>)</option>
                                    <?php
                                }
                                if ($pgdatatokenarr['coinpayments4usr'] == 1) {
                                    ?>
                                    <option value="coinpaymentsmercid">Bitcoin (<?php echo myvalidate($mbrpaystr['coinpaymentsmercid']) ? $mbrpaystr['coinpaymentsmercid'] : '...'; ?>)</option>
                                    <?php
                                }
                                if ($payrow['manualpay4usr'] == 1) {
                                    ?>
                                    <option value="manualpayipn"><?php echo myvalidate($payrow['manualpayname']); ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><?php echo myvalidate($LANG['m_withdrawamount']); ?></span>
                            </div>
                            <input type="number" <?php echo myvalidate($minwdrstr . ' ' . $maxwdrstr); ?> step="any" id="txamount" name="txamount" class="form-control" onChange="dowithdrawfee('<?php echo myvalidate($fval); ?>', '<?php echo myvalidate($fcapval); ?>', '<?php echo myvalidate($bpprow['currencysym']); ?>', '<?php echo myvalidate($LANG['m_withdrawfee']); ?>', '<?php echo myvalidate($LANG['m_withdrawreceive']); ?>');" placeholder="0.00" required="">
                        </div>
                        <h6 class="text-muted text-small">
                            <span class="badge badge-info float-right" id="txamountstr2"></span>
                            <span class="badge badge-info float-right" id="txamountstr1"></span>
                        </h6>
                    </div>

                    <div class="float-md-right mt-4">
                        <a href="index.php?hal=withdrawreq" class="btn btn-danger"><i class="fa fa-fw fa-redo"></i> <?php echo myvalidate($LANG['g_clear']); ?></a>
                        <?php echo myvalidate($wdrbtnstr); ?>
                    </div>

                </div>

            </div>
        </div>
        <div class="card-footer bg-whitesmoke">
            <div class="row">
                <div class="col-sm-12 text-small text-danger">
                    <?php echo myvalidate($LANG['m_withdrawreqnote']); ?>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
    <input type="hidden" name="dosubmit" value="1">
    <?php echo myvalidate($formendstr); ?>

    <div class="row">
        <?php
        if (count($withdrawlist) > 0) {
            $numwdr = 0;
            foreach ($withdrawlist as $val) {
                if ($val['txamount'] <= 0) {
                    continue;
                }
                $paybyoptico = $avalpaygateicon_array[$val['txpaytype']];
                $paybyopt = $avalwithdrawgate_array[$val['txpaytype']];

                $headdtbg = 'bg-primary text-light';
                $statusbadge = '';
                switch ($val['txstatus']) {
                    case "1":
                        $headdtbg = 'bg-light';
                        $statusbadge .= "<span class='badge badge-secondary'>{$LANG['g_withdrawispaid']}</span>";
                        break;
                    case "2":
                        $statusbadge .= "<span class='badge badge-info'>{$LANG['g_withdrawislook']}</span>";
                        break;
                    default:
                        $statusbadge .= "<span class='badge badge-light'>{$LANG['g_withdrawiswait']}</span>";
                }
                ?>

                <div class="col-12 col-md-4 col-lg-4">
                    <div class="pricing">
                        <div class="pricing-title <?php echo myvalidate($headdtbg); ?>">
                            <?php echo formatdate($val['txdatetm'], 'dt'); ?>
                        </div>
                        <div class="pricing-padding">
                            <span class="pricing-price" data-toggle="tooltip" title="<?php echo myvalidate($paybyopt); ?>"><?php echo myvalidate($paybyoptico); ?></span>
                            <div class="pricing-price">
                                <h4><?php echo myvalidate($bpprow['currencysym'] . $val['txamount'] . ' ' . $bpprow['currencycode']); ?></h4>
                            </div>
                            <?php echo myvalidate($statusbadge); ?>
                        </div>
                    </div>
                </div>

                <?php
                $numwdr++;
            }
            if ($numwdr < 1) {
                echo "No Record(s) Found!";
            }
        }
        ?>
    </div>

</div>

