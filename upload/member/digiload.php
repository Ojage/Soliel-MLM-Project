<?php
if (!defined('OK_LOADME')) {
    die('o o p s !');
}

$flgrid = intval($FORM['grid']);

$groupcatlist = '<option value="">-</option>';
$row = $db->getAllRecords(DB_TBLPREFIX . '_groups', '*', " AND grtype = 'file'");
$grouprow = array();
foreach ($row as $value) {
    $grouprow = array_merge($grouprow, $value);
    $strsel = ($grouprow['grid'] == $flgrid) ? ' selected' : '';
    $groupcatlist .= "<option value='{$grouprow['grid']}'{$strsel}>{$grouprow['grtitle']}</option>";
}

$sqlflgrid = ($flgrid > 0) ? " AND flgrid = '{$flgrid}'" : '';
$flcount = 0;
$dlfilelist = '';
$row = $db->getAllRecords(DB_TBLPREFIX . '_files', '*', " AND flname != '' AND flstatus = '1'{$sqlflgrid}");
$pgcntrow = array();
$mppidarr = mbrpparr($mbrstr['id']);
$mbrppidarr = $mppidarr['mppid'];

foreach ($row as $value) {

    $flppidsarr = str_getcsv($value['flppids']);
    if (count(array_intersect((array) $mbrppidarr, $flppidsarr)) === 0) {
        continue;
    }

    if ($value['flavalto'] == 1 && $mbrstr['mbrstatus'] != 1) {
        continue;
    }
    if ($value['flavalto'] == 2 && $mbrstr['mpstatus'] != 1) {
        continue;
    }
    if ($value['flavalto'] == 3 && $mbrstr['mpstatus'] == 1) {
        continue;
    }

    $flhash = md5($cfgrow['dldir'] . $value['flid'] . date("md"));
    $flimgstr = ($value['flimage']) ? $value['flimage'] : DEFIMG_FILE;

    $extfile = pathinfo($value['flpath'], PATHINFO_EXTENSION);
    $dlfilename = strtolower($value['flname']);
    $dlfilename = preg_replace('/[\W]/', '_', $dlfilename) . '.' . $extfile;

    $dldlink = "index.php?dlfn={$dlfilename}&dlid={$value['flid']}&l={$flhash}";

    $dlfilelist .= <<<INI_HTML
    <div class="col-lg-6">
        <div class="card card-large-icons">
            <div class="card-icon bg-info text-white mx-auto text-center">
                <img src='{$flimgstr}' alt='[^-^]' class='mr-3 img-fluid rounded mx-auto d-block' width='{$cfgrow['mbrmax_image_width']}' height='{$cfgrow['mbrmax_image_height']}'></i>
            </div>
            <div class="card-body">
                <h4>{$value['flname']}</h4>
                <p>{$value['fldescr']}</p>
                <a href="javascript:;" onclick="location.href = '{$dldlink}'" class="btn btn-sm btn-primary">{$LANG['g_download']}</a>
                <small class="text-muted">{$value['flsize']}</small>
            </div>
        </div>
    </div>
INI_HTML;
    $flcount++;
}

if ($flcount < 1) {
    $dlfilelist = <<<INI_HTML
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="empty-state">
                        <div class="empty-state-icon bg-info">
                            <i class="fas fa-question"></i>
                        </div>
                        <h2>{$LANG['m_nofile']}</h2>
                        <p class="lead">
                            {$LANG['m_nofilenote']}
                        </p>
                    </div>
                </div>
            </div>
        </div>
INI_HTML;
}
?>

<div class="section-header">
    <h1><i class="fa fa-fw fa-cloud-download-alt"></i> <?php echo myvalidate($LANG['m_digiload']); ?></h1>
</div>

<div class="section-body">
    <div class="row">
        <div class="col-md-12">
            <form method="get" action="index.php" id="digiload">
                <div class="card">
                    <div class="card-body">
                        <div class="form-group">
                            <input type="hidden" name="hal" value="digiload">
                            <div class="input-group">
                                <select name="grid" class="custom-select">
                                    <?php echo myvalidate($groupcatlist); ?>
                                </select>
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="submit"><i class="fas fa-arrow-right fa-fw"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <?php echo myvalidate($dlfilelist); ?>
    </div>
</div>
