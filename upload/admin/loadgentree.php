<?php

header("Content-Type: application/javascript");
include_once('../common/init.loader.php');

if (verifylog_sess('admin') == '') {
    die('o o p s !');
}

$isactiveon = intval($FORM['statusFltr']);
$mbrstr = getmbrinfo('', '', $FORM['loadId']);
$mpid = $mbrstr['mpid'];

$nodearr = array();
$topusername = strtoupper($mbrstr['username']);
$topimage = ($mbrstr['mbr_image'] != '') ? $mbrstr['mbr_image'] : $cfgrow['mbr_defaultimage'];
$toplink = "index.php?hal=getuser&getId={$mbrstr['id']}&getMpid={$mpid }";
$nodearr[] = "t{$mbrstr['mpid']}";

$nodepid = "ppmbr" . $mbrstr['mppid'];
$nodestatus = " ssmbr" . $mbrstr['mpstatus'];
// dotted border indicate the node structure values do not the same as the general/system structure values (width and/or depth)
$ismbroutcss = ($mbrstr['mpwidth'] != $bpprow['maxwidth'] || $mbrstr['mpdepth'] != $bpprow['maxdepth']) ? " ismbrout" : '';

$genview_content = <<<INI_HTML
    t{$mbrstr['mpid']} = {
        text: {
            name: "{$topusername}",
        },
        link: {
            href: "{$toplink}",
            target: "_self"
        },
        image: "{$topimage}",
        HTMLclass: "{$nodepid}{$nodestatus}{$ismbroutcss}"
    },
INI_HTML;

function gentree($mpid) {
    global $db, $cfgrow, $bpprow, $nodearr, $isactiveon;

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
            $nodelink = "index.php?hal=getuser&getId={$dlnstr['id']}&getMpid={$dlnstr['mpid']}";

            $nodepid = "ppmbr" . $dlnstr['mppid'];
            $nodestatus = " ssmbr" . $dlnstr['mpstatus'];

            if (in_array("t{$dlnstr['mpid']}", $nodearr, TRUE)) {
                continue;
            }
            $nodearr[] = "t{$dlnstr['mpid']}";

            // dotted border indicate the node structure values do not the same as the general/system structure values (width and/or depth)
            $ismbroutcss = ($dlnstr['mpwidth'] != $bpprow['maxwidth'] || $dlnstr['mpdepth'] != $bpprow['maxdepth']) ? " ismbrout" : '';

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
                                    HTMLclass: "{$nodepid}{$nodestatus}{$ismbroutcss}"
                                },
INI_HTML;
            $genview_content .= gentree($dlnstr['mpid']);
        }
    }
    return $genview_content;
}

$genview_content .= gentree($mpid);

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
            type: 'straight', // curve || bCurve || step || straight
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
