<?php

include_once('../common/config.php');
if (!defined('INSTALL_PATH') || !defined('OK_LOADME')) {
    die("<title>Error!</title><body>No such file or directory.</body>");
}

include_once('../common/db.func.php');
$dsn = "mysql:dbname=" . DB_NAME . ";host=" . DB_HOST . "";
$pdo = "";
try {
    $pdo = new PDO($dsn, base64_decode(DB_USER), base64_decode(DB_PASSWORD));
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
$db = new Database($pdo);

// -----

$setsesname = md5(DB_NAME . INSTALL_PATH);
session_name($setsesname);
session_start();

// get referrer username
$refun = preg_replace("/[^A-Za-z0-9]/", '', $_REQUEST['ref']);
$redir = preg_replace("/[^A-Za-z0-9]/", '', $_REQUEST['redir']);

if ($_SESSION['ref_sess_un'] != $refun) {
    $mbrhits = $db->getRecFrmQry("SELECT id, hits FROM " . DB_TBLPREFIX . "_mbrs WHERE username = '{$refun}' AND mbrstatus = '1'");
    $id = $mbrhits[0]['id'];
    $hits = $mbrhits[0]['hits'] + 1;
    $db->update(DB_TBLPREFIX . '_mbrs', array('hits' => $hits), array('id' => $id));

    $_SESSION['ref_sess_un'] = '';
    setcookie('ref_sess_un', '', time() - 86400);
}

$_SESSION['ref_sess_un'] = $refun;
setcookie('ref_sess_un', $_SESSION['ref_sess_un'], time() + (86400 * 1));

$cfgid = $db->getRecFrmQry("SELECT cfgid FROM " . DB_TBLPREFIX . "_configs WHERE cfgtoken LIKE '%|reflinklp:|%'");
if ($redir != '') {
    header('Location: ../' . $redir);
} else if ($cfgid[0]['cfgid'] > 0) {
    header('Location: ../');
} else {
    header('Location: ../' . MBRFOLDER_NAME . '/register.php');
}
exit;
