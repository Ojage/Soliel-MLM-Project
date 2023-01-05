<?php

include_once('../common/init.loader.php');

dellog_sess('member');
$username = ucwords(strtolower($FORM['un']));
$_SESSION['show_msg'] = showalert('success', $LANG['g_greetout'] . ' ' . $username . ',', $LANG['g_successlogout']);
redirpageto('login.php');
exit;
