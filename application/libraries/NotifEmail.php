<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once('my_phpmailer.php');
class NotifEmail extends PHPMailer {

	public function email_credentials(){
        #still needs update on the credentials based on firewall setup
        return array(
            'host' => 'smtp.gmail.com',
            'username' => 'pmsdacayo@gmail.com',
            'password' => 'icwvbjzmycfjpoim',
			'port' =>  465,
            'SMTPAuth' => true,
            'mail_sender' => 'pmsdacayo@gmail.com',
            'SMTPSecure' =>  'ssl'
        );
    }
    
	public function email_notification($email,$subject,$body) {

		$email_credentials = $this->email_credentials();
        $this->isSMTP();                                        // Set mailer to use SMTP
        $this->Host = $email_credentials['host'];               // Specify main and backup SMTP servers
        $this->SMTPAuth = $email_credentials['SMTPAuth'];       // Enable SMTP authentication
        $this->Username = $email_credentials['username'];       // SMTP username
        $this->Password = $email_credentials['password'];       // SMTP password
        $this->SMTPSecure = $email_credentials['SMTPSecure'];   // Enable TLS encryption, `ssl` also accepted
        $this->Port = $email_credentials['port'];
		$this->setFrom($email_credentials['mail_sender'], "Mailer");
        $this->addAddress($email); 

		$this->isHTML(true);                                  // Set email format to HTML

        $this->Subject = $subject;
        $this->Body    = ''.$body.'';

        $this->AltBody = 'Health Records Organizer (HERO) Notification';
        if(!$this->send()) {
            echo $this->ErrorInfo;
        } else {
			echo TRUE;
        }

	}

}

