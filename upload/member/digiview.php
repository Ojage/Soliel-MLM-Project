<?php
if (!defined('OK_LOADME')) {
    die('o o p s !');
}

if (isset($FORM['pgid'])) {
    $pgid = mystriptag($FORM['pgid']);
    $row = $db->getAllRecords(DB_TBLPREFIX . "_pages", "*", " AND pgid = '{$pgid}' AND pgstatus = '1' AND (pglang = '' OR pglang = '{$mbrstr['mylang']}')");
    $pgcntrow = array();
    foreach ($row as $value) {
        $pgcntrow = array_merge($pgcntrow, $value);
    }

    if (!iscontentmbr($pgcntrow['pgavalon'], $pgcntrow['pgppids'], $mbrstr)) {
        $pgcntrow['pgtitle'] = "We couldn't find any data";
        $pgcntrow['pgsubtitle'] = $pgcntrow['pgcontent'] = '';
    } else {
        $pgcntrow['pgsubtitle'] = base64_decode($pgcntrow['pgsubtitle']);
        $pgcntrow['pgcontent'] = base64_decode($pgcntrow['pgcontent']);
    }
}

$pggrid = intval($FORM['grid']);

$groupcatlist = '<option value="">-</option>';
$row = $db->getAllRecords(DB_TBLPREFIX . '_groups', '*', " AND grtype = 'content'");
$grouprow = array();
foreach ($row as $value) {
    $grouprow = array_merge($grouprow, $value);
    $strsel = ($grouprow['grid'] == $pggrid) ? ' selected' : '';
    $groupcatlist .= "<option value='{$grouprow['grid']}'{$strsel}>{$grouprow['grtitle']}</option>";
}

$sqlpggrid = ($pggrid > 0) ? " AND pggrid = '{$pggrid}'" : '';
$msgListData = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_pages WHERE 1 AND pgstatus = '1' AND (pglang = '' OR pglang = '{$mbrstr['mylang']}'){$sqlpggrid}");

$noviewpage = <<<INI_HTML
                <div class="empty-state">
                    <div class="empty-state-icon bg-info">
                        <i class="fas fa-question"></i>
                    </div>
                    <h2>{$LANG['g_nocontent']}</h2>
                    <p class="lead">
                        {$LANG['g_nocontentinfo']}
                    </p>
                </div>
INI_HTML;
?>

<div class="section-header">
    <h1><i class="fa fa-fw fa-window-restore"></i> <?php echo myvalidate($LANG['a_digicontent']); ?></h1>
</div>

<div class="section-body">
    <div class="row">
        <div class="col-md-4">	
            <div class="card">
                <div class="card-header">
                    <h4><?php echo myvalidate($LANG['g_content']); ?></h4>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <form method="get" action="index.php" id="dgview">
                            <input type="hidden" name="hal" value="digiview">
                            <div class="input-group">
                                <select name="grid" class="custom-select">
                                    <?php echo myvalidate($groupcatlist); ?>
                                </select>
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="submit"><i class="fas fa-arrow-right fa-fw"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="form-group">
                        <?php
                        if (count($msgListData) > 0) {
                            $numpage = 0;
                            foreach ($msgListData as $val) {
                                if (iscontentmbr($val['pgavalon'], $val['pgppids'], $mbrstr)) {
                                    $strsel = ($FORM['pgid'] == $val['pgid']) ? ' selected' : '';
                                    $pagelink = "index.php?hal=digiview&pgid={$val['pgid']}&grid={$pggrid}";
                                    ?>
                                    <button type="button" class="btn btn-info mt-2" onclick="location.href = '<?php echo myvalidate($pagelink); ?>'"><?php echo isset($val['pgmenu']) ? $val['pgmenu'] : '?'; ?></button>
                                    <?php
                                    $numpage++;
                                } else {
                                    continue;
                                }
                            }
                            if ($numpage < 1) {
                                echo "No Record(s) Found!";
                            } else {
                                $noviewpage = '<i class="fa fa-fw fa-long-arrow-alt-left"></i> ' . $LANG['m_clicklefttocnt'];
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8">	
            <div class="card">

                <div class="card-header">
                    <h4><?php echo myvalidate($pgcntrow['pgtitle']); ?></h4>
                </div>

                <div class="card-body">
                    <p class="text-muted"><?php echo ($FORM['pgid'] != '') ? "<div class='section-title mt-2'>{$pgcntrow['pgsubtitle']}</div>" : $noviewpage; ?></p>

                    <?php
                    if ($FORM['pgid'] != '') {
                        echo isset($pgcntrow['pgcontent']) ? $pgcntrow['pgcontent'] : '';
                    }
                    ?>

                </div>

            </div>
        </div>
    </div>
</div>
