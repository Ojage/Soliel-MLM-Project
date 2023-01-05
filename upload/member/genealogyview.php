<?php
if (!defined('OK_LOADME')) {
    die('o o p s !');
}

$clistnow = ($_SESSION['clisti'] > 0) ? $_SESSION['clisti'] : ' ' . $LANG['g_membership'];
$clistid = ($_SESSION['clistview'] > 0) ? $_SESSION['clistview'] : $mbrstr['mpid'];
$genmpid = ($FORM['loadId']) ? $FORM['loadId'] : $clistid;

$mbrgenvstr = getmbrinfo('', '', $genmpid);

if (isset($FORM['clist']) && intval($FORM['clist']) > 0) {
    $_SESSION['clisti'] = intval($FORM['clisti']);
    $_SESSION['clistview'] = intval($FORM['clist']);
    redirpageto('index.php?hal=genealogyview');
    exit;
}

// redirect and reset if if ppid/mppid is not registered by member
$flterppidnow = $mbrgenvstr['mppid'];
if ($FORM['dohal'] == 'clear' || ($flterppidnow != '' && !in_array($flterppidnow, $mbrstr['pparr_all']))) {
    $_SESSION['showFltr'] = $_SESSION['clistview'] = $_SESSION['clisti'] = '';
    redirpageto('index.php?hal=genealogyview');
    exit;
}

$icyc = 0;
$userData = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_mbrplans WHERE 1 AND idmbr = '{$mbrgenvstr['id']}' ORDER BY mpid");
if (count($userData) > 0) {
    foreach ($userData as $val) {
        $icyc++;
        $ismarked = ($_SESSION['clisti'] == $icyc) ? ' &#10003;' : '';
        $isusercyc = ($val['cyclingbyid'] > 0) ? ' &#x269F;' : '';
        $plancycname = ($frlmtdcfg['isxplans'] == 1) ? $bpparr[$val['mppid']]['ppname'] . $isusercyc : $LANG['g_cycle'] . ' ' . $icyc;
        $optviewcycle .= '<a class="dropdown-item" href="index.php?hal=genealogyview&clist=' . $val['mpid'] . '&clisti=' . $icyc . '">' . strtoupper($mbrgenvstr['username']) . ' - ' . $plancycname . $ismarked . '</a>';
    }
}

if ($frlmtdcfg['isxplans'] == 1) {
    $isusercyc = ($mbrgenvstr['cyclingbyid'] > 0) ? ' &#x269F;' : '';
    $plancycname = $bpparr[$mbrgenvstr['mppid']]['ppname'] . $isusercyc;
    $plancycstr = $LANG['g_plan'];
} else {
    $plancycname = $clistnow;
    $plancycstr = $LANG['g_cycle'];
}

$_SESSION['showFltr'] = $FORM['showFltr'] = ($FORM['showFltr'] != '') ? $FORM['showFltr'] : $_SESSION['showFltr'];
$statusmbrsopt = '';
$statusmbrsarr = array('0' => $LANG['g_all'], '1' => $LANG['g_enhanced']);
foreach ($statusmbrsarr as $key => $value) {
    $btnselcolor = ($FORM['showFltr'] == $key) ? 'success' : 'secondary';
    $statusmbrsopt .= "<a href='index.php?hal=genealogyview&showFltr={$key}' class='btn btn-{$btnselcolor}'>{$value}</a>";
}
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
                    <h4><?php echo myvalidate($LANG['m_membergenealogy']); ?></h4>
                    <div class="card-header-action">
                        <a href='index.php?hal=genealogyview&dohal=clear' class='btn btn-warning'><i class="fa fa-fw fa-redo"></i> <?php echo myvalidate($LANG['g_reset']); ?></a>
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
                        <div class="btn-group">
                            <?php echo myvalidate($statusmbrsopt); ?>
                        </div>
                    </div>
                </div>
                <div class="card-body">

                    <div class="genchart" id="genviewer"></div>

                </div>
            </div>
        </div>
    </div>
</div>

<script src="../assets/fellow/treant/raphael.js"></script>
<script src="../assets/fellow/treant/Treant.js"></script>
<script src="../assets/fellow/treant/jquery.mousewheel.js"></script>
<script src="../assets/fellow/treant/perfect-scrollbar.js"></script>
<script src="loadgenview.php?loadId=<?php echo myvalidate($genmpid); ?>&showFltr=<?php echo myvalidate($FORM['showFltr']); ?>"></script>

<script type="text/javascript">
    new Treant(chart_config);
</script>
