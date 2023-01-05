<?php

// file execute by page load
if (!defined('OK_LOADME')) {
    die("^-^ DODODO");
}

$nowdatetm = date('Y-m-d H:i:s', time() + (3600 * $cfgrow['time_offset']));
$lastdatetm = date('Y-m-d H:i:s', time() + (3600 * $cfgrow['time_offset']) - 60);
$mw = $bpprow['ma' . 'xwi' . 'dth'];
$md = $bpprow['m' . 'axde' . 'pth'];
if ($cfgrow['cronts'] < $lastdatetm) {
    /* ========= */
    /*  Do Task  */
    /* ========= */

    // process commission
    dotrxwallet();

    // delete old session
    dellog_sess();

    // check expired member
    do_expmbr();

    // process database backup
    do_dbbakup();

    // update cron
    $data = array(
        'cronts' => $nowdatetm,
    );
    $db->update(DB_TBLPREFIX . '_configs', $data, array('cfgid' => $didId));
}
$bpprow['ma' . 'xwi' . 'dth'] = ($mw > $frlmtdcfg['ismw']) ? $frlmtdcfg['ismw'] : $mw;
$bpprow['m' . 'axde' . 'pth'] = ($md > $frlmtdcfg['ismd']) ? $frlmtdcfg['ismd'] : $md;
