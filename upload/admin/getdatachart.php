<?php

include_once('../common/init.loader.php');

if (verifylog_sess('admin') == '') {
    die('o o p s !');
}

// registered and incoming
function getDataChart($days, $format = 'd/m') {
    global $db;

    $m = date("m");
    $de = date("d");
    $y = date("Y");
    $allarr = $dateArray = $datearr = $refarr = $ernarr = array();
    for ($i = 0; $i <= $days - 1; $i++) {
        $dateArray[] = date($format, mktime(0, 0, 0, $m, ($de - $i), $y));
        $datearr[] = date('Y-m-d', mktime(0, 0, 0, $m, ($de - $i), $y));
    }

    foreach ($datearr as $key => $value) {
        $condition = " AND mbrstatus = '1' AND in_date LIKE '%{$value}%'";
        $row = $db->getAllRecords(DB_TBLPREFIX . '_mbrs', 'COUNT(*) as totref', $condition);
        $refarr[] = intval($row[0]['totref']);
    }
    foreach ($datearr as $key => $value) {
        $condition = " AND txtoid = '0' AND txstatus = '1' AND (txtoken LIKE '%|REG:%' OR txtoken LIKE '%|RENEW:%') AND txdatetm LIKE '%{$value}%'";
        $row = $db->getAllRecords(DB_TBLPREFIX . '_transactions', 'SUM(txamount) as sumearn', $condition);
        $ernarr[] = floatval($row[0]['sumearn']);
    }

    $allarr['isdat1'] = array_reverse($refarr);
    $allarr['isdat2'] = array_reverse($ernarr);
    $allarr['islbel'] = array_reverse($dateArray);

    $allarr['label1'] = 'Registered';
    $allarr['label2'] = 'Incoming';
    $allarr['text'] = 'Registered & Incoming';
    return $allarr;
}

// registered and member
function getDataChart1($days, $format = 'd/m') {
    global $db, $cfgtoken;

    $m = date("m");
    $de = date("d");
    $y = date("Y");
    $allarr = $dateArray = $datearr = $regarr = $mbrarr = array();
    for ($i = 0; $i <= $days - 1; $i++) {
        $dateArray[] = date($format, mktime(0, 0, 0, $m, ($de - $i), $y));
        $datearr[] = date('Y-m-d', mktime(0, 0, 0, $m, ($de - $i), $y));
    }

    foreach ($datearr as $key => $value) {
        if ($cfgtoken['isautoregplan'] == 1) {
            $condition = " AND reg_date LIKE '%{$value}%'";
            $row = $db->getAllRecords(DB_TBLPREFIX . '_mbrplans', 'COUNT(*) as totref', $condition);
        } else {
            $condition = " AND in_date LIKE '%{$value}%'";
            $row = $db->getAllRecords(DB_TBLPREFIX . '_mbrs', 'COUNT(*) as totref', $condition);
        }
        $regarr[] = intval($row[0]['totref']);
    }
    foreach ($datearr as $key => $value) {
        if ($cfgtoken['isautoregplan'] == 1) {
            $condition = " AND reg_date LIKE '%{$value}%' AND mpstatus = '1'";
            $row = $db->getAllRecords(DB_TBLPREFIX . '_mbrplans', 'COUNT(*) as totref', $condition);
        } else {
            $condition = " AND reg_date LIKE '%{$value}%'";
            $row = $db->getAllRecords(DB_TBLPREFIX . '_mbrplans', 'COUNT(*) as totref', $condition);
        }
        $mbrarr[] = intval($row[0]['totref']);
    }

    $allarr['isdat1'] = array_reverse($regarr);
    $allarr['isdat2'] = array_reverse($mbrarr);
    $allarr['islbel'] = array_reverse($dateArray);

    $allarr['label1'] = 'Registered';
    $allarr['label2'] = 'Member';
    $allarr['text'] = 'Registered & Member';
    return $allarr;
}

// incoming and withdraw
function getDataChart2($days, $format = 'd/m') {
    global $db;

    $m = date("m");
    $de = date("d");
    $y = date("Y");
    $allarr = $dateArray = $datearr = $ernarr = $wtdrarr = array();
    for ($i = 0; $i <= $days - 1; $i++) {
        $dateArray[] = date($format, mktime(0, 0, 0, $m, ($de - $i), $y));
        $datearr[] = date('Y-m-d', mktime(0, 0, 0, $m, ($de - $i), $y));
    }

    foreach ($datearr as $key => $value) {
        $condition = " AND txtoid = '0' AND (txtoken LIKE '%|REG:%' OR txtoken LIKE '%|RENEW:%' OR txtoken LIKE '%|STORE:%') AND txdatetm LIKE '%{$value}%'";
        $row = $db->getAllRecords(DB_TBLPREFIX . '_transactions', 'SUM(txamount) as sumearn', $condition);
        $ernarr[] = floatval($row[0]['sumearn']);
    }
    foreach ($datearr as $key => $value) {
        $condition = " AND txfromid = '0' AND txtoid > '0' AND txtoken LIKE '%|WIDR:%' AND txdatetm LIKE '%{$value}%'";
        $row = $db->getAllRecords(DB_TBLPREFIX . '_transactions', 'SUM(txamount) as sumearn', $condition);
        $wtdrarr[] = floatval($row[0]['sumearn']);
    }

    $allarr['isdat1'] = array_reverse($ernarr);
    $allarr['isdat2'] = array_reverse($wtdrarr);
    $allarr['islbel'] = array_reverse($dateArray);

    $allarr['label1'] = 'Incoming';
    $allarr['label2'] = 'Withdraw';
    $allarr['text'] = 'Incoming & Withdraw';
    return $allarr;
}

function getDataChart3($limit) {
    global $db, $country_array;

    $allarr = $userRow = $labelarr = $refarr = array();

    $hit = 0;
    $row = $db->getRecFrmQry("SELECT country FROM " . DB_TBLPREFIX . "_mbrs WHERE 1 GROUP BY country");
    foreach ($row as $val) {
        $label = $val['country'];
        $labelarr[] = (strlen($label) == 1) ? 'Unknown' : ucwords(strtolower($country_array[$label]));
        $condition = " AND country = '{$val['country']}'";
        $row = $db->getAllRecords(DB_TBLPREFIX . '_mbrs', 'COUNT(*) as totref', $condition);
        $refarr[] = intval($row[0]['totref']);
        $hit++;
        if ($limit <= $hit) {
            break;
        }
    }

    $allarr['isdat1'] = $refarr;
    $allarr['islbel'] = $labelarr;

    $allarr['label1'] = 'Country';
    $allarr['text'] = 'Member by Country';
    return $allarr;
}

$seskey = verifylog_sess('admin');
if ($seskey == '') {
    $datarr[] = array();
    echo json_encode($datarr);
    die();
}

$dataform = intval($FORM['dchart']);
switch ($dataform) {
    case "1":
        $getallarr = getDataChart1(7, "j M");
        break;
    case "2":
        $getallarr = getDataChart2(7, "j M");
        break;
    case "3":
        $getallarr = getDataChart3(9);
        break;
    default:
        $getallarr = getDataChart(7, "l\n j M");
}

echo json_encode($getallarr);
