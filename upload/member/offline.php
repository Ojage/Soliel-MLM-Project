<?php
include_once('../common/init.loader.php');

header("Refresh: 300; URL=index.php");

if ($cfgrow['site_status'] == 1 && $_SERVER['HTTP_REFERER']) {
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
}

$page_content = <<<INI_HTML
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
        <title>{$page_header}</title>
        <meta name="description" content="{$cfgrow['site_descr']}">
        <meta name="keywords" content="{$cfgrow['site_keywrd']}">
        <meta name="author" content="MLMScript.net">

        <link rel="shortcut icon" type="image/png" href="../assets/image/favicon.png"/>

        <!-- General CSS Files -->
        <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
        <link rel="stylesheet" href="../assets/fellow/fontawesome5121/css/all.min.css">

        <!-- CSS Libraries -->
        <link rel="stylesheet" href="../assets/css/pace-theme-minimal.css">

        <!-- Template CSS -->
        <link rel="stylesheet" href="../assets/css/fontmuli.css">
        <link rel="stylesheet" href="../assets/css/style.css">
        <link rel="stylesheet" href="../assets/css/components.css">
        <link rel="stylesheet" href="../assets/css/custom.css">

        <!-- include summernote css/js -->
        <link href="../assets/css/summernote-bs4.css" rel="stylesheet">

    </head>

    <body>
        <div id="app">
INI_HTML;
echo myvalidate($page_content, 1);

$show_msg = $_SESSION['show_msg'];
$_SESSION['show_msg'] = '';
?>
<section class="section">
    <div class="container mt-4">
        <div class="row">
            <div class="col-12 col-sm-10 offset-sm-1 col-md-8 offset-md-2 col-lg-8 offset-lg-2 col-xl-8 offset-xl-2">
                <div class="login-brand">
                    <img src="<?php echo myvalidate($site_logo); ?>" alt="logo" width="100" class="shadow-light rounded-circle">
                </div>

                <?php echo myvalidate($show_msg); ?>

                <div class="card card-danger">
                    <div class="card-body">
                        <?php
                        echo showalert('danger', 'Oops!', base64_decode($cfgrow['site_status_note']));
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php
$thisyear = date("Y");
$site_subname = ($cfgtoken['site_subname'] != '') ? "<a href='{$cfgrow['site_url']}'>{$cfgtoken['site_subname']}</a>" : "<a href='https://www.mlmscript.net/id/{$cfgrow['envacc']}' target='_blank'>{$cfgrow['site_name']}</a>";

$page_content = <<<INI_HTML
                <div class="simple-footer">
                    <!--
                    You are not allowed to remove this credit link unless you have right to do so by own the Extended license or order the Branding Removal license at https://www.mlmscript.net/order
                    -->
                    <div class="text-small">{$LANG['g_builtwith']} <i class="fa fa-fw fa-heart"></i> {$thisyear} <div class="bullet"></div> {$site_subname}{$cfgrow['_isnocreditstr']}
                </div>

</div>

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