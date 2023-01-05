<?php
if (!defined('OK_LOADME')) {
    die('o o p s !');
}

if ($FORM['dohal'] == 'filter' && $FORM['dompid']) {
    $_SESSION['filteruid'] = $FORM['doval'];
    $_SESSION['filterupid'] = $FORM['dompid'];
}

$clistnow = ($_SESSION['clisti'] > 0) ? $_SESSION['clisti'] : ' ' . $LANG['g_membership'];
$clistid = ($_SESSION['clistview'] > 0) ? $_SESSION['clistview'] : $mbrstr['mpid'];

$mbrusrlstr = getmbrinfo('', '', $clistid);

if ($_SESSION['filterupid']) {
    $filterupid = intval($_SESSION['filterupid']);
    $filterusrstr = getmbrinfo('', '', $filterupid);
    $btnclorclear = 'btn-danger';
    $clearfilterusrstr = " filter for member ({$filterusrstr['username']})";
    $filterusrnow = " <h5><span class='badge badge-danger'>" . strtoupper($filterusrstr['username']) . "</span></h5>";
} else {
    $filterupid = intval($clistid);
    $filterusrstr = getmbrinfo('', '', $filterupid);
    $btnclorclear = 'btn-warning';
    $clearfilterusrstr = $filterusrnow = "";
}

$conditionflter = $condition = '';

if ($_SESSION['filterupid'] && strpos($filterusrstr['sprlist'], ":{$clistid}|") === false) {
    $_SESSION['filterupid'] = "";
    redirpageto('index.php?hal=userlist');
    exit;
} else {
    $conditionflter = " AND sprlist LIKE '%:{$filterupid}|%' ";
}

if (isset($FORM['name']) && $FORM['name'] != "") {
    $condition .= ' AND (firstname LIKE "%' . $FORM['name'] . '%" OR lastname LIKE "%' . $FORM['name'] . '%") ';
}
if (isset($FORM['username']) && $FORM['username'] != "") {
    $condition .= ' AND username LIKE "%' . $FORM['username'] . '%" ';
}
if (isset($FORM['email']) && $FORM['email'] != "") {
    $condition .= ' AND email LIKE "%' . $FORM['email'] . '%" ';
}

if (isset($FORM['clist']) && intval($FORM['clist']) > 0) {
    $_SESSION['clisti'] = intval($FORM['clisti']);
    $_SESSION['clistview'] = intval($FORM['clist']);
    redirpageto('index.php?hal=userlist');
    exit;
}

if (isset($FORM['vlist']) && $FORM['vlist'] != '') {
    $_SESSION['vlistview'] = ($FORM['vlist'] == 'all') ? '' : $FORM['vlist'];
    redirpageto('index.php?hal=userlist');
    exit;
}

// redirect and reset if if ppid/mppid is not registered by member
$flterppidnow = ($_SESSION['filterupid'] > 0) ? $_SESSION['filterupid'] : $mbrusrlstr['mppid'];
if ($FORM['dohal'] == 'clear' || ($flterppidnow != '' && !in_array($flterppidnow, $mbrstr['pparr_all']))) {
    $_SESSION['filteruid'] = $_SESSION['filterupid'] = $_SESSION['clistview'] = $_SESSION['clisti'] = '';
    redirpageto('index.php?hal=userlist');
    exit;
}

$optviewlevel = $optviewcycle = $viewlnow = $addviewsql = '';
if ($_SESSION['vlistview'] != '') {
    if ($_SESSION['vlistview'] == 'ref') {
        $addviewsql = " AND idref = '{$filterusrstr['id']}'";
        $viewlnow = 'Personal';
    } else {
        for ($i = 1; $i <= $bpprow['maxdepth']; $i++) {
            if ($_SESSION['vlistview'] == 'l' . $i) {
                $addviewsql = " AND sprlist LIKE '%|{$i}:{$filterupid}|%'";
                $viewlnow = 'Level ' . $i;
                break;
            }
        }
    }
} else {
    $condition .= $conditionflter;
}

$icyc = 0;
$userData = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_mbrplans WHERE 1 AND idmbr = '{$mbrusrlstr['id']}' ORDER BY mpid");
if (count($userData) > 0) {
    foreach ($userData as $val) {
        $icyc++;
        $ismarked = ($_SESSION['clisti'] == $icyc) ? ' &#10003;' : '';
        $isusercyc = ($val['cyclingbyid'] > 0) ? ' &#x269F;' : '';
        $plancycname = ($frlmtdcfg['isxplans'] == 1) ? $bpparr[$val['mppid']]['ppname'] . $isusercyc : $LANG['g_cycle'] . ' ' . $icyc;
        $optviewcycle .= '<a class="dropdown-item" href="index.php?hal=userlist&clist=' . $val['mpid'] . '&clisti=' . $icyc . '">' . strtoupper($mbrusrlstr['username']) . ' - ' . $plancycname . $ismarked . '</a>';
    }
}

if ($frlmtdcfg['isxplans'] == 1) {
    $isusercyc = ($mbrusrlstr['cyclingbyid'] > 0) ? ' &#x269F;' : '';
    $plancycname = $bpparr[$mbrusrlstr['mppid']]['ppname'] . $isusercyc;
    $plancycstr = $LANG['g_plan'];
} else {
    $plancycname = $clistnow;
    $plancycstr = $LANG['g_cycle'];
}

for ($i = 1; $i <= $bpprow['maxdepth']; $i++) {
    $ismarked = ($_SESSION['vlistview'] == 'l' . $i) ? ' &#10003;' : '';
    $optviewlevel .= '<a class="dropdown-item" href="index.php?hal=userlist&vlist=l' . $i . '">' . $LANG['g_referralonlevel'] . ' ' . $i . $ismarked . '</a>';
}

$tblshort_arr = array("in_date", "username", "email");
$tblshort = dborder_arr($tblshort_arr, $FORM['_stbel'], $FORM['_stype']);
if ($FORM['_stbel'] != '' && (in_array($FORM['_stbel'], $tblshort_arr))) {
    $sqlshort = ($FORM['_stype'] == 'up') ? " ORDER BY {$FORM['_stbel']} DESC " : " ORDER BY {$FORM['_stbel']} ASC ";
} else {
    $sqlshort = " ORDER BY id DESC ";
}

//Main queries
$sql = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_mbrs LEFT JOIN " . DB_TBLPREFIX . "_mbrplans ON id = idmbr WHERE 1 " . $condition . $addviewsql . "");
$pages->items_total = count($sql);
$pages->mid_range = 3;
$pages->paginate();

$userData = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_mbrs AS mbr LEFT JOIN " . DB_TBLPREFIX . "_mbrplans AS plan ON id = idmbr LEFT JOIN " . DB_TBLPREFIX . "_sessions AS ses ON mbr.id = ses.sesidmbr WHERE 1 " . $condition . $addviewsql . ' GROUP BY id ' . $sqlshort . $pages->limit . "");
?>

<div class="section-header">
    <h1><i class="fa fa-fw fa-users"></i> <?php echo myvalidate($LANG['g_referrallist']); ?></h1>
    <?php echo myvalidate($filterusrnow); ?>
</div>

<div class="section-body">

    <form method="get">
        <div class="card card-primary">
            <div class="card-header">
                <h4><i class="fa fa-fw fa-search"></i> <?php echo myvalidate($LANG['g_findreferral']); ?></h4>
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
                    <div class="dropdown">
                        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                            <?php echo myvalidate($LANG['m_viewlist']); ?> <span class="badge badge-light"><?php echo myvalidate($viewlnow); ?></span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="index.php?hal=userlist&vlist=all"><?php echo myvalidate($LANG['g_allreferral']); ?></a>
                            <a class="dropdown-item" href="index.php?hal=userlist&vlist=ref"><?php echo myvalidate($LANG['g_personalreferral']); ?></a>
                            <div class="dropdown-divider"></div>
                            <?php echo myvalidate($optviewlevel); ?>
                        </div>
                    </div>
                </div>

            </div>
            <div class="card-body">
                <div class="row">

                    <div class="col-sm-4">
                        <div class="form-group">
                            <label><?php echo myvalidate($LANG['g_firstname']); ?></label>
                            <input type="text" name="name" id="name" class="form-control" value="<?php echo isset($FORM['name']) ? $FORM['name'] : '' ?>" placeholder="<?php echo myvalidate($LANG['m_typerefname']); ?>">
                        </div>
                    </div>

                    <div class="col-sm-4">
                        <div class="form-group">
                            <label><?php echo myvalidate($LANG['g_username']); ?></label>
                            <input type="text" name="username" id="username" class="form-control" value="<?php echo isset($FORM['username']) ? $FORM['username'] : '' ?>" placeholder="<?php echo myvalidate($LANG['m_typerefusername']); ?>">
                        </div>
                    </div>

                    <div class="col-sm-4">
                        <div class="form-group">
                            <label><?php echo myvalidate($LANG['g_email']); ?></label>
                            <input type="email" name="email" id="useremail" class="form-control" value="<?php echo isset($FORM['email']) ? $FORM['email'] : '' ?>" placeholder="<?php echo myvalidate($LANG['m_typerefemail']); ?>">
                        </div>
                    </div>

                </div>
            </div>
            <div class="card-footer bg-whitesmoke">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="float-md-right">
                            <a href="index.php?hal=userlist&dohal=clear" class="btn <?php echo myvalidate($btnclorclear); ?>"><i class="fa fa-fw fa-redo"></i> <?php echo myvalidate($LANG['g_reset']); ?><?php echo myvalidate($clearfilterusrstr); ?></a>
                            <button type="submit" name="submit" value="search" id="submit" class="btn btn-primary"><i class="fa fa-fw fa-search"></i> <?php echo myvalidate($LANG['g_search']); ?></button>
                        </div>
                        <div class="d-block d-sm-none">
                            &nbsp;
                        </div>
                        <div>
                            <?php
                            if ($cfgtoken['isregbymbr'] == '1') {
                                ?>
                                <a href="javascript:;" data-href="adduser.php?redir=userlist&addref=<?php echo myvalidate($mbrusrlstr['username']); ?>" data-poptitle="<i class='fa fa-fw fa-plus-circle'></i> <?php echo myvalidate($LANG['g_addreferral']); ?>" class="openPopup btn btn-dark"><i class="fa fa-fw fa-user-plus"></i> <?php echo myvalidate($LANG['g_addreferral']); ?></a>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
        <input type="hidden" name="hal" value="userlist">
    </form>

    <hr>

    <div class="clearfix"></div>

    <div class="row marginTop">
        <div class="col-sm-12 paddingLeft pagerfwt">
            <?php if ($pages->items_total > 0) { ?>
                <div class="row">
                    <div class="col-md-7">
                        <?php echo myvalidate($pages->display_pages()); ?>
                    </div>
                    <div class="col-md-5 text-right">
                        <span class="d-none d-md-block">
                            <?php echo myvalidate($pages->display_items_per_page()); ?>
                            <?php echo myvalidate($pages->display_jump_menu()); ?>
                            <?php echo myvalidate($pages->items_total()); ?>
                        </span>
                    </div>
                <?php } ?>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="clearfix"></div>
    </div>

    <div class="clearfix"></div>

    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col" nowrap><?php echo myvalidate($tblshort['in_date']); ?><?php echo myvalidate($LANG['g_date']); ?></th>
                    <th scope="col" nowrap><?php echo myvalidate($tblshort['username']); ?><?php echo myvalidate($LANG['g_username']); ?></th>
                    <th scope="col" nowrap><?php echo myvalidate($tblshort['email']); ?><?php echo myvalidate($LANG['g_email']); ?></th>
                    <th scope="col" class="text-center"></th>
                    <th scope="col" class="text-center"><?php echo myvalidate($LANG['g_action']); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (count($userData) > 0) {
                    $pgnow = ($FORM['page'] > 1) ? $FORM['page'] - 1 : 0;
                    $s = ($FORM['ipp'] > 0) ? $pgnow * $FORM['ipp'] : $pgnow * $cfgrow['maxpage'];

                    $lastses = time() - (5 * 3600 * ($cfgrow['time_offset'] + 1));
                    foreach ($userData as $val) {
                        $s++;
                        $usernamestr = ($val['idref'] == $filterusrstr['id'] && $_SESSION['vlistview'] != 'ref') ? "<strong>{$val['username']}</strong>" : $val['username'];

                        $overview = "<label>Info</label><div>" . $val['adminfo'] . "</div>";
                        $mbrimgval = ($val['mbr_image']) ? $val['mbr_image'] : $cfgrow['mbr_defaultimage'];
                        $mbrimgvalstr = "<img alt='?' src='{$mbrimgval}'class='img-fluid float-left mr-3 rounded-circle' width='96'>";

                        $valmail = maskmail($val['email']);
                        $stremail = (strlen($valmail) > 24) ? substr($valmail, 0, 21) . '...' : $valmail;

                        $isuseron = ($val['sesid'] > 0 && $val['sestime'] > $lastses) ? "<span class='beep'></span>" : '';
                        ?>
                        <tr>

                            <th scope="row"><?php echo myvalidate($s); ?></th>
                            <td data-toggle="tooltip" title="<?php echo myvalidate($val['in_date']); ?>" nowrap><?php echo formatdate($val['in_date']); ?></td>
                            <td data-toggle='tooltip' title='<?php echo myvalidate($val['firstname']) . ' ' . myvalidate($val['lastname']); ?>'><?php echo myvalidate($isuseron . $usernamestr); ?></td>
                            <td data-toggle='tooltip' title='<?php echo myvalidate($valmail); ?>'><?php echo myvalidate($stremail); ?></td>
                            <td align="center" nowrap><?php echo badgembrplanstatus($val['mbrstatus'], $val['mpstatus'], $bpparr[$val['mppid']]['ppname'], $mbrimgval); ?></td>
                            <td align="center" nowrap>
                                <a href="javascript:;"
                                   class="btn btn-sm btn-secondary"
                                   data-html="true"
                                   data-toggle="popover"
                                   data-trigger="hover"
                                   data-placement="left" 
                                   title="<?php echo strtoupper($val['username']); ?>"
                                   data-content="<?php echo strtoupper($mbrimgvalstr); ?>
                                   ">
                                    <i class="far fa-fw fa-question-circle"></i>
                                </a>
                                <?php
                                // if referrer and inside of structure
                                if (strpos($val['sprlist'], ":{$filterupid}|") !== false) {
                                    ?>
                                    <a href="index.php?hal=getuser&getId=<?php echo myvalidate($val['id']); ?>&getMpid=<?php echo myvalidate($val['mpid']); ?>" class="btn btn-sm btn-info" data-toggle="tooltip" title="View <?php echo myvalidate($val['username']); ?>"><i class="far fa-fw fa-id-badge"></i></a>
                                    <?php
                                }
                                ?>

                            </td>

                        </tr>
                        <?php
                    }
                } else {
                    ?>
                    <tr>
                        <td colspan="6">
                            <div class="text-center mt-4 text-muted">
                                <div>
                                    <i class="fa fa-3x fa-question-circle"></i>
                                </div>
                                <div><?php echo myvalidate($LANG['g_norecordinfo']); ?></div>
                            </div>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
    </div>

    <div class="clearfix"></div>

    <div class="row marginTop">
        <div class="col-sm-12 paddingLeft pagerfwt">
            <?php if ($pages->items_total > 0) { ?>
                <div class="row">
                    <div class="col-md-7">
                        <?php echo myvalidate($pages->display_pages()); ?>
                    </div>
                    <div class="col-md-5 text-right">
                        <span class="d-none d-md-block">
                            <?php echo myvalidate($pages->display_items_per_page()); ?>
                            <?php echo myvalidate($pages->display_jump_menu()); ?>
                            <?php echo myvalidate($pages->items_total()); ?>
                        </span>
                    </div>
                <?php } ?>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="clearfix"></div>
    </div>

</div>
