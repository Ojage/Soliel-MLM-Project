<?php
include_once('../common/init.loader.php');

if (verifylog_sess('admin') == '') {
    die('o o p s !');
}

$_SESSION['redirto'] = redir_to($FORM['redir']);

if (isset($FORM['dosubmit']) and $FORM['dosubmit'] == '1') {
    extract($FORM);

    $redirto = $_SESSION['redirto'];
    $_SESSION['redirto'] = '';

    if (!dumbtoken($dumbtoken, 32)) {
        $_SESSION['show_msg'] = showalert('danger', 'Error!', $LANG['g_invalidtoken']);
        $redirval = $cfgrow['site_url'] . "/" . ADMFOLDER_NAME . "?res=erradmtoken";
        redirpageto($redirval);
        exit;
    }

    // reserved username
    $isunexist = is_unamereserved($FORM['username']);

    // if new username exist, keep using old username
    $condition = ' AND username LIKE "' . $FORM['username'] . '" ';
    $sql = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_mbrs WHERE 1 " . $condition . "");
    if (count($sql) > 0 || $isunexist) {
        // do nothing
        $_SESSION['dotoaster'] = "toastr.warning('Record not added <strong>Username exist!</strong>', 'Warning');";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['dotoaster'] = "toastr.error('Record not added <strong>Invalid input format!</strong>', 'Error');";
    } else {
        $in_date = date('Y-m-d H:i:s', time() + (3600 * $cfgrow['time_offset']));
        $log_ip = get_userip();
        $country = get_countrycode($log_ip);
        $password = ($password1 != '') ? getpasshash($password1) : $password;
        $data = array(
            'in_date' => $in_date,
            'firstname' => mystriptag($firstname),
            'lastname' => mystriptag($lastname),
            'username' => mystriptag($username, 'user'),
            'email' => mystriptag($email, 'email'),
            'password' => $password,
            'log_ip' => $log_ip,
            'country' => $country,
        );
        $insert = $db->insert(DB_TBLPREFIX . '_mbrs', $data);
        $newmbrid = $db->lastInsertId();

        if ($newmbrid > 0) {
            require_once('../common/mailer.do.php');

            // send welcome email
            $cntaddarr['fullname'] = $firstname . ' ' . $lastname;
            $cntaddarr['login_url'] = $cfgrow['site_url'] . "/" . MBRFOLDER_NAME;
            $cntaddarr['rawpassword'] = $password2;
            delivermail('mbr_reg', $newmbrid, $cntaddarr);

            // register to membership
            $newmbrstr = getmbrinfo($newmbrid);
            $refstr = getmbrinfo($unref, 'username');
            $regtoppid = ($regtoppid > 0) ? $regtoppid : 1;
            regmbrplans($newmbrstr, $refstr['id'], $regtoppid);
        }

        if ($insert) {
            $_SESSION['dotoaster'] = "toastr.success('Record added successfully!', 'Success');";
        } else {
            $_SESSION['dotoaster'] = "toastr.error('Record not added <strong>Please try again!</strong>', 'Warning');";
        }
    }
    header('location: ' . $redirto);
    exit;
}

// list available plans
$avalmbrplanarr = array();
foreach ($bpparr as $key => $value) {
    $avalmbrplanarr[$value['ppid']] = $value['ppname'] . ' - ' . $value['regfee'];
}
$avalmbrplan_menu = select_opt($avalmbrplanarr);
?>

<div class="row">
    <div class="col-md-12">

        <p class="text-primary">Fields with <span class="text-danger">*</span> are mandatory!</p>

        <form method="post" action="adduser.php" oninput='password1.setCustomValidity(password2.value != password1.value ? "Passwords do not match." : "")'>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Referrer Username</label>
                    <input type="text" name="unref" id="unref" class="form-control" value="" placeholder="Enter referrer username" onBlur="checkMember('un2i', this.value, '')">
                </div>
                <div class="form-group col-md-6">
                    <label>Referrer Name</label>
                    <div id="resultGetMbr">?</div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label><?php echo myvalidate($LANG['g_firstname']); ?> <span class="text-danger">*</span></label>
                    <input type="text" name="firstname" id="firstname" class="form-control" value="" minlength="3" placeholder="Enter member first name" required>
                </div>
                <div class="form-group col-md-6">
                    <label><?php echo myvalidate($LANG['g_lastname']); ?> <span class="text-danger">*</span></label>
                    <input type="text" name="lastname" id="lastname" class="form-control" value="" minlength="3" placeholder="Enter member last name" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Username <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><i class="fa fa-fw fa-user"></i></div>
                        </div>
                        <input type="text" name="username" id="username" class="form-control" value="" minlength="4" maxlength="16" placeholder="Enter member name" required>
                    </div>
                </div>
                <div class="form-group col-md-6">
                    <label>Email <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><i class="fa fa-fw fa-envelope"></i></div>
                        </div>
                        <input type="email" name="email" id="email" class="form-control" value="" minlength="8" placeholder="Enter member email" required>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <input type="hidden" name="password" value="<?php echo isset($rowstr['password']) ? $rowstr['password'] : ''; ?>">
                <div class="form-group col-md-6">
                    <label>Password <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" name="password2" id="password2" value="" placeholder="Enter member password" required>
                </div>
                <div class="form-group col-md-6">
                    <label>Password Confirm <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" name="password1" id="password1" value="" placeholder="Confirm member password">
                </div>
            </div>

            <?php
            if ($cfgrow['mylicver'] != 'reg') {
                ?>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Membership Plan</label>
                        <select name="regtoppid" id="regtoppid" class="form-control select1" required>
                            <?php echo myvalidate($avalmbrplan_menu); ?>
                        </select>
                    </div>
                </div>
                <?php
            }
            ?>

            <div class="text-md-right">
                <a href="javascript:;" class="btn btn-secondary" data-dismiss="modal"><i class="far fa-fw fa-times-circle"></i> Cancel</a>
                <button type="submit" name="submit" value="submit" id="submit" class="btn btn-primary">
                    <i class="fa fa-fw fa-plus-circle"></i> Submit
                </button>
                <input type="hidden" name="dosubmit" value="1">
                <input type="hidden" name="dumbtoken" value="<?php echo myvalidate($_SESSION['dumbtoken']); ?>">
            </div>

        </form>

    </div>

</div>
