<?php
include_once('../common/init.loader.php');

if ($cfgrow['site_status'] != 1) {
    redirpageto($cfgrow['site_url'] . '/' . MBRFOLDER_NAME . '/offline.php');
    exit;
}

if ($FORM['wsid'] > 0) {
    $wsid = intval($FORM['wsid']);
    $usersiteto = base64_decode($FORM['url64']);
    $wshash = md5($wsid . $usersiteto . date("dh"));
    if ($FORM['wshash'] == $wshash) {
        if ($_SESSION[$wshash] == '') {
            $usrRow = getmbrinfo($wsid);
            $mbrsite_hit = $usrRow['mbrsite_hit'] + 1;
            $data = array(
                'mbrsite_hit' => $mbrsite_hit,
            );
            $update = $db->update(DB_TBLPREFIX . '_mbrs', $data, array('id' => $wsid));
            $_SESSION[$wshash] = date("dh");
        }
        header("Location: {$usersiteto}");
    } else {
        header("Location: {$cfgrow['site_url']}?wshashmsg=linkerr");
    }
}

$thisyear = date("Y");
$site_subname = ($cfgtoken['site_subname'] != '') ? "<a href='{$cfgrow['site_url']}'>{$cfgtoken['site_subname']}</a>" : "<a href='https://www.mlmscript.net/id/{$cfgrow['envacc']}' target='_blank'>{$cfgrow['site_name']}</a>";
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="<?php echo myvalidate($LANG['lang_charset']); ?>">
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
        <title><?php echo myvalidate($cfgrow['site_name']); ?></title>

        <meta name="description" content="<?php echo myvalidate($cfgrow['site_descr']); ?>">
        <meta name="keywords" content="<?php echo myvalidate($cfgrow['site_keywrd']); ?>">
        <meta name="author" content="MLMScript.net">

        <!-- General CSS Files -->
        <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
        <link rel="stylesheet" href="../assets/fellow/fontawesome5121/css/all.min.css">

        <link rel="shortcut icon" type="image/png" href="../assets/image/favicon.png"/>

        <!-- CSS Libraries -->
        <link rel="stylesheet" href="../assets/css/pace-theme-minimal.css">

        <!-- Template CSS -->
        <link rel="stylesheet" href="../assets/css/fontmuli.css">
        <link rel="stylesheet" href="../assets/css/style.css">
        <link rel="stylesheet" href="../assets/css/components.css">
    </head>

    <body>
        <div id="app">
            <div class="main-wrapper">
                <div class="navbar-bg"></div>

                <!-- Main Content -->
                <div class="container">
                    <section class="section">
                        <div class="section-header">
                            <h1><?php echo myvalidate($cfgrow['site_name']); ?></h1>
                            <div class="section-header-breadcrumb">
                                <div class="breadcrumb-item">Member Website Directory</div>
                                <div class="breadcrumb-item active"><a href="<?php echo myvalidate($cfgrow['site_url'] . '/' . MBRFOLDER_NAME . '/register.php'); ?>">Register</a></div>
                                <div class="breadcrumb-item"><a href="<?php echo myvalidate($cfgrow['site_url'] . '/' . MBRFOLDER_NAME . '/'); ?>">Login</a></div>
                            </div>
                        </div>

                        <div class="section-body">
                            <div class="row">

                                <?php
                                $wscount = 0;
                                $wsuserlist = '';
                                $condition = " AND mbrsite_url != '' AND mbrsite_title != '' AND mbrsite_desc != '' AND showsite = '1'";
                                $row = $db->getAllRecords(DB_TBLPREFIX . '_mbrs', '*', $condition);
                                $pgcntrow = array();
                                foreach ($row as $value) {

                                    if ($value['mbrstatus'] != 1) {
                                        continue;
                                    }

                                    $wshash = md5($value['id'] . $value['mbrsite_url'] . date("dh"));
                                    $wsimgstr = ($value['mbrsite_img']) ? $value['mbrsite_img'] : DEFIMG_SITE;

                                    $wsdesc = base64_decode($value['mbrsite_desc']);
                                    $wslink = "?wshash={$wshash}&wsid={$value['id']}&url64=" . base64_encode($value['mbrsite_url']);
                                    $wshits = $value['mbrsite_hit'];

                                    $wsuserlist .= <<<INI_HTML
    <div class="col-sm-12 col-md-6">
        <div class="card card-large-icons">
            <div class="card-icon bg-light text-white mx-auto text-center">
                <img src='{$wsimgstr}' alt='[^-^]' class='mr-3 img-fluid rounded mx-auto d-block' width='{$cfgrow['mbrmax_image_width']}' height='{$cfgrow['mbrmax_image_height']}'></i>
            </div>
            <div class="card-body">
                <h4>{$value['mbrsite_title']}</h4>
                <h6><span class="badge badge-info">{$value['mbrsite_cat']}</span></h6>
                <p>{$wsdesc}</p>
                <div class='float-right'>
                <small class="text-muted"><i class='fa fa-thumbs-up fa-fw'></i> {$wshits}</small>&nbsp;
                <a href="javascript:;" onclick="location.href = '{$wslink}'" class="btn btn-sm btn-primary"><i class='fa fa-link'></i></a>
                </div>
            </div>
        </div>
    </div>
INI_HTML;
                                    $wscount++;
                                }

                                if ($wscount < 1) {
                                    $wsuserlist = <<<INI_HTML
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="empty-state">
                        <div class="empty-state-icon bg-info">
                            <i class="fas fa-unlink"></i>
                        </div>
                        <h2>{$LANG['g_nosite']}</h2>
                        <p class="lead">
                            {$LANG['g_nositenote']}
                        </p>
                    </div>
                </div>
            </div>
        </div>
INI_HTML;
                                }

                                echo myvalidate($wsuserlist);
                                ?>

                            </div>
                        </div>
                    </section>
                </div>
                <div class="simple-footer">
                    <!--
                    You are not allowed to remove this credit link unless you have right to do so by own the Extended license or order the Branding Removal license at https://www.mlmscript.net/order
                    -->
                    <div class="text-small"><?php echo myvalidate($LANG['g_builtwith']); ?> <i class="fa fa-fw fa-heart"></i> <?php echo "{$thisyear} {$site_subname}{$cfgrow['_isnocreditstr']}"; ?>
                    </div>

                </div>      
            </div>
        </div>

        <!-- General JS Scripts -->
        <script src="../assets/js/jquery-3.4.1.min.js"></script>
        <script src="../assets/js/popper.min.js"></script>
        <script src="../assets/js/bootstrap.min.js"></script>
        <script src="../assets/js/jquery.nicescroll.min.js"></script>
        <script src="../assets/js/moment.min.js"></script>
        <script src="../assets/js/pace.min.js"></script>

        <!-- JS Libraies -->
        <script src="../assets/js/stisla.js"></script>


        <!-- Template JS File -->
        <script src="../assets/js/scripts.js"></script>
        <script src="../assets/js/custom.js"></script>

        <!-- Page Specific JS File -->
    </body>
</html>
