<?php

// file execute by system cron schedule
include_once('config.php');
if (!defined('OK_LOADME')) {
    die('o o p s !');
}

// -----

include_once('db.func.php');
$LANG = array_merge((array) $LANG, (array) $lang);

$dsn = "mysql:dbname=" . DB_NAME . ";host=" . DB_HOST . "";
$pdo = "";

try {
    $pdo = new PDO($dsn, base64_decode(DB_USER), base64_decode(DB_PASSWORD));
    $pdo->exec('SET CHARACTER SET utf8');
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

$db = new Database($pdo);
$db->doQueryStr("SET SESSION sql_mode = ''");

// load site configuration

$didId = 1;

// settings
$row = $db->getAllRecords(DB_TBLPREFIX . '_configs', '*', ' AND cfgid = "' . $didId . '"');
$cfgrow = array();
foreach ($row as $value) {
    $cfgrow = array_merge($cfgrow, $value);
}
$langloadf = INSTALL_PATH . '/common/lang/' . $cfgrow['langiso'] . '.lang.php';
if (file_exists($langloadf)) {
    $TEMPLANG = $LANG;
    include_once($langloadf);
    $LANG = array_filter($LANG);
    $LANG = array_merge($TEMPLANG, $LANG);
}

$bpprow = array();

// baseplan
$row = $db->getAllRecords(DB_TBLPREFIX . '_baseplan', '*', ' AND bpid = "' . $didId . '"');
foreach ($row as $value) {
    $bpprow = array_merge($bpprow, $value);
}
// payplan
$row = $db->getAllRecords(DB_TBLPREFIX . '_payplans', '*', ' AND ppid = "' . $didId . '"');
foreach ($row as $value) {
    $bpprow = array_merge($bpprow, $value);
}
$bpprow['currencysym'] = base64_decode($bpprow['currencysym']);

// other functions
include_once('sys.func.php');
include_once('value.list.php');
include_once('en.lang.php');

$cfgtoken = get_optionvals($cfgrow['cfgtoken']);
$bptoken = get_optionvals($bpprow['bptoken']);
$bpparr = ppdbarr();

// load cron do
include_once('cron.do.php');

// end vars
$row = $value = '';
