<?php
if (!defined('OK_LOADME')) {
    die('o o p s !');
}

$didId = intval($FORM['toppid']);
$bpprow = ppdbplan($didId);
$planimg = $bpprow['planimg'];

$isregppidarr = do_reginorder($mbrstr);
if ($frlmtdcfg['isreginorder'] == 0 || in_array($didId, $isregppidarr)) {
    $mbrpplistarr = mbrpparr($mbrstr['id']);
    $mbrppstr = $mbrpplistarr[$didId];
} else {
    $mbrppstr = $mbrstr;
}

$newsprstr = '';
if ($FORM['myrefun'] != '') {
    // custom referrer username
    $refstr = getmbrinfo($FORM['myrefun'], 'username');
    $refidmbr = $refstr['id'];
} else {
    // default referrer username from existing referrer or from session tracking id
    $refstr = getmbrinfo($mbrstr['idref']);
    $refidmbr = ($refstr['mpid'] > 0) ? $refstr['id'] : $sesref['id'];
}

// disable self referring
if ($refstr['username'] == $mbrstr['username']) {
    $refidmbr = 0;
}

$sesref = getmbrinfo($refidmbr, '', '', $bpprow['ppid']);
$refmpid = $sesref['mpid'];

$ceknewmpid = getmpidflow($refmpid, $didId, $mbrstr);
if ($ceknewmpid != $refmpid) {
    $sesnewref = getmbrinfo('', '', $ceknewmpid);
    $newsprstr = "<blockquote class='text-primary text-left'>You were referred by <strong>{$sesref['username']}</strong> who has a maximum number of referrals. The system has assigned <strong>{$sesnewref['username']}</strong> as your new sponsor. This may be subject to change depending on the time when you complete the payment for this registration process.</blockquote>";
    $idref = $sesnewref['id'];
}

if ($bpprow['planstatus'] == 1 && $FORM['doid'] > 0) {

    if ($frlmtdcfg['isxplans'] == 1) {
        //--- register to new plan (register with multiple plans)
        $resultarr = regmbrplans($mbrstr, $refidmbr, $bpprow['ppid']);
    } else {
        //--- update from current plan to new plan (one plan at a time)
        $resultarr = regmbrplans($mbrstr, $refidmbr, $bpprow['ppid'], $mbrstr['mpid']);
    }
    redirpageto('index.php?hal=planpay&didId=' . $bpprow['ppid']);
    exit;
}
?>

<div class="section-header">
    <h1><i class="fa fa-fw fa-unlock-alt"></i> <?php echo myvalidate($LANG['m_planreg']); ?></h1>
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
                            <?php echo myvalidate($bpprow['currencysym'] . $bpprow['regfee'] . ' ' . $bpprow['currencycode']); ?>
                        </span>
                        <?php
                        if ($sesref['username'] && $mbrstr['username'] != $sesref['username']) {
                            ?>
                            <span class="article-badge-item bg-warning">
                                Referred by <?php echo ($mbrppstr['mpid'] > 0) ? myvalidate($mbrstr['username']) : myvalidate($sesref['username']); ?>
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
                        <?php echo myvalidate($newsprstr); ?>

                        <?php
                        if ($mbrppstr['idmbr'] == $mbrstr['id'] && $mbrppstr['mpstatus'] > 0) {
                            ?>
                            <span class="badge badge-secondary">
                                REGISTERED
                            </span>
                            <?php
                            if ($mbrppstr['mpstatus'] == 1) {
                                ?>
                                <span class="badge badge-success">
                                    ACTIVE <i class="fas fa-fw fa-check"></i>
                                </span>
                                <?php
                            } else {
                                ?>
                                <span class="badge badge-warning">
                                    INACTIVE <i class="fas fa-fw fa-exclamation"></i>
                                </span>
                                <?php
                            }
                            ?>
                            <?php
                        } elseif ($mbrppstr['idmbr'] == $mbrstr['id'] && $mbrppstr['mpstatus'] == 0) {
                            ?>
                            <a href="index.php?hal=planpay&doid=<?php echo myvalidate($bpprow['ppid']); ?>" class="btn btn-lg btn-danger">MAKE PAYMENT <i class="fas fa-fw fa-long-arrow-alt-right"></i></a>
                            <?php
                        } else {
                            if ($bpprow['planstatus'] == 1) {
                                $refbystr = ($sesref['username']) ? "Your default referrer is <strong>{$sesref['username']}</strong><br />" : '';
                                $refbystr .= ($sesnewref['username']) ? "Your assigned sponsor is <strong>{$sesnewref['username']}</strong><br />" : '';
                                if ($sesref['username'] == '' || $sesref['username'] == $mbrstr['username']) {
                                    $refbystr = '';
                                }
                                ?>
                                <div class="form-group">
                                    <form method="GET" id="doregform">
                                        <div class="input-group">
                                            <?php
                                            if ($mbrstr['mpstatus'] < 1 || $sesref['username'] == '' || $frlmtdcfg['isregallrefs'] == 1) {
                                                ?>
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Referrer Username</span>
                                                </div>
                                                <input type="text" class="form-control" name="myrefun" placeholder="Enter referrer username here or leave empty to default system">
                                                <div class="input-group-append">
                                                    <?php
                                                } else {
                                                    ?>
                                                    <div class="text-right">
                                                        <?php
                                                    }
                                                    ?>
                                                    <button class="btn btn-primary bootboxformconfirm" data-form="doregform" data-poptitle="<?php echo myvalidate($bpprow['ppname']); ?> - <?php echo myvalidate($bpprow['currencysym'] . $bpprow['regfee'] . ' ' . $bpprow['currencycode']); ?>" data-popmsg="<?php echo myvalidate($refbystr); ?><p>Are you sure want to register to this membership?</p><div class='text-info'><strong>Note!</strong> this process cannot be reversed! After clicking the Confirm button you need to complete the registration process, otherwise, your membership will remain inactive.</div>" type="submit">
                                                        REGISTER <i class="fas fa-fw fa-long-arrow-alt-right"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <input type="hidden" name="hal" value="planreg">
                                            <input type="hidden" name="doid" value="<?php echo myvalidate($bpprow['ppid']); ?>">
                                            <input type="hidden" name="toppid" value="<?php echo myvalidate($bpprow['ppid']); ?>">
                                            </form>
                                        </div>
                                        <?php
                                    } else {
                                        ?>
                                        <span class="badge badge-danger"><i class="fa fa-fw fa-times"></i> REGISTRATION DISABLE</span>
                                        <?php
                                    }
                                }
                                ?>

                        </div>
                    </div>
            </article>

        </div>
    </div>
</div>
