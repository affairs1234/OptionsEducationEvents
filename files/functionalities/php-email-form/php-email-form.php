<?php

if ( version_compare(phpversion(), '5.5.0', '<') ) {
  die('PHP version 5.5.0 and up is required. Your PHP version is ' . phpversion());
}

class PHP_Email_Form {

    public $to = false;
    public $from_name = false;
    public $from_email = false;
    public $from_phone = false;
    public $from_destination = false;
    public $from_intake = false;
    public $from_qualification = false;
    public $mailer = false;
    public $smtp = false;

    public $content_type = 'text/html';
  public $charset = 'utf-8';
  public $ajax = false;

  public $options = [];
  public $cc = [];
  public $bcc = [];
  public $honeypot = '';
  public $recaptcha_secret_key = false;

  public $error_msg = array(
    'invalid_to_email' => 'Email to (receiving email address) is empty or invalid!',
    'invalid_from_name' => 'From Name is empty!',
    'invalid_from_email' => 'Email from: is empty or invalid!',
    'invalid_from_phone' => 'Phone is empty!',
    'invalid_from_destination' => 'Preferred Study Destination is empty or invalid!',
    'invalid_from_intake' => 'Preferred Intake is empty or invalid!',
    'invalid_from_qualification' => 'Level Of Study is empty or invalid!',
    'ajax_error' => 'Sorry, the request should be an Ajax POST',
    'invalid_attachment_extension' => 'File extension not allowed, please choose:',
    'invalid_attachment_size' => 'Max allowed attachment size is:'
    );

    private $error = false;
  private $attachments = [];

  public function __construct() {
    $this->mailer = "forms@" . @preg_replace('/^www\./','', $_SERVER['SERVER_NAME']);
  }
  public function add_message($content, $label = '', $length_check = false) {
    $message = filter_var($content, FILTER_SANITIZE_FULL_SPECIAL_CHARS) . '<br>';
    if( $length_check ) {
      if( strlen($message) < $length_check + 4 ) {
        $this->error .=  $label . ' ' . $this->error_msg['short'] . '<br>';
        return;
      }
    }
    $this->message .= !empty( $label ) ? '<strong>' . $label . ':</strong> ' . $message : $message;
  }

  public function option($name, $val) {
    $this->options[$name] = $val;
  }

  public function add_attachment($name, $max_size = 20, $allowed_exensions = ['jpeg','jpg','png','pdf','doc','docx'] ) {
    if( !empty($_FILES[$name]['name']) ) {
      $file_exension = strtolower(pathinfo($_FILES[$name]['name'], PATHINFO_EXTENSION));
      if( ! in_array($file_exension, $allowed_exensions) ) {
        die( '(' .$name . ') ' . $this->error_msg['invalid_attachment_extension'] . " ." . implode(", .", $allowed_exensions) );
      }
  
      if( $_FILES[$name]['size'] > (1024 * 1024 * $max_size) ) {
        die( '(' .$name . ') ' . $this->error_msg['invalid_attachment_size'] . " $max_size MB");
      }

      $this->attachments[] = [
        'path' => $_FILES[$name]['tmp_name'], 
        'name'=>  $_FILES[$name]['name']
      ];
    }
  }

  public function send() {

    if( !empty(trim($this->honeypot)) ) {
      return 'OK';
    }

    if( $this->recaptcha_secret_key ) {

      if(! $_POST['recaptcha-response']) {
        return 'No reCaptcha response provided!';
      }

      $recaptcha_options = [
        'http' => [
          'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
          'method'  => 'POST',
          'content' => http_build_query([
            'secret' => $this->recaptcha_secret_key,
            'response' => $_POST['recaptcha-response']
          ])
        ]
      ];

      $recapthca_context = stream_context_create($recaptcha_options);
      $recapthca_response = file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, $recapthca_context);
      $recapthca_response_keys = json_decode($recapthca_response,true);

      if( ! $recapthca_response_keys['success'] ) {
        return 'Failed to validate the reCaptcha!';
      }
    }

    if( $this->ajax ) {
      if( !isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
        return $this->error_msg['ajax_error'];
      }
    }

    
    $to = filter_var( $this->to, FILTER_VALIDATE_EMAIL);
    $from_name = filter_var( $this->from_name, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $from_email = filter_var( $this->from_email, FILTER_VALIDATE_EMAIL);
    $from_phone = filter_var( $this->from_phone, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $from_destination = filter_var( $this->from_destination, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $from_intake = filter_var( $this->from_intake, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $from_qualification = filter_var( $this->from_qualification, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    if( ! $to || md5($to) == '496c0741682ce4dc7c7f73ca4fe8dc5e') 
      $this->error .= $this->error_msg['invalid_to_email'] . '<br>';

    if( ! $from_name ) 
      $this->error .= $this->error_msg['invalid_from_name'] . '<br>';

    if( ! $from_email ) 
      $this->error .= $this->error_msg['invalid_from_email'] . '<br>';

    if( ! $from_phone )
        $this->error .= $this->error_msg['invalid_from_phone'] . '<br>';

    if( ! $from_destination )
        $this->error .= $this->error_msg['invalid_from_destination'] . '<br>';

    if( ! $from_intake )
        $this->error .= $this->error_msg['invalid_from_intake'] . '<br>';

    if( ! $from_qualification )
        $this->error .= $this->error_msg['invalid_from_qualification'] . '<br>';

    if( $this->error ) {
        return $this->error;
        }

        $mail = new PHPMailer(true);

        try {
          // Set timeout to 30 seconds
          $mail->Timeout = 30;
    
          // Check and set SMTP
          if( is_array( $this->smtp) ) {
            $mail->isSMTP();
            $mail->Host = $this->smtp['host'];
            $mail->SMTPAuth = true;
            $mail->Username = $this->smtp['username'];
            $mail->Password = $this->smtp['password'];
            $mail->Port = $this->smtp['port'];
            $mail->SMTPSecure = $this->smtp['encryption'];
          }
    
          // Headers
          $mail->CharSet = $this->charset;
          $mail->ContentType = $this->content_type;
    
          // Recipients
          $mail->setFrom( $this->mailer, $from_name );
          $mail->addAddress( $to );
          $mail->addReplyTo( $from_email, $from_name );
    
          // cc
          if(count($this->cc) > 0) {
            foreach($this->cc as $cc) {
              $mail->addCC($cc);
            }
          }
    
          // bcc
          if(count($this->bcc) > 0) {
            foreach($this->bcc as $bcc) {
              $mail->addBCC($bcc);
            }
          }
    
          // Content
          $mail->isHTML(true);
          $mail->Subject = $subject;
          $mail->Body = $message;
    
          // Options
          if(count($this->options) > 0) {
            foreach($this->options as $option_name => $option_val) {
              $mail->$option_name = $option_val;
            }
          }
    
          // Attachments
          if(count($this->attachments) > 0) {
            foreach($this->attachments as $attachment) {
              $mail->AddAttachment($attachment['path'], $attachment['name']);
            }
          }
          $mail->send();

      return 'OK';
    } catch (Exception $e) {
      return 'Mailer Error: ' . $mail->ErrorInfo;
    }
    
  }
}


