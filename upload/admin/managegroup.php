<?php
if (!defined('OK_LOADME')) {
    die('o o p s !');
}

$grpListArr = array("" => "-", "1" => $LANG['a_digifile'], "2" => $LANG['a_digicontent']);
$grpDbListArr = array("1" => "file", "2" => "content", "3" => "item");

$grid = $FORM['grid'];
$grtp = intval($FORM['grtp']);

if ($grtp > 0) {
    if (md5($grtp . '*' . $grid) == $FORM['del']) {
        $db->delete(DB_TBLPREFIX . '_groups', array('grid' => $grid));
        $_SESSION['dotoaster'] = "toastr.success('Group deleted successfully!', 'Success');";
        redirpageto('index.php?hal=managegroup');
        exit;
    }

    $grouplistbtn = '';
    $grtype = $grpDbListArr[$grtp];
    $row = $db->getAllRecords(DB_TBLPREFIX . '_groups', '*', ' AND grtype = "' . $grtype . '"');
    $grouprow = array();
    foreach ($row as $value) {
        $grouprow = array_merge($grouprow, $value);
        $linkto = "index.php?grtp={$grtp}&hal=managegroup&grid={$grouprow['grid']}";
        $grouplistbtn .= "<button type='button' class='btn btn-secondary btn-icon icon-left' onclick=\"location.href='{$linkto}';\"><i class='fas fa-ellipsis-v'></i> {$grouprow['grtitle']} <span class='badge badge-transparent'></span></button> ";
    }
}

$grpListData = '';
foreach ($grpListArr as $key => $value) {
    $strsel = ($FORM['grtp'] == $key) ? ' selected' : '';
    $grpListData .= "<option value='{$key}'{$strsel}>{$value}</option>";
}

if (isset($FORM['dosubmit']) and $FORM['dosubmit'] == '1') {

    extract($FORM);
    $grid = intval($grid);
    $grtype = $grpDbListArr[$grtp];

    $data = array(
        'grtitle' => mystriptag($grtitle),
        'grtype' => $grtype,
        'gradminfo' => mystriptag($gradminfo),
        'groptions' => $groptions,
        'grtoken' => $grtoken,
        'grorder' => intval($grorder),
    );

    if ($grid > 0) {
        $update = $db->update(DB_TBLPREFIX . '_groups', $data, array('grid' => $grid));
        if ($update) {
            $_SESSION['dotoaster'] = "toastr.success('Group updated successfully!', 'Success');";
        } else {
            $_SESSION['dotoaster'] = "toastr.warning({$LANG['g_nomajorchanges']}, 'Info');";
        }
    } else {
        $insert = $db->insert(DB_TBLPREFIX . '_groups', $data);
        if ($insert) {
            $_SESSION['dotoaster'] = "toastr.success('Group added successfully!', 'Success');";
        } else {
            $_SESSION['dotoaster'] = "toastr.error('Group not added <strong>Please try again!</strong>', 'Warning');";
        }
    }

    //header('location: index.php?hal=' . $hal);
    redirpageto("index.php?hal={$hal}&grid={$grid}");
    exit;
}

if ($grid > 0) {
    $grid = intval($grid);
    $row = $db->getAllRecords(DB_TBLPREFIX . '_groups', '*', ' AND grid = "' . $grid . '"');
    $groupStr = array();
    foreach ($row as $value) {
        $groupStr = array_merge($groupStr, $value);
    }
}
?>

<div class="section-header">
    <h1><i class="fa fa-fw fa-object-ungroup"></i> <?php echo myvalidate($LANG['a_managegroup']); ?></h1>
</div>

<div class="section-body">
    <div class="row">
        <div class="col-md-4">	
            <div class="card">
                <div class="card-header">
                    <h4>Category</h4>
                </div>
                <form method="get">
                    <div class="card-body">
                        <div class="form-group">
                            <select name="grtp" class="form-control select1">
                                <?php
                                if ($grpListData != '') {
                                    echo $grpListData;
                                } else {
                                    echo "<option disabled>{$LANG['g_norecordinfo']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="text-right">
                            <button type="button" class="btn btn-primary" onclick="location.href = 'index.php?hal=managegroup&grid=0'">
                                Create New
                            </button>
                            <button type="submit" value="Load" id="load" class="btn btn-info">
                                <i class="fa fa-fw fa-redo"></i> Load
                            </button>
                            <input type="hidden" name="hal" value="managegroup">
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-md-8">	
            <div class="card">

                <div class="card-header">
                    <h4>Options</h4>
                </div>

                <form method="post" action="index.php" id="msgtplform">
                    <input type="hidden" name="hal" value="managegroup">
                    <div class="card-body">
                        <p class="text-muted">
                            <?php
                            if ($grid == '') {
                                echo ($grouplistbtn != '') ? $grouplistbtn : '<i class="fa fa-fw fa-long-arrow-alt-left"></i> Please select the category from the drop down list on the left or click the <strong>Create New</strong> button to add a new category!';
                            }
                            ?>
                        </p>

                        <?php
                        if ($grid != '') {
                            ?>
                            <div class="form-group">
                                <label for="gridnew">Category</label>
                                <select name="grtp" class="form-control select1">
                                    <?php
                                    if ($grpListData != '') {
                                        echo $grpListData;
                                    } else {
                                        echo "<option disabled>{$LANG['g_norecordinfo']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <input type="hidden" name="grid" value="<?php echo myvalidate($grid); ?>">

                            <div class="form-group">
                                <label for="grtitle">Title</label>
                                <input type="text" name="grtitle" id="grtitle" class="form-control" value="<?php echo isset($groupStr['grtitle']) ? $groupStr['grtitle'] : ''; ?>" placeholder="Category name" required>
                            </div>
                            <div class="form-group">
                                <label for="gradminfo">Description</label>
                                <textarea class="form-control rowsize-sm" name="gradminfo" id="gradminfo" placeholder="Category note, available for administrator only"><?php echo isset($groupStr['gradminfo']) ? $groupStr['gradminfo'] : ''; ?></textarea>
                            </div>

                            <?php
                        }
                        ?>

                    </div>

                    <?php
                    if ($grid != '') {
                        ?>
                        <div class="card-footer text-md-right">
                            <?php
                            if ($groupStr['grid'] != '0') {
                                ?>
                                <div class="form-group float-left text-left">
                                    <a href="javascript:;" data-href="index.php?grid=<?php echo myvalidate($groupStr['grid']); ?>&hal=managegroup&del=<?php echo md5($FORM['grtp'] . '*' . $groupStr['grid']); ?>&grtp=<?php echo myvalidate($FORM['grtp']); ?>" class="btn btn-danger bootboxconfirm" data-poptitle="Group: <?php echo myvalidate($groupStr['grtitle']); ?>" data-popmsg="Are you sure want to delete this category?" data-toggle="tooltip" title="Delete: <?php echo myvalidate($groupStr['grtitle']); ?>"><i class="far fa-fw fa-trash-alt"></i> Remove</a>
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
                        </div>
                        <input type="hidden" name="dosubmit" value="1">
                        <?php
                    }
                    ?>

                </form>
            </div>
        </div>
    </div>
</div>
