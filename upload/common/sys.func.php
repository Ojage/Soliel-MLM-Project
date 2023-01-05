<?php

if (!defined('OK_LOADME')) {
    die("<title>Error!</title><body>No such file or directory.</body>");
}
include 'sys.class.php';

function read_file_size($size) {
    if (intval($size) == 0) {
        return("0 Bytes");
    }
    $filesizename = array(" Bytes", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");
    return round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . $filesizename[$i];
}

function dborder_arr($tblarr, $tblsel, $tblsrt) {
    $curqryurl = $_SERVER['REQUEST_URI'];
    if ((strpos($curqryurl, "_stbel=") !== false)) {
        $rtblsrt = ($tblsrt == 'up') ? "down" : "up";
        $curqryurl = str_replace("_stbel={$tblsel}", "_stbel=^", $curqryurl);
    } else {
        $curqryx = (false !== strpos($_SERVER['REQUEST_URI'], '?')) ? "&" : "?";
        $curqryurl .= $curqryx . "_stbel=^&_stype=down";
    }

    $tblarrlink = array();
    foreach ($tblarr as $key => $value) {
        if ($tblsel == $value) {
            $curqryurlgo = str_replace("_stype={$tblsrt}", "_stype={$rtblsrt}", $curqryurl);
            $curqryurlgo = str_replace("_stbel=^", "_stbel={$value}", $curqryurlgo);
            $curfontaw = ($tblsrt != 'up') ? "fa fa-fw fa-long-arrow-alt-down" : "fa fa-fw fa-long-arrow-alt-up";
        } else {
            $curqryurlgo = str_replace("_stbel=^", "_stbel={$value}", $curqryurl);
            $curfontaw = "fa fa-fw fa-arrows-alt-v";
        }
        $tblarrlink[$value] = "<a href='{$curqryurlgo}'><i class='{$curfontaw}'></i></a>";
    }
    return $tblarrlink;
}

function select_opt($valarr, $valsel = '', $tostr = 0) {
    if ($tostr != 0) {
        $selopt = $valarr[$valsel];
    } else {
        $selopt = ($valsel == '') ? "<option selected>-</option>" : "<option disabled>-</option>";
        foreach ($valarr as $key => $value) {
            if ($value == '') {
                continue;
            }
            $selopt .= ($key == $valsel) ? "<option value='{$key}' selected>{$value}</option>" : "<option value='{$key}'>{$value}</option>";
        }
    }
    return $selopt;
}

function checkbox_opt($value, $targetval = 1, $tostr = 0) {
    if ($tostr != 0) {
        $cekopt = ($value == $targetval) ? "Yes" : "No";
    } else {
        $cekopt = ($value == $targetval) ? " checked" : "";
    }
    return $cekopt;
}

function radiobox_opt($valuearr, $targetval = 1) {
    $cekopt = array();
    foreach ($valuearr as $key => $value) {
        $cekopt[$key] = ($value == $targetval) ? ' checked="checked"' : '';
    }
    return $cekopt;
}

function redir_to($redir = '') {
    $refredir = $_SERVER["HTTP_REFERER"];
    $redirto = ($redir == '') ? $refredir : "index.php?hal=" . $redir;
    return $redirto;
}

function myvalidate($myodata) {
    return $myodata;
}

function mystriptag($mysdata, $filter = 'string') {
    global $cfgtoken;

    $mysdata = trim($mysdata);
    if ($filter == 'email') {
        $mysdata = filter_var($mysdata, FILTER_SANITIZE_EMAIL);
        $mysdata = strtolower(trim($mysdata));
    } elseif ($filter == 'url') {
        $mysdata = filter_var($mysdata, FILTER_SANITIZE_URL);
        $mysdata = rtrim($mysdata, "/");
    } else {
        $mysdata = filter_var($mysdata, FILTER_SANITIZE_STRING);
    }
    if ($filter == 'user') {
        $mysdata = preg_replace("/[^A-Za-z0-9]/", '', $mysdata);
        $mysdata = ($cfgtoken['unlowercs'] == '1') ? strtolower($mysdata) : $mysdata;
    }
    return strip_tags($mysdata);
}

function imageupload($outfname, $fileimg, $oldimg = '') {
    $valid_extensions = array('jpeg', 'jpg', 'png', 'gif');

    $newimg = $oldimg;
    $path = '../assets/imagextra/';
    if ($fileimg) {
        $img = $fileimg['name'];
        $tmp = $fileimg['tmp_name'];
        $ext = strtolower(pathinfo($img, PATHINFO_EXTENSION));
        $final_image = $outfname . '.' . $ext;
        // check's valid format
        if (in_array($ext, $valid_extensions)) {
            if ($oldimg != '' && file_exists($oldimg) && strpos($oldimg, '/imagextra/') !== false) {
                unlink($oldimg);
            }
            $path = $path . strtolower($final_image);
            if (move_uploaded_file($tmp, $path)) {
                $newimg = $path;
            }
        }
    }
    return $newimg;
}

function readfile_chunked($filename, $retbytes = true) {
    $chunksize = 2 * (1024 * 1024);
    $buffer = '';
    $cnt = 0;

    $handle = fopen($filename, 'rb');
    if ($handle === false) {
        return false;
    }
    while (!feof($handle)) {
        $buffer = fread($handle, $chunksize);
        echo myvalidate($buffer);
        ob_flush();
        flush();
        if ($retbytes) {
            $cnt += strlen($buffer);
        }
    }
    $status = fclose($handle);
    if ($retbytes && $status) {
        return $cnt;
    }
    return $status;
}

function dodlfile($file_path, $file_name, $mtype) {
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: public");
    header("Content-Description: File Transfer");
    header("Content-Type: $mtype");
    header("Content-Disposition: attachment; filename=\"$file_name\"");
    header("Content-Transfer-Encoding: binary");
    header("Content-Length: " . filesize($file_path));

    //@readfile($file_path);
    readfile_chunked($file_path);
}

function badgembrplanstatus($statusid, $mpstatus = 0, $mpnamestr = '', $imgstr = '') {
    global $LANG;

    $statusbadge = '';
    switch ($statusid) {
        case "1":
            $statustr = $LANG['g_active'];
            $statuclr = 'success';
            $statumrk = 'online';
            break;
        case "2":
            $statustr = $LANG['g_limited'];
            $statuclr = 'warning';
            $statumrk = 'away';
            break;
        case "3":
            $statustr = $LANG['g_pending'];
            $statuclr = 'danger';
            $statumrk = 'busy';
            break;
        default:
            $statustr = $LANG['g_inactive'];
            $statuclr = 'light';
            $statumrk = 'offline';
    }
    if ($imgstr == '') {
        $statusbadge .= "<span class='badge badge-{$statuclr}'>{$statustr}</span>";
    } else {
        $statusbadge .= '
                    <figure class="avatar mr-2 avatar-sm">
                      <img src="' . $imgstr . '" alt="...">
                      <i class="fa fa-id-badge text-' . $statuclr . ' avatar-icon" data-toggle="tooltip" title="' . $LANG['g_account'] . ' - ' . $statustr . '"></i>
                    </figure>
        ';
    }
    $mpnamestr = ($mpnamestr == '') ? $LANG['g_membership'] : $mpnamestr;
    $mpnamestr .= ' - ';
    switch ($mpstatus) {
        case "0":
            $statusbadge .= "<span class='badge badge-light' data-toggle='tooltip' title='{$mpnamestr}{$LANG['g_registeredonly']}'><i class='fa fa-fw fa-user'></i></span>";
            break;
        case "1":
            $statusbadge .= "<span class='badge badge-success' data-toggle='tooltip' title='{$mpnamestr}{$LANG['g_active']}'><i class='fa fa-fw fa-check'></i></span>";
            break;
        case "2":
            $statusbadge .= "<span class='badge badge-warning' data-toggle='tooltip' title='{$mpnamestr}{$LANG['g_expire']}'><i class='fa fa-fw fa-exclamation'></i></span>";
            break;
        case "3":
            $statusbadge .= "<span class='badge badge-danger' data-toggle='tooltip' title='{$mpnamestr}{$LANG['g_pending']}'><i class='fa fa-fw fa-times'></i></span>";
            break;
        default:
            $statusbadge .= "<span class='badge badge-light' data-toggle='tooltip' title='{$LANG['g_unregistered']}'><i class='fa fa-fw fa-question'></i></span>";
    }
    return $statusbadge;
}

// function to get ip address
function get_userip() {
    $ip = false;
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }
    return ($ip ? $ip : $_SERVER['REMOTE_ADDR']);
}

function redirpageto($destinationurl, $delay = 0) {
    $delay = intval($delay);
    echo "<meta http-equiv='refresh' content='{$delay};url={$destinationurl}'>";
    exit;
}

function formatdate($datetimestr, $type = 'd') {
    global $cfgrow;

    $dtformat = ($type == 'd') ? $cfgrow['sodatef'] : $cfgrow['lodatef'];
    return date($dtformat, strtotime($datetimestr));
}

function addlog_sess($username, $type = 'system', $rememberme = '') {
    global $db, $cfgrow;

    dellog_sess('member');

    $userip = get_userip();
    $mbrstr = getmbrinfo($username, 'username');
    $sesdata = put_optionvals('', 'un', $username);
    $sesdata = put_optionvals($sesdata, 'ip', $userip);

    $sestime = time() + (3600 * $cfgrow['time_offset']);
    $seskey = getpasshash($username . '|' . $userip);

    $data = array(
        'sestype' => $type,
        'sesidmbr' => intval($mbrstr['id']),
        'sesdata' => $sesdata,
        'sestime' => intval($sestime),
        'seskey' => $seskey,
    );

    $sesRow = getlog_sess($seskey);
    if ($sesRow['sesid'] < 1) {
        $db->insert(DB_TBLPREFIX . '_sessions', $data);
    } else {
        $db->update(DB_TBLPREFIX . '_sessions', $data, array('sesid' => $sesRow['sesid']));
    }

    $_SESSION[$cfgrow['md5sess'] . $type] = $seskey;
    if ($rememberme == 1) {
        setcookie($cfgrow['md5sess'] . $type, $seskey, time() + (3600 * 72) + (3600 * $cfgrow['time_offset']), "/");
    } else {
        setcookie($cfgrow['md5sess'] . $type, $seskey, time() + (3600 * 1) + (3600 * $cfgrow['time_offset']), "/");
    }
    return $seskey;
}

function getlog_sess($seskey, $isupdate = '') {
    global $db, $cfgrow;

    $condition = ' AND seskey = "' . $seskey . '" ';
    $row = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_sessions WHERE 1 " . $condition . "");
    $sesRow = array();
    foreach ($row as $value) {
        $sesRow = array_merge($sesRow, $value);
    }

    // update time
    if ($sesRow['sesid'] > 0 && $isupdate == 1) {
        $sestime = time() + (3600 * $cfgrow['time_offset']);
        $data = array(
            'sestime' => intval($sestime),
        );
        $db->update(DB_TBLPREFIX . '_sessions', $data, array('sesid' => $sesRow['sesid']));
    }
    return $sesRow;
}

function dellog_sess($type = '') {
    global $db, $cfgrow;

    if ($type != '') {
        // delete type session
        $_SESSION['filteruid'] = $_SESSION['clisti'] = $_SESSION['clistview'] = $_SESSION['dotoaster'] = $_SESSION['show_msg'] = '';
        $seskey = ($_SESSION[$cfgrow['md5sess'] . $type] ? $_SESSION[$cfgrow['md5sess'] . $type] : $_COOKIE[$cfgrow['md5sess'] . $type]);
        if ($seskey != '') {
            $db->delete(DB_TBLPREFIX . '_sessions', array('seskey' => $seskey));

            $_SESSION[$cfgrow['md5sess'] . $type] = '';
            setcookie($cfgrow['md5sess'] . $type, '', time() - (3600 * $cfgrow['time_offset']), "/");
        }
    } else {
        // delete old sessions
        $sqlarr = array();
        $tmintvarr = array("system" => (3600 * 6), "admin" => (3600 * 12), "member" => (3600 * 72));
        foreach ($tmintvarr as $key => $value) {
            $sestime = time() - $value;
            $sqlarr[] = "(sestype = '{$key}' AND sestime < {$sestime})";
        }
        $sqladd = implode(' OR ', $sqlarr);
        $condition = "AND ({$sqladd})";
        $db->doQueryStr("DELETE FROM " . DB_TBLPREFIX . "_sessions WHERE 1 " . $condition);
    }
}

function verifylog_sess($type = 'system', $isupdate = '') {
    global $cfgrow;

    $hasil = '';
    $seskey = ($_SESSION[$cfgrow['md5sess'] . $type] ? $_SESSION[$cfgrow['md5sess'] . $type] : $_COOKIE[$cfgrow['md5sess'] . $type]);

    $userip = get_userip();
    $sesRow = getlog_sess($seskey, $isupdate);
    $username = get_optionvals($sesRow['sesdata'], 'un');

    if (password_verify(md5($username . '|' . $userip), $seskey)) {
        $hasil = $seskey;
    } else {
        dellog_sess($seskey);
    }
    return $hasil;
}

function time_since($sestime) {
    global $cfgrow, $LANG;

    // "year, month, week, day, hour, minute, second"
    $timearr = explode(',', str_replace(' ', '', $LANG['g_timelist']));

    $since = time() + (3600 * $cfgrow['time_offset']) - $sestime;
    $chunks = array(
        array(60 * 60 * 24 * 365, $timearr[0]),
        array(60 * 60 * 24 * 30, $timearr[1]),
        array(60 * 60 * 24 * 7, $timearr[2]),
        array(60 * 60 * 24, $timearr[3]),
        array(60 * 60, $timearr[4]),
        array(60, $timearr[5]),
        array(1, $timearr[6])
    );

    for ($i = 0, $j = count($chunks); $i < $j; $i++) {
        $seconds = $chunks[$i][0];
        $name = $chunks[$i][1];
        if (($count = floor($since / $seconds)) != 0) {
            break;
        }
    }

    $print = ($count == 1) ? '1 ' . $name : "$count {$name}{$timearr[7]}";
    return $print;
}

function time_expiry($sestime, $isminsec = 0) {
    global $cfgrow, $LANG;

    // "year, month, week, day, hour, minute, second"
    $timearr = explode(',', str_replace(' ', '', $LANG['g_timelist']));

    $result = '';
    if ($sestime > $cfgrow['datetimestr']) {
        $expire = \DateTime::createFromFormat('Y-m-d H:i:s', $sestime);
        $now = new \DateTime();

        $diff = $expire->diff(($now));

        if ($diff->y) {
            $result .= $diff->y . ($diff->y > 1 ? " {$timearr[0]}{$timearr[7]} " : " {$timearr[0]} ");
        }
        if ($diff->m) {
            $result .= $diff->m . ($diff->m > 1 ? " {$timearr[1]}{$timearr[7]} " : " {$timearr[1]} ");
        }
        if ($diff->d) {
            $result .= $diff->d . ($diff->d > 1 ? " {$timearr[3]}{$timearr[7]} " : " {$timearr[3]} ");
        }
        if ($diff->h) {
            $result .= ' and ' . $diff->h . ($diff->h > 1 ? " {$timearr[4]}{$timearr[7]} " : " {$timearr[4]} ");
        }
        if ($diff->i && $isminsec == 1) {
            $result .= $diff->i . ($diff->i > 1 ? " {$timearr[5]}{$timearr[7]} " : " {$timearr[5]} ");
        }
        if ($diff->s && $isminsec == 1) {
            $result .= $diff->s . ($diff->s > 1 ? " {$timearr[6]}{$timearr[7]} " : " {$timearr[6]} ");
        }
    }
    return $result;
}

function showalert($type, $title, $message) {

    $faiconarr = array("info" => "lightbulb", "success" => "check-circle", "warning" => "question-circle", "danger" => "times-circle", "secondary" => "bell", "light" => "bell", "dark" => "bell", "primary" => "bell");
    $faicon = $faiconarr[$type];

    $alert_content = <<<INI_HTML
                <div class="alert alert-{$type} alert-dismissible alert-has-icon show fade">
                    <div class="alert-icon"><i class="far fa-{$faicon} fa-fw"></i></div>
                    <div class="alert-body">
                        <button class="close" data-dismiss="alert">
                            <span>&times;</span>
                        </button>
                        <div class="alert-title">{$title}</div>
                        {$message}
                    </div>
                </div>
INI_HTML;

    return $alert_content;
}

function getmbrinfo($id, $bfield = '', $mpid = 0, $ppid = 0) {
    global $db, $cfgrow, $cfgtoken, $LANG;

    $userRow = $mbrpparrall = $mbrpparract = array();
    $userRow['pparr_all'] = $userRow['pparr_act'] = $mbrpparrall;
    $bfield = ($bfield == '') ? 'id' : $bfield;

    if ($id != '') {
        $row = $db->getAllRecords(DB_TBLPREFIX . '_mbrs', '*', " AND {$bfield} = '{$id}'");
        foreach ($row as $value) {
            $userRow = array_merge($userRow, $value);
        }

        $condition = ($ppid > 0) ? " AND mppid = '{$ppid}'" : " ORDER BY cyclingbyid ASC, mpid DESC LIMIT 1";
        $row = $db->getAllRecords(DB_TBLPREFIX . '_mbrplans', '*', " AND idmbr = '{$userRow['id']}'" . $condition . "");
        foreach ($row as $value) {
            $userRow = array_merge($userRow, $value);
        }

        // get all registered plans in array
        $condition = " ORDER BY cyclingbyid ASC, mpid DESC";
        $row = $db->getAllRecords(DB_TBLPREFIX . '_mbrplans', '*', " AND idmbr = '{$userRow['id']}'" . $condition . "");
        foreach ($row as $value) {
            if ($value['mpstatus'] == 1) {
                $mbrpparract[] = $value['mppid'];
            }
            $mbrpparrall[] = $value['mppid'];
        }
        $userRow['pparr_all'] = $mbrpparrall;
        $userRow['pparr_act'] = $mbrpparract;
    }

    // plan member
    if ($mpid > 0) {
        $row = $db->getAllRecords(DB_TBLPREFIX . '_mbrplans', '*', " AND mpid = '{$mpid}'");
        foreach ($row as $value) {
            $userRow = array_merge($userRow, $value);
        }
        if ($id == '') {
            $row = $db->getAllRecords(DB_TBLPREFIX . '_mbrs', '*', " AND id = '{$userRow['idmbr']}'");
            foreach ($row as $value) {
                $userRow = array_merge($userRow, $value);
            }
        }
    }

    // payment options
    if ($userRow['id'] > 0) {
        $row = $db->getAllRecords(DB_TBLPREFIX . '_paygates', '*', " AND pgidmbr = '{$userRow['id']}'");
        foreach ($row as $value) {
            $userRow = array_merge($userRow, $value);
        }
    }

    if ($userRow['mbrstatus'] == '1' && $userRow['mpstatus'] == '1' && $cfgtoken['disreflink'] != 1) {
        $userRow['reflinkseo'] = $cfgrow['site_url'] . '/' . UIDFOLDER_NAME . '/' . $userRow['username'];
        $userRow['reflinkreg'] = $cfgrow['site_url'] . '/' . UIDFOLDER_NAME . '/?ref=' . $userRow['username'];
        $userRow['reflink'] = ($cfgtoken['isreflinkreg'] == 1) ? $userRow['reflinkreg'] : $userRow['reflinkseo'];
    }
    $statusaccarr = array(0 => $LANG['g_inactive'], 1 => $LANG['g_active'], 2 => $LANG['g_limited'], 3 => $LANG['g_pending']);
    $userRow['straccstatus'] = $statusaccarr[$userRow['mbrstatus']];
    $statusmbrarr = array(0 => $LANG['g_inactive'], 1 => $LANG['g_active'], 2 => $LANG['g_expire'], 3 => $LANG['g_pending']);
    $userRow['strmbrstatus'] = $statusmbrarr[$userRow['mpstatus']];

    $userRow['username'] = ($userRow['username'] == '') ? $cfgtoken['admin_subname'] : $userRow['username'];
    $userRow['firstname'] = ($userRow['username'] == $cfgtoken['admin_subname']) ? 'ADMIN' : $userRow['firstname'];
    $userRow['lastname'] = ($userRow['username'] == $cfgtoken['admin_subname']) ? 'Administrator' : $userRow['lastname'];
    $userRow['fullname'] = $userRow['firstname'] . ' ' . $userRow['lastname'];

    return $userRow;
}

function getusernameid($srcval, $targetstr = 'id') {
    global $db, $cfgtoken;

    if ($srcval < 1) {
        $userRow[$targetstr] = $cfgtoken['admin_subname'];
    } else {
        if ($targetstr == 'id') {
            $sqlwhere = "username LIKE '{$srcval}'";
        } else {
            $sqlwhere = "id = '{$srcval}'";
        }

        $userRow = array();
        $row = $db->getAllRecords(DB_TBLPREFIX . '_mbrs', '*', ' AND ' . $sqlwhere);
        foreach ($row as $value) {
            $userRow = array_merge($userRow, $value);
        }
    }

    return $userRow[$targetstr];
}

function parsenotify($cntarr, $msg) {
    foreach ((array) $cntarr as $key => $value) {
        $msg = str_replace("[[{$key}]]", $value, $msg);
    }

    // add custom parse
    $msg = str_replace("[[fullname]]", $cntarr['firtname'] . ' ' . $cntarr['lastname'], $msg);

    return $msg;
}

function printlog($idstr = '', $err = '') {
    global $cfgrow;

    if (defined('ISPRINTLOG')) {
        $datetm = date('Y-m-d H:i:s', time() + (3600 * $cfgrow['time_offset']));
        $myfile = file_put_contents('printlog.log', "[{$datetm}][{$idstr}] {$err}" . PHP_EOL, FILE_APPEND | LOCK_EX);
        return $myfile;
    }
}

function passmeter($password) {
    global $payrow, $LANG;

    if ($payrow['testpayon'] == 1) {
        return 1;
    }

    $uppercase = preg_match('#[A-Z]#', $password);
    $lowercase = preg_match('#[a-z]#', $password);
    $number = preg_match('#[0-9]#', $password);
    $specialChars = preg_match('#[^\w]#', $password);

    if (!$uppercase || !$lowercase || !$number || !$specialChars || strlen($password) < 8) {
        return $LANG['g_passmeter'];
    } else {
        return 1;
    }
}

function dosprlist($mpid, $sprlist, $mpdepth) {
    $sprlist = str_replace(' ', '', $sprlist);
    $sprlistarr = explode(',', $sprlist);
    $pos = 2;
    $mpid = intval($mpid);
    $newsprlist = array("|1:{$mpid}|");
    foreach ($sprlistarr as $key => $value) {
        $valarr = explode(':', $value);
        $sprval = intval(str_replace('|', '', $valarr[1]));
        $newsprlist[] = "|{$pos}:{$sprval}|";
        $pos++;
    }
    if ($mpdepth > 0) {
        $newsprlist = array_slice($newsprlist, 0, $mpdepth);
    }

    $newsprout = implode(', ', $newsprlist);
    return $newsprout;
}

function getsprlistid($sprlist, $tier = '') {
    $mpid = array();
    $sprlist = str_replace(array(' ', '|'), '', $sprlist);
    $sprlistarr = explode(',', $sprlist);
    foreach ($sprlistarr as $key => $value) {
        $valarr = explode(':', $value);
        $postier = intval($valarr[0]);
        $valtier = intval($valarr[1]);
        if ($tier != '' && $postier != $tier) {
            continue;
        }
        $mpid[$postier] = $valtier;
    }
    return $mpid;
}

function getamount($xcm, $regfee, $mrank = 0) {
    $cm = str_replace(' ', '', $xcm);
    if (floatval($regfee) <= 0) {
        $resamount = (strpos($cm, '%') !== false) ? 0 : $cm;
    } else {
        $resamount = (strpos($cm, '%') !== false) ? $cm * $regfee / 100 : $cm;
    }
    $resamountstr = sprintf('%0.2f', $resamount);
    return $resamountstr;
}

function getcmlist($sprstr, $sprlist, $cmlist, $mbrstr = array(), $trxstr = array()) {
    global $bpparr, $frlmtdcfg;

    $sprcmlist = array();

    // allow refer higher plan and get commission
    $sprppstr = getmbrinfo($sprstr['id'], '', '', $mbrstr['mppid']);
    if ((in_array($mbrstr['mppid'], $sprppstr['pparr_all']) && $sprppstr['mpstatus'] == 1) || $frlmtdcfg['isregallrefs'] == 1) {
        $mbr_fee = (strpos($trxstr['txtoken'], '|RENEW:') !== false) ? $mbrstr['renew_fee'] : $mbrstr['reg_fee'];
        $regnow_fee = (defined('ISAMOUNT_BYMBR')) ? $mbr_fee : $trxstr['txamount'];
        $mpdepth = $mbrstr['mpdepth'];

        $sprlistarr = explode(',', str_replace(array(' ', '|'), '', $sprlist));
        $defppid = ($frlmtdcfg['isgencmbyup'] != 1) ? $mbrstr['mppid'] : $sprstr['mppid'];
        $sprppidcmarr = get_sprppcm($defppid, $cmlist);
        for ($i = 0; $i < $mpdepth; $i++) {
            $j = $i + 1;
            $valarr = explode(':', $sprlistarr[$i]);
            $sprval = intval($valarr[1]);
            if ($sprval < 1) {
                break;
            }

            $sprlvlstr = getmbrinfo('', '', $sprval);
            $sprpidcm = $sprppidcmarr[$sprlvlstr['mppid']][$j];
            $sprcm = getamount($sprpidcm, $regnow_fee);
            $sprcmlist[$sprval] = $sprcm;
        }
    }

    return $sprcmlist;
}

function addcmlist($memo, $tokencode, $valcmlist = array(), $mbrstr = array(), $trxstr = array(), $addtxtoken = '') {
    global $db, $cfgrow, $bpprow;

    if (!function_exists('delivermail')) {
        require_once(INSTALL_PATH . '/common/mailer.do.php');
    }
    $reg_utctime = $cfgrow['datetimestr'];
    $addtxtoken = ($addtxtoken != '') ? ', ' . trim($addtxtoken, ',') : '';

    $cmcount = 1;
    foreach ((array) $valcmlist as $key => $value) {
        $sprstr = getmbrinfo('', '', $key);
        $txamount = (float) $value;
        $txtoken = "|SRCTXID:{$trxstr['txid']}|, |SRCIDMBR:{$mbrstr['id']}|, |SRCLVPOS:{$cmcount}|, |LCM:{$tokencode}|";

        // avoid duplication using hash
        $txonehash = md5($sprstr['id'] . $txamount . $mbrstr['mppid'] . $txtoken);
        $condition = " AND txtoken LIKE '%|txonehash:$txonehash|%'";
        $existTxData = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_transactions WHERE 1 " . $condition . "");
        if (count($existTxData) > 0) {
            continue;
        }
        if ($key > 0 && $txamount > 0) {
            $cmcountstr = (strpos($tokencode, 'TIER') !== false) ? " [{$cmcount}]" : '';
            $data = array(
                'txdatetm' => $reg_utctime,
                'txtoid' => $sprstr['id'],
                'txamount' => $txamount,
                'txmemo' => $memo . $cmcountstr,
                'txppid' => $mbrstr['mppid'],
                'txtoken' => $txtoken . $addtxtoken . ", |txonehash:$txonehash|",
            );
            $insert = $db->insert(DB_TBLPREFIX . '_transactions', $data);

            if ($insert && $sprstr['id'] > 0) {
                $cntaddarr['ncm_memo'] = $memo . $cmcountstr;
                $cntaddarr['ncm_amount'] = $bpprow['currencysym'] . $txamount . ' ' . $bpprow['currencycode'];
                $cntaddarr['dln_username'] = $mbrstr['username'];
                delivermail('mbr_newcm', $sprstr['id'], $cntaddarr);
            }
            $cmcount++;
        }
    }

    return $cmcount;
}

function dolvldone($mbrstr, $trxstr, $mppid = 1) {
    global $db, $bpparr, $frlmtdcfg;

    $dirsprstr = ($frlmtdcfg['isregallrefs'] == 1) ? getmbrinfo($mbrstr['idspr']) : getmbrinfo($mbrstr['idspr'], '', '', $mbrstr['mppid']);
    $mpidspr = ($dirsprstr['mppid'] > $mbrstr['mppid']) ? $mbrstr['mppid'] : $dirsprstr['mppid'];

    $rwlist = ($frlmtdcfg['isgencmbyup'] != 1) ? $bpparr[$mbrstr['mppid']]['rwlist'] : $bpparr[$mpidspr]['rwlist'];
    for ($i = 1; $i <= $mbrstr['mpdepth']; $i++) {
        $mpidarr = getsprlistid($mbrstr['sprlist'], $i);
        $mpid = $mpidarr[$i];
        if ($mpid < 1 || $mbrstr['mpwidth'] <= 0) {
            break;
        } else {
            $sprtag = "|{$i}:{$mpid}|";
            $condition = " AND sprlist LIKE '%{$sprtag}%' AND mpstatus != '0'";
            $row = $db->getAllRecords(DB_TBLPREFIX . '_mbrplans', 'COUNT(*) as totref', $condition);
            $myreftotal = $row[0]['totref'];

            $ix = $i;
            if (pow($mbrstr['mpwidth'], $ix) == $myreftotal) {
                $sprstr = getmbrinfo('', '', $mpid);
                $rwdx = "FRWD{$mpid}-{$ix}";
                $condition = ' AND txtoid = "' . $sprstr['id'] . '" AND txppid = "' . $mppid . '" AND txtoken LIKE "' . "%|LCM:{$rwdx}|%" . '" ';
                $sql = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_transactions WHERE 1 " . $condition . "");
                if (count($sql) < 1) {
                    $iy = $ix - 1;
                    $rwlistarr = explode(',', str_replace(' ', '', $rwlist));
                    $fixedrwd = getamount($rwlistarr[$iy], $trxstr['txamount']);
                    $getcmlist = array($sprstr['mpid'] => $fixedrwd);
                    addcmlist("Level Reward", "{$rwdx}", $getcmlist, $mbrstr, $trxstr);

                    //process available commission to wallet
                    dotrxwallet();
                }

                if ($mbrstr['mpdepth'] == $i) {
                    $isrecycling = $bpparr[$mbrstr['mppid']]['isrecycling'];
                    if ($isrecycling > 0) {
                        $mbrcyc = getmbrinfo('', '', $mpid);
                        if ($isrecycling == 3) {
                            // process cycling fee only
                            $nowregfee = $bpparr[$mbrstr['mppid']]['regfee'];
                            $newamount = $mbrcyc['ewallet'] - $nowregfee;
                            $recycppname = $bpparr[$mbrstr['mppid']]['ppname'];
                            //syntax:
                            //adjusttrxwallet($oldamount, $newamount, $idmbr, $txtokenstr = '', $txadminfo = '', $isminval = 0, $isrenewtxid = 0);
                            adjusttrxwallet($mbrcyc['ewallet'], $newamount, $mbrcyc['id'], "Repayment {$recycppname}");
                            $data = array(
                                'ewallet' => $newamount,
                            );
                            $db->update(DB_TBLPREFIX . '_mbrs', $data, array('id' => $mbrcyc['id']));
                        } else {
                            // re-entry to the same plan
                            if ($isrecycling == 1) {
                                $entrytoidmbr = $mbrcyc['idspr'];
                            } else {
                                $entrytoidmbr = $mbrcyc['idref'];
                            }
                            do_autoregplan($mbrcyc, $mbrstr['mpid'], $entrytoidmbr, $mbrcyc['mppid']);
                        }
                    }

                    // re-entry to another plan
                    $recyclingto = $bpparr[$mbrstr['mppid']]['recyclingto'];
                    if ($recyclingto > 0) {
                        $mbrcyc = getmbrinfo('', '', $mpid);
                        if ($isrecycling == 1) {
                            $entrytoidmbr = $mbrcyc['idspr'];
                        } else {
                            $entrytoidmbr = $mbrcyc['idref'];
                        }
                        do_autoregplan($mbrcyc, $mbrstr['mpid'], $entrytoidmbr, $recyclingto);
                    }

                    // process cycling fee
                    $recyclingfee = $bpparr[$mbrstr['mppid']]['recyclingfee'];
                    if (floatval($recyclingfee) > 0) {
                        $mbrcyc = getmbrinfo('', '', $mpid);

                        // commission
                        $maxfullrward = $getcmlist[$mbrcyc['mpid']];
                        // plan reg
                        $nowregfee = $bpparr[$mbrstr['mppid']]['regfee'];
                        // next plan reg
                        $nextregfee = $bpparr[$recyclingto]['regfee'];
                        // get cycling fee
                        $netpoolrwrd = $maxfullrward - $nowregfee - $nextregfee;
                        $getcycfee = getamount($recyclingfee, $netpoolrwrd);

                        $newamount = $mbrcyc['ewallet'] - $getcycfee;
                        $recycppname = $bpparr[$mbrstr['mppid']]['ppname'];
                        //syntax:
                        //adjusttrxwallet($oldamount, $newamount, $idmbr, $txtokenstr = '', $txadminfo = '', $isminval = 0, $isrenewtxid = 0);
                        adjusttrxwallet($mbrcyc['ewallet'], $newamount, $mbrcyc['id'], "Admin Charge {$recycppname}");
                        $data = array(
                            'ewallet' => $newamount,
                        );
                        $db->update(DB_TBLPREFIX . '_mbrs', $data, array('id' => $mbrcyc['id']));
                    }
                }
            }
        }
    }
}

function regmbrplans($mbrstr = array(), $refidmbr = '', $ppid = 1, $existmpid = 0) {
    global $db, $cfgrow, $bpprow, $LANG, $frlmtdcfg;

    $existmpid = intval($existmpid);

    $resultarr = array();
    $resultarr['mpid'] = $resultarr['txid'] = $resultarr['regfee'] = 0;

    // start: define whether under the same plan or not
    $refstr = getmbrinfo($refidmbr);
    if ($frlmtdcfg['isxplans'] == 1) {
        if (in_array($ppid, $refstr['pparr_act'])) {
            // if referrer registered to the plan and active
            $refstr = getmbrinfo($refidmbr, '', '', $ppid);
        } else {
            // if referrer is not registered to the same plan, use admin as sposnor
            $refstr = getmbrinfo('', '', 0);
        }
    }
    // end: define whether under the same plan or not

    $refmpid = intval($refstr['mpid']);
    $orirefid = intval($refidmbr);

    // disable self referring
    if ($refstr['username'] == $mbrstr['username'] && $frlmtdcfg['isselfreferring'] != 1) {
        $refstr = getmbrinfo('', '', 0);
    }

    $mppid = intval($ppid);
    $idref = intval($refstr['id']);
    $idmbr = $mbrstr['id'];

    // stages
    $stgId = $mppid;
    if ($stgId < 1 || $stgId > $frlmtdcfg['mxstages']) {
        $stgId = 1;
    }
    $row = $db->getAllRecords(DB_TBLPREFIX . '_payplans', '*', ' AND ppid = "' . $stgId . '"');
    foreach ($row as $value) {
        $bpprow = array_merge($bpprow, $value);
    }

    $condition = " AND idmbr = '{$idmbr}' AND mppid = '{$stgId}' AND cyclingbyid = '0'";
    $sql = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_mbrplans WHERE 1 " . $condition . "");
    if ($bpprow['planstatus'] == 1 && count($sql) < 1) {
        $reg_date = date('Y-m-d', time() + (3600 * $cfgrow['time_offset']));
        $reg_utctime = date('Y-m-d H:i:s', time() + (3600 * $cfgrow['time_offset']));
        $reg_ip = get_userip();

        $mpstatus = ($bpprow['regfee'] <= 0) ? 1 : 0;
        $reg_expd = $reg_date;

        $is_ppsubscr = is_ppsubscr($stgId);
        if ($is_ppsubscr) {
            $expdarr = get_actdate($bpprow['expday']);
            $reg_expd = $expdarr['next'];
        }

        $renew_fee = ($bpprow['renewfee'] > 0) ? floatval($bpprow['renewfee']) : floatval($bpprow['regfee']);

        $rprmpid = getmpidflow($refmpid, $stgId, $mbrstr);
        $sprstr = getmbrinfo('', '', $rprmpid);
        $idspr = intval($sprstr['id']);

        // self referring
        if (($idmbr == $idref || $idmbr == $idspr) && $frlmtdcfg['isselfreferring'] != 1) {
            $idref = $idspr = 0;
            $sprlist = '';
        } else {
            $sprlist = dosprlist($sprstr['mpid'], $sprstr['sprlist'], $sprstr['mpdepth']);
        }

        $existmbr = getmbrinfo('', '', $existmpid);
        $isexistmpid = ($existmbr['mpid'] > 0) ? $existmpid : 0;
        if ($isexistmpid > 0) {
            $data = array(
                'mppid' => $stgId,
                'isdefault' => 1,
                'reg_date' => $reg_date,
                'reg_expd' => $reg_expd,
                'reg_ip' => $reg_ip,
                'reg_fee' => (float) $bpprow['regfee'],
                'renew_fee' => (float) $renew_fee,
                'mpstatus' => $mpstatus,
                'idref' => $idref,
                'idspr' => $idspr,
                'sprlist' => $sprlist,
                'mpwidth' => $bpprow['maxwidth'],
                'mpdepth' => $bpprow['maxdepth'],
            );
            $update = $db->update(DB_TBLPREFIX . '_mbrplans', $data, array('mpid' => $isexistmpid));
            $resultarr['mpid'] = $isexistmpid;
        } else {
            $idhostmbr = 0;
            $hostspr = ($orirefid != $idref) ? $orirefid : 0;

            $data = array(
                'idhostmbr' => $idhostmbr,
                'idmbr' => $idmbr,
                'mppid' => $stgId,
                'isdefault' => 1,
                'reg_date' => $reg_date,
                'reg_expd' => $reg_expd,
                'reg_utctime' => $reg_utctime,
                'reg_ip' => $reg_ip,
                'reg_fee' => (float) $bpprow['regfee'],
                'renew_fee' => (float) $renew_fee,
                'mpstatus' => $mpstatus,
                'hostspr' => $hostspr,
                'idref' => $idref,
                'idspr' => $idspr,
                'sprlist' => $sprlist,
                'mpwidth' => $bpprow['maxwidth'],
                'mpdepth' => $bpprow['maxdepth'],
            );
            $insert = $db->insert(DB_TBLPREFIX . '_mbrplans', $data);
            $newmbrplanid = $db->lastInsertId();
            $resultarr['mpid'] = $newmbrplanid;
        }
        // get updated $mbrstr
        $mbrstr = getmbrinfo('', '', $resultarr['mpid']);

        if ($update || $insert) {
            $_SESSION['dotoaster'] = "toastr.success({$LANG['g_toastsuccessinfo']}, {$LANG['g_toastsuccess']});";

            // add transaction records
            if ($bpprow['regfee'] > 0) {
                $data = array(
                    'txdatetm' => $reg_utctime,
                    'txfromid' => $idmbr,
                    'txamount' => (float) $bpprow['regfee'],
                    'txmemo' => $LANG['g_registrationfee'],
                    'txppid' => $stgId,
                    'txtoken' => "|REG:{$resultarr['mpid']}|",
                );
                $insert = $db->insert(DB_TBLPREFIX . '_transactions', $data);
                $newtrxid = $db->lastInsertId();
                $resultarr['txid'] = $newtrxid;
                $resultarr['regfee'] = (float) $bpprow['regfee'];
            }

            // send new referral signup
            if ($idspr > 0 && $isexistmpid == 0) {
                if (!function_exists('delivermail')) {
                    require_once(INSTALL_PATH . '/common/mailer.do.php');
                }
                $cntaddarr['ppname'] = $bpprow['ppname'];
                $cntaddarr['dln_fullname'] = $mbrstr['firstname'] . " " . $mbrstr['lastname'];
                $cntaddarr['dln_username'] = $mbrstr['username'];
                delivermail('mbr_newdl', $idspr, $cntaddarr);
            }
        } else {
            $_SESSION['dotoaster'] = "toastr.error({$LANG['g_toastfailinfo']}, {$LANG['g_toastfail']});";
        }

        return $resultarr;
    }
}

function iscontentmbr($pgavalon, $pgppids, $mbrstr) {
    $hasil = true;

    $mppidarr = mbrpparr($mbrstr['id']);
    $mbrppidarr = $mppidarr['mppid'];
    $pgppidsarr = str_getcsv($pgppids);
    $tmatch = count(array_intersect((array) $mbrppidarr, $pgppidsarr));
    $avalfor = get_optionvals($pgavalon);

    if ($avalfor['mbr'] == '1') {
        if ($avalfor['mbpp1'] != '1' && $mbrstr['mpstatus'] == 1) {
            $hasil = false;
        }
        if ($avalfor['mbpp0'] != '1' && $mbrstr['mpstatus'] != 1) {
            $hasil = false;
        }
        if ($tmatch == 0) {
            $hasil = false;
        }
    } else {
        if ($tmatch == 0 && $mbrstr['mpid'] > 0) {
            $hasil = false;
        }
    }

    return $hasil;
}

function dotrxwallet($txtoid = 0, $limit = 25) {
    global $db, $cfgrow;

    $sqltoid = ($txtoid == 0) ? "txtoid > '0'" : "txtoid = '{$txtoid}'";
    $ListData = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_transactions WHERE 1 AND txfromid = '0' AND " . $sqltoid . " AND txstatus = '0' AND txtoken NOT LIKE '%|WIDR:%' LIMIT {$limit}");
    if (count($ListData) > 0) {
        $numcount = $ewallet = 0;
        $txtmstamp = date('Y-m-d H:i:s', time() + (3600 * $cfgrow['time_offset']));
        foreach ($ListData as $val) {

            $txbatch = 'WLN' . date("dH-ims-") . $val['txid'];
            $txtoken = $val['txtoken'] . ', |WALT:IN|';

            $data = array(
                'txpaytype' => 'system',
                'txbatch' => $txbatch,
                'txtmstamp' => $txtmstamp,
                'txtoken' => $txtoken,
                'txstatus' => 1,
            );
            $update = $db->update(DB_TBLPREFIX . '_transactions', $data, array('txid' => $val['txid']));

            $mbrstr = getmbrinfo($val['txtoid']);
            $ewallet = $mbrstr['ewallet'] + $val['txamount'];
            $update = $db->update(DB_TBLPREFIX . '_mbrs', array('ewallet' => $ewallet), array('id' => $mbrstr['id']));

            $numcount++;
            if ($numcount < 1) {
                break;
            }
        }
    }
}

function adjusttrxwallet($oldamount, $newamount, $idmbr, $txtokenstr = '', $txadminfo = '', $isminval = 0, $isrenewtxid = 0) {
    global $db, $cfgrow, $LANG;

    if ($oldamount != $newamount && ($newamount > 0 || $isminval == 1)) {

        $hittxrow = $db->getRecFrmQry("SELECT COUNT(txid) as hittx FROM " . DB_TBLPREFIX . "_transactions");
        $hittx = $hittxrow[0]['hittx'] + 1;
        $numrand = mt_rand(10, 99);
        $txbatch = date("dH-{$idmbr}i-s{$numrand}{$hittx}");
        if ($oldamount < $newamount) {
            // add
            $txfromid = 0;
            $txtoid = $idmbr;
            $txamount = $newamount - $oldamount;
            $txmemo = "Wallet Credit Correction";
            $txbatch = 'WLN' . $txbatch;
            $txtoken = '|WALT:IN|';
        } else {
            $memostr = (strpos($txtokenstr, 'REREG') !== false) ? $LANG['g_reentryfee'] : $LANG['g_renewalfee'];
            // deduct
            $txfromid = $idmbr;
            $txtoid = 0;
            $txamount = $oldamount - $newamount;
            $txmemo = ($isrenewtxid > 0) ? $memostr : "Wallet Debit Correction";
            $txbatch = 'WLT' . $txbatch;
            $txtoken = '|WALT:OUT|';
        }

        $mbrstr = getmbrinfo($idmbr);
        $txamount = (float) $txamount;

        $txtoken64 = base64_encode($txtokenstr);
        $txtoken = $txtoken . ", |NOTE:{$txtoken64}|";

        $txdatetm = date('Y-m-d H:i:s', time() + (3600 * $cfgrow['time_offset']));
        $data = array(
            'txdatetm' => $txdatetm,
            'txfromid' => $txfromid,
            'txtoid' => $txtoid,
            'txpaytype' => ($isrenewtxid > 0) ? 'wallet' : 'system',
            'txamount' => $txamount,
            'txmemo' => $txmemo,
            'txbatch' => $txbatch,
            'txtmstamp' => $txdatetm,
            'txppid' => $mbrstr['mppid'],
            'txstatus' => ($isrenewtxid > 0) ? 0 : 1,
            'txadminfo' => $txadminfo,
        );

        if ($isrenewtxid > 0) {
            // adjust token from existing transaction
            $txunpaidrow = $db->getRecFrmQry("SELECT txtoken FROM " . DB_TBLPREFIX . "_transactions WHERE txid = '$isrenewtxid'");
            $oritxtoken = $txunpaidrow[0]['txtoken'];
            $data['txtoken'] = $oritxtoken . ', ' . $txtoken;

            // update existing transaction id
            $update = $db->update(DB_TBLPREFIX . '_transactions', $data, array('txid' => $isrenewtxid));
        } else if ($txfromid > 0 || $txtoid > 0) {
            $data['txtoken'] = $txtoken;
            $insert = $db->insert(DB_TBLPREFIX . '_transactions', $data);
        }
    }
}

function getwebssdata($mbrstr, $url) {
    $mbrid = $mbrstr['id'];
    if (function_exists('curl_init') && intval($mbrid) > 0 && filter_var($url, FILTER_VALIDATE_URL) !== FALSE && $_SESSION['getwebssdata' . $mbrid] == '') {
        $ch = curl_init("https://www.googleapis.com/pagespeedonline/v2/runPagespeed?url={$url}&screenshot=true");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $response = curl_exec($ch);
        curl_close($ch);
        $googlepsdata = json_decode($response, true);
        $snap = $googlepsdata['screenshot']['data'];
        $snap = str_replace(['_', '-'], ['/', '+'], $snap);

        if ($snap) {
            $imgtofile = "/assets/imagextra/mbr_imgsrc_{$mbrid}.dat";
            $datfile = INSTALL_PATH . $imgtofile;
            file_put_contents($datfile, $snap, LOCK_EX);
            $_SESSION['getwebssdata' . $mbrid] = 1;
            return $imgtofile;
        }
    }
}

function getdocurl($initurl, $arrdata) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $initurl);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($arrdata, '', '&'));
    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        $arrResponse['err'] = curl_error($ch);
    }
    curl_close($ch);
    $arrResponse['data'] = json_decode($response, true);
    return $arrResponse;
}

function do_imgresize($targetFile, $originalFile, $newWidth, $newHeight = 0, $ext = '') {

    $info = getimagesize($originalFile);
    $mime = ($ext == '') ? $info['mime'] : "image/{$ext}";

    switch ($mime) {
        case 'image/jpeg':
            $image_save_func = 'imagejpeg';
            $new_image_ext = 'jpg';
            break;

        case 'image/png':
            $image_save_func = 'imagepng';
            $new_image_ext = 'png';
            break;

        case 'image/gif':
            $image_save_func = 'imagegif';
            $new_image_ext = 'gif';
            break;

        default:
            exit();
    }

    $img = imagecreatefromstring(file_get_contents($originalFile));
    list($width, $height) = getimagesize($originalFile);

    $propHeight = ($height / $width) * $newWidth;
    $newHeight = ($newHeight > 0) ? $newHeight : $propHeight;
    $tmp = imagecreatetruecolor($newWidth, $newHeight);
    imagecopyresampled($tmp, $img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

    $targetFile = '../assets/imagextra/' . $targetFile;

    if (file_exists($targetFile)) {
        unlink($targetFile);
    }
    $newimg = "$targetFile.$new_image_ext";
    $image_save_func($tmp, $newimg);
    return $newimg;
}

/* usage example:
  $resultdate = get_actdate($intvdatetime, $basedate);
  $resultdate['var'] = $intvdatetime type ('H', 'D', 'W', 'M', 'Y', ''=in days)
  $resultdate['var_str'] = $intvdatetime type ('Hour', 'Day', 'Week', 'Month', 'Year', ''=in days)
  $resultdate['val'] = value from the $intvdatetime, example 10 -> 10, 12w -> 12, 4m -> 4, etc;
  $resultdate['val_str'] = value from the $intvdatetime in days, example 10 -> 10, 23h -> 0, 5d -> 5, 2w -> 14, 1m -> 30, etc;
  $resultdate['next'] = $basedate + $intvdatetime;
  $resultdate['prev'] = $basedate - $intvdatetime;
  $resultdate['now'] = $basedate;
  $resultdate['diffdays'] = different (in days) between $basedate and $resultdate['next'];
 */

function get_actdate($intvdatetime, $basedate = '') {
    global $cfgrow;

    $basedate = ($basedate == '') ? date('Y-m-d H:i:s', time() + (3600 * $cfgrow['time_offset'])) : $basedate;
    $arrdate = getdate(strtotime($basedate));
    $istime = (strlen($basedate) > 12 && $arrdate['hours'] != '') ? 'y' : 'n';

    $result = array();
    $intvdatetime = str_replace(" ", "", strtoupper($intvdatetime));
    if (!is_numeric($intvdatetime)) {
        $result['var'] = substr($intvdatetime, -1);
        $result['val'] = str_replace($result['var'], "", $intvdatetime);
        $result['val'] = intval($result['val']);

        switch ($result['var']) {
            case "H":
                $result['var_str'] = 'Hour';
                $result['val_str'] = $result['val'] * 0;
                $strjng = 'hour';
                break;
            case "W":
                $result['var_str'] = 'Week';
                $result['val_str'] = $result['val'] * 7;
                $strjng = 'week';
                break;
            case "M":
                $result['var_str'] = 'Month';
                $result['val_str'] = $result['val'] * 30;
                $strjng = 'month';
                break;
            case "Y":
                $result['var_str'] = 'Year';
                $result['val_str'] = $result['val'] * 365;
                $strjng = 'year';
                break;
            default:
                $result['var_str'] = 'Day';
                $result['val_str'] = $result['val'];
                $strjng = 'day';
        }

        if ($result['val'] > 1)
            $strjng .= 's';
    } else {
        $result['var'] = 'D';
        $result['var_str'] = 'Day';
        $strjng = 'day';
        $result['val'] = $result['val_str'] = intval($intvdatetime);
        if ($result['val'] > 1)
            $strjng .= 's';
    }

    $str_basedate = strtotime($basedate);
    $str_diffdate = $result['val'] . ' ' . $strjng;
    $str_basedate_add = strtotime("+" . $str_diffdate, $str_basedate);
    $str_basedate_les = strtotime("-" . $str_diffdate, $str_basedate);

    if ($istime == 'y') {
        $result['next'] = date("Y-m-d H:i:s", $str_basedate_add);
        $result['prev'] = date("Y-m-d H:i:s", $str_basedate_les);
    } else {
        $result['next'] = date("Y-m-d", $str_basedate_add);
        $result['prev'] = date("Y-m-d", $str_basedate_les);
    }

    $result['now'] = $basedate;
    $dateTimeEnd = $result['next'];
    $dateTimeBegin = $result['now'];

    $timedifference = strtotime($dateTimeEnd) - strtotime($dateTimeBegin);
    $result['diffdays'] = floor($timedifference / 86400);

    return $result;
}

function get_unpaidtxid($mbrstr) {
    global $db;

    $txunpaidrow = $db->getRecFrmQry("SELECT txid FROM " . DB_TBLPREFIX . "_transactions WHERE txfromid = '{$mbrstr['id']}' AND txppid = '{$mbrstr['mppid']}' AND (txtoken LIKE '%|REG:%' OR txtoken LIKE '%|RENEW:%') AND txamount > 0 AND txstatus = '0'");
    return $txunpaidrow[0]['txid'];
}

function do_expmbr($limitcheck = 48) {
    global $db, $cfgrow, $bptoken, $bpparr;

    $reg_utctime = date('Y-m-d H:i:s', time() + (3600 * $cfgrow['time_offset']));
    $now_date = date('Y-m-d', time() + (3600 * $cfgrow['time_offset']));
    foreach ($bpparr as $key => $value) {


        $graceday = floatval($value['graceday']);

        $is_ppsubscr = is_ppsubscr($value['ppid']);
        if ($is_ppsubscr) {

            //reminder
            $remindreg = $bptoken['remindreg'];
            if (intval($remindreg) > 0) {
                $expdarr = get_actdate($remindreg, $now_date);
                $remindate = $expdarr['next'];
                $condition = " AND mpstatus = '1' AND mppid = '{$value['ppid']}' AND reg_expd <= '{$remindate}' AND rmdexp = '0' ORDER BY RAND() LIMIT {$limitcheck}";
                $userData = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_mbrs LEFT JOIN " . DB_TBLPREFIX . "_mbrplans ON id = idmbr WHERE 1 " . $condition . "");

                if (count($userData) > 0) {
                    foreach ($userData as $val) {
                        // send message here
                        require_once('mailer.do.php');
                        $cntaddarr['ppname'] = $value['ppname'];
                        $cntaddarr['fullname'] = $val['firstname'] . ' ' . $val['lastname'];
                        $cntaddarr['login_url'] = $cfgrow['site_url'] . "/" . MBRFOLDER_NAME;
                        delivermail('mbr_rereg', $val['id'], $cntaddarr);

                        $db->update(DB_TBLPREFIX . '_mbrplans', array('rmdexp' => '1'), array('mpid' => $val['mpid']));

                        do_renewtx($reg_utctime, $val);
                    }
                }
            }

            //expired
            $grace_prev = date('Y-m-d', strtotime('-' . $graceday . ' day', strtotime($reg_utctime)));

            $condition = " AND (mpstatus = '1' OR mpstatus = '2') AND mppid = '{$value['ppid']}' AND reg_expd < '{$reg_utctime}' ORDER BY RAND() LIMIT {$limitcheck}";
            $userData = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_mbrs LEFT JOIN " . DB_TBLPREFIX . "_mbrplans ON id = idmbr WHERE 1 " . $condition . "");
            if (count($userData) > 0) {
                foreach ($userData as $val) {
                    do_renewtx($reg_utctime, $val);

                    if ($val['mpstatus'] == '1' && $graceday > 0 && $val['reg_expd'] < $grace_prev && $val['reg_date'] < $val['reg_expd'] && $val['reg_fee'] > 0) {
                        $db->update(DB_TBLPREFIX . '_mbrplans', array('mpstatus' => 2), array('mpid' => $val['mpid']));
                    }
                }
            }

            // auto-renewal using available ewallet balance
            $isrenewbywallet = get_optionvals($value['plantoken'], 'isrenewbywallet');

            if ($isrenewbywallet == '1') {
                $condition = " AND mpstatus = '2' ORDER BY RAND() LIMIT {$limitcheck}";
                $userData = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_mbrs LEFT JOIN " . DB_TBLPREFIX . "_mbrplans ON id = idmbr WHERE 1 " . $condition . "");
                if (count($userData) > 0) {
                    foreach ($userData as $val) {
                        if ($val['ewallet'] >= $val['renew_fee'] && $val['renew_fee'] > 0) {
                            $mbrstr = getmbrinfo('', '', $val['mpid']);
                            do_walletplanpay($mbrstr);
                        }
                    }
                }
            }
        }
    }
}

function do_walletplanpay($mbrstr) {
    global $db, $cfgrow, $bpparr;

    $txid = get_unpaidtxid($mbrstr);
    $mpid = $mbrstr['mpid'];
    $newmppid = $mbrstr['mppid'];

    $condition = " AND txid = '{$txid}'";
    $txrow = $db->getAllRecords(DB_TBLPREFIX . '_transactions', '*', $condition);
    $txrowamount = $txrow[0]['txamount'];

    if ($mbrstr['ewallet'] >= $txrowamount) {
        $txbatch = "R" . date("md") . "-" . date("H") . "{$mpid}";
        $newamount = $mbrstr['ewallet'] - $txrowamount;

        $txtokenstr = "Wallet Debit [RENEW] " . $bpparr[$newmppid]['ppname'];
        adjusttrxwallet($mbrstr['ewallet'], $newamount, $mbrstr['id'], $txtokenstr, '', 1, $txid);
        $data = array(
            'ewallet' => $newamount,
        );
        $db->update(DB_TBLPREFIX . '_mbrs', $data, array('id' => $mbrstr['id']));

        include_once('sandbox.php');
        $FORM['sb_type'] = 'payreg';
        $txmpid = $txid . '-' . $mpid;
        $paygate = 'wallet';
        doipnbox($txmpid, $txrowamount, $paygate, $txbatch, '-HTTPREF-', '');

        $mpstatus = ($newamount >= 0) ? 1 : 3;
        $data = array(
            'mpstatus' => $mpstatus,
        );
        $update = $db->update(DB_TBLPREFIX . '_mbrplans', $data, array('mpid' => $mpid));
    }
}

function do_prerenewtx($txmpid, $mpstatus) {
    global $cfgrow;

    if ($mpstatus == 2) {
        // if expiry check transaction history and generate it if not exist
        $sb_txmpidarr = explode('-', $txmpid);
        $mpid = $sb_txmpidarr[1];
        $reg_utctime = date('Y-m-d H:i:s', time() + (3600 * $cfgrow['time_offset']));
        $mbrstr = getmbrinfo('', '', $mpid);
        $txid = do_renewtx($reg_utctime, $mbrstr);
        $newtxmpid = $txid . '-' . $mpid;
    } else {
        $newtxmpid = $txmpid;
    }
    return $newtxmpid;
}

function do_renewtx($utctime, $mbrevalarr) {
    global $db, $bpparr, $LANG;

    $renewfee = $bpparr[$mbrevalarr['mppid']]['renewfee'];
    $renew_fee = ($renewfee > 0) ? $renewfee : $mbrevalarr['reg_fee'];

    $sql = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_transactions WHERE txfromid = '{$mbrevalarr['id']}' AND txppid = '{$mbrevalarr['mppid']}' AND txtoken LIKE '%|PREVEXP:{$mbrevalarr['reg_expd']}|%'");
    if ($renew_fee > 0 && count($sql) < 1) {
        $data = array(
            'txdatetm' => $utctime,
            'txfromid' => $mbrevalarr['id'],
            'txamount' => (float) $renew_fee,
            'txmemo' => $LANG['g_renewalfee'],
            'txppid' => $mbrevalarr['mppid'],
            'txtoken' => "|RENEW:{$mbrevalarr['mpid']}|, |PREVEXP:{$mbrevalarr['reg_expd']}|",
        );
        $db->insert(DB_TBLPREFIX . '_transactions', $data);
        $txid = $db->lastInsertId();
    } else {
        $txid = $sql[0]['txid'];
    }
    return $txid;
}

function maskmail($email) {
    if (!defined('ISDEMOMODE')) {
        return $email;
    } else {
        $em = explode("@", $email);
        $name = implode('@', array_slice($em, 0, count($em) - 1));
        $len = floor(strlen($name) / 2);
        return substr($name, 0, $len) . str_repeat('*', $len) . "*@" . end($em);
    }
}

function get_countrycode($log_ip) {
    global $country_array;

    require_once('geoip.class.php');
    $geoplugin = new geoPlugin();
    $geoplugin->locate($log_ip);

    $countryc = $geoplugin->countryCode;
    $countryc = strtoupper($countryc);
    if (array_key_exists($countryc, $country_array)) {
        return $countryc;
    } else {
        return '';
    }
}

function ppdblist($ppidarr = array(), $listall = 0) {
    global $db;

    $result = '';
    $condition = ($listall != 1) ? " AND planstatus = '1'" : '';
    $userData = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_payplans WHERE 1 AND ppname != '' " . $condition . "");
    if (count($userData) > 0) {
        foreach ($userData as $val) {
            $isselect = (in_array($val['ppid'], $ppidarr)) ? " selected" : "";
            $isselect = ($val['planstatus'] != '1') ? " disabled" : $isselect;
            $result .= "<option value='{$val['ppid']}'{$isselect}>{$val['ppname']}";
        }
    }
    return $result;
}

function ppdbplan($mppid = 1) {
    global $bpprowbase, $bpparr, $planlogo;

    $mppid = ($mppid < 1) ? 1 : $mppid;
    $bpprowplan = $bpparr[$mppid];
    $result = (intval($bpprowplan['ppid']) < 1) ? $bpprowbase : array_merge($bpprowbase, $bpprowplan);
    $planimg = ($result['planimg']) ? $result['planimg'] : DEFIMG_PLAN;
    $planlogo = ($result['planlogo']) ? $result['planlogo'] : DEFIMG_LOGO;
    $result['planimg'] = $planimg;
    $result['planlogo'] = $planlogo;

    return $result;
}

function do_autoregplan($mbrstr, $cyclingbyid, $entrytoidmbr, $newmppid = 1) {
    global $db, $bpparr, $FORM;

    $data = array(
        'isdefault' => '0',
        'cyclingbyid' => $cyclingbyid,
    );
    $update = $db->update(DB_TBLPREFIX . '_mbrplans', $data, array('mpid' => $mbrstr['mpid']));

    $resultarr = regmbrplans($mbrstr, $entrytoidmbr, $newmppid);
    $txid = $resultarr['txid'];
    $mpid = $resultarr['mpid'];

    $doreactive = get_optionvals($bpparr[$mbrstr['mppid']]['plantoken'], 'doreactive');

    if ($doreactive == '1') {

        $txbatch = "E" . date("md") . "-" . date("H") . "{$mbrstr['mpid']}";
        $payamount = $bpparr[$newmppid]['regfee'];
        $newamount = $mbrstr['ewallet'] - $payamount;

        $txtokenstr = "Wallet Debit [REREG] " . $bpparr[$newmppid]['ppname'];

        adjusttrxwallet($mbrstr['ewallet'], $newamount, $mbrstr['id'], $txtokenstr, '', 1, $txid);
        $data = array(
            'ewallet' => $newamount,
        );
        $db->update(DB_TBLPREFIX . '_mbrs', $data, array('id' => $mbrstr['id']));

        include_once('sandbox.php');
        $FORM['sb_type'] = 'payreg';
        $txmpid = $txid . '-' . $mpid;
        $paygate = 'wallet';
        //doipnbox($txmpid, $payamount, $paygate, $txbatch, '', 'OK', 0, '');
        doipnbox($txmpid, $payamount, $paygate, $txbatch, '', 'continue', 0, '');

        $mpstatus = ($newamount >= 0) ? 1 : 3;
        $data = array(
            'mpstatus' => $mpstatus,
        );
        $update = $db->update(DB_TBLPREFIX . '_mbrplans', $data, array('mpid' => $resultarr['mpid']));

        printlog('sys.func/do_autoregplan', "$newamount = {$mbrstr['ewallet']} - $payamount / {$txid} / $cyclingbyid = $mpstatus");
    }
}

function mbrpparr($idmbr) {
    global $db;

    $result = array();
    $condition = " AND idmbr = '{$idmbr}'";
    $userData = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_mbrplans WHERE 1" . $condition . "");
    if (count($userData) > 0) {
        foreach ($userData as $val) {
            $result['mppid'][] = $val['mppid'];
            foreach ($val as $key => $value) {
                $result[$val['mppid']][$key] = $value;
            }
        }
    }
    return $result;
}

function add_sprlist($mpid, $sprlist) {
    $sprlistx = "|1:{$mpid}|";
    $dlist = explode(',', str_replace(' ', '', $sprlist));
    for ($i = 1; $i <= count($dlist); $i++) {
        $node = explode(':', str_replace('|', '', $dlist[$i]));
        $pos = $node[0];
        $val = $node[1];
        if (intval($pos) < 1) {
            break 1;
        }
        if (intval($val) > 0) {
            $pos++;
        }
        $listx = ", |" . $pos . ":" . $val . "|";
        $sprlistx = $sprlistx . $listx;
    }
    return $sprlistx;
}

function do_movembr($mbrstr, $newunspr) {
    global $db;

    $newsprstr = getmbrinfo($newunspr, 'username');
    if ($newsprstr['id'] > 0) {
        $newsprlist = add_sprlist($newsprstr['mpid'], $newsprstr['sprlist']);
        $data = array(
            'idspr' => $newsprstr['id'],
            'sprlist' => $newsprlist,
        );
        $update = $db->update(DB_TBLPREFIX . '_mbrplans', $data, array('mpid' => $mbrstr['mpid']));

        $xdlist = ":" . $mbrstr['mpid'] . "|";
        $condition = " AND sprlist LIKE '%{$xdlist}%'";
        $userData = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_mbrplans WHERE 1 " . $condition . "");
        foreach ($userData as $val) {
            $mysprstr = getmbrinfo($val['idspr']);
            $sprlist = add_sprlist($mysprstr['mpid'], $mysprstr['sprlist']);
            $data = array(
                'sprlist' => $sprlist,
            );
            $db->update(DB_TBLPREFIX . '_mbrplans', $data, array('mpid' => $val['mpid']));
        }
        return $update;
    }
}

function do_dbbakup() {
    global $db, $cfgrow, $cfgtoken, $umbasever;

    $dbaknow = date('Y-m-d H:i:s', time() + (3600 * $cfgrow['time_offset']));
    $dbakint = $cfgtoken['dbakint'];
    $dbakeml = base64_decode($cfgtoken['dbakeml']);
    $dbakdate = base64_decode($cfgtoken['dbakdate']);

    $nextbak = get_actdate($dbakint, $dbakdate);
    $datenextbak = $nextbak['next'];

    if (($dbakdate == null || $datenextbak <= $dbaknow) && $dbakint != '0' && $dbakeml != '') {
        $dat = date('Ymd_His');
        if (function_exists('gzencode')) {
            $cmp = "gz";
            $backup_filename = "" . DB_NAME . "_$dat.sql.$cmp";
        } else {
            $cmp = "";
            $backup_filename = "" . DB_NAME . "_$dat.sql";
        }

        include_once('../common/umver.php');
        require_once('../common/mailer.do.php');
        $bakdbcnt = gobackup($cmp);

        //Set the subject line
        $msgsubject = "Database backup " . $backup_filename;

        // HTML body
        $fmessagehtml = "<font size=3><b>UniMatrix v{$umbasever} - Database Backup</b></font><br /><br />";
        $fmessagehtml .= "{$cfgtoken['site_subname']}<br />";
        $fmessagehtml .= "Creation date: <b>" . date("Y-m-d H:i:s", time()) . "</b><br />";
        $fmessagehtml .= "Database: " . DB_NAME . "<br />";

        // Plain text body (for mail clients that cannot read HTML)
        $fmessage = "UniMatrix v{$umbasever} - Database Backup\n";
        $fmessage .= "{$cfgtoken['site_subname']}\n";
        $fmessage .= "Creation date: " . date("Y-m-d H:i:s", time()) . "\n";
        $fmessage .= "Database: " . DB_NAME . "\n";

        $isdomailer = domailer($cfgtoken['site_subname'], $dbakeml, $msgsubject, $fmessagehtml, $fmessage, $bakdbcnt, $backup_filename);

        if ($isdomailer) {
            $newcfgtoken = $cfgrow['cfgtoken'];
            $newcfgtoken = put_optionvals($newcfgtoken, 'dbakdate', base64_encode($dbaknow));
            $data = array(
                'cfgtoken' => $newcfgtoken,
            );
            $update = $db->update(DB_TBLPREFIX . '_configs', $data, array('cfgid' => '1'));
        }
    }
}

function do_mbrdel($delId, $istx = '') {
    global $db, $cfgtoken;

    $delmbrstr = getmbrinfo($delId);
    $db->delete(DB_TBLPREFIX . '_mbrs', array('id' => $delId));
    $db->delete(DB_TBLPREFIX . '_mbrplans', array('idmbr' => $delId));

    // remove transaction history
    if ($istx == '1') {
        $condition = " AND (txtoken LIKE '%|SRCIDMBR:{$delId}|%' OR txfromid = '{$delId}' OR txtoid = '{$delId}')";
        $deltxrow = $db->getAllRecords(DB_TBLPREFIX . '_transactions', '*', $condition);
        foreach ($deltxrow as $key => $txval) {
            $deltxid = $txval['txid'];
            $db->delete(DB_TBLPREFIX . '_transactions', array('txid' => $deltxid));

            // adjust member ewallet
            if ($txval['txtoid'] > 0 && $txval['txtoid'] != $delId && $txval['txstatus'] == '1') {
                $mbrtostr = getmbrinfo($txval['txtoid']);
                $newamount = $mbrtostr['ewallet'] - $txval['txamount'];
                adjusttrxwallet($mbrtostr['ewallet'], $newamount, $txval['txtoid'], "Reversal {$txval['txmemo']}", "Adjustment from the member removal: {$delmbrstr['fullname']} ({$delId} {$delmbrstr['username']}) {$delmbrstr['email']}", 1);
                $data = array(
                    'ewallet' => $newamount,
                );
                $db->update(DB_TBLPREFIX . '_mbrs', $data, array('id' => $mbrtostr['id']));
            }
        }
    }
}

function get_withdrawfee() {
    global $cfgrow;

    $wdrwfeearr = array();
    $wdvarval = $cfgrow['wdrawfee'];
    $wdvarvalarr = explode('|', $wdvarval);
    $fval = (strpos($wdvarvalarr[0], '%') !== false) ? $wdvarvalarr[0] / 100 : $wdvarvalarr[0];
    $wdrwfeearr['fee'] = (float) $fval;
    $wdrwfeearr['cap'] = (float) $wdvarvalarr[1];
    return $wdrwfeearr;
}

function get_pgmbrtoken($mbrstr) {
    global $db, $mbrpaystr;

    $pgdatatoken = $mbrstr['pgdatatoken'];
    $pgmbrtokenarr = get_optionvals($pgdatatoken);

    $mbrperfectmoneycfg = get_optarr($pgmbrtokenarr['perfectmoneycfg']);
    $mbrpayfastcfg = get_optarr($pgmbrtokenarr['payfastcfg']);
    $mbrpaystackcfg = get_optarr($pgmbrtokenarr['paystackcfg']);
    $mbrcoinpaymentscfg = get_optarr($pgmbrtokenarr['coinpaymentscfg']);
    $mbrpaypalcfg = get_optarr($pgmbrtokenarr['paypalcfg']);
    $mbrstripecfg = get_optarr($pgmbrtokenarr['stripecfg']);

    $mbrpaystr = array();
    $mbrpayrow = $db->getAllRecords(DB_TBLPREFIX . '_paygates', '*', ' AND pgidmbr = "' . $mbrstr['id'] . '"');
    $mbrpaystr['manualpayipn'] = base64_decode($mbrpayrow[0]['manualpayipn']);

    $mbrpaystr['perfectmoneyacc'] = $mbrperfectmoneycfg['perfectmoneyacc'];
    $mbrpaystr['payfastmercid'] = $mbrpayfastcfg['payfastmercid'];
    $mbrpaystr['paystackpub'] = $mbrpaystackcfg['paystackpub'];
    $mbrpaystr['coinpaymentsmercid'] = $mbrcoinpaymentscfg['coinpaymentsmercid'];
    $mbrpaystr['paypalacc'] = $mbrpaypalcfg['paypalacc'];
    $mbrpaystr['stripeacc'] = $mbrstripecfg['stripeacc'];

    return $mbrpaystr;
}

function do_withdrawreq($mbrstr, $txamount, $txpaytype) {
    global $db, $cfgrow, $LANG, $avalwithdrawgate_array;

    if ($txamount <= 0) {
        return false;
    }

    $wdrwfeearr = get_withdrawfee();
    $fval = $wdrwfeearr['fee'];
    $fcapval = $wdrwfeearr['cap'];

    $mbrpaystr = get_pgmbrtoken($mbrstr);

    $txamountval = $txamount;
    $txwdrfee = $txamountfee = 0;
    if ($fval > 0) {
        $txwdrfee = $txamount * $fval;
        $txamountfeeopt = ($fcapval <= $txwdrfee) ? $fcapval : $txwdrfee;
        $txamountfee = (float) sprintf('%0.2f', $txamountfeeopt);
        $txamountval = $txamount - $txamountfee;
    }

    // deduct wallet
    $ewallet = $mbrstr['ewallet'] - $txamount;
    $data = array(
        'ewallet' => $ewallet,
    );
    $update = $db->update(DB_TBLPREFIX . '_mbrs', $data, array('id' => $mbrstr['id']));

    // add withdraw request
    $paybyopt = $avalwithdrawgate_array[$txpaytype];
    $txadminfo = "Payout To [{$paybyopt}]: ";
    $txadminfo .= $mbrpaystr[$txpaytype];
    $txdatetm = date('Y-m-d H:i:s', time() + (3600 * $cfgrow['time_offset']));
    $data = array(
        'txdatetm' => $txdatetm,
        'txpaytype' => $txpaytype,
        'txfromid' => 0,
        'txtoid' => $mbrstr['id'],
        'txamount' => $txamountval,
        'txmemo' => $LANG['g_withdrawstr'],
        'txppid' => $mbrstr['mppid'],
        'txtoken' => "|WIDR:OUT|, |WDRTXFEE:{$txamountfee}|",
        'txstatus' => 0,
        'txadminfo' => $txadminfo,
    );
    $insert = $db->insert(DB_TBLPREFIX . '_transactions', $data);

    if ($insert) {
        $newtrxid = $db->lastInsertId();
        if ($txamountfee > 0) {
            $txdatetm = date('Y-m-d H:i:s', time() + (3600 * $cfgrow['time_offset']));
            $txlogtime = date('mdH-is-' . $newtrxid, time() + (3600 * $cfgrow['time_offset']));
            $txbatch = "WDFE" . date("m-dH-i") . $newtrxid;
            $data = array(
                'txdatetm' => $txdatetm,
                'txpaytype' => $txpaytype,
                'txfromid' => $mbrstr['id'],
                'txtoid' => 0,
                'txamount' => $txamountfee,
                'txbatch' => $txbatch,
                'txmemo' => $LANG['g_withdrawfee'],
                'txppid' => $mbrstr['mppid'],
                'txtoken' => "|WDRTXID:{$newtrxid}|, |NOTE:" . base64_encode("WDRID-{$txlogtime}") . "|",
                'txstatus' => 1,
            );
            $insertrx = $db->insert(DB_TBLPREFIX . '_transactions', $data);
        }
    }

    return $insert;
}

function is_ppsubscr($mppid = 1) {
    global $bpparr;

    $iswhat = ($bpparr[$mppid]['expday'] != '') ? true : false;
    return $iswhat;
}

function is_unamereserved($username) {
    global $cfgrow;

    $unamereservedarr = explode(',', str_replace(' ', '', $cfgrow['badunlist']));
    $isexist = (in_array($username, $unamereservedarr)) ? true : false;

    return $isexist;
}

function do_reginorder($mbrstr) {
    global $bpparr, $frlmtdcfg;

    $directnextid = '';
    $nextppidarr = array();

    foreach ($bpparr as $key => $value) {
        $ppid = $value['ppid'];
        if ($mbrstr['mppid'] == $ppid) {
            continue;
        }
        if ($frlmtdcfg['isreginorder'] == 0) {
            // all available
            $nextppidarr[] = $ppid;
        } else {
            if ($mbrstr['mppid'] < $ppid) {
                // any next
                $nextppidarr[] = $ppid;
                if ($directnextid == '') {
                    $directnextid = 'upnextid';
                    $nextppidarr['upnextid'] = $ppid;
                }
                if ($frlmtdcfg['isreginorder'] == 2) {
                    // next only
                    break;
                }
            } else {
                continue;
            }
        }
    }

    return $nextppidarr;
}

function get_txinfo($txid) {
    global $db;

    $txRow = array();
    $txid = intval($txid);
    if ($txid > 0) {
        $row = $db->getAllRecords(DB_TBLPREFIX . '_transactions', '*', " AND txid = '{$txid}'");
        foreach ($row as $value) {
            $txRow = array_merge($txRow, $value);
        }
    }

    return $txRow;
}

// check referrer and get new sponsor
function do_resprmpid($mbrstr) {
    global $db, $frlmtdcfg;

    $refstr = getmbrinfo($mbrstr['idref'], '', '', $mbrstr['mppid']);
    $sprmpid = getmpidflow($refstr['mpid'], $mbrstr['mppid']);

    $sprstr = getmbrinfo('', '', $sprmpid);
    $idspr = intval($sprstr['id']);

    // new sponsor is not the same as current sponsor
    // update sponsor id and sponsor list
    if ($mbrstr['idspr'] != $idspr && $frlmtdcfg['isregallrefs'] != 1) {
        $sprlist = dosprlist($sprstr['mpid'], $sprstr['sprlist'], $mbrstr['mpdepth']);
        $data = array(
            'idspr' => $idspr,
            'sprlist' => $sprlist,
        );
        $update = $db->update(DB_TBLPREFIX . '_mbrplans', $data, array('mpid' => $mbrstr['mpid']));
        $mbrstr = getmbrinfo('', '', $mbrstr['mpid']);
    }
    return $mbrstr;
}

function get_calcumount($mbrstr, $condition) {
    global $db;

    $result = array();

    $toth = $refbon = $sprbon = $rwdbon = $slsbon = $totwalet = $waletout = $totern = $mypaymn = $renewfee = $totpending = $reqwdrwait = $reqwdrdone = $feewdr = 0;
    $userData = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_transactions WHERE 1 " . $condition . "");
    foreach ($userData as $val) {
        $toth++;
        $txtoken = get_optionvals($val['txtoken']);

        if ($val['txstatus'] == 1) {

            // general incoming and payout/debet
            if ($val['txfromid'] == $mbrstr['id']) {
                if ($txtoken['WIDR'] == 'OUT') {
                    $totwalet = $totwalet - $val['txamount'];
                }
                if ($txtoken['WALT'] == 'OUT') {
                    $waletout = $waletout + $val['txamount'];
                }
                if (strpos($val['txtoken'], '|REG:') !== false) {
                    $mypaymn = $mypaymn + $val['txamount'];
                }
            } elseif ($val['txtoid'] == $mbrstr['id']) {
                if ($txtoken['WALT'] == 'IN') {
                    $totwalet = $totwalet + $val['txamount'];
                }
                if ($txtoken['WIDR'] != 'OUT') {
                    $totern = $totern + $val['txamount'];
                }
            }

            // referrer bonuses
            if ($txtoken['LCM'] == 'PREF' && $txtoken['WALT'] == 'IN') {
                $refbon = $refbon + $val['txamount'];
            }

            // sponsor bonuses
            if ($txtoken['LCM'] == 'TIER' && $txtoken['WALT'] == 'IN') {
                $sprbon = $sprbon + $val['txamount'];
            }

            // reward bonuses
            if (strpos($val['txtoken'], '|LCM:FRWD') !== false && $txtoken['WALT'] == 'IN') {
                $rwdbon = $rwdbon + $val['txamount'];
            }

            // sales bonuses
            if ($txtoken['LCM'] == 'SLSTIER' && $txtoken['WALT'] == 'IN') {
                $slsbon = $slsbon + $val['txamount'];
            }

            // renewal fee
            if (strpos($val['txtoken'], '|RENEW:') !== false) {
                $renewfee = $renewfee + $val['txamount'];
            }

            // withdraw amount
            if ($txtoken['WIDR'] == 'OUT') {
                $reqwdrdone = $reqwdrdone + $val['txamount'];
            }
            // withdraw fee
            if (strpos($val['txtoken'], '|WDRTXID:') !== false) {
                $feewdr = $feewdr + $val['txamount'];
            }
        } else {
            if ($txtoken['WIDR'] == 'OUT') {
                $reqwdrwait = $reqwdrwait + $val['txamount'];
            }
            $totpending = $totpending + $val['txamount'];
        }
    }

    $result['hist_tot'] = $toth;
    $result['hist_refbonus'] = sprintf("%0.2f", $refbon);
    $result['hist_sprbonus'] = sprintf("%0.2f", $sprbon);
    $result['hist_rwdbonus'] = sprintf("%0.2f", $rwdbon);
    $result['hist_slsbon'] = sprintf("%0.2f", $slsbon);
    $result['hist_waletout'] = sprintf("%0.2f", $waletout);
    $result['hist_ewallet'] = sprintf("%0.2f", $totwalet - $waletout - $reqwdrdone - $reqwdrwait - $feewdr);
    $result['hist_mypaymn'] = sprintf("%0.2f", $mypaymn);
    $result['hist_earning'] = sprintf("%0.2f", $totern);
    $result['hist_renewfee'] = sprintf("%0.2f", $renewfee);
    $result['hist_reqwdrwait'] = sprintf("%0.2f", $reqwdrwait);
    $result['hist_reqwdrdone'] = sprintf("%0.2f", $reqwdrdone);
    $result['hist_feewdr'] = sprintf("%0.2f", $feewdr);
    $result['hist_pending'] = sprintf("%0.2f", $totpending);

    return $result;
}

function get_admcalcumount($ppid = 0) {
    global $db;

    $result = array();

    $toth = $refbon = $sprbon = $rwdbon = $slsbon = $tincome = $totern = $reqwdrwait = $reqwdrdone = $feewdr = $totpending = 0;

    $condition = " AND (txfromid = '0' OR txtoid = '0') ";
    $condition .= ($ppid > 0) ? " AND txppid = '{$ppid}' " : '';
    $userData = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_transactions WHERE 1 " . $condition . "");
    foreach ($userData as $val) {
        $toth++;
        $txtoken = get_optionvals($val['txtoken']);

        if ($val['txstatus'] == 1) {

            // general incoming and payout/debet
            if ($val['txfromid'] == 0 && $val['txtoid'] > 0) {
                if ($txtoken['WIDR'] == 'OUT') {
                    $reqwdrdone = $reqwdrdone + $val['txamount'];
                }
            } elseif ($val['txfromid'] > 0 && $val['txtoid'] == 0) {
                if (strpos($val['txtoken'], '|REG:') !== false || strpos($val['txtoken'], '|RENEW:') !== false || strpos($val['txtoken'], '|STORE:') !== false) {
                    $tincome = $tincome + $val['txamount'];
                }
                // withdraw fee
                if (strpos($val['txtoken'], '|WDRTXID:') !== false) {
                    $feewdr = $feewdr + $val['txamount'];
                }
            }

            // referrer bonuses
            if ($txtoken['LCM'] == 'PREF' && $txtoken['WALT'] == 'IN') {
                $refbon = $refbon + $val['txamount'];
            }

            // sponsor bonuses
            if ($txtoken['LCM'] == 'TIER' && $txtoken['WALT'] == 'IN') {
                $sprbon = $sprbon + $val['txamount'];
            }

            // reward bonuses
            if (strpos($val['txtoken'], '|LCM:FRWD') !== false && $txtoken['WALT'] == 'IN') {
                $rwdbon = $rwdbon + $val['txamount'];
            }

            // sales bonuses
            if ($txtoken['LCM'] == 'SLSTIER' && $txtoken['WALT'] == 'IN') {
                $slsbon = $slsbon + $val['txamount'];
            }
        } else {
            if ($txtoken['WIDR'] == 'OUT') {
                $reqwdrwait = $reqwdrwait + $val['txamount'];
            }
            $totpending = $totpending + $val['txamount'];
        }
    }

    $result['hist_tot'] = $toth;
    $result['hist_refbonus'] = sprintf("%0.2f", $refbon);
    $result['hist_sprbonus'] = sprintf("%0.2f", $sprbon);
    $result['hist_rwdbonus'] = sprintf("%0.2f", $rwdbon);
    $result['hist_slsbon'] = sprintf("%0.2f", $slsbon);
    $result['hist_tincome'] = sprintf("%0.2f", $tincome);
    $result['hist_earning'] = sprintf("%0.2f", $tincome - $reqwdrdone - $feewdr);
    $result['hist_reqwdrwait'] = sprintf("%0.2f", $reqwdrwait);
    $result['hist_reqwdrdone'] = sprintf("%0.2f", $reqwdrdone);
    $result['hist_feewdr'] = sprintf("%0.2f", $feewdr);
    $result['hist_pending'] = sprintf("%0.2f", $totpending);

    return $result;
}

function get_sysplanstr($mbrstr = array()) {
    global $bpprow;

    if ($mbrstr['id'] > 0) {
        if ($mbrstr['mpwidth'] < 1) {
            $planstr = ($mbrstr['mpdepth'] == 1) ? 'Unilevel' : 'Unilevel &darr;' . $mbrstr['mpdepth'];
        } elseif ($mbrstr['mpwidth'] == 1) {
            $planstr = 'Powerline &darr;' . $mbrstr['mpdepth'];
        } else {
            $planstr = 'Matrix ' . $mbrstr['mpwidth'] . '&times;' . $mbrstr['mpdepth'];
        }
    } else {
        if ($bpprow['maxwidth'] < 1) {
            $planstr = ($bpprow['maxdepth'] == 1) ? 'Unilevel' : 'Unilevel &darr;' . $bpprow['maxdepth'];
        } elseif ($bpprow['maxwidth'] == 1) {
            $planstr = 'Powerline &darr;' . $bpprow['maxdepth'];
        } else {
            $planstr = 'Matrix ' . $bpprow['maxwidth'] . '&times;' . $bpprow['maxdepth'];
        }
    }

    return $planstr;
}

function get_peppylink($url, $metatitle, $metadesc = '') {
    global $cfgrow;

    $peppyapi = base64_decode($cfgrow['peppyapi']);
    $urlenc = urlencode($url);
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://peppy.link/api/url/add",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 2,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_HTTPHEADER => array(
            "Authorization: Bearer {$peppyapi}",
            "Content-Type: application/json",
        ),
        CURLOPT_POSTFIELDS => '{
                "url": "' . $urlenc . '",
                "type": "direct",
                "metatitle": "' . $metatitle . '",
                "metadescription": "' . $metadesc . '"
            }',
    ));

    $response = curl_exec($curl);
    curl_close($curl);
    /* response:
      {
      "error": [error_code, 0 = no error],
      "id": [short_url_id],
      "shorturl": "https:\/\/peppy.link\/[random_alias]"
      }
     * 
     */
    return $response;
}

function get_sprppcm($defppid, $cmlist, $sprmppid = 0, $sprtier = 0) {
    global $bpprow, $frlmtdcfg;

    $ppVal = preg_replace("/(\x{00a0}|\s+|\r|\n)/", "", $cmlist);
    $ppvalarr = explode('#', $ppVal);

    $stagecmarr = $stagearr = array();
    if (count($ppvalarr) > 1) {
        $mxstages = ($frlmtdcfg['mxstages'] > 1) ? intval($frlmtdcfg['mxstages']) : 1;
        for ($i = 1; $i <= $mxstages; $i++) {
            $value = $ppvalarr[$i];
            $tiervalarr = explode('=', $value);
            $ppidspr = $tiervalarr[0];

            if (in_array($ppidspr, $stagearr) || $value == '' || $tiervalarr[0] < 1) {
                continue;
            }
            $stagearr[] = $ppidspr;

            $ppidcm = $tiervalarr[1];
            $tiercmarr = explode(',', $ppidcm);

            $tier = 0;
            $maxdepth = ($bpprow['maxdepth'] > 1) ? intval($bpprow['maxdepth']) : 1;
            for ($j = 0; $j < $maxdepth; $j++) {
                $tier = $j + 1;
                $tiercm = ($tiercmarr[$j]) ? $tiercmarr[$j] : 0;
                $tiercmstr = (preg_match("/[\d.]+%?/", $tiercm, $matches)) ? $matches[0] : 0;
                $stagecmarr[$ppidspr][$tier] = $tiercmstr;
            }
        }
    } else {
        $ppidcm = $ppvalarr[0];
        $ppidspr = ($sprmppid > 1) ? $sprmppid : $defppid;
        $tiercmarr = explode(',', $ppidcm);

        $tier = 0;
        $maxdepth = ($bpprow['maxdepth'] > 1) ? intval($bpprow['maxdepth']) : 1;
        for ($j = 0; $j < $maxdepth; $j++) {
            $tier = $j + 1;
            $tiercm = ($tiercmarr[$j]) ? $tiercmarr[$j] : 0;
            $tiercmstr = (preg_match("/[\d.]+%?/", $tiercm, $matches)) ? $matches[0] : 0;
            $stagecmarr[$ppidspr][$tier] = $tiercmstr;
        }
    }

    if (intval($sprmppid) > 0) {
        $result = $stagecmarr[$sprmppid][$sprtier];
    } else {
        ksort($stagecmarr);
        $result = $stagecmarr;
    }

    return $result;
}

