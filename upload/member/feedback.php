<?php
if (!defined('OK_LOADME')) {
    die('o o p s !');
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$tempsess = $_SESSION;

$fsubject = base64_decode($tempsess['fsubject']);
$fmessage = base64_decode($tempsess['fmessage']);
$fmsgtypearr = array(0, 1, 2);
$fmsgtype_cek = radiobox_opt($fmsgtypearr, $tempsess['fmsgtype']);

if ($FORM['isconfirm'] != '') {
    // transaction
    $isconfirm = base64_decode($FORM['isconfirm']);
    $txmpid = explode('-', $isconfirm);
    $txid = intval($txmpid[0]);
    $mpid = intval($txmpid[1]);
    $trxrow = array();
    $row = $db->getAllRecords(DB_TBLPREFIX . '_transactions', '*', ' AND txid = "' . $txid . '"');
    foreach ($row as $value) {
        $trxrow = array_merge($trxrow, $value);
    }
    $amount = $bpprow['currencysym'] . $trxrow['txamount'] . ' ' . $bpprow['currencycode'] . " ({$mbrstr['username']}-{$txid})";

    $fsubject = 'Payment Confirmation: ' . $amount;
    $fmessage = 'Write the confirmation message here...';
}

if (isset($FORM['dosubmit']) and $FORM['dosubmit'] == '1') {

    extract($FORM);

    if (!defined('ISDEMOMODE') && !defined('ISNOMAILER') && $fsubject && $fmessage) {
        if (!dumbtoken($dumbtoken)) {
            $_SESSION['show_msg'] = showalert('danger', 'Error!', $LANG['g_invalidtoken']);
            $redirval = "?res=errtoken";
            redirpageto($redirval);
            exit;
        }

        if ($fmsgtype != 9) {
            $_SESSION['fsubject'] = base64_encode($fsubject);
            $_SESSION['fmessage'] = base64_encode($fmessage);
        }
        $_SESSION['fmsgtype'] = $fmsgtype;
        $_SESSION['fmsgtime'] = time();

        $manpaytxid = filter_var($manpaytxid, FILTER_SANITIZE_STRING);
        $sb_label = filter_var($sb_label, FILTER_SANITIZE_STRING);

        switch ($fmsgtype) {
            case "1":
                $fmsgtypestr = "Support Request";
                break;
            case "2":
                $fmsgtypestr = "Feedback or Suggestion";
                break;
            case "9":
                $fmsgtypestr = "Payment Confirmation";
                $manpaytxidstr = ($manpaytxid != '') ? " {$manpaytxid}" : '';
                break;
            default:
                $fmsgtypestr = "General Question";
        }


        require_once('../common/mailer.do.php');

        //Set the subject line
        $msgsubject = mystriptag($fsubject);

        $fmessage = mystriptag($fmessage);
        $fmessageadd = "{$fmsgtypestr}:{$manpaytxidstr}
            Name: {$mbrstr['firstname']} {$mbrstr['lastname']} ({$mbrstr['email']})
            Username: {$mbrstr['username']}

            ";
        // Plain text body (for mail clients that cannot read HTML)
        $fmessage = $fmessageadd . $fmessage;

        // HTML body
        $fmessagehtml = nl2br($fmessage);

        $filegetcnt = $filenameout = '';
        if ($_FILES["fattfile"]["name"]) {
            $extfile = array("zip", "rar", "gif", "jpg", "png");
            $faextension = pathinfo($_FILES["fattfile"]["name"], PATHINFO_EXTENSION);
            $isFileExtension = ( (in_array($faextension, $extfile)) ? true : false );
            if ($isFileExtension) {
                $filegetcnt = file_get_contents($_FILES["fattfile"]["tmp_name"]);
                $filenameout = $_FILES["fattfile"]["name"];
            }
        }

        //send the message, check for errors
        $isdomailer = domailer($bpprow['pay_emailname'], $bpprow['pay_emailaddr'], $msgsubject, $fmessagehtml, $fmessage, $filegetcnt, $filenameout);

        if ($isdomailer) {
            $imgsentstr = '';
            $extimgfile = array("gif", "jpg", "png");
            $isImgFileExtension = (in_array($faextension, $extimgfile)) ? true : false;
            if ($txid > 0 && $isImgFileExtension) {
                $proofimg = 'proofimg' . $txid;
                do_imgresize($proofimg, $_FILES["fattfile"]["tmp_name"], 720, 0, 'jpeg');

                $txrow = $db->getAllRecords(DB_TBLPREFIX . '_transactions', 'txtoken', ' AND txid="' . $txid . '"');
                $txtoken = $txrow[0]['txtoken'];
                $txtoken = put_optionvals($txtoken, 'proofimg', $proofimg . '.jpg');
                $manpaytxid64 = base64_encode($manpaytxid);
                $txtoken = put_optionvals($txtoken, 'manpaytxid', $manpaytxid64);
                $txtoken = put_optionvals($txtoken, 'sb_label', $sb_label);

                $data = array(
                    'txtoken' => $txtoken,
                );
                $update = $db->update(DB_TBLPREFIX . '_transactions', $data, array('txid' => $txid));
                if ($txid && $fmsgtype == 9) {
                    $imgsentstr = 'Image uploaded and ';
                }
                $hal = 'dashboard';
            }

            $_SESSION['dotoaster'] = "toastr.success('{$imgsentstr}Message sent!', 'Success');";
        } else {
            $_SESSION['dotoaster'] = "toastr.error('Mailer Error ({$fsubject}): {$mail->ErrorInfo}. Please contact your hosting provider for assistance!', 'Warning');";
        }
    }

    //header('location: index.php?hal=' . $hal);
    redirpageto('index.php?hal=' . $hal);
    exit;
}

// less than five minutes ago
if ($_SESSION['fsubject'] != '' && $_SESSION['fmsgtime'] > time() - 60 * 5) {
    $btnsendaval = " disabled";
} else {
    $_SESSION['fsubject'] = $_SESSION['fmessage'] = $_SESSION['fmsgtype'] = $_SESSION['fmsgtime'] = '';
    $btnsendaval = '';
}
?>

<div class="section-header">
    <h1><i class="fa fa-fw fa-life-ring"></i> <?php echo myvalidate($LANG['m_feedback']); ?></h1>
</div>

<div class="section-body">
    <div class="row">
        <div class="col-md-4">	
            <div class="card">
                <div class="card-header">
                    <h4><?php echo myvalidate($LANG['m_contactus']); ?></h4>
                </div>
                <div class="card-body">
                    <div class="chocolat-parent">
                        <div>
                            <img alt="image" src="<?php echo myvalidate($site_logo); ?>" class="img-fluid rounded-circle img-shadow author-box-picture">
                        </div>
                    </div>
                    <div class="mb-2 text-muted"><?php echo isset($cfgrow['site_descr']) ? $tempsess['site_descr'] : ''; ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-8">	
            <div class="card">

                <form method="post" action="index.php" enctype="multipart/form-data" id="fbackform">
                    <input type="hidden" name="hal" value="feedback">

                    <div class="card-header">
                        <h4><?php echo myvalidate($LANG['m_feedbackform']); ?></h4>
                    </div>

                    <div class="card-body">
                        <div class="tab-content no-padding" id="myTab2Content">
                            <div class="tab-pane fade show active">
                                <p class="text-muted"><?php echo myvalidate($LANG['m_feedbacknote']); ?></p>

                                <div class="form-group">
                                    <label for="fsubject"><?php echo myvalidate($LANG['m_feedbacksubject']); ?></label>
                                    <input type="text" name="fsubject" id="fsubject" class="form-control" value="<?php echo isset($fsubject) ? $fsubject : ''; ?>" placeholder="<?php echo myvalidate($LANG['m_userfeedbacksubject']); ?>" required>
                                </div>

                                <div class="form-group">
                                    <label for="fmessage"><?php echo myvalidate($LANG['m_feedbackmessage']); ?></label>
                                    <textarea class="form-control rowsize-md" name="fmessage" id="fmessage" placeholder="<?php echo myvalidate($LANG['m_userfeedbackmessage']); ?>" required><?php echo isset($fmessage) ? $fmessage : ''; ?></textarea>
                                </div>

                                <?php
                                if ($FORM['isconfirm'] != '') {
                                    ?>
                                    <div class="form-group">
                                        <label for="manpaytxid"><?php echo myvalidate($LANG['m_feedbackpaytxid']); ?></label>
                                        <input type="text" name="manpaytxid" id="manpaytxid" class="form-control" value="" placeholder="<?php echo myvalidate($LANG['m_userfeedbackpaytxid']); ?>">
                                    </div>
                                    <?php
                                }
                                ?>

                                <div class="form-group">
                                    <label for="fattfile"><?php echo myvalidate($LANG['m_feedbackupload']); ?> (<?php echo isset($txid) ? $LANG['m_feedbackuploadproof'] : $LANG['m_feedbackuploadarchive']; ?>)</label>
                                    <input type="file" name="fattfile" id="uploadImgFile" class="form-control">
                                    <div class="form-text text-muted"><?php echo myvalidate($LANG['g_uploadsize']); ?></div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="selectgroup-pills"><?php echo myvalidate($LANG['m_feedbacktype']); ?></label>
                                <div class="selectgroup selectgroup-pills">
                                    <?php
                                    if ($FORM['isconfirm'] != '') {
                                        ?>
                                        <label class="selectgroup-item">
                                            <input type="radio" name="fmsgtype" value="9" class="selectgroup-input" checked="checked">
                                            <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-fw fa-question-circle"></i> <?php echo myvalidate($LANG['m_feedbacktypepayment']); ?></span>
                                        </label>
                                        <?php
                                    } else {
                                        ?>
                                        <label class="selectgroup-item">
                                            <input type="radio" name="fmsgtype" value="0" class="selectgroup-input"<?php echo myvalidate($fmsgtype_cek[0]); ?>>
                                            <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-fw fa-question-circle"></i> <?php echo myvalidate($LANG['m_feedbacktypegeneral']); ?></span>
                                        </label>
                                        <label class="selectgroup-item">
                                            <input type="radio" name="fmsgtype" value="1" class="selectgroup-input"<?php echo myvalidate($fmsgtype_cek[1]); ?>>
                                            <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-fw fa-hands-helping"></i> <?php echo myvalidate($LANG['m_feedbacktypesupport']); ?></span>
                                        </label>
                                        <label class="selectgroup-item">
                                            <input type="radio" name="fmsgtype" value="2" class="selectgroup-input"<?php echo myvalidate($fmsgtype_cek[2]); ?>>
                                            <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-fw fa-comment-medical"></i> <?php echo myvalidate($LANG['m_feedbacktypesuggest']); ?></span>
                                        </label>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </div>

                        </div>

                    </div>

                    <div class="card-footer bg-whitesmoke text-md-right">
                        <button type="reset" name="reset" value="reset" id="reset" class="btn btn-warning">
                            <i class="fa fa-fw fa-undo"></i> <?php echo myvalidate($LANG['g_reset']); ?>
                        </button>
                        <button type="submit" name="submit" value="submit" id="submit" class="btn btn-primary"<?php echo myvalidate($btnsendaval); ?>>
                            <i class="fa fa-fw fa-plus-circle"></i> <?php echo myvalidate($LANG['g_sendmessage']); ?>
                        </button>
                        <input type="hidden" name="dosubmit" value="1">
                        <input type="hidden" name="txid" value="<?php echo myvalidate($txid); ?>">
                        <input type="hidden" name="sb_label" value="<?php echo myvalidate($FORM['pby']); ?>">
                        <input type="hidden" name="dumbtoken" value="<?php echo myvalidate($_SESSION['dumbtoken']); ?>">
                    </div>

                </form>
            </div>

        </div>
    </div>
</div>
</div>
