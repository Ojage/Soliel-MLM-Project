<?php
include_once('init.loader.php');

$page_header = $LANG['g_resetpass'];
include('pub.header.php');

if ($_SESSION['pr_key'] == '') {
    echo '<section class="section"><div class="container mt-4">';
    echo "<div class='row'><div class='col-md-12 text-center'>{$_SESSION['show_msg']}</div></div>";
    echo '</div></section>';
    $_SESSION['show_msg'] = '';
    $fderto = ($FORM['f'] == 1) ? MBRFOLDER_NAME : ADMFOLDER_NAME;
    redirpageto('../' . $fderto . '?t=' . date("ymdHis"), 5);
    exit;
} else {

    if (isset($FORM['dosubmit']) and $FORM['dosubmit'] == '1') {
        extract($FORM);

        $passres = passmeter($password);
        if ($password != $passwordconfirm) {
            $_SESSION['show_msg'] = showalert('danger', $LANG['g_passwordfail'], $LANG['g_passwordfailinfo']);
        } elseif ($passres == 1) {
            $sesRow = getlog_sess($_SESSION['pr_key']);
            $dataun = get_optionvals($sesRow['sesdata'], 'un');
            $dataarr = explode('-', $dataun);

            if ($sesRow['sesid'] > 0 && $dataarr[0] == 'resetpass') {

                $dataid = $dataarr[1];
                $hashedpassword = getpasshash($password);

                if ($dataid > 0) {
                    $data = array(
                        'password' => $hashedpassword,
                    );
                    $update = $db->update(DB_TBLPREFIX . '_mbrs', $data, array('id' => $dataid));
                    $fder = 1;
                } else {
                    $data = array(
                        'admin_password' => $hashedpassword,
                    );
                    $update = $db->update(DB_TBLPREFIX . '_configs', $data, array('cfgid' => '1'));
                    $fder = 0;
                }

                $db->delete(DB_TBLPREFIX . '_sessions', array('seskey' => $_SESSION['pr_key']));
                $_SESSION['show_msg'] = showalert('success', $LANG['g_congrats'], $LANG['g_passwordupdated']);
            } else {
                $_SESSION['show_msg'] = showalert('danger', $LANG['g_sessionexpiry'], $LANG['g_sessionpassexpiry']);
                $fder = $f;
            }
            $_SESSION['pr_key'] = '';
        } else {
            $_SESSION['show_msg'] = showalert('warning', $LANG['g_passwordhint'], $passres);
        }
        redirpageto('reset-password.php?f=' . $fder . '&t=' . date("ymdHis"));
        exit;
    }

    $show_msg = $_SESSION['show_msg'];
    ?>
    <section class="section">
        <div class="container mt-4">
            <div class="row">
                <div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">
                    <div class="login-brand">
                        <img src="<?php echo myvalidate($site_logo); ?>" alt="logo" width="100" class="shadow-light rounded-circle">
                        <div><?php echo myvalidate($cfgrow['site_name']); ?></div>
                    </div>

                    <div class="card card-primary">
                        <div class="card-header"><h4><?php echo myvalidate($LANG['g_resetpass']); ?></h4></div>

                        <?php
                        if ($_SESSION['show_msg'] == '') {
                            ?>
                            <div class="card-body">
                                <p class="text-muted"><?php echo myvalidate($LANG['g_completeform']); ?></p>
                                <form method="POST">
                                    <div class="form-group">
                                        <label for="password"><?php echo myvalidate($LANG['g_passwordnew']); ?></label>
                                        <input id="password" type="password" class="form-control pwstrength" name="password" tabindex="2" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="passwordconfirm"><?php echo myvalidate($LANG['g_passwordconfirm']); ?></label>
                                        <input id="passwordconfirm" type="password" class="form-control" name="passwordconfirm" tabindex="2" required>
                                    </div>

                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary btn-lg btn-block" tabindex="4">
                                            <?php echo myvalidate($LANG['g_resetpassbtn']); ?>
                                        </button>
                                        <input type="hidden" name="f" value="<?php echo myvalidate($FORM['f']); ?>">
                                        <input type="hidden" name="dosubmit" value="1">
                                    </div>
                                </form>
                            </div>
                            <?php
                        }
                        $_SESSION['show_msg'] = '';
                        ?>

                    </div>

                    <?php echo myvalidate($show_msg); ?>

                </div>
            </div>
        </div>
    </section>
    <?php
}
include('pub.footer.php');
