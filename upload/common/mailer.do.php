<?php

if (!defined('OK_LOADME')) {
    die('n o m a i l e r !');
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require(INSTALL_PATH . '/assets/fellow/phpmailer/Exception.php');
require(INSTALL_PATH . '/assets/fellow/phpmailer/PHPMailer.php');
require(INSTALL_PATH . '/assets/fellow/phpmailer/SMTP.php');

function delivermail($ntcode, $toid, $cntaddarr = '') {
    global $db, $cfgrow, $bpprow;

    if (!defined('ISDEMOMODE')) {

        // load message
        $msgRow = array();
        $condition = " AND ntcode = '{$ntcode}' AND ntoptions LIKE '%|email:1|%'";
        $row = $db->getAllRecords(DB_TBLPREFIX . '_notifytpl', '*', $condition);
        foreach ($row as $value) {
            $msgRow = array_merge($msgRow, $value);
        }

        // populate array
        $cntarr = array();
        $cntarr = array_merge($bpprow, $cntarr);
        $cntarr = array_merge($cfgrow, $cntarr);
        if ($toid > 0) {
            // Get member details
            $mbrdata = getmbrinfo($toid);
            $cntarr = array_merge($mbrdata, $cntarr);
            $emailtoname = $mbrdata['firstname'] . ' ' . $mbrdata['lastname'];
            $emailtoaddr = $mbrdata['email'];
        } else {
            $emailtoname = $cfgrow['site_emailname'];
            $emailtoaddr = $cfgrow['site_emailaddr'];
        }
        $cntarr = array_merge((array) $cntaddarr, $cntarr);

        $getoutput = array();
        $getoutput['result'] = 0;
        $getoutput['errmsg'] = '';

        if ($msgRow['ntid'] < 1) {
            $getoutput['errmsg'] = 'Email notification not found or was disabled by the Administrator!';
        } else if ($toid < 1 || $mbrdata['optinme'] == 1) {

            // get option
            $ntoptionsarr = get_optionvals($msgRow['ntoptions']);
            $cctoadmin = $ntoptionsarr['cctoadmin'];

            // parse content
            $msgsubject = parsenotify($cntarr, $msgRow['ntsubject']);
            $msgtext = parsenotify($cntarr, $msgRow['nttext']);
            $msghtml = parsenotify($cntarr, $msgRow['nthtml']);

            // Instantiation and passing `true` enables exceptions
            $mail = new PHPMailer(true);
            $mail->CharSet = 'UTF-8';
            $mail->Encoding = 'base64';

            if ($cfgrow['emailer'] == 'smtp') {
                $smtpencr = explode(',', str_replace(' ', '', $cfgrow['smtpencr']));
                $mail->isSMTP();
                $mail->Host = $cfgrow['smtphost'];
                $mail->SMTPAuth = true;
                $mail->Username = $cfgrow['smtpuser'];
                $mail->Password = base64_decode($cfgrow['smtppass']);
                $mail->SMTPSecure = (trim($smtpencr[1]) != '') ? trim($smtpencr[1]) : 'ssl';
                $mail->Port = intval($smtpencr[0]);
            }

            try {
                //Set who the message is to be sent from
                $mail->setFrom($cfgrow['site_emailaddr'], $cfgrow['site_emailname']);
                //Set an alternative reply-to address
                //$mail->addReplyTo($cfgrow['site_emailaddr'], $cfgrow['site_emailname']);
                //Set who the message is to be sent to
                $mail->addAddress($emailtoaddr, $emailtoname);
                //Set the subject line
                $mail->Subject = $msgsubject;
                //Read an HTML message body from an external file, convert referenced images to embedded,
                //convert HTML into a basic plain-text alternative body
                $mail->msgHTML($msghtml, __DIR__);
                //Replace the plain text body with one created manually
                $mail->AltBody = $msgtext;
                //Attach an image file
                //$mail->addAttachment('images/phpmailer_mini.png');
                //send the message, check for errors
                if (!$mail->send()) {
                    $getoutput['errmsg'] = $mail->ErrorInfo . ' Contact the Administrator for further assistance!';
                    printlog('mailer.do', "Mailer Error ({$toid}/{$msgsubject}): {$mail->ErrorInfo}");
                } else {
                    $getoutput['result'] = 1;
                    printlog('mailer.do', "Message sent ({$toid}/{$msgsubject})!");
                }

                // copy email to admin
                if ($cctoadmin == '1') {
                    //Clear existing recipients
                    $mail->clearAllRecipients();
                    //Set who the message is to be sent to
                    $mail->addAddress($cntarr['pay_emailaddr'], $cntarr['pay_emailname']);
                    //Set the subject line
                    $mail->Subject = 'COPY: ' . $msgsubject;

                    //send the message, check for errors
                    if (!$mail->send()) {
                        $getoutput['errmsg'] = $mail->ErrorInfo . ' Contact the Administrator for further assistance!';
                        printlog('mailer.do', "Mailer Error ({$toid}/COPY: {$msgsubject}): {$mail->ErrorInfo}");
                    } else {
                        $getoutput['result'] = 1;
                        printlog('mailer.do', "Message sent ({$toid}/COPY: {$msgsubject})!");
                    }
                }
            } catch (Exception $e) {
                $getoutput['errmsg'] = $mail->ErrorInfo . ' Contact the Administrator for further assistance!';
                printlog('mailer.do', "Message ({$toid}/{$ntcode}) could not be sent. Mailer Error: {$mail->ErrorInfo}");
            }
        } else {
            $getoutput['errmsg'] = 'Sending email features was disabled or no valid recipients found!';
        }
        return $getoutput;
    }
}

function domailer($emailtoname, $emailtoaddr, $msgsubject, $msghtml, $msgtext, $attc_cnt = '', $attc_filename = '', $displayerr = 0, $emailfromname = '', $emailfromaddr = '') {
    global $cfgrow;

    if (!defined('ISDEMOMODE')) {

        if ($emailtoaddr != '' && $msgsubject != '') {

            // Instantiation and passing `true` enables exceptions
            $mail = new PHPMailer(true);
            $mail->CharSet = 'UTF-8';
            $mail->Encoding = 'base64';

            $issent = ($displayerr == 1) ? '' : false;
            if ($cfgrow['emailer'] == 'smtp') {
                $smtpencr = explode(',', str_replace(' ', '', $cfgrow['smtpencr']));
                $mail->isSMTP();
                $mail->Host = $cfgrow['smtphost'];
                $mail->SMTPAuth = true;
                $mail->Username = $cfgrow['smtpuser'];
                $mail->Password = base64_decode($cfgrow['smtppass']);
                $mail->SMTPSecure = (trim($smtpencr[1]) != '') ? trim($smtpencr[1]) : 'ssl';
                $mail->Port = intval($smtpencr[0]);
            }

            try {
                //Set who the message is to be sent from
                $fromemail = ($emailfromaddr == '') ? $cfgrow['site_emailaddr'] : $emailfromaddr;
                $fromname = ($emailfromaddr == '') ? $cfgrow['site_emailname'] : $emailfromname;
                $mail->setFrom($fromemail, $fromname);
                //Set an alternative reply-to address
                //$mail->addReplyTo($cfgrow['site_emailaddr'], $cfgrow['site_emailname']);
                //Set who the message is to be sent to
                $mail->addAddress($emailtoaddr, $emailtoname);
                //Set the subject line
                $mail->Subject = $msgsubject;
                //Read an HTML message body from an external file, convert referenced images to embedded,
                //convert HTML into a basic plain-text alternative body
                $mail->msgHTML($msghtml, __DIR__);
                //Replace the plain text body with one created manually
                $mail->AltBody = $msgtext;
                //Attach a file
                if ($attc_cnt != '' && $attc_filename != '') {
                    $mail->AddStringAttachment($attc_cnt, $attc_filename);
                }
                //send the message, check for errors
                if (!$mail->send()) {
                    $issent = ($displayerr == 1) ? $mail->ErrorInfo : '';
                    printlog('mailer.do/domailer', "Mailer Error ({$emailtoaddr}/{$msgsubject}): {$mail->ErrorInfo}");
                } else {
                    $issent = ($displayerr == 1) ? 'Message Sent [OK]' : true;
                    printlog('mailer.do/domailer', "Message sent ({$emailtoaddr}/{$msgsubject})!");
                }
            } catch (Exception $e) {
                $issent = ($displayerr == 1) ? $mail->ErrorInfo : '';
                printlog('mailer.do/domailer', "Message ({$emailtoaddr}/{$msgsubject}) could not be sent. Mailer Error: {$mail->ErrorInfo}");
            }
            return $issent;
        }
    }
}
