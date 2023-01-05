<?php
include_once('../common/init.loader.php');
require_once('../common/mailer.do.php');

if (verifylog_sess('admin') == '') {
    die('o o p s !');
}

if (defined('ISDEMOMODE')) {
    die("<span class='badge badge-danger'>Demo Mode</span> f e a t u r e d i s a b l e !</span>");
}

$mtastr = ($cfgrow['emailer'] == 'smtp') ? 'SMTP' : 'PHPMail';
$smtpencr = explode(',', str_replace(' ', '', $cfgrow['smtpencr']));
$smtpenct = (trim($smtpencr[1]) != '') ? trim($smtpencr[1]) : 'ssl';
$datetm = date("Y-m-d H:i:s", time());

//Set the subject line
$msgsubject = "Test sending email by " . $cfgtoken['site_subname'];

// HTML body
$fmessagehtml = "<font size=3><b>UniMatrix - {$mtastr} Test Email</b></font><br /><br />";
$fmessagehtml .= "{$cfgtoken['site_subname']}<br />";
$fmessagehtml .= "Date: <b>" . $datetm . "</b><br />";

// Plain text body (for mail clients that cannot read HTML)
$fmessage = "UniMatrix - {$mtastr} Test Email\n";
$fmessage .= "{$cfgtoken['site_subname']}\n";
$fmessage .= "Date: " . $datetm . "\n";

$isdomailer = domailer($cfgtoken['site_subname'], $cfgrow['site_emailaddr'], $msgsubject, $fmessagehtml, $fmessage, '', '', 1);
if (strpos($isdomailer, '[OK]') !== false) {
    $isdomailerstr = "<span class='text-success'>{$isdomailer}</span>";
    $resicofa = '';
} else {
    $isdomailerstr = "<span class='text-danger'>{$isdomailer}</span>";
    $resicofa = "<div class='float-right'><i class='fa fa-exclamation-triangle text-danger'></i></div>";
}
?>
<table class="table table-striped">
    <tbody>
        <?php
        if ($mtastr == 'SMTP') {
            ?>
            <tr>
                <th scope="row">Host</th>
                <td><?php echo myvalidate($cfgrow['smtphost']); ?></td>
            </tr>
            <tr>
                <th scope="row">Port / Encryption</th>
                <td><?php echo myvalidate($smtpencr[0] . ' / ' . strtoupper($smtpenct)); ?></td>
            </tr>
            <tr>
                <th scope="row">Username</th>
                <td><?php echo myvalidate($cfgrow['smtpuser']); ?></td>
            </tr>
            <tr>
                <th scope="row">Password</th>
                <td><?php echo isset($cfgrow['smtppass']) ? 'Yes' : 'No'; ?></td>
            </tr>
            <?php
        }
        ?>
        <tr>
            <th scope="row">Result<?php echo myvalidate($resicofa); ?></th>
            <td><?php echo myvalidate($isdomailerstr); ?></td>
        </tr>
    </tbody>
</table>
<div class='text-small text-muted float-right'><em><?php echo myvalidate($datetm); ?></em></div>