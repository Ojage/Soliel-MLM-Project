<?php
if (!defined('OK_LOADME')) {
    die('o o p s !');
}

// create tx for manual renewal in advance
if ($FORM['renewHash'] == md5($mbrstr['mpid'] . $mbrstr['reg_expd']) && $FORM['renewId'] == $mbrstr['mpid'] && $FORM['redir']) {
    $utctime = date('Y-m-d H:i:s', time() + (3600 * $cfgrow['time_offset']));
    do_renewtx($utctime, $mbrstr);
    redirpageto('index.php?hal=' . $FORM['redir']);
    exit;
}

$condition = ' AND sprlist LIKE "%:' . $mbrstr['mpid'] . '|%" ';
$row = $db->getAllRecords(DB_TBLPREFIX . '_mbrplans', 'COUNT(*) as totref', $condition);
$myreftotal = $row[0]['totref'];

$condition = ' AND idref = "' . $mbrstr['id'] . '" ';
$row = $db->getAllRecords(DB_TBLPREFIX . '_mbrplans', 'COUNT(*) as totref', $condition);
$myrefonly = $row[0]['totref'];

$condition = ' AND txtoid = "' . $mbrstr['id'] . '" AND txstatus = "1" AND txtoken LIKE "%|LCM:%" ';
$row = $db->getAllRecords(DB_TBLPREFIX . '_transactions', 'SUM(txamount) as totincome', $condition);
$myincometotal = sprintf("%0.2f", $row[0]['totincome']);
$myewallet = sprintf("%0.2f", $mbrstr['ewallet']);

$hitratio = $LANG['g_performance'] . ': ';
$adjthits = ($mbrstr['hits'] > 0) ? $mbrstr['hits'] : 1;
$prcnthit = $myrefonly / $adjthits * 100;
if ($prcnthit > 100000) {
    $hitratio .= "<i class='fas fa-star fa-fw text-warning'></i><i class='fas fa-star fa-fw text-warning'></i><i class='fas fa-star fa-fw text-warning'></i>";
} else if ($prcnthit > 10000) {
    $hitratio .= "<i class='fas fa-star fa-fw text-warning'></i><i class='fas fa-star fa-fw text-warning'></i>";
} else if ($prcnthit > 1000) {
    $hitratio .= "<i class='fas fa-star fa-fw text-warning'></i>";
} else {
    $hitratio = $LANG['m_ibconversion'] . ': ';
    $hitratio .= ($myrefonly > 0) ? number_format($prcnthit, 2) . '%' : '0%';
}

// ---

$condition = " AND (txfromid = '{$mbrstr['id']}' OR txtoid = '{$mbrstr['id']}') ";
$hostcalcarr = get_calcumount($mbrstr, $condition);

$refbon = $hostcalcarr['hist_refbonus'];
$sprbon = $hostcalcarr['hist_sprbonus'];
$rwdbon = $hostcalcarr['hist_rwdbonus'];
$mypaymn = $hostcalcarr['hist_mypaymn'];
$renewfee = $hostcalcarr['hist_renewfee'];
$reqwdrwait = $hostcalcarr['hist_reqwdrwait'];

$reqwdrdone = $hostcalcarr['hist_reqwdrdone'];
$feewdr = $hostcalcarr['hist_feewdr'];
$waletout = $hostcalcarr['hist_waletout'];

$mytxintotal = $hostcalcarr['hist_earning'];
$mytxouttotal = $reqwdrdone + $feewdr + $waletout;
$mydiftrx = $hostcalcarr['hist_pending'];
$mytottrx = $hostcalcarr['hist_tot'];
$mytxwallet = $hostcalcarr['hist_ewallet'];
// ---

$mbrimgstr = ($mbrstr['mbr_image']) ? $mbrstr['mbr_image'] : $cfgrow['mbr_defaultimage'];

switch ($mbrstr['mbrstatus']) {
    case "1":
        $regbadge_class = "badge-success";
        $regbadge_text = $LANG['g_active'];
        break;
    case "2":
        $regbadge_class = "badge-warning";
        $regbadge_text = $LANG['g_limited'];
        break;
    case "3":
        $regbadge_class = "badge-danger";
        $regbadge_text = $LANG['g_pending'];
        break;
    default:
        $regbadge_class = "badge-secondary";
        $regbadge_text = $LANG['g_inactive'];
}
$myregstatus = "<div class='badge {$regbadge_class}'>{$regbadge_text}</div>";

if (intval($mbrstr['mpid']) > 0) {
    $myplanpay = '';
    switch ($mbrstr['mpstatus']) {
        case "1":
            $badge_class = "badge-success";
            $badge_text = $LANG['g_active'];
            break;
        case "2":
            $badge_class = "badge-warning";
            $badge_text = $LANG['g_expire'];
            break;
        case "3":
            $badge_class = "badge-danger";
            $badge_text = $LANG['g_pending'];
            break;
        default:
            $badge_class = "badge-secondary";
            $badge_text = $LANG['g_inactive'];
    }
    $myplanstatus = "<div class='badge {$badge_class}'>{$badge_text}</div>" . $myplanpay;
    $reg_date = formatdate($mbrstr['reg_date']);
    $regmbrsince = "<span class='text-muted'>{$LANG['m_registeredsince']}</span> {$reg_date}";
} else {
    $myplanstatus = "<a href='#planreg' class='btn btn-danger btn-round'>{$LANG['g_register']}</a>";
    $regmbrsince = '';
}

// ---

$sprstr = getmbrinfo($mbrstr['idspr']);
$sprstr['fullname'] = $sprstr['firstname'] . ' ' . $sprstr['lastname'];
$sprimgstr = ($sprstr['mbr_image']) ? $sprstr['mbr_image'] : $cfgrow['mbr_defaultimage'];
$spremailstr = (strlen($sprstr['email']) > 23) ? substr($sprstr['email'], 0, 20) . '...' : $sprstr['email'];
$sprphonestr = ($sprstr['phone']) ? $sprstr['phone'] : '-';
$sprcountrystr = ucwords(strtolower($country_array[$sprstr['country']]));
$sprstatusstr = badgembrplanstatus($sprstr['mbrstatus'], $sprstr['mpstatus'], $bpparr[$sprstr['mppid']]['ppname']);
$spraboutstr = ($sprstr['mbr_intro']) ? "<blockquote class='text-small'>" . base64_decode($sprstr['mbr_intro']) . "</blockquote>" : '';

// ---

$recentrefl = '';
$condition = " AND sprlist LIKE '%:{$mbrstr['mpid']}|%' AND mppid = '{$mbrstr['mppid']}' AND id != {$mbrstr['id']}";
$userData = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_mbrs LEFT JOIN " . DB_TBLPREFIX . "_mbrplans ON id = idmbr WHERE 1 " . $condition . " ORDER BY mpid DESC LIMIT 9");
if (count($userData) > 0) {
    foreach ($userData as $val) {
        $sestime = strtotime($val['reg_utctime']);
        $timejoin = time_since($sestime);
        $dlnimgfile = ($val['mbr_image']) ? $val['mbr_image'] : $cfgrow['mbr_defaultimage'];
        $val['fullname'] = $val['firstname'] . ' ' . $val['lastname'];
        $stremail = (strlen($val['email']) > 24) ? substr($val['email'], 0, 21) . '...' : $val['email'];
        $recentrefl .= "<li class='media'>
                            <img class='mr-3 rounded-circle' width='48' src='{$dlnimgfile}' alt='avatar'>
                            <div class='media-body'>
                                <div class='float-right text-small text-success'>{$timejoin} {$LANG['g_timeago']}</div>
                                <div class='media-title'>{$val['username']}</div>
                                <span class='text-small text-muted'><div>{$val['fullname']}</div><div data-toggle='tooltip' title='{$val['email']}'>{$stremail}</div></span>
                            </div>
                       </li>";
    }
} else {
    $recentrefl = '<div class="text-center mt-4 text-muted">
                        <div>
                            <i class="fa fa-3x fa-question-circle"></i>
                        </div>
                        <div>' . $LANG['g_norecordinfo'] . '</div>
                   </div>';
}

$expdatestr = ($mbrstr['reg_expd'] > $mbrstr['reg_date']) ? $LANG['m_expiration'] . ': ' . formatdate($mbrstr['reg_expd']) : '';
$istrial = get_optionvals($mbrstr['mptoken'], 'istrial');

$mysiteurl = $mbrstr['reflink'];
if ($cfgtoken['isrefqrcode'] == '1' && $mysiteurl != '') {
    // or use https://chart.apis.google.com/ instead
    $google_chart_api_url = "https://chart.googleapis.com/chart?chs={$cfgrow['mbrmax_image_width']}x{$cfgrow['mbrmax_image_width']}&cht=qr&chl=" . urlencode($mysiteurl) . "&choe=UTF-8";
    $mysiteurlqr = "<img class='mr-3 rounded-circle img-fluid' src='" . $google_chart_api_url . "' alt=''>";
} else {
    $mysiteurlqr = '';
}
?>

<div class="section-header">
    <h1><i class="fa fa-fw fa-chart-line"></i> <?php echo myvalidate($LANG['g_dashboardtitle']); ?></h1>
</div>

<div class="section-body">
    <div class="row">
        <div class="col-lg-4 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-info">
                    <i class="far fa-paper-plane"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4><?php echo myvalidate($LANG['g_hits']); ?></h4>
                    </div>
                    <div class="card-body">
                        <?php echo myvalidate($mbrstr['hits']); ?>
                        <div class="text-small text-muted">
                            <?php echo myvalidate($hitratio); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <a href="index.php?hal=userlist&clist=1&clisti=1">
                    <div class="card-icon bg-info">
                        <i class="far fa-handshake"></i>
                    </div>
                </a>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4><?php echo myvalidate($LANG['g_referrals']); ?></h4>
                    </div>
                    <div class="card-body">
                        <?php echo myvalidate($myreftotal); ?>
                        <div class="text-small text-muted">
                            <?php echo myvalidate($LANG['m_ibpersonal']); ?>: <?php echo myvalidate($myrefonly); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <a href="index.php?hal=historylist&dohal=clear">
                    <div class="card-icon bg-warning">
                        <i class="far fa-money-bill-alt"></i>
                    </div>
                </a>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4><?php echo myvalidate($LANG['g_earning']); ?></h4>
                    </div>
                    <div class="card-body">
                        <?php echo myvalidate($bpprow['currencysym'] . $myincometotal); ?>
                        <div class="text-small text-muted">
                            <?php echo myvalidate($LANG['m_ibwallet']); ?>: <?php echo myvalidate($bpprow['currencysym'] . $myewallet); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-8 col-md-12 col-12 col-sm-12">
            <?php
            $unpaidtxid = get_unpaidtxid($mbrstr);
            $myplanstatusbtn = ($unpaidtxid > 0) ? "<a href='index.php?hal=planpay' class='btn btn-danger btn-round'>{$LANG['m_makepayment']}</a>" : $myplanstatus;

            if (intval($mbrstr['mpid']) < 1 || intval($mbrstr['mpstatus']) != 1 || $unpaidtxid > 0) {
                ?>
                <div class="alert alert-light alert-has-icon">
                    <div class="alert-icon text-danger"><i class="far fa-bell"></i></div>
                    <div class="alert-body text-danger">
                        <div class="alert-title"><?php echo myvalidate($LANG['m_notice']); ?></div>
                        <?php
                        if (intval($mbrstr['mpid']) < 1) {
                            echo $LANG['m_noticereg'] . " <strong>any program</strong>.";
                        } elseif (intval($mbrstr['mpstatus']) != 1) {
                            if ($unpaidtxid > 0) {
                                echo $LANG['m_noticepay'];
                            } else {
                                echo $LANG['m_noticeadm'];
                                $myplanstatus = '';
                                $myplanstatusbtn = "<a href='index.php?hal=feedback' class='btn btn-info'>{$LANG['m_contactus']}</a>";
                            }
                        } elseif ($unpaidtxid > 0) {
                            echo $LANG['m_noticerepay'];
                        }
                        ?>
                        <div class="float-right mt-4">
                            <?php echo myvalidate($myplanstatusbtn); ?>
                        </div>
                    </div>
                </div>
                <?php
            }
            ?>

            <!-- Overview -->
            <div class="card">
                <div class="card-header">
                    <h4><?php echo myvalidate($LANG['g_accoverview']); ?></h4>
                    <div class="card-header-action">
                        <?php echo myvalidate($myregstatus); ?>
                    </div>
                </div>
                <div class="card-body">
                    <div class="summary-item">
                        <ul class="list-unstyled list-unstyled-border">
                            <li class="media">
                                <div class="avatar-item">
                                    <img class='mr-3 rounded-circle img-fluid' width='<?php echo myvalidate($cfgrow['mbrmax_image_width']); ?>' height='<?php echo myvalidate($cfgrow['mbrmax_image_height']); ?>' src='<?php echo myvalidate($mbrimgstr); ?>' alt='<?php echo myvalidate($mbrstr['username']); ?>'>
                                    <?php
                                    if (strpos($mbrimgstr, 'mbr_defaultimage') !== false) {
                                        ?>
                                        <div class="avatar-badge" title="Update" data-toggle="tooltip"><a href='index.php?hal=accountcfg'><i class="fas fa-wrench text-secondary"></i></a></div>
                                        <?php
                                    }
                                    ?>
                                </div>
                                <div class="media-body">
                                    <div class="media-title">
                                        <div class="float-right"><?php echo myvalidate($mysiteurlqr); ?></div>
                                    </div>
                                </div>
                            </li>
                            <li class="media">
                                <div class="media-body">
                                    <div class="text-small"><?php echo myvalidate($LANG['g_registered']); ?></div>
                                    <div class="media-title"><?php echo formatdate($mbrstr['in_date']); ?></div>
                                </div>
                            </li>
                            <li class="media">
                                <div class="media-body">
                                    <div class="text-small"><?php echo myvalidate($LANG['g_name']); ?></div>
                                    <div class="media-title"><?php echo myvalidate($mbrstr['fullname'] . ' (' . $mbrstr['email'] . ')'); ?></div>
                                </div>
                            </li>
                            <?php
                            if ($mysiteurl != '') {
                                ?>
                                <li class="media">
                                    <div class="media-body">
                                        <div class="text-small"><?php echo myvalidate($LANG['g_refurl']); ?> <a href="<?php echo myvalidate($mysiteurl); ?>" target="_blank" class="d-sm-none" data-toggle="tooltip" title="<?php echo myvalidate($mysiteurl); ?>"><span class="text-small"><i class="fa fa-fw fa-external-link-alt"></i></span></a></div>
                                        <div class="media-title">
                                            <a class="d-none d-sm-block" href="<?php echo myvalidate($mysiteurl); ?>" target="_blank" data-toggle="tooltip" title="<?php echo myvalidate($mysiteurl); ?>">
                                                <?php echo myvalidate($mysiteurl); ?>
                                            </a>
                                            <div class="d-sm-none">
                                                <div class="form-group">
                                                    <div class="input-group mb-3">
                                                        <input type="text" class="form-control form-control-sm" value="<?php echo myvalidate($mysiteurl); ?>" id="myrefurlid" readonly>
                                                        <div class="input-group-append">
                                                            <button class="btn btn-primary btn-sm" type="button" onclick="copyInputText('myrefurlid')"><i class="fa fa-copy fa-fw"></i></button>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <?php
                            }
                            ?>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Performance -->
            <?php
            if (intval($mbrstr['mpstatus']) == 1) {
                ?>
                <div class="card">
                    <div class="card-header">
                        <h4><?php echo myvalidate($LANG['g_performance']); ?></h4>
                    </div>
                    <div class="card-body">
                        <canvas id="myChart" height="182"></canvas>
                    </div>
                </div>
                <?php
            }
            ?>

            <!-- Membership -->
            <div class="card">
                <div class="card-header">
                    <h4><?php echo myvalidate($LANG['g_membership']); ?></h4>
                    <div class="card-header-action">
                        <?php
                        if ($mbrstr['mpid'] > 0) {
                            echo myvalidate($myplanstatus);
                        }
                        ?>
                    </div>
                </div>
                <div class="card-body">
                    <div class="summary">
                        <div class="summary-info">
                            <h4><span class="text-success"><i class="fas fa-caret-up"></i></span><?php echo myvalidate($bpprow['currencysym'] . $mytxintotal); ?> <span class="text-danger"><i class="fas fa-caret-down"></i></span><?php echo myvalidate($bpprow['currencysym'] . $mytxouttotal); ?> <small><span class="text-warning"><i class="far fa-pause-circle"></i></span><?php echo myvalidate($bpprow['currencysym'] . $mydiftrx); ?></small></h4>
                            <div class="text-muted"><?php echo myvalidate($LANG['m_totalhistory'] . ': ' . $mytottrx); ?></div>
                            <h3 class="mt-2"><span class="text-info"><i class="fas fa-wallet"></i></span><?php echo myvalidate($bpprow['currencysym'] . $mytxwallet . ' ' . $bpprow['currencycode']); ?></h3>
                            <div class="d-block mt-2">
                                <a href="index.php?hal=historylist"><?php echo myvalidate($LANG['m_detailhistory']); ?></a>
                            </div>
                        </div>
                        <div class="summary-item">
                            <a name='planreg'></a>

                            <h6><?php echo myvalidate($regmbrsince); ?></h6>
                            <ul class="list-unstyled list-unstyled-border">

                                <?php
                                $mbrpplistarr = mbrpparr($mbrstr['id']);
                                $isregppidarr = do_reginorder($mbrstr);

                                // display registered payplan only
                                $condition = " AND ppid = '{$mbrstr['mppid']}'";
                                // display all available payplan
                                $condition = " AND planstatus = '1'";
                                $nextbtndisable = '';
                                $isregbtnexist = '';
                                $condition .= " ORDER BY ppid ASC";

                                $minmbrppid = min($mbrstr['pparr_all']);

                                $userData = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_payplans WHERE 1" . $condition . "");
                                if (count($userData) > 0) {
                                    foreach ($userData as $val) {
                                        $mbrplanlogo = ($val['planlogo']) ? $val['planlogo'] : DEFIMG_PLAN;
                                        $mbrppstr = $mbrpplistarr[$val['ppid']];
                                        $expdatestr = ($mbrppstr['reg_expd'] > $mbrppstr['reg_date']) ? $LANG['m_expiration'] . ': ' . formatdate($mbrppstr['reg_expd']) : '';

                                        $strregfree = $bpprow['currencysym'] . $val['regfee'] . ' ' . $bpprow['currencycode'];
                                        $reglinkbtn = "<a href='index.php?hal=planreg&toppid={$val['ppid']}' class='btn btn-primary btn-round btn-sm'>{$LANG['g_register']}</a>";
                                        $istrial = get_optionvals($mbrppstr['mptoken'], 'istrial');

                                        if ($val['ppid'] < $minmbrppid) {
                                            if ($frlmtdcfg['isreginorder'] == 0 || $frlmtdcfg['isxplans'] == 1) {
                                                $myplanstatus = ($unpaidtxid > 0) ? '' : $reglinkbtn;
                                            } else {
                                                $myplanstatus = ($unpaidtxid > 0) ? '' : "<i class='fas fa-ban'></i>";
                                                $strregfree = '';
                                            }
                                        } else if (in_array($val['ppid'], $mbrstr['pparr_all'])) {
                                            $myplanpay = $regppsince = '';
                                            switch ($mbrppstr['mpstatus']) {
                                                case "1":
                                                    $badge_class = "badge-success";
                                                    $badge_text = $LANG['g_active'];
                                                    $reg_date = formatdate($mbrppstr['reg_date']);
                                                    $regppsince = "<span class='text-muted'>{$LANG['m_registerppsince']}</span> {$reg_date}";
                                                    break;
                                                case "2":
                                                    $badge_class = "badge-warning";
                                                    $badge_text = $LANG['g_expire'];
                                                    break;
                                                case "3":
                                                    $badge_class = "badge-danger";
                                                    $badge_text = $LANG['g_pending'];
                                                    break;
                                                default:
                                                    $badge_class = "badge-primary";
                                                    $badge_text = "";
                                                    $myplanpay = ($mbrppstr['mppid'] == $val['ppid']) ? $myplanstatusbtn : $bpprow['currencysym'] . $val['regfee'] . ' ' . $bpprow['currencycode'];
                                            }

                                            $myplanstatus = "<div class='badge {$badge_class}'>{$badge_text}</div>" . $myplanpay;
                                            $strregfree = '';
                                        } else if ($mbrppstr['mpid'] < 1 || $isregppidarr['upnextid'] <= $val['ppid']) {
                                            $myplanstatus = (in_array($val['ppid'], $isregppidarr) && $val['planstatus'] == 1 && $mbrppstr['mpstatus'] != 1) ? $reglinkbtn : "<i class='fas fa-times text-danger'></i>";

                                            if ($frlmtdcfg['isxplans'] == 1 && $mbrppstr['mpstatus'] != 1 && $isregbtnexist != 1 && $myplanstatus == '') {
                                                $myplanstatus = $reglinkbtn;
                                                $isregbtnexist = 1;
                                            }

                                            if ($unpaidtxid > 0) {
                                                $myplanstatus = '';
                                            }

                                            $regppsince = '';
                                        } else {
                                            $myplanstatus = $regppsince = $strregfree = '';
                                        }
                                        ?>

                                        <li class="media">
                                            <img class="mr-3 rounded" width="50" src="<?php echo myvalidate($mbrplanlogo); ?>" alt="Membership">
                                            <div class="media-body">
                                                <div class="media-right text-right">
                                                    <?php echo myvalidate($strregfree); ?>
                                                    <div><?php echo myvalidate($myplanstatus); ?></div>
                                                </div>
                                                <div class="media-title"><?php echo myvalidate($val['ppname']); ?></div>
                                                <h6>
                                                    <?php echo myvalidate($regppsince); ?>
                                                    <div>
                                                        <span class="text-small">
                                                            <?php
                                                            if ($val['expday'] > 0) {
                                                                if ($mbrppstr['reg_expd'] < $cfgrow['datestr']) {
                                                                    ?>
                                                                    <span class="badge badge-danger"><?php echo myvalidate($expdatestr); ?></span>
                                                                    <?php
                                                                } else {
                                                                    $renewHash = md5($mbrppstr['mpid'] . $mbrppstr['reg_expd']);
                                                                    ?>
                                                                    <span class="badge badge-info"><?php echo myvalidate($expdatestr); ?></span>
                                                                    <?php
                                                                    if ($cfgtoken['isadvrenew'] == '1' && intval($bpparr[$mbrppstr['mppid']]['expday']) > 0 && $unpaidtxid < 1 && $mbrppstr['reg_expd'] < $cfgrow['datestr']) {
                                                                        ?>
                                                                        <span class="float-md-right">
                                                                            <a href="javascript:;" data-href="index.php?hal=dashboard&renewHash=<?php echo myvalidate($renewHash); ?>&renewId=<?php echo myvalidate($mbrppstr['mpid']); ?>&redir=planpay" class="btn btn-sm btn-info bootboxconfirm" data-poptitle="Current Expiration: <?php echo formatdate($mbrppstr['reg_expd']); ?>" data-popmsg="Are you sure want to renew your membership in advance?" data-toggle="tooltip" title="Manual renewal for <?php echo myvalidate($mbrppstr['username']); ?>"><i class="fa fa-fw fa-sync-alt"></i> Renew</a>
                                                                        </span>
                                                                        <?php
                                                                    }
                                                                }
                                                                if ($istrial > 0) {
                                                                    ?>
                                                                    <span class="badge badge-danger">Trial</span>
                                                                    <?php
                                                                }
                                                            }
                                                            ?>
                                                        </span>
                                                    </div>
                                                </h6>
                                                <div class="text-muted text-small"><?php echo myvalidate($val['planinfo']); ?></div>
                                            </div>
                                        </li>
                                        <?php
                                    }
                                }
                                ?>

                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-12 col-12 col-sm-12">
            <?php
            if ($mbrstr['idspr'] > 0) {
                ?>
                <div class="card">
                    <div class="card-header">
                        <h4><?php echo myvalidate($LANG['g_mysponsor']); ?></h4>
                        <div class="card-header-action">
                            <?php echo myvalidate($sprstatusstr); ?>
                        </div>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled list-unstyled-border">
                            <li class='media'>
                                <img class='mr-3 rounded-circle' width='48' src='<?php echo myvalidate($sprimgstr); ?>' alt='avatar'>
                                <div class='media-body'>
                                    <div class='float-right text-small text-success'></div>
                                    <div class='media-title'><?php echo myvalidate($sprstr['username']); ?></div>
                                    <span class='text-small text-muted'>
                                        <div><?php echo myvalidate($sprstr['fullname']); ?></div>
                                        <div data-toggle='tooltip' title='<?php echo myvalidate($sprstr['email']); ?>'><i class="fa fa-fw fa-envelope"></i> <?php echo myvalidate($spremailstr); ?></div>
                                        <div><i class="fa fa-fw fa-mobile-alt"></i> <?php echo myvalidate($sprphonestr); ?></div>
                                        <div><?php echo myvalidate($sprcountrystr); ?></div>
                                    </span>
                                </div>
                            </li>
                        </ul>
                        <div><?php echo myvalidate($spraboutstr); ?></div>
                    </div>
                </div>
                <?php
            }
            ?>

            <div class="card">
                <div class="card-header">
                    <h4><?php echo myvalidate($LANG['g_recentref']); ?></h4>
                    <div class="card-header-action">
                        <a href="index.php?hal=userlist" class="btn btn-primary" data-toggle="tooltip" title="View All"><i class="fa fa-ellipsis-h"></i></a>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled list-unstyled-border">
                        <?php echo myvalidate($recentrefl); ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Template JS File -->
<script src="../assets/js/chart.min.js"></script>

<!-- Page Specific JS File -->
<script src="../assets/js/ucpchart.js"></script>

