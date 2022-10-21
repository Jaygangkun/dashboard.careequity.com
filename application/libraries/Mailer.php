<?php

require_once APPPATH."libraries/PHPMailer/Exception.php";
require_once APPPATH."libraries/PHPMailer/PHPMailer.php";
require_once APPPATH."libraries/PHPMailer/SMTP.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mailer 
{

	function __construct()
	{
		$this->CI =& get_instance();

	}

    function initMailer(){
        $mail = new PHPMailer();

		$mail->IsSMTP();
		$mail->Host = 'mail.tools.careequity.com';
		$mail->Port = 465;
		$mail->SMTPAuth = true;
		$mail->Username = 'pubmed@pubmed.careequity.com';
		$mail->Password = 'M?r;=[_Wq631';
		$mail->SMTPSecure = 'ssl';
		$mail->SMTPDebug  = 1;  
		$mail->SMTPAuth   = TRUE;

        $mail->isHTML();
        $mail->IsHTML(true);

        $mail->From = 'contact@tools.careequity.com';
        $mail->FromName = 'Care Equity Tools';

        return $mail;
    }

    function sendTestMail(){
        $mail = $this->initMailer();
        
        $mail->From = 'pubmed@pubmed.careequity.com';
		$mail->FromName = 'Clinic';

        $mail->IsHTML(true);
		$mail->Subject = "Message from contact form";
    	$mail->Body    = "This is test email";
		$mail->AddAddress('kingdeveloper@yahoo.com');

		$mail->addAttachment('searchresults/Test Attachment.pdf');

		if(!$mail->Send()) {
			echo $mail->ErrorInfo;
		}

		echo "success";
    }

	function sendVerificationCode($data){
        $mail = $this->initMailer();

		$mail->Subject = "Message from Care Equity Tools";
    	$mail->Body    = '
            <p>This is verification code!</p>
            <p><strong>'.$data['code'].'</strong></p>

            <br>
            <br>

            <p>Regards, <br> 
            Care Equity Tools <br> 
            </p>
        ';

        $mail->AddAddress($data['to']);

		if(!$mail->Send()) {
			return array(
                'success' => false,
                'msg' => $mail->ErrorInfo
            );
		}

        return array(
            'success' => true
        );
    }

    function sendResetPasswordMail($data){
        $mail = $this->initMailer();

		$mail->Subject = "Message from Care Equity Tools";
    	$mail->Body    = '<h3>Hi ' .$data['username'].'</h3>
        <p>Welcome to the Care Equity Tools!</p>
        <p>We have received a request to reset your password. If you did not initiate this request, you can simply ignore this message and no action will be taken.</p> 
        <p>To reset your password, please click the link below:</p> 
        <p><a href="'.$data['reset_link'].'">'.$data['reset_link'].'</a></p>

        <br>
        <br>

        <p>&#169; 2021 Care Equity Tools - All rights reserved</p>
        ';

        $mail->AddAddress($data['to']);

		if(!$mail->Send()) {
			return array(
                'success' => false,
                'msg' => $mail->ErrorInfo
            );
		}

        return array(
            'success' => true
        );
    }

}
?>