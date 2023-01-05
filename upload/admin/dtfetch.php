<?php

include_once('../common/init.loader.php');

if (verifylog_sess('admin') == '') {
    die('o o p s !');
}

$notitoaststrarr = array();
$cfgtokentoast = ($cfgtoken['toastdt'] != '') ? $cfgtoken['toastdt'] : '2000-01-01 00:00:01';

$condition = " AND reg_utctime >= '{$cfgtokentoast}'";
$newuserlist = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_mbrs LEFT JOIN " . DB_TBLPREFIX . "_mbrplans ON id = idmbr WHERE 1 " . $condition . "");
foreach ($newuserlist as $val) {
    $notitoaststrarr[] = "user-check:Registered:{$val['firstname']} {$val['lastname']} ({$val['username']})";
}

$condition = " AND log_date >= '{$cfgtokentoast}'";
$loguserlist = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_mbrs LEFT JOIN " . DB_TBLPREFIX . "_mbrplans ON id = idmbr WHERE 1 " . $condition . "");
foreach ($loguserlist as $val) {
    $notitoaststrarr[] = "user-lock:Signin:{$val['username']} ({$val['log_ip']})";
}

$condition = " AND txdatetm >= '{$cfgtokentoast}' AND txtoken LIKE '%|WIDR:OUT|%'";
$wdrwallist = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_mbrs LEFT JOIN " . DB_TBLPREFIX . "_transactions ON id = txfromid WHERE 1 " . $condition . "");
foreach ($wdrwallist as $val) {
    $notitoaststrarr[] = "wallet:Withdrawal:{$bpprow['currencysym']}{$val['txamount']} by {$val['username']}";
}

$nowdatetm = date('Y-m-d H:i:s', time() + (3600 * $cfgrow['time_offset']) - 10);

if ($_SESSION['notifytoaststatus'] != '') {
    $notitoaststrarr[] = "exclamation-circle:Update:Realtime notification " . $_SESSION['notifytoaststatus'];
    $_SESSION['notifytoaststatus'] = '';
}

if (count($notitoaststrarr) > 0) {
    $cfgtoken = put_optionvals($cfgrow['cfgtoken'], 'toastdt', $nowdatetm);
    $data = array(
        'cfgtoken' => $cfgtoken,
    );
    $update = $db->update(DB_TBLPREFIX . '_configs', $data, array('cfgid' => $didId));
}

$notitoaststr = implode('|', $notitoaststrarr);

$data = array(
    "notitoaststr" => $notitoaststr
);

echo json_encode($data);
