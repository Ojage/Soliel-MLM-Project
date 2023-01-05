<?php

// file execute by page load
if (!defined('OK_LOADME')) {
    die("^-^ REGL");
}

function is_valid_domain($url) {

    $validation = FALSE;
    /* Parse URL */
    $urlparts = parse_url(filter_var($url, FILTER_SANITIZE_URL));
    /* Check host exist else path assign to host */
    if (!isset($urlparts['host'])) {
        $urlparts['host'] = $urlparts['path'];
    }

    if ($urlparts['host'] != '') {
        /* Add scheme if not found */
        if (!isset($urlparts['scheme'])) {
            $urlparts['scheme'] = 'http';
        }
        /* Validation */
        if (checkdnsrr($urlparts['host'], 'A') && in_array($urlparts['scheme'], array('http', 'https')) && ip2long($urlparts['host']) === FALSE) {
            $urlparts['host'] = preg_replace('/^www\./', '', $urlparts['host']);
            $url = $urlparts['scheme'] . '://' . $urlparts['host'] . "/";

            if (filter_var($url, FILTER_VALIDATE_URL) !== false && @get_headers($url)) {
                $validation = TRUE;
            }
        }
    }

    return $validation;
    /* //
      if (!$validation) {
      echo "Invalid Domain";
      } else {
      echo "Valid Domain";
      }
      // */
}

function find_SQL_Version() {
    if (function_exists('mysqli_get_client_info') || function_exists('mysql_get_client_info')) {
        $mysql_get_clinfo = (phpversion() >= 5.5) ? mysqli_get_client_info() : mysql_get_client_info();
        preg_match('@[0-9]+\.[0-9]+\.[0-9]+@', $mysql_get_clinfo, $version);
        return $version[0];
    } else {
        return '?';
    }
}

$phpver = phpversion();
if (7.2 <= $phpver) {
    $phpversionstr = "<i class='fa fa-fw fa-check text-success'></i> PHP {$phpver}</li>";
} else if (8 <= $phpver) {
    $phpversionstr = "<i class='fa fa-fw fa-question text-warning'></i> PHP {$phpver} - <em>not tested</em></li>";
} else {
    $phpversionstr = "<i class='fa fa-fw fa-times text-danger'></i> PHP {$phpver}</li>";
    $doregsbtnsubmit = '';
}

$sqlver = find_SQL_Version();
if ($sqlver >= 5) {
    $sqlversionstr = "<i class='fa fa-fw fa-check text-success'></i> SQL {$sqlver}</li>";
} else {
    $sqlversionstr = "<i class='fa fa-fw fa-times text-danger'></i> SQL {$sqlver}</li>";
}

if (extension_loaded('ionCube Loader')) {
    $ioncubestr = "<i class='fa fa-fw fa-check text-success'></i> IonCube loader</li>";
} else {
    $ioncubestr = "<i class='fa fa-fw fa-times text-warning'></i> IonCube loader</li>";
    $doregsbtnsubmit = '';
}

if (function_exists('curl_init')) {
    $curlstr = "<i class='fa fa-fw fa-check text-success'></i> cUrl function</li>";
} else {
    $curlstr = "<i class='fa fa-fw fa-times text-danger'></i> cUrl function</li>";
    $doregsbtnsubmit = '';
}

if (function_exists('fsockopen')) {
    $fsockopenstr = "<i class='fa fa-fw fa-check text-success'></i> fSockOpen function</li>";
} else {
    $fsockopenstr = "<i class='fa fa-fw fa-times text-warning'></i> fSockOpen function</li>";
}

if (get_extension_funcs('json')) {
    $jsonstr = "<i class='fa fa-fw fa-check text-success'></i> JSON extension</li>";
} else {
    $jsonstr = "<i class='fa fa-fw fa-times text-danger'></i> JSON extension</li>";
    $doregsbtnsubmit = '';
}

if (get_extension_funcs('zlib')) {
    $zlibstr = "<i class='fa fa-fw fa-check text-success'></i> zLib extension</li>";
} else {
    $zlibstr = "<i class='fa fa-fw fa-times text-danger'></i> zLib extension</li>";
    $doregsbtnsubmit = '';
}

if (function_exists('gzencode')) {
    $gzencodestr = "<i class='fa fa-fw fa-check text-success'></i> gzEncode function</li>";
} else {
    $gzencodestr = "<i class='fa fa-fw fa-times text-warning'></i> gzEncode function</li>";
    $doregsbtnsubmit = '';
}

$isdomainname = (in_array($_SERVER['REMOTE_ADDR'], array('127.0.0.1', "::1")) || !is_valid_domain($_SERVER['HTTP_HOST'])) ? "<i class='fa fa-fw fa-unlink text-warning'></i> Domain or Online Server</li>" : "<i class='fa fa-fw fa-globe text-success'></i> Domain or Online Server</li>";

$servsoftver = strtolower($_SERVER['SERVER_SOFTWARE']);
$servsoftverstr = (strpos($servsoftver, 'apache') !== false) ? "<i class='fa fa-fw fa-layer-group text-success'></i> {$servsoftver}</li>" : "<i class='fa fa-fw fa-question text-warning'></i> {$servsoftver}</li>";

$showreg_server = <<<INI_HTML
        <ul class="list-unstyled">
            <li>{$isdomainname}</li>
            <li>{$servsoftverstr}</li>
            <li>{$phpversionstr}</li>
            <li>{$sqlversionstr}</li>
            <li>{$ioncubestr}</li>
            <li>{$curlstr}</li>
            <li>{$fsockopenstr}</li>
            <li>{$jsonstr}</li>
            <li>{$zlibstr}</li>
            <li>{$gzencodestr}</li>
        </ul>
INI_HTML;
