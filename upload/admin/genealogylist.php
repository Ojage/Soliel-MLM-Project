<?php
if (!defined('OK_LOADME')) {
    die('o o p s !');
}

$topmbrsopt = '';
$topmbrs = array();
$row = $db->getAllRecords(DB_TBLPREFIX . '_mbrplans', '*', ' AND idspr = "0"');
foreach ($row as $value) {
    $mbrstr = getmbrinfo('', '', $value['mpid']);
    $isselected = ($FORM['loadId'] == $mbrstr['mpid']) ? ' selected' : '';
    $isusercyc = ($value['cyclingbyid'] > 0) ? ' &#x269F;' : '';
    $topmbrsopt .= "<option value='{$mbrstr['mpid']}'{$isselected}>{$mbrstr['id']}.{$mbrstr['mpid']}. {$mbrstr['username']} ({$mbrstr['firstname']} {$mbrstr['lastname']} - " . maskmail($mbrstr['email']) . "){$isusercyc}</option>";
}

$_SESSION['statusFltr'] = $FORM['statusFltr'] = ($FORM['statusFltr'] != '') ? $FORM['statusFltr'] : $_SESSION['statusFltr'];
$statusmbrsopt = '';
$statusmbrsarr = array('0' => $LANG['g_all'], '1' => $LANG['g_enhanced']);
foreach ($statusmbrsarr as $key => $value) {
    $isselected = ($FORM['statusFltr'] == $key) ? ' selected' : '';
    $statusmbrsopt .= "<option value='{$key}'{$isselected}>{$value}</option>";
}

$mbrstr = getmbrinfo('', '', $FORM['loadId']);
if ($mbrstr['idspr'] != 0) {
    $topmbrsopt .= "<option value='{$mbrstr['mpid']}' selected>{$mbrstr['id']}.{$mbrstr['mpid']}. {$mbrstr['firstname']} {$mbrstr['lastname']} ({$mbrstr['username']} - " . maskmail($mbrstr['email']) . ")</option>";
}

$clistnow = ($FORM['cyclist'] > 0) ? $FORM['cyclist'] : '';
$icyc = 0;
$optviewcycle = '';
$userData = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_mbrplans WHERE 1 AND idmbr = '{$mbrstr['id']}' ORDER BY mpid");
if (count($userData) > 0) {
    foreach ($userData as $val) {
        $icyc++;
        $ismarked = ($FORM['cyclist'] == $icyc) ? ' &#10003;' : '';
        $isusercyc = ($val['cyclingbyid'] > 0) ? ' &#x269F;' : '';
        $plancycname = ($frlmtdcfg['isxplans'] == 1) ? $bpparr[$val['mppid']]['ppname'] . $isusercyc : $LANG['g_cycle'] . ' ' . $icyc;
        $optviewcycle .= "<a class='dropdown-item' href='index.php?hal=genealogylist&loadId={$val['mpid']}&cyclist={$icyc}'>" . strtoupper($mbrstr['username']) . " - {$plancycname}{$ismarked}</a>";
    }
}

if ($frlmtdcfg['isxplans'] == 1) {
    $plancycname = $bpparr[$mbrstr['mppid']]['ppname'];
    $plancycstr = $LANG['g_plan'];
} else {
    $plancycname = $clistnow;
    $plancycstr = $LANG['g_cycle'];
}

$displaygen = ($mbrstr['id'] > 0) ? $displaygen = "<script type='text/javascript'>new Treant(chart_config);</script>" : '';
?>

<link rel="stylesheet" href="../assets/fellow/treant/Treant.css">
<link rel="stylesheet" href="../assets/fellow/treant/simple-scrollbar.css">
<link rel="stylesheet" href="../assets/fellow/treant/perfect-scrollbar.css">

<div class="section-header">
    <h1><i class="fa fa-fw fa-sitemap"></i> <?php echo myvalidate($LANG['m_genealogyview']); ?></h1>
</div>

<div class="section-body">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Member Genealogy</h4>
                    <div class="card-header-action">
                        <?php
                        if ($icyc > 1) {
                            ?>
                            <div class="dropdown">
                                <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">
                                    <?php echo myvalidate($plancycstr); ?> <span class="badge badge-light"><?php echo myvalidate($plancycname); ?></span>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <?php echo myvalidate($optviewcycle); ?>
                                </div>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                </div>
                <div class="card-body">
                    <form method="get" action="index.php">
                        <input type="hidden" name="hal" value="genealogylist">
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-prepend d-none d-md-block">
                                    <span class="input-group-text">Frontend Member</span>
                                </div>
                                <select name='loadId' class="custom-select" id="inputGroupSelect04">
                                    <option selected>-</option>
                                    <?php echo myvalidate($topmbrsopt); ?>
                                </select>
                                <select name='statusFltr' class="custom-select">
                                    <?php echo myvalidate($statusmbrsopt); ?>
                                </select>
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-primary" type="button">Load</button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="genchart" id="genviewer">
                        <div class="empty-state" data-height="400">
                            <div class="empty-state-icon bg-info">
                                <i class="fas fa-question"></i>
                            </div>
                            <h2><?php echo myvalidate($LANG['g_norecordgen']); ?></h2>
                            <p class="lead">
                                <?php echo myvalidate($LANG['g_norecordgeninfo']); ?>
                            </p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<style>
<?php
foreach ($bpparr as $val) {
    $ppnowid = $val['ppid'];
    $ppbdcolor = base64_decode(get_optionvals($val['plantoken'], 'ppbdcolor'));
    $ppbgcolor = base64_decode(get_optionvals($val['plantoken'], 'ppbgcolor'));
    $ppnowbdcolor = ($ppbdcolor != '') ? $ppbdcolor : '#999';
    $ppnowbgcolor = ($ppbgcolor != '') ? $ppbgcolor : '#EEE';
    echo ".ppmbr{$ppnowid}{border-color: {$ppnowbdcolor} !important;background-color: {$ppnowbgcolor} !important;} ";
}
?>
</style>

<script src="../assets/fellow/treant/raphael.js"></script>
<script src="../assets/fellow/treant/Treant.js"></script>
<script src="../assets/fellow/treant/jquery.mousewheel.js"></script>
<script src="../assets/fellow/treant/perfect-scrollbar.js"></script>
<script src="loadgentree.php?loadId=<?php echo myvalidate($FORM['loadId']); ?>&statusFltr=<?php echo myvalidate($FORM['statusFltr']); ?>"></script>

<?php echo myvalidate($displaygen); ?>