<?php

if (!defined('OK_LOADME')) {
    die('o o p s !');
}

$dotoaster = $_SESSION['dotoaster'];
$_SESSION['dotoaster'] = '';

$thisyear = date("Y");
$site_subname = ($cfgtoken['site_subname'] != '') ? "<a href='{$cfgrow['site_url']}'>{$cfgtoken['site_subname']}</a>" : "<a href='https://www.mlmscript.net/id/{$cfgrow['envacc']}' target='_blank'>{$cfgrow['site_name']}</a>";

$member_content = <<<INI_HTML
<footer class="main-footer">
    <!--
    You are not allowed to remove this credit link unless you have right to do so by own the Extended license or order the Branding Removal license at https://www.mlmscript.net/order
    -->
    <div class="footer-left">
        {$LANG['g_builtwith']} <i class="fa fa-fw fa-heart"></i> {$thisyear} {$site_subname}{$cfgrow['_isnocreditstr']}
    </div>
    <div class="footer-right">
        <div class="d-none d-sm-block text-small">
        <a href="javascript:;" data-href="../common/terms.html" data-poptitle="Terms and Conditions" class="openPopup text-info">{$LANG['g_termscon']}</a>
        </div>
    </div>
</footer>
</div>

</div>

        <!-- Template JS File -->
        <script src="../assets/js/scripts.js"></script>
        <script src="../assets/js/custom.js"></script>

        <!-- Page Specific JS File -->
        <script type="text/javascript">
        toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "preventDuplicates": true,
        "onclick": null
        }
        {$dotoaster}
        </script>

</body>
</html>
INI_HTML;
echo myvalidate($member_content);

