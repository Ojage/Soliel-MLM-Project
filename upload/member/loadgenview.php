<?php

header("Content-Type: application/javascript");
include_once('../common/init.loader.php');

$seskey = verifylog_sess('member');
if ($seskey == '') {
    die('o o p s !');
}

$sesRow = getlog_sess($seskey);
$username = get_optionvals($sesRow['sesdata'], 'un');
$mbrstr = getmbrinfo($username, 'username');

$sprmpid = ($_SESSION['clistview'] > 0) ? $_SESSION['clistview'] : $mbrstr['mpid'];

$isactiveon = intval($FORM['showFltr']);
$loadmpid = intval($FORM['loadId']);
$mpid = ($loadmpid > 0) ? $loadmpid : $sprmpid;

if ($mpid < 1) {
    die();
}

$dlnstr = getmbrinfo('', '', $mpid);
if ($mbrstr['id'] != $dlnstr['id'] && strpos($dlnstr['sprlist'], ":{$sprmpid}|") === false) {
    die();
}

$nodearr = array();
$topusername = strtoupper($dlnstr['username']);
$topimage = ($dlnstr['mbr_image'] != '') ? $dlnstr['mbr_image'] : $cfgrow['mbr_defaultimage'];
$toplink = "index.php?hal=accountcfg";
$nodearr[] = "t{$dlnstr['mpid']}";
$genview_content = <<<INI_HTML
    t{$dlnstr['mpid']} = {
        text: {
            name: "{$topusername}",
        },
        link: {
            href: "{$toplink}",
            target: "_self"
        },
        image: "{$topimage}"
    },
INI_HTML;

function gentree($mpid, $mppid, $recursvdeep = 0) {
    global $db, $cfgrow, $bpprow, $nodearr, $isactiveon;

    $recursvdeep++;

    $condition = " AND id = idmbr AND sprlist LIKE '%|1:{$mpid}|%'";
    $condition = ($isactiveon == 1) ? $condition . " AND mpstatus != '0'" : $condition;
    $userData = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_mbrs, " . DB_TBLPREFIX . "_mbrplans WHERE 1 " . $condition . " LIMIT 20");
    if (count($userData) > 0) {
        foreach ($userData as $dlnstr) {
            if ($dlnstr['mpid'] < 1) {
                break;
            }
            $nodeusername = strtoupper($dlnstr['username']);
            $nodeimage = ($dlnstr['mbr_image'] != '') ? $dlnstr['mbr_image'] : $cfgrow['mbr_defaultimage'];
            if ($recursvdeep <= $bpprow['maxdepth']) {
                $nodelink = "index.php?hal=getuser&getId={$dlnstr['id']}&getMpid={$dlnstr['mpid']}";
            } else {
                $nodelink = "";
            }
            $nodestatus = "ismbr" . $dlnstr['mpstatus'];

            if (in_array("t{$dlnstr['mpid']}", $nodearr, TRUE)) {
                continue;
            }
            $nodearr[] = "t{$dlnstr['mpid']}";
            $genview_content .= <<<INI_HTML
                                t{$dlnstr['mpid']} = {
                                    parent: t{$mpid},
                                    text: {
                                        name: "{$nodeusername}",
                                    },
                                    link: {
                                        href: "{$nodelink}",
                                        target: "_self"
                                    },
                                    image: "{$nodeimage}",
                                    HTMLclass: "{$nodestatus}"
                                },
INI_HTML;
            $genview_content .= gentree($dlnstr['mpid'], $mppid, $recursvdeep);
        }
    }
    return $genview_content;
}

$genview_content .= gentree($mpid, $dlnstr['mppid']);

$nodelist = implode(',', $nodearr);
$genview_content = <<<INI_HTML
var config = {
        container: "#genviewer",
        rootOrientation:  'NORTH', // NORTH || EAST || WEST || SOUTH
        nodeAlign: "CENTER", // CENTER || TOP || BOTTOM
        scrollbar: "fancy",
        padding: 32,
        siblingSeparation: 20,
        subTeeSeparation: 30,
        connectors: {
            type: 'step', // curve || bCurve || step || straight
        },
        node: {
            HTMLclass: 'nodeStyle',
        }
    },

    {$genview_content}

    chart_config = [
        config,
        {$nodelist}
    ];
INI_HTML;

//echo "<pre>$genview_content</pre>";
echo "$genview_content";
