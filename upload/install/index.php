<?php
ini_set('log_errors', 'On');
ini_set('error_log', 'ins_error.log');
define('DO_INSTALLER', 1);

$configorifile = "../common/config_ori.php";
$configfile = "../common/config.php";

if (isset($_GET['doinstall']) && $_GET['doinstall'] == 'fresh' && file_exists($configorifile)) {
    rename($configorifile, $configfile);
    header("Location: index.php");
    exit;
}
if (file_exists($configfile)) {
    include($configfile);
} else {
    die("<pre>The configuration file is not ready, please read the <strong>documentation</strong> for the installation instructions and <a href='?doinstall=fresh'>click here</a> to start a <strong>fresh installation</strong>. Thank you.</pre>");
}

include_once('../common/umver.php');
include_once('../common/db.func.php');
include_once('../common/sys.func.php');

include_once('var.list.php');

$FORM = array_merge((array) $FORM, (array) $_REQUEST);
$setsesname = md5($_SERVER['REMOTE_ADDR'] . date('Md'));
session_name($setsesname);
session_start();

if (isset($_GET['reflush']) && $_GET['reflush'] == '1') {
    session_destroy();
    header("Location: index.php");
    exit;
}

function dostepredir($dostep) {
    $_SESSION['dostep'] = intval($dostep);
    redirpageto($_SERVER['PHP_SELF']);
    exit;
}

$page_header = "Installation Wizard";
$page_content = <<<INI_HTML
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
        <title>{$page_header}</title>
        <meta name="author" content="MLMScript.net">

        <link rel="shortcut icon" type="image/png" href="../assets/image/favicon.png"/>

        <!-- General CSS Files -->
        <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
        <link rel="stylesheet" href="../assets/fellow/fontawesome5121/css/all.min.css">

        <!-- CSS Libraries -->
        <link rel="stylesheet" href="../assets/css/pace-theme-minimal.css">

        <!-- Template CSS -->
        <link rel="stylesheet" href="../assets/css/fontmuli.css">
        <link rel="stylesheet" href="../assets/css/style.css">
        <link rel="stylesheet" href="../assets/css/components.css">
        <link rel="stylesheet" href="../assets/css/custom.css">

    </head>

    <body>
        <div id="app">
INI_HTML;
echo myvalidate($page_content);

if (defined('INSTALL_PATH') && $_SESSION['dostep'] < 4) {
    $_SESSION['dostep'] = '';
}

$formval = $_SESSION;
$instep = $_SESSION['dostep'];

$markstep1 = $markstep2 = $markstep3 = '';
if ($instep == 4) {
    $markstep1 = $markstep2 = $markstep3 = $markstep4 = " wizard-step-active";
} elseif ($instep == 3) {
    $markstep1 = $markstep2 = $markstep3 = " wizard-step-active";
} elseif ($instep == 2) {
    $markstep1 = $markstep2 = " wizard-step-active";
} else {
    $markstep1 = " wizard-step-active";
}

$errmsg = $_SESSION['errmsg'];
if ($errmsg) {
    if ($instep == 2) {
        $markstep2 = " wizard-step-warning";
    } elseif ($instep == 3) {
        $markstep3 = " wizard-step-warning";
    }
}
$_SESSION['errmsg'] = '';

if (isset($FORM['dosubmit']) and $FORM['dosubmit'] == '1') {

    $_SESSION = array_merge($_SESSION, $FORM);
    extract($_SESSION);

    if ($instep == 2) {
        // check database
        $dsn = "mysql:dbname=" . $db_name . ";host=" . $db_host . "";
        $pdo = "";
        try {
            $pdo = new PDO($dsn, $db_username, $db_password);
        } catch (PDOException $e) {
            $_SESSION['errmsg'] = "Connection failed: " . $e->getMessage();
            $dostep = 2;
            dostepredir($dostep);
        }
    } elseif ($instep == 3) {
        // settings
        if ($admin_password != $admin_passwordx) {
            $_SESSION['errmsg'] = "Password do not same, please re-enter correctly!";
            $dostep = 3;
            dostepredir($dostep);
        }
        if (strlen($lickey) < 16) {
            $_SESSION['errmsg'] = "Invalid license key!";
            $dostep = 3;
            dostepredir($dostep);
        }

$_X='lfnizg';$_Y='edoce';
$_F='eta'.$_X;$_E=$_Y.'d_46esab';
$_G=strrev($_F);$_D=strrev($_E);
$_Z='
FZLHjqNaAAV/5S1GcreQhpzU6gXJZGOSCZsWXKIJBpP5+tezrarFWZw/P8L3BeTZD0Hl0+Xrz4/0fcmz
1+Xvnx/h7yVJ/yGR+J7m9ztfP37151e+Ju1HedZ90SZz/vHPf1yumaGk45xwkuiEeCe6EiVlVgMFmqRE
yuu2R+CogYjEw5tn7uGKL22OwPie00uutD3lktgJFR7LuDPw0J6633ESMnDWAqG2Mq6H4l08dCcLm3OB
s00yk/DVLx6kfMAxecLwXe3u4UgrWxA91pdbIizui2HjJISDP+v2RgUlFCc7fzzUKf+tXRZadG2gNRWf
myyc9BxNQAOeDCuGCOCmm2YWGodshuRkz/tcDbwGTu5ahSPXpwF+vvqzsElKX/hMp0dxn21N8AdYyUjp
cEAIkthIqsXGwHm73W+FqRB3NjT2VCtlFYuFLio7xgHFuaa6gVk5doqurReBpF+RXE+V0b9eZcG6Lb43
OdaDHiO1yxFu72r+ZaKwQuknnGONedMhNctpChcGYOKs0awj2qsB05hn0od9wqiTnPrNlhUekDHYJafC
HtG6IRqup0vRuo/lOtiB7KNsPfKm0T59QlkbX6gjWHGN5Q0em+0CFun8h7E6prRI6trg7WqNWMyrvJ5K
JUt0hTAVatnk/lSTx7y6HaDzm1Tw7AmVFjq2TESV/BUgC69y4Ih6vpVekiVdX/2R+sE72Z2U4iwifwgH
lM1+qg4YGT6C2STBlfKezhi38DRGW5O/aXrxBSlaxcyLm7PdcHFiWqSLYiWGm+rVcWhGTspAz4ZpHDuK
4jPrVCXatyopkZayn8TKVJMUioDJvPKO1E9R2HxsO+9H7MWSou82o6GJLOHm9rtmr4OKSFFMJ6jTWX7v
amdVcO7RC1Z2p3+Scq9ClcGGwdN44o3QBa0jLQVt6xGhylQ8LynNmV0uBKYcDA6nFM2gwdWGdO9Cjedn
MosUh5FI0ga9geKVppc2pHu5wCr83my8aHmyC8mDl22v2q+Dw4wB5V+nahrXNAr4JWDJt+xsr9KRYm89
St3UFhRaBwpa3xANw5z9/X35/Pz8+u9/
';
eval($_G($_D($_Z)));

        if ($arrResponse['isvalid'] != 1) {
            $_SESSION['errmsg'] = $arrResponse['errmsg'];
            $dostep = 3;
            dostepredir($dostep);
        }

        // save config file
        $cfgfile = "../common/config.php";
        if ($arrResponse['sqlstr'] && $fh = @fopen($cfgfile, 'w')) {
            $heretimezone = ($mytimezone) ? $mytimezone : date_default_timezone_get();
            $sql_username_enc = base64_encode($db_username);
            $sql_password_enc = base64_encode($db_password);
            $installbasepath = dirname(dirname(__FILE__));

            $installhash = bin2hex(random_bytes(32));
            $installkeys = md5($sql_password_enc . rand());

            $settings_sql = <<<INI_HTML
<?php

define('OK_LOADME', 1);
error_reporting(E_ALL ^ E_NOTICE ^ E_STRICT);

// --- start ---

date_default_timezone_set('{$heretimezone}');

define('DB_HOST', '{$db_host}');
define('DB_USER', '{$sql_username_enc}');
define('DB_PASSWORD', '{$sql_password_enc}');
define('DB_NAME', '{$db_name}');
define('DB_TBLPREFIX', '{$db_tblprefix}');

define('INSTALL_HASH', '{$installhash}');
define('INSTALL_KEYS', '{$installkeys}');

define('INSTALL_PATH', '{$installbasepath}');
define('DECIMAL_POINT', 2);

define('DEFIMG_LOGO', '../assets/image/logo_defaultimage.png');
define('DEFIMG_PLAN', '../assets/image/plan_defaultimage.jpg');
define('DEFIMG_FILE', '../assets/image/file_defaultimage.jpg');
define('DEFIMG_SITE', '../assets/image/site_defaultimage.jpg');
define('DEFIMG_ADM', '../assets/image/adm_defaultimage.jpg');
define('DEFIMG_MBR', '../assets/image/mbr_defaultimage.jpg');

define('ADMFOLDER_NAME', 'admin');
define('MBRFOLDER_NAME', 'member');
define('UIDFOLDER_NAME', 'id');
define('USLFOLDER_NAME', 'listing');

//ini_set('log_errors', 'On');
//ini_set('error_log', '{$db_tblprefix}_error.log');

INI_HTML;

            fwrite($fh, $settings_sql);
            fclose($fh);

            // ---
            require($cfgfile);

            // save basic settings
            $dsn = "mysql:dbname=" . $db_name . ";host=" . $db_host . "";
            $pdo = "";
            try {
                $pdo = new PDO($dsn, $db_username, $db_password);
            } catch (PDOException $e) {
                $_SESSION['errmsg'] = "Connection failed: " . $e->getMessage();
                $dostep = 2;
                dostepredir($dostep);
            }

            // get sql and insert tables
            $sqlbasestr = base64_decode($arrResponse['sqlstr']);
            $sqlbase = json_decode($sqlbasestr, true);

            // execute sql
            $db = new Database($pdo);

            // make sure database collation to utf8mb4_unicode_ci
            $db->doQueryStr("ALTER DATABASE {$db_name} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");

            // loop through each line
            foreach ($sqlbase as $line) {
                // skip it if it's a comment
                $line = trim($line);
                $start_character = substr($line, 0, 2);
                if ($start_character == '--' || $start_character == '/*' || $start_character == '//' || $line == '') {
                    continue;
                }
                // add this line to the current segment
                $templine .= $line;
                // if it has a semicolon at the end, it's the end of the query
                if (substr($line, -1, 1) == ';') {
                    // perform the query
                    $templine = preg_replace("/\r|\n/", " ", $templine);
                    $templine = str_replace("#TBLPREFIX#", $db_tblprefix, $templine);
                    $db->doQueryStr($templine);
                    // reset temp variable to empty
                    $templine = '';
                }
            }

            // make sure tables collation also to utf8mb4_unicode_ci
            $tables = $db->getRecFrmQry("SHOW TABLE STATUS FROM {$db_name} LIKE '%'");
            if ($tables != 0) {
                $tbnamecharsql = '';
                foreach ($tables as $key => $table) {
                    $tb_name = $table['Name'];
                    $tbnamecharsql .= "ALTER TABLE `{$tb_name}` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;";
                }
                $db->doQueryStr($tbnamecharsql);
            }

            $geturl = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
            $geturl .= $_SERVER['SERVER_NAME'];
            $geturl .= $_SERVER['REQUEST_URI'];
            $site_url = dirname(dirname($geturl));

            $softversion = ($umbasever != '') ? $umbasever : $arrResponse['softversion'];

            $data = array(
                'site_name' => $site_name,
                'site_url' => $site_url,
                'dldir' => $installbasepath . '/downloads',
                'admin_user' => $admin_user,
                'admin_password' => getpasshash($admin_password),
                'softversion' => $softversion,
                'lickey' => base64_encode($lickey),
                'site_emailaddr' => $site_emailaddr,
                'installdate' => $arrResponse['installdate'],
                'installhash' => $arrResponse['installhash'],
                'licdate' => $arrResponse['boughtdate'],
                'cfgtoken' => base64_decode($arrResponse['cfgtoken']),
                'lichash' => $arrResponse['lichash'],
            );

            $condition = ' AND cfgid = "1" ';
            $sql = $db->getRecFrmQry("SELECT * FROM " . $db_tblprefix . "_configs WHERE 1 " . $condition . "");
            if (count($sql) > 0) {
                $update = $db->update($db_tblprefix . '_configs', $data, array('cfgid' => '1'));
            } else {
                $insert = $db->insert($db_tblprefix . '_configs', $data);
            }
        } else {
            $_SESSION['errmsg'] = "Cannot write to config file, please try it again.!";
            $dostep = 3;
        }
    } elseif ($instep == 4) {
        // remove installer
        session_destroy();
        redirpageto('../admin');
        exit;
    }

    dostepredir($dostep);
}

$show_msg = $_SESSION['show_msg'];
$_SESSION['show_msg'] = '';
?>
<section class="section">
    <div class="container mt-4">
        <div class="row">
            <div class="col-12 col-sm-10 offset-sm-1 col-md-8 offset-md-2 col-lg-8 offset-lg-2 col-xl-8 offset-xl-2">
                <div class="login-brand">
                    <img src="../assets/image/logo_defaultimage.png" alt="logo" width="100" class="shadow-light rounded-circle">
                </div>

                <?php echo myvalidate($show_msg); ?>

                <div class="card card-primary">
                    <div class="card-header">
                        <h4>Installation</h4>
                        <div class='card-header-action'><span class='badge badge-info'><?php echo myvalidate($umbasever); ?></span></div>
                    </div>

                    <div class="card-body">

                        <?php
                        if (defined('INSTALL_PATH') && $instep < 1) {
                            echo showalert('info', 'Congratulation!', 'The script has been installed.');
                        } else {
                            ?>
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="wizard-steps">
                                        <div class="wizard-step<?php echo myvalidate($markstep1); ?>">
                                            <div class="wizard-step-icon">
                                                <i class="fas fa-lightbulb"></i>
                                            </div>
                                            <div class="wizard-step-label">
                                                Welcome
                                            </div>
                                        </div>
                                        <div class="wizard-step<?php echo myvalidate($markstep2); ?>">
                                            <div class="wizard-step-icon">
                                                <i class="fas fa-database"></i>
                                            </div>
                                            <div class="wizard-step-label">
                                                Database
                                            </div>
                                        </div>
                                        <div class="wizard-step<?php echo myvalidate($markstep3); ?>">
                                            <div class="wizard-step-icon">
                                                <i class="fas fa-tools"></i>
                                            </div>
                                            <div class="wizard-step-label">
                                                Settings
                                            </div>
                                        </div>
                                        <div class="wizard-step<?php echo myvalidate($markstep4); ?>">
                                            <div class="wizard-step-icon">
                                                <i class="fas fa-clipboard-check"></i>
                                            </div>
                                            <div class="wizard-step-label">
                                                Complete
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-12">
                                    <?php
                                    if ($errmsg) {
                                        echo showalert('danger', 'Warning!', $errmsg);
                                    }
                                    ?>
                                    <form method="post" id='installer' class="wizard-content mt-2">
                                        <div class="wizard-pane">
                                            <?php
                                            if ($instep == 4) {
                                                ?>
                                                <div class="card">
                                                    <div class="card-header">
                                                        <h4>Complete</h4>
                                                    </div>
                                                    <div class="card-body">
                                                        <p>The script has been successfully installed. The Admin CP Username is <strong><?php echo myvalidate($formval['admin_user']); ?></strong>. Log in to the Admin CP and configure the script.</p><p>Please refer to the <a href='https://www.mlmscript.net/docs/unimatrix' target='_blank' title="Online Documentation">documentation</a> for the available options and features in the script. Thank you for your business.</p>
                                                    </div>
                                                    <div class="card-footer bg-whitesmoke">
                                                        <div class="text-muted"><i class="fas fa-exclamation-triangle"></i> For security reasons, rename or remove the installation folder.</div>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <div class="text-right">
                                                        <button type="button" name="continueadmcp" id="continueadmcp" class="btn btn-success" onclick="location.href = '../admin?install=done'">
                                                            Continue <i class="fas fa-arrow-right fa-fw"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <?php
                                                session_destroy();
                                            } elseif ($instep == 3) {
                                                $modalcontent = file_get_contents('terms.html');

                                                $timezonelist = '';
                                                $timezonenow = date_default_timezone_get();
                                                foreach ($timezones as $key => $value) {
                                                    $isselected = ($key == $timezonenow) ? " selected" : '';
                                                    $timezonelist .= "<option value='{$key}'{$isselected}>{$value}";
                                                }
                                                ?>

                                                <div class="form-group">
                                                    <label for="lickey">License Key</label>
                                                    <input type="text" name="lickey" id="lickey" class="form-control" value="<?php echo isset($formval['lickey']) ? $formval['lickey'] : ''; ?>" placeholder="Script license key or purchase code" required>
                                                </div>

                                                <div class="form-group">
                                                    <label for="site_name">Site Title</label>
                                                    <input type="text" name="site_name" id="site_name" class="form-control" value="<?php echo isset($formval['site_name']) ? $formval['site_name'] : ''; ?>" placeholder="Site name or title" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="site_emailaddr">Email Address</label>
                                                    <input type="email" name="site_emailaddr" id="site_emailaddr" class="form-control" value="<?php echo isset($formval['site_emailaddr']) ? $formval['site_emailaddr'] : ''; ?>" placeholder="Your email address" required>
                                                    <h6 class="text-muted text-small"><span class="badge badge-danger">Important!</span> <span class="badge badge-secondary">If you are forgetting your Admin CP login details, use this email address to reset your password.</span></h6>
                                                </div>

                                                <div class="form-group">
                                                    <label for="admin_user">Admin Username</label>
                                                    <input type="text" name="admin_user" id="admin_user" class="form-control" value="<?php echo isset($formval['admin_user']) ? $formval['admin_user'] : ''; ?>" placeholder="Admin username" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="admin_password">Admin Password</label>
                                                    <input type="password" name="admin_password" id="admin_password" class="form-control" value="" placeholder="Admin password" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="admin_passwordx">Confirm Password</label>
                                                    <input type="password" name="admin_passwordx" id="admin_passwordx" class="form-control" value="" placeholder="Confirm admin password" required>
                                                </div>

                                                <div class="form-group">
                                                    <label>Website Timezone</label>
                                                    <select class="form-control" name="mytimezone">
                                                        <?php echo myvalidate($timezonelist); ?>
                                                    </select>
                                                </div>

                                                <div class="form-group">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" name="iagree" value="1" class="custom-control-input" id="iagree" required>
                                                        <label class="custom-control-label" for="iagree">I agree with the <a href="javascript:;" data-toggle="modal" data-target="#myModalterm">terms and conditions</a></label>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="float-left">
                                                        <button type="button" name="startover" id="startover" class="btn btn-secondary" onclick="location.href = 'index.php?reflush=1'">
                                                            <i class="fa fa-fw fa-stop"></i> Start Over
                                                        </button>
                                                    </div>
                                                    <div class="text-right">
                                                        <button type="reset" name="reset" value="reset" id="reset" class="btn btn-warning">
                                                            <i class="fa fa-fw fa-undo"></i> Reset
                                                        </button>
                                                        <button type="submit" name="submit" value="submit" id="submit" class="btn btn-primary">
                                                            Next <i class="fas fa-arrow-right"></i>
                                                        </button>
                                                        <input type="hidden" name="dostep" value="4">
                                                        <input type="hidden" name="dosubmit" value="1">
                                                    </div>
                                                </div>

                                                <?php
                                            } elseif ($instep == 2) {
                                                ?>
                                                <div class="form-group">
                                                    <label for="db_host">DB Host</label>
                                                    <input type="text" name="db_host" id="db_host" class="form-control" value="<?php echo isset($formval['db_host']) ? $formval['db_host'] : 'localhost'; ?>" placeholder="Database host" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="db_name">DB Name</label>
                                                    <input type="text" name="db_name" id="db_name" class="form-control" value="<?php echo isset($formval['db_name']) ? $formval['db_name'] : ''; ?>" placeholder="Database name" required>
                                                </div>

                                                <div class="form-group">
                                                    <label for="db_tblprefix">Table Prefix</label>
                                                    <input type="text" name="db_tblprefix" id="db_tblprefix" class="form-control" value="<?php echo isset($formval['db_tblprefix']) ? $formval['db_tblprefix'] : 'netw'; ?>" placeholder="Table prefix" required>
                                                </div>

                                                <div class="form-group">
                                                    <label for="db_username">DB Username</label>
                                                    <input type="text" name="db_username" id="db_username" class="form-control" value="<?php echo isset($formval['db_username']) ? $formval['db_username'] : ''; ?>" placeholder="Database username" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="db_password">DB Password</label>
                                                    <input type="password" name="db_password" id="db_password" class="form-control" value="" placeholder="Database password" required>
                                                </div>

                                                <div class="form-group">
                                                    <div class="float-left">
                                                        <button type="button" name="startover" id="startover" class="btn btn-secondary" onclick="location.href = 'index.php?reflush=1'">
                                                            <i class="fa fa-fw fa-stop"></i> Start Over
                                                        </button>
                                                    </div>
                                                    <div class="text-right">
                                                        <button type="reset" name="reset" value="reset" id="reset" class="btn btn-warning">
                                                            <i class="fa fa-fw fa-undo"></i> Reset
                                                        </button>
                                                        <button type="submit" name="submit" value="submit" id="submit" class="btn btn-primary">
                                                            Next <i class="fas fa-arrow-right"></i>
                                                        </button>
                                                        <input type="hidden" name="dostep" value="3">
                                                        <input type="hidden" name="dosubmit" value="1">
                                                    </div>
                                                </div>
                                                <?php
                                            } else {

                                                $doregsbtnsubmit = 1;
                                                include('../common/reqlist.php');
                                                $btndisablestr = ($doregsbtnsubmit != 1) ? " disabled" : '';
                                                ?>

                                                <div class="card">
                                                    <div class="card-header">
                                                        <h4>Introduction</h4>
                                                    </div>
                                                    <div class="card-body">
                                                        <p>Thank you for purchasing our script. Your support and trust in us are much appreciated.</p>
                                                        <p>Please read the <strong>Documentation</strong> and follow the instructions before start the installation.</p>
                                                        <p>All The Best.<br />
                                                            MLMScript.net</p>
                                                    </div>
                                                    <div class="card-footer bg-whitesmoke">
                                                        <p>Server Requirements:</p>
                                                        <?php echo myvalidate($showreg_server); ?>
                                                        <p>Click <strong>Start Installation</strong> button to continue.</p>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <div class="text-right">
                                                        <button type="button" class="btn btn-info" onclick="window.open('https://www.mlmscript.net/askqu/', '_blank')">
                                                            Help <i class="fa fa-fw fa-question"></i>
                                                        </button>
                                                        <button type="submit" name="submit" value="submit" id="submit" class="btn btn-primary" <?php echo myvalidate($btndisablestr); ?>>
                                                            Start Installation <i class="fas fa-arrow-right"></i>
                                                        </button>
                                                        <input type="hidden" name="dostep" value="2">
                                                        <input type="hidden" name="dosubmit" value="<?php echo myvalidate($doregsbtnsubmit); ?>">
                                                    </div>
                                                </div>
                                                <?php
                                                // end
                                            }
                                            ?>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <?php
                        }
                        ?>

                        <div class='text-small'>*) Need assistance? the <a href="https://www.mlmscript.net/index.php?itz=2&a=order&src_utm=startins" target="_blank" title="Installation Service Only">Installation Services</a> are available.</div>

                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
</section>

<?php
$yearnow = date("Y");
$page_content = <<<INI_HTML
                <div class="simple-footer">
                    <div>{$cfgrow['site_name']}</div>
                    Crafted with <i class="fa fa-fw fa-heart"></i> {$yearnow} <div class="bullet"></div> <a href="https://www.mlmscript.net">AmazeGo</a>
                </div>

        <!-- Modal -->
        <div class="modal fade" id="myModalterm" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Terms and Conditions</h5>
                    </div>
                    <div class="modal-body">
                        <small class="text-muted">{$modalcontent}</small>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
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
INI_HTML;
echo myvalidate($page_content);
