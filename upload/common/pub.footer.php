<?php

if (!defined('OK_LOADME')) {
    die('o o p s !');
}
$thisyear = date("Y");
$site_subname = ($cfgtoken['site_subname'] != '') ? "<a href='{$cfgrow['site_url']}'>{$cfgtoken['site_subname']}</a>" : "<a href='https://www.mlmscript.net/id/{$cfgrow['envacc']}' target='_blank'>{$cfgrow['site_name']}</a>";

if ($cfgtoken['iscookieconsent'] == '1') {
    $iscookieconsentstr = <<<INI_HTML
        <div id="cookieAlertBar" class="cookieAlertBar">
            {$LANG['g_cookieconsent']}<br /><br /> <button id="cookieAlertBarConfirm" class="btn btn-sm btn-warning">{$LANG['g_yesgetit']}</button>
        </div>
INI_HTML;
} else {
    $iscookieconsentstr = '';
}

$page_content = <<<INI_HTML
                <div class="simple-footer">
                    <!--
                    You are not allowed to remove this credit link unless you have right to do so by own the Extended license or order the Branding Removal license at https://www.mlmscript.net/order
                    -->
                    <div class="text-small">{$LANG['g_builtwith']} <i class="fa fa-fw fa-heart"></i> {$thisyear} {$site_subname}{$cfgrow['_isnocreditstr']}
                </div>

</div>

    {$iscookieconsentstr}

        <!-- General JS Scripts -->
        <script src="../assets/js/jquery-3.4.1.min.js"></script>
        <script src="../assets/js/popper.min.js"></script>
        <script src="../assets/js/bootstrap.min.js"></script>
        <script src="../assets/js/jquery.nicescroll.min.js"></script>
        <script src="../assets/js/moment.min.js"></script>
        <script src="../assets/js/pace.min.js"></script>

        <!-- JS Libraies -->
        <script src="../assets/js/stisla.js"></script>

        <!-- Template JS File -->
        <script src="../assets/js/scripts.js"></script>
        <script src="../assets/js/custom.js"></script>

        <!-- Page Specific JS File -->
        <!-- include summernote css/js -->
        <script src="../assets/js/summernote-bs4.min.js"></script>

   </body>
</html>
INI_HTML;
echo myvalidate($page_content);
