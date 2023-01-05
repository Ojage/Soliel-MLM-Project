<?php
include_once('../common/init.loader.php');

$page_header = "Admin CP Login";

if (verifylog_sess('admin') != '') {
    redirpageto('index.php?hal=dashboard');
    exit;
}

if (isset($FORM['dosubmit']) and $FORM['dosubmit'] == '1') {
    extract($FORM);

    $isrecapv3 = 1;
    if ($cfgrow['isrecaptcha'] == 1 && $cfgtoken['isrcapcadmin'] == 1 && isset($FORM['g-recaptcha-response'])) {
        $secret = $cfgrow['rc_securekey'];
        $response = $FORM['g-recaptcha-response'];
        $remoteIp = $_SERVER['REMOTE_ADDR'];
        // call curl to POST request
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array('secret' => $secret, 'response' => $response, 'remoteip' => $remoteIp), '', '&'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        $arrResponse = json_decode($response, true);

        // verify the response
        if ($arrResponse["success"] == '1' && $arrResponse["score"] >= 0.5) {
            // valid submission
        } else {
            $isrecapv3 = 0;
        }
    }

    $username = trim($username);

    if (!dumbtoken($dumbtoken)) {
        $_SESSION['show_msg'] = showalert('danger', 'Error!', $LANG['g_invalidtoken']);
        $redirval = "?res=erradmtoken";
        redirpageto($redirval);
        exit;
    }

    $cfgtoken = get_optionvals($cfgrow['cfgtoken']);
    $username64 = base64_encode($username);
    if ($username64 == $cfgtoken['subadmuser'] && base64_encode($password) == $cfgtoken['subadmpass'] && 1 == $cfgtoken['subadmis']) {
        $issubadm = $_SESSION['isunsubadm'] = $cfgtoken['subadmuser'];
    } else {
        $_SESSION['isunsubadm'] = '';
    }

    $passveradm = password_verify(md5($password), $cfgrow['admin_password']);
    if ($isrecapv3 == 0) {
        $_SESSION['show_msg'] = showalert('warning', 'Error!', 'Recaptcha failed, please try it again!');
        $redirval = "?res=rcapt";
    } elseif ($issubadm == $username64 || (($cfgrow['admin_user'] == $username || $cfgrow['site_emailaddr'] == $username) && $passveradm)) {
        checknewver();
        addlog_sess($cfgrow['admin_user'], 'admin', $remember);
        $_SESSION['admauthr'] = md5($password . '.' . $cfgrow['admin_password']);
        redirpageto('index.php?hal=dashboard');
        exit;
    } else {
        $_SESSION['show_msg'] = showalert('danger', 'Invalid Login', 'Username and Password are case sensitive. Please try it again.');
        redirpageto('login.php?err=' . $username);
        exit;
    }
}

$loadcallbyadm = 1;
$show_msg = $_SESSION['show_msg'];
$_SESSION['show_msg'] = '';

include('../common/pub.header.php');
?>
<section class="section">
    <div class="container mt-4">
        <div class="row">
            <div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">
                <div class="login-brand">
                    <img src="<?php echo myvalidate($site_logo); ?>" alt="logo" width="100" class="shadow-light rounded-circle">
                    <div><?php echo myvalidate($cfgrow['site_name']); ?></div>
                </div>

                <?php echo myvalidate($show_msg); ?>

                <div class="card card-primary">
                    <div class="card-header"><h4><?php echo myvalidate($page_header); ?></h4></div>

                    <div class="card-body">
                        <form method="POST" class="needs-validation" id="letmeinform">
                            <?php
                            if ($cfgrow['isrecaptcha'] == 1 && $cfgtoken['isrcapcadmin'] == 1) {
                                echo '<script src="https://www.google.com/recaptcha/api.js" async defer></script>';
                                $isrecaptcha_content = <<<INI_HTML
                                    <script type="text/javascript">
                                        function onSubmit(token) {
                                            document.getElementById('letmeinform').submit();
                                        }
                                    </script>
INI_HTML;
                                echo myvalidate($isrecaptcha_content);
                            }
                            ?>
                            <div class="form-group">
                                <label for="username">Username</label>
                                <input id="username" type="text" class="form-control" name="username" tabindex="1" required autofocus>
                                <div class="invalid-feedback">
                                    Please fill in your username
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="d-block">
                                    <label for="password" class="control-label">Password</label>
                                    <div class="float-right">
                                        <a href="forgot-password.php" class="text-small">
                                            <?php echo myvalidate($LANG['g_forgotpass']); ?>?
                                        </a>
                                    </div>
                                </div>
                                <input id="password" type="password" class="form-control" name="password" tabindex="2" required>
                                <div class="invalid-feedback">
                                    please fill in your password
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" name="remember" class="custom-control-input" tabindex="3" id="remember-me">
                                    <label class="custom-control-label" for="remember-me"><?php echo myvalidate($LANG['g_rememberme']); ?></label>
                                </div>
                            </div>

                            <div class="form-group">
                                <button data-sitekey="<?php echo myvalidate($cfgrow['rc_sitekey']); ?>" data-callback='onSubmit' class="btn btn-primary btn-lg btn-block g-recaptcha" tabindex="4">
                                    Login
                                </button>
                                <input type="hidden" name="dosubmit" value="1">
                                <input type="hidden" name="dumbtoken" value="<?php echo myvalidate($_SESSION['dumbtoken']); ?>">
                            </div>
                        </form>

                    </div>
                </div>

                <?php
                if ($cfgrow['site_status'] != 1) {
                    echo showalert('info', $LANG['g_websitestatus'], base64_decode($cfgrow['site_status_note']));
                }
                ?>
            </div>
        </div>
    </div>
</section>
<?php
include('../common/pub.footer.php');
