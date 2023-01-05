<?php
if (!defined('OK_LOADME')) {
    die('o o p s !');
}

$doppid = intval($FORM['doppid']);
if ($doppid > $frlmtdcfg['mxstages']) {
    $doppid = 1;
}

$bpprow = ppdbplan($doppid);
$bpnowplantokenarr = get_optionvals($bpprow['plantoken']);

$lwide_menu = $ldeep_menu = '';
for ($i = 0; $i <= $frlmtdcfg['ismw']; $i++) {
    $lvelmax = ($i > 0) ? $i : 'Unilevel';
    $isselected = ($i == $bpprow['maxwidth']) ? "selected" : '';
    $lwide_menu .= "<option value='{$i}' {$isselected}>{$lvelmax}";
}

for ($i = 1; $i <= $frlmtdcfg['ismd']; $i++) {
    $lvelmax = $i;
    $isselected = ($i == $bpprow['maxdepth']) ? "selected" : '';
    $ldeep_menu .= "<option value='{$i}' {$isselected}>{$lvelmax}";
}

$ifrolluptoarr = array(0, 1);
$ifrollupto_cek = radiobox_opt($ifrolluptoarr, $bpprow['ifrollupto']);
$isrecyclingarr = array(0, 1, 2, 3);
$isrecycling_cek = radiobox_opt($isrecyclingarr, $bpprow['isrecycling']);
$spilloverarr = array(0, 1);
$spillover_cek = radiobox_opt($spilloverarr, $bpprow['spillover']);
$expdayarr = array('', '30', '1m', '3m', '1y');
$expday_cek = radiobox_opt($expdayarr, $bpprow['expday']);
$isrenewbywalletarr = array(0, 1);
$isrenewbywallet_cek = radiobox_opt($isrenewbywalletarr, $bpnowplantokenarr['isrenewbywallet']);
$planstatusarr = array(0, 1);
$planstatus_cek = radiobox_opt($planstatusarr, $bpprow['planstatus']);
$remindregarr = array('', '3', '5', '1w');
$remindreg_cek = radiobox_opt($remindregarr, $bptoken['remindreg']);
$gracedayarr = array(0, 1, 3);
$graceday_cek = radiobox_opt($gracedayarr, $bpprow['graceday']);

$isgenview_cek = checkbox_opt($bptoken['isgenview']);
$isrecognzfreembr_cek = checkbox_opt($bptoken['isrecognzfreembr']);
$doreactive_cek = checkbox_opt($bpnowplantokenarr['doreactive']);

$ppbdcolor = base64_decode($bpnowplantokenarr['ppbdcolor']);
$ppbgcolor = base64_decode($bpnowplantokenarr['ppbgcolor']);

$doselected = ($bpprow['recyclingto'] < 1) ? ' checked' : '';
$recyclingtolist = <<<INI_HTML
                  <label class="selectgroup-item">
                     <input type="radio" name="recyclingto" value="0" class="selectgroup-input"{$doselected}>
                     <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-fw fa-times"></i> Disable</span>
                  </label>
INI_HTML;
foreach ($bpparr as $key => $value) {
    if ($value['planstatus'] != 1 || $value['ppid'] == $bpprow['ppid']) {
        continue;
    }
    $doselected = ($bpprow['recyclingto'] == $value['ppid']) ? ' checked' : '';
    $recyclingtolist .= <<<INI_HTML
                  <label class="selectgroup-item">
                     <input type="radio" name="recyclingto" value="{$value['ppid']}" class="selectgroup-input"{$doselected}>
                     <span class="selectgroup-button selectgroup-button-icon">
                         <i class="fas fa-fw fa-long-arrow-alt-right"></i> {$value['ppname']}
                     </span>
                  </label>
INI_HTML;
}

if (isset($FORM['dosubmit']) and $FORM['dosubmit'] == '1') {

    extract($FORM);

    $paymupdate = date('Y-m-d H:i:s', time() + (3600 * $cfgrow['time_offset']));
    $didIdnow = $didId;

    $maxwidth = ($maxwidth == 1 && $maxdepth == 1) ? 2 : $maxwidth;
    if ($didId == 0) {
        $bptoken = $bpprow['bptoken'];
        $bptoken = put_optionvals($bptoken, 'remindreg', $remindreg);
        $bptoken = put_optionvals($bptoken, 'isgenview', $isgenview);
        $bptoken = put_optionvals($bptoken, 'isrecognzfreembr', '');

        $basedata = array(
            'pay_emailname' => mystriptag($pay_emailname),
            'pay_emailaddr' => mystriptag($pay_emailaddr, 'email'),
            'currencysym' => base64_encode($currencysym),
            'currencycode' => $currencycode,
            'maxwidth' => intval($maxwidth),
            'maxdepth' => intval($maxdepth),
            'bptoken' => $bptoken,
        );

        $didbId = 1;
        $condition = ' AND bpid = "' . $didbId . '" ';
        $sql = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_baseplan WHERE 1 " . $condition . "");
        if (count($sql) > 0) {
            $update = $db->update(DB_TBLPREFIX . '_baseplan', $basedata, array('bpid' => $didbId));
            if ($update) {
                $_SESSION['dotoaster'] = "toastr.success('Configuration updated successfully!', 'Success');";
            } else {
                $_SESSION['dotoaster'] = "toastr.warning({$LANG['g_nomajorchanges']}, 'Info');";
            }
        } else {
            $insert = $db->insert(DB_TBLPREFIX . '_baseplan', $basedata);
            if ($insert) {
                $_SESSION['dotoaster'] = "toastr.success('Configuration added successfully!', 'Success');";
            } else {
                $_SESSION['dotoaster'] = "toastr.error('Configuration not added <strong>Please try again!</strong>', 'Warning');";
            }
        }
    } else {
        $didId = intval($didId);
        $bpprow = ppdbplan($didId);

        $planlogo = imageupload('planlogo' . $didId, $_FILES['planlogo'], $old_planlogo);
        $planimg = imageupload('planimg' . $didId, $_FILES['planimg'], $old_planimg);
        $doreactive = ($recyclingto > 0) ? 1 : $doreactive;

        $plantoken = $bpprow['plantoken'];
        $plantoken = put_optionvals($plantoken, 'isrenewbywallet', $isrenewbywallet);
        $plantoken = put_optionvals($plantoken, 'doreactive', $doreactive);

        $plantoken = put_optionvals($plantoken, 'ppbdcolor', base64_encode($ppbdcolor));
        $plantoken = put_optionvals($plantoken, 'ppbgcolor', base64_encode($ppbgcolor));

        if (defined('ISDEMOMODE')) {
            $planstatus = '1';
        }
        $data = array(
            'ppname' => mystriptag($ppname),
            'planinfo' => mystriptag($planinfo),
            'planlogo' => $planlogo,
            'planimg' => $planimg,
            'regfee' => floatval($regfee),
            'renewfee' => floatval($renewfee),
            'expday' => mystriptag($expday),
            'graceday' => intval($graceday),
            'limitref' => intval($limitref),
            'ifrollupto' => intval($ifrollupto),
            'minref4splovr' => $minref4splovr,
            'spillover' => intval($spillover),
            'isrecycling' => intval($isrecycling),
            'recyclingto' => intval($recyclingto),
            'recyclingfee' => strval($recyclingfee),
            'cmdrlist' => $cmdrlist,
            'cmlist' => $cmlist,
            'cmlistrnew' => $cmlistrnew,
            'rwlist' => $rwlist,
            'planstatus' => intval($planstatus),
            'plantoken' => $plantoken,
        );

        $tnowplans = count($bpparr) + 1;
        $condition = ' AND ppid = "' . $didId . '" ';
        $sql = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_payplans WHERE 1 " . $condition . "");
        if (count($sql) > 0) {
            if ($newppid > 1) {
                $condition = ' AND ppid = "' . $newppid . '" ';
                $sql = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_payplans WHERE 1 " . $condition . "");
                if (count($sql) < 1 && $newppid < $tnowplans) {
                    $data['ppid'] = intval($newppid);
                    $didIdnow = $newppid;
                    $db->doQueryStr("ALTER TABLE " . DB_TBLPREFIX . "_payplans AUTO_INCREMENT = {$tnowplans}");
                }
            }
            $update = $db->update(DB_TBLPREFIX . '_payplans', $data, array('ppid' => $didId));
            if ($update) {
                $datadt = array(
                    'paymupdate' => $paymupdate,
                );
                $update = $db->update(DB_TBLPREFIX . '_payplans', $datadt, array('ppid' => $didId));
                $_SESSION['dotoaster'] = "toastr.success('Configuration updated successfully!', 'Success');";
            } else {
                $_SESSION['dotoaster'] = "toastr.warning({$LANG['g_nomajorchanges']}, 'Info');";
            }
        } else {
            $insert = $db->insert(DB_TBLPREFIX . '_payplans', $data);
            if ($insert) {
                $_SESSION['dotoaster'] = "toastr.success('Configuration added successfully!', 'Success');";
            } else {
                $_SESSION['dotoaster'] = "toastr.error('Configuration not added <strong>Please try again!</strong>', 'Warning');";
            }
        }
    }
    //header('location: index.php?hal=' . $hal);
    redirpageto('index.php?hal=' . $hal . '&doppid=' . $didIdnow);
    exit;
}

$btnplan = $ischecount = '';
$mxstages = ($frlmtdcfg['mxstages'] > 1) ? intval($frlmtdcfg['mxstages']) : 1;
$doppid = ($doppid > $mxstages ) ? 1 : $doppid;
for ($i = 1; $i <= $mxstages; $i++) {
    if ($i == $doppid) {
        $btnppcl = ' active';
        $ischecount = 1;
    } else {
        $btnppcl = '';
    }
    $valppname = ($bpparr[$i]['ppname']) ? $bpparr[$i]['ppname'] : '+';
    $btnplan .= '<li class="nav-item"><a href="index.php?hal=payplancfg&doppid=' . $i . '" class="nav-link' . $btnppcl . '">' . $valppname . '</a></li>';
}
$navbtn = ($ischecount == 1) ? '' : ' active';

$planlogo = ($bpparr[$doppid]['planlogo'] != '') ? $bpparr[$doppid]['planlogo'] : DEFIMG_LOGO;
$planimg = ($bpparr[$doppid]['planimg'] != '') ? $bpparr[$doppid]['planimg'] : DEFIMG_PLAN;

$iconstatusplanstr = ($bpprow['planstatus'] == 1) ? "<i class='fa fa-check text-success' data-toggle='tooltip' title='Program Status is Enable'></i>" : "<i class='fa fa-times text-danger' data-toggle='tooltip' title='Program Status is Disable'></i>";
?>

<div class="section-header">
    <h1><i class="fa fa-fw fa-gem"></i> <?php echo myvalidate($LANG['a_payplan']); ?></h1>
</div>

<div class="section-body">
    <div class="row">
        <div class="col-md-4">	
            <div class="card">
                <div class="card-header">
                    <h4>Payplan</h4>
                </div>
                <div class="card-body">
                    <ul class="nav nav-pills flex-column">
                        <li class="nav-item">
                            <a href="index.php?hal=payplancfg" class="nav-link<?php echo myvalidate($navbtn); ?>">Structure</a>
                        </li>
                        <?php echo myvalidate($btnplan); ?>
                    </ul>
                </div>
            </div>

            <?php
            if ($cfgrow['mylicver'] == 'reg' && $bpparr[1]['ppid'] > 0 && $payrow['testpayon'] == 1) {
                $uptopluscnt = file_get_contents("../common/plus.html");
                ?>
                <div class="text-center">
                    <a href="javascript:;" data-href="https://www.mlmscript.net/docs/unimatrix/index.php?todo=upgrade" class="btn btn-danger bootboxconfirm mb-4" data-poptitle="Add Membership" data-popmsg="<?php echo myvalidate($uptopluscnt); ?>" data-toggle="tooltip" title="Add more membership package or plan"><i class="fas fa-fw fa-question-circle"></i> Add More</a>
                </div>
                <?php
            }

            if ($doppid > 0) {
                ?>
                <div class="card">
                    <div class="card-header">
                        <h4>Settings</h4>
                        <div class="card-header-action">
                            <?php echo myvalidate($iconstatusplanstr); ?>
                        </div>
                    </div>
                    <div class="card-body">
                        <ul class="nav nav-pills flex-column" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="config-tab1" data-toggle="tab" href="#bpptab1" role="tab" aria-controls="program" aria-selected="true">Program</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="config-tab2" data-toggle="tab" href="#bpptab2" role="tab" aria-controls="commission" aria-selected="false">Commission</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="config-tab3" data-toggle="tab" href="#bpptab3" role="tab" aria-controls="others" aria-selected="false">Others</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h4><?php echo isset($bpprow['ppname']) ? $bpprow['ppname'] : 'Program'; ?></h4>
                    </div>
                    <div class="card-body">
                        <div class="mb-2 text-muted text-small">Update: <?php echo isset($bpprow['paymupdate']) ? $bpprow['paymupdate'] : '-'; ?></div>
                        <div class="planbgimg">
                            <div>
                                <img alt="image" src="<?php echo myvalidate($planimg); ?>" class="img-fluid rounded author-box-picture">
                                <img class="overplanlogo img-fluid" alt="image" src="<?php echo myvalidate($planlogo); ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>
        <div class="col-md-8">	
            <div class="card">

                <form method="post" action="index.php" enctype="multipart/form-data" id="bpidform_name" id="bpidform">
                    <input type="hidden" name="hal" value="payplancfg">

                    <div class="card-header">
                        <h4>Options</h4>
                    </div>

                    <div class="card-body">
                        <?php
                        if ($doppid < 1) {
                            ?>

                            <div class="form-row">
                                <div class="form-group col-md-12">
                                    <div class="alert alert-light alert-has-icon">
                                        <div class="alert-icon"><i class="fas fa-exclamation-triangle text-danger"></i></div>
                                        <div class="alert-body text-small">
                                            <div class="alert-title">Important!</div>
                                            <div class="text-danger">The level settings (Width and Depth) will be embedded in the member data when they register to the system, changing these values after the member registered may result in the member commissions and structure not being processed properly.</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="maxwidth">Level Width</label>
                                    <div class="input-group">
                                        <select name="maxwidth" id="maxwidth" class="form-control select2">
                                            <?php echo myvalidate($lwide_menu); ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="maxdepth">Level Depth</label>
                                    <div class="input-group">
                                        <select name="maxdepth" id="maxdepth" class="form-control select2">
                                            <?php echo myvalidate($ldeep_menu); ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group col-md-12">
                                    <div class="custom-control custom-checkbox">
                                        <input name="isgenview" value="1" type="checkbox" class="custom-control-input" id="isgenview"<?php echo myvalidate($isgenview_cek); ?>>
                                        <label class="custom-control-label text-muted text-small" for="isgenview"><em><?php echo myvalidate($LANG['a_genealogynote']); ?></em></label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="currencysym">Currency Symbol</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text"><i class="fa fa-fw fa-coins"></i></div>
                                        </div>
                                        <input type="text" name="currencysym" id="currencysym" class="form-control" value="<?php echo isset($bpprow['currencysym']) ? $bpprow['currencysym'] : '$'; ?>" placeholder="$" required>
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="currencycode">Currency Code</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text"><i class="fa fa-fw fa-money-bill-wave"></i></div>
                                        </div>
                                        <input type="text" name="currencycode" id="currencycode" class="form-control" value="<?php echo isset($bpprow['currencycode']) ? $bpprow['currencycode'] : 'USD'; ?>" placeholder="USD" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="pay_emailname">Sender Name</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text"><i class="fa fa-fw fa-user"></i></div>
                                        </div>
                                        <input type="text" name="pay_emailname" id="pay_emailname" class="form-control" value="<?php echo isset($bpprow['pay_emailname']) ? $bpprow['pay_emailname'] : ''; ?>" placeholder="Sender Name">
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="pay_emailaddr">Sender Email</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text"><i class="fa fa-fw fa-envelope"></i></div>
                                        </div>
                                        <input type="email" name="pay_emailaddr" id="pay_emailaddr" class="form-control" value="<?php echo isset($bpprow['pay_emailaddr']) ? $bpprow['pay_emailaddr'] : ''; ?>" placeholder="Sender Email Address" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="selectgroup-pills">Reminder Interval Before Account Expiry</label>
                                <div class="selectgroup selectgroup-pills">
                                    <label class="selectgroup-item">
                                        <input type="radio" name="remindreg" value="" class="selectgroup-input"<?php echo myvalidate($remindreg_cek[0]); ?>>
                                        <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-fw fa-times-circle"></i> Disable</span>
                                    </label>
                                    <label class="selectgroup-item">
                                        <input type="radio" name="remindreg" value="3" class="selectgroup-input"<?php echo myvalidate($remindreg_cek[1]); ?>>
                                        <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-fw fa-check-circle"></i> 3 Days</span>
                                    </label>
                                    <label class="selectgroup-item">
                                        <input type="radio" name="remindreg" value="5" class="selectgroup-input"<?php echo myvalidate($remindreg_cek[2]); ?>>
                                        <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-fw fa-check-circle"></i> 5 Days</span>
                                    </label>
                                    <label class="selectgroup-item">
                                        <input type="radio" name="remindreg" value="1w" class="selectgroup-input"<?php echo myvalidate($remindreg_cek[3]); ?>>
                                        <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-fw fa-check-circle"></i> 1 Week</span>
                                    </label>
                                </div>
                            </div>

                            <?php
                        } else {
                            ?>

                            <div class="tab-content no-padding">
                                <div class="tab-pane fade show active" id="bpptab1" role="tabpanel" aria-labelledby="config-tab1">

                                    <div class="form-group">
                                        <label for="ppname">Program Name</label>
                                        <input type="text" name="ppname" id="ppname" class="form-control" value="<?php echo isset($bpprow['ppname']) ? $bpprow['ppname'] : ''; ?>" placeholder="Program Name" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="planinfo">Program Description</label>
                                        <textarea class="form-control rowsize-sm" name="planinfo" id="planinfo" placeholder="Program Description"><?php echo isset($bpprow['planinfo']) ? $bpprow['planinfo'] : ''; ?></textarea>
                                    </div>

                                    <div class="form-group">
                                        <label for="planlogo">Program Logo (approx. 128x128 px)</label>
                                        <input type="file" name="planlogo" id="planlogo" class="form-control">
                                        <input type="hidden" name="old_planlogo" value="<?php echo myvalidate($planlogo); ?>">
                                        <div class="form-text text-muted">The image must have a maximum size of 1MB</div>
                                    </div>

                                    <div class="form-group">
                                        <label for="planimg">Program Header Image (approx. 980x240 px)</label>
                                        <input type="file" name="planimg" id="planimg" class="form-control">
                                        <input type="hidden" name="old_planimg" value="<?php echo myvalidate($planimg); ?>">
                                        <div class="form-text text-muted">The image must have a maximum size of 1MB</div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label for="regfee">Registration Fee</label>
                                            <div class="input-group">
                                                <input type="text" name="regfee" id="regfee" class="form-control" value="<?php echo isset($bpprow['regfee']) ? $bpprow['regfee'] : '0'; ?>" placeholder="0" required>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="renewfee">Renewal Fee (optional)<?php echo myvalidate($reglicmarker); ?></label>
                                            <div class="input-group">
                                                <input type="text" name="renewfee" id="renewfee" class="form-control" value="<?php echo ($bpprow['renewfee'] > 0) ? $bpprow['renewfee'] : ''; ?>" placeholder="0">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="selectgroup-pills">Membership Interval</label>
                                        <div class="selectgroup selectgroup-pills">
                                            <label class="selectgroup-item">
                                                <input type="radio" name="expday" value="" class="selectgroup-input"<?php echo myvalidate($expday_cek[0]); ?> id="expday" onchange="doHideShow(document.getElementById('expday'), '', false, 'dHS_doexpiry');">
                                                <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-fw fa-award"></i> Lifetime</span>
                                            </label>
                                            <label class="selectgroup-item">
                                                <input type="radio" name="expday" value="30" class="selectgroup-input"<?php echo myvalidate($expday_cek[1]); ?> id="expday30" onchange="doHideShow(document.getElementById('expday30'), '30', true, 'dHS_doexpiry');">
                                                <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-fw fa-calendar-alt"></i> 30 Days</span>
                                            </label>
                                            <label class="selectgroup-item">
                                                <input type="radio" name="expday" value="1m" class="selectgroup-input"<?php echo myvalidate($expday_cek[2]); ?> id="expday1m" onchange="doHideShow(document.getElementById('expday1m'), '1m', true, 'dHS_doexpiry');">
                                                <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-fw fa-calendar-day"></i> Monthly</span>
                                            </label>
                                            <label class="selectgroup-item">
                                                <input type="radio" name="expday" value="3m" class="selectgroup-input"<?php echo myvalidate($expday_cek[3]); ?> id="expday3m" onchange="doHideShow(document.getElementById('expday3m'), '3m', true, 'dHS_doexpiry');">
                                                <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-fw fa-calendar-week"></i> Quarterly</span>
                                            </label>
                                            <label class="selectgroup-item">
                                                <input type="radio" name="expday" value="1y" class="selectgroup-input"<?php echo myvalidate($expday_cek[4]); ?> id="expday1y" onchange="doHideShow(document.getElementById('expday1y'), '1y', true, 'dHS_doexpiry');">
                                                <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-fw fa-calendar-check"></i> Yearly</span>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="subcfg-option" id="dHS_doexpiry">
                                        <div class="form-group">
                                            <label for="selectgroup-pills">Renewal Payment by using Member Ewallet Balance</label>
                                            <div class="selectgroup selectgroup-pills">
                                                <label class="selectgroup-item">
                                                    <input type="radio" name="isrenewbywallet" value="0" class="selectgroup-input"<?php echo myvalidate($isrenewbywallet_cek[0]); ?>>
                                                    <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-fw fa-times-circle"></i> Disable</span>
                                                </label>
                                                <label class="selectgroup-item">
                                                    <input type="radio" name="isrenewbywallet" value="1" class="selectgroup-input"<?php echo myvalidate($isrenewbywallet_cek[1]); ?>>
                                                    <span class="selectgroup-button selectgroup-button-icon" data-toggle="tooltip" title="If possible and process it automatically"><i class="fas fa-fw fa-check-circle"></i> Enable</span>
                                                </label>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="selectgroup-pills">Grace Period Before Account Marked as Expired</label>
                                            <div class="selectgroup selectgroup-pills">
                                                <label class="selectgroup-item">
                                                    <input type="radio" name="graceday" value="0" class="selectgroup-input"<?php echo myvalidate($graceday_cek[0]); ?>>
                                                    <span class="selectgroup-button selectgroup-button-icon" data-toggle="tooltip" title="Keep Status Unchanged"><i class="fas fa-fw fa-times-circle"></i> Disable and Unchanged</span>
                                                </label>
                                                <label class="selectgroup-item">
                                                    <input type="radio" name="graceday" value="1" class="selectgroup-input"<?php echo myvalidate($graceday_cek[1]); ?>>
                                                    <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-fw fa-check-circle"></i> 1 Day</span>
                                                </label>
                                                <label class="selectgroup-item">
                                                    <input type="radio" name="graceday" value="3" class="selectgroup-input"<?php echo myvalidate($graceday_cek[2]); ?>>
                                                    <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-fw fa-check-circle"></i> 3 Days</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="selectgroup-pills">Program Status</label>
                                        <div class="selectgroup selectgroup-pills">
                                            <label class="selectgroup-item">
                                                <input type="radio" name="planstatus" value="0" class="selectgroup-input"<?php echo myvalidate($planstatus_cek[0]); ?>>
                                                <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-fw fa-times-circle"></i> Disable</span>
                                            </label>
                                            <label class="selectgroup-item">
                                                <input type="radio" name="planstatus" value="1" class="selectgroup-input"<?php echo myvalidate($planstatus_cek[1]); ?>>
                                                <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-fw fa-check-circle"></i> Enable</span>
                                            </label>
                                        </div>
                                    </div>

                                </div>

                                <div class="tab-pane fade" id="bpptab2" role="tabpanel" aria-labelledby="config-tab2">
                                    <div class="form-group">
                                        <label for="cmdrlist">Personal Referral Commission</label>
                                        <input type="text" name="cmdrlist" id="cmdrlist" class="form-control" value="<?php echo isset($bpprow['cmdrlist']) ? $bpprow['cmdrlist'] : ''; ?>" placeholder="Personal referral commission">
                                    </div>

                                    <div class="form-group">
                                        <label for="cmlist">Initial Level Commission</label>
                                        <textarea class="form-control rowsize-sm" name="cmlist" id="cmlist" placeholder="Commission list from member registration, separated with comma"><?php echo isset($bpprow['cmlist']) ? $bpprow['cmlist'] : ''; ?></textarea>
                                    </div>

                                    <div class="form-group">
                                        <label for="cmlistrnew">Renewal Level Commission<?php echo myvalidate($reglicmarker); ?></label>
                                        <textarea class="form-control rowsize-sm" name="cmlistrnew" id="cmlistrnew" placeholder="Commission list from renewal, separated with comma"><?php echo isset($bpprow['cmlistrnew']) ? $bpprow['cmlistrnew'] : ''; ?></textarea>
                                    </div>

                                    <div class="form-group">
                                        <label for="rwlist">Level Complete Reward (matrix plan)</label>
                                        <textarea class="form-control rowsize-sm" name="rwlist" id="rwlist" placeholder="Reward value, separated with comma"><?php echo isset($bpprow['rwlist']) ? $bpprow['rwlist'] : ''; ?></textarea>
                                    </div>

                                </div>

                                <div class="tab-pane fade" id="bpptab3" role="tabpanel" aria-labelledby="config-tab3">
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label for="limitref">Max Personal Referral</label>
                                            <div class="input-group">
                                                <input type="number" min="0" name="limitref" id="limitref" class="form-control" value="<?php echo isset($bpprow['limitref']) ? $bpprow['limitref'] : ''; ?>" placeholder="0">
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="minref4splovr">Min Personal Referral to Get Spillover</label>
                                            <div class="input-group">
                                                <input type="number" min="0" name="minref4splovr" id="minref4splovr" class="form-control" value="<?php echo isset($bpprow['minref4splovr']) ? $bpprow['minref4splovr'] : ''; ?>" placeholder="0">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="selectgroup-pills">Spillover Option (matrix plan)</label>
                                        <div class="selectgroup selectgroup-pills">
                                            <label class="selectgroup-item">
                                                <input type="radio" name="spillover" value="0" class="selectgroup-input"<?php echo myvalidate($spillover_cek[0]); ?>>
                                                <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-fw fa-people-carry"></i> First Complete</span>
                                            </label>
                                            <label class="selectgroup-item">
                                                <input type="radio" name="spillover" value="1" class="selectgroup-input"<?php echo myvalidate($spillover_cek[1]); ?>>
                                                <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-fw fa-street-view"></i> Spread Evenly</span>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="selectgroup-pills">Roll-up member placement</label>
                                        <div class="selectgroup selectgroup-pills">
                                            <label class="selectgroup-item">
                                                <input type="radio" name="ifrollupto" value="0" class="selectgroup-input"<?php echo myvalidate($ifrollupto_cek[0]); ?>>
                                                <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-fw fa-building"></i> Company (without Sponsor)</span>
                                            </label>
                                            <label class="selectgroup-item">
                                                <input type="radio" name="ifrollupto" value="1" class="selectgroup-input"<?php echo myvalidate($ifrollupto_cek[1]); ?>>
                                                <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-fw fa-user"></i> Next Sponsor</span>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="selectgroup-pills">Account Cycling Option (on completed matrix plan)</label>
                                        <div class="selectgroup selectgroup-pills">
                                            <label class="selectgroup-item">
                                                <input type="radio" name="isrecycling" value="0" class="selectgroup-input"<?php echo myvalidate($isrecycling_cek[0]); ?> id="isrecycling0" onchange="doHideShow(document.getElementById('isrecycling0'), '0', false, 'dHS_doreactive');">
                                                <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-fw fa-times"></i> Disable</span>
                                            </label>
                                            <label class="selectgroup-item">
                                                <input type="radio" name="isrecycling" value="1" class="selectgroup-input"<?php echo myvalidate($isrecycling_cek[1]); ?> id="isrecycling1" onchange="doHideShow(document.getElementById('isrecycling1'), '1', true, 'dHS_doreactive');">
                                                <span class="selectgroup-button selectgroup-button-icon" data-toggle="tooltip" title="Re-entry and place new entry under sponsor structure"><i class="fas fa-fw fa-user"></i> Re-entry sponsor</span>
                                            </label>
                                            <label class="selectgroup-item">
                                                <input type="radio" name="isrecycling" value="2" class="selectgroup-input"<?php echo myvalidate($isrecycling_cek[2]); ?> id="isrecycling2" onchange="doHideShow(document.getElementById('isrecycling2'), '2', true, 'dHS_doreactive');">
                                                <span class="selectgroup-button selectgroup-button-icon" data-toggle="tooltip" title="Re-entry and place new entry under referrer structure"><i class="fas fa-fw fa-user-secret"></i> Re-entry follow referrer</span>
                                            </label>
                                            <label class="selectgroup-item">
                                                <input type="radio" name="isrecycling" value="3" class="selectgroup-input"<?php echo myvalidate($isrecycling_cek[3]); ?> id="isrecycling3" onchange="doHideShow(document.getElementById('isrecycling3'), '3', false, 'dHS_doreactive');">
                                                <span class="selectgroup-button selectgroup-button-icon" data-toggle="tooltip" title="Using member wallet fund, otherwise, the membership will be disabled"><i class="fas fa-fw fa-handshake"></i> Plan repayment</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="subcfg-option" id="dHS_doreactive">
                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" name="doreactive" value="1" class="custom-control-input" id="doreactive"<?php echo myvalidate($doreactive_cek); ?>>
                                                <label class="custom-control-label" for="doreactive">If possible: deduct the member wallet fund for account payment.</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label for="ppbdcolor">Border Color</label>
                                            <div class="input-group">
                                                <input type="color" name="ppbdcolor" id="ppbdcolor" class="form-control" value="<?php echo ($ppbdcolor != '') ? $ppbdcolor : '#999999'; ?>" placeholder="#999">
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="ppbgcolor">Background Color</label>
                                            <div class="input-group">
                                                <input type="color" name="ppbgcolor" id="ppbgcolor" class="form-control" value="<?php echo ($ppbgcolor != '') ? $ppbgcolor : '#EEEEEE'; ?>" placeholder="#EEE">
                                            </div>
                                        </div>
                                    </div>

                                </div>

                            </div>


                            <?php
                        }
                        ?>
                    </div>

                    <div class="card-footer bg-whitesmoke text-md-right">
                        <?php
                        //use this form to update the "plan id" and make it in order
                        //only needed if previously the record was accidentally removed from the database
                        //and there are NO data referring to this "plan id"
                        if ($FORM['doppid'] > 1 && $FORM['updatethisplanid'] == 'o') {
                            ?>
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            Membership Plan Id#
                                        </div>
                                    </div>
                                    <input type="number" name="newppid" value="<?php echo isset($bpprow['ppid']) ? $bpprow['ppid'] : ''; ?>" class="form-control currency" placeholder="Do not change unless you know what you are doing!">
                                </div>
                            </div>
                            <?php
                        }
                        ?>

                        <button type="reset" name="reset" value="reset" id="reset" class="btn btn-warning">
                            <i class="fa fa-fw fa-undo"></i> Reset
                        </button>
                        <button type="submit" name="submit" value="submit" id="submit" class="btn btn-primary">
                            <i class="fa fa-fw fa-plus-circle"></i> Save Changes
                        </button>
                        <input type="hidden" name="dosubmit" value="1">
                        <input type="hidden" name="didId" value="<?php echo myvalidate($doppid); ?>">
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
<?php
if (in_array($bpprow['isrecycling'], array(0, 3))) {
    echo '$("#dHS_doreactive").hide();';
}
if ($bpprow['expday'] == '') {
    echo '$("#dHS_doexpiry").hide();';
}
?>
    });
</script>