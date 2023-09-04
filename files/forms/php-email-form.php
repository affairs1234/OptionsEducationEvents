<?php

class PHP_Email_Form {
    public $to = 'false';
    public $from_firstname = false;
    public $from_lastname = false;
    public $from_email = false;
    public $from_phone = false;
    public $study_destination = false;
    public $preferred_intake = false;
    public $level_of_education = false;
    public $how_did_you_hear_about_us = false;


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
        'invalid_from_firstname' => 'From FirstName is empty!',
        'invalid_from_lastname' => 'From LastName is empty!',
        'invalid_from_email' => 'From Email is empty or invalid!',
        'invalid_from_phone' => 'From Phone is empty!',
        'invalid_study_destination' => 'Study Destination is empty!',
        'invalid_preferred_intake' => 'Preferred Intake is empty!',
        'invalid_level_of_education' => 'Level of Education is empty!',
        'invalid_how_did_you_hear_about_us' => 'How did you hear about us is empty!',
        'short' => 'is too short or empty!',
        'ajax_error' => 'Sorry, the request should be an Ajax POST',
        'captcha' => 'Captcha is invalid or empty!'
    );

    private $error = false;
  private $attachments = [];

  public function __construct() {
    $this->mailer = "forms@" . @preg_replace('/^www\./','', $_SERVER['SERVER_NAME']);
  }
  public function option($name, $val) {
    $this->options[$name] = $val;
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
    $from_firstname = filter_var( $this->from_firstname, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $from_lastname = filter_var( $this->from_lastname, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $from_email = filter_var( $this->from_email, FILTER_VALIDATE_EMAIL);
    $from_phone = filter_var( $this->from_phone, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $study_destination = filter_var( $this->study_destination, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $preferred_intake = filter_var( $this->preferred_intake, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $level_of_education = filter_var( $this->level_of_education, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $how_did_you_hear_about_us = filter_var( $this->how_did_you_hear_about_us, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    if( ! $to || md5($to) == '496c0741682ce4dc7c7f73ca4fe8dc5e') 
      $this->error .= $this->error_msg['invalid_to_email'] . '<br>';
      if( ! $from_firstname ) 
        $this->error .= $this->error_msg['invalid_from_firstname'] . '<br>';
        if( ! $from_lastname ) 
          $this->error .= $this->error_msg['invalid_from_lastname'] . '<br>';
          if( ! $from_email ) 
            $this->error .= $this->error_msg['invalid_from_email'] . '<br>';
            if( ! $from_phone ) 
              $this->error .= $this->error_msg['invalid_from_phone'] . '<br>';
              if( ! $study_destination ) 
                $this->error .= $this->error_msg['invalid_study_destination'] . '<br>';
                if( ! $preferred_intake ) 
                  $this->error .= $this->error_msg['invalid_preferred_intake'] . '<br>';
                  if( ! $level_of_education ) 
                    $this->error .= $this->error_msg['invalid_level_of_education'] . '<br>';
                    if( ! $how_did_you_hear_about_us ) 
                      $this->error .= $this->error_msg['invalid_how_did_you_hear_about_us'] . '<br>';

                      if( $this->error ) {
                        return $this->error;
                      }

                      $message = '';
                      $message .= '<html><body>';
                      $message .= '<table rules="all" style="border-color: #666;" cellpadding="10">';
                      $message .= "<tr style='background: #eee;'><td><strong>From:</strong> </td><td>" . $from_firstname . ' ' . $from_lastname . "</td></tr>";
                      $message .= "<tr><td><strong>Email:</strong> </td><td>" . $from_email . "</td></tr>";
                      $message .= "<tr><td><strong>Phone:</strong> </td><td>" . $from_phone . "</td></tr>";
                      $message .= "<tr><td><strong>Preferred Study Destination:</strong> </td><td>" . $study_destination . "</td></tr>";
                      $message .= "<tr><td><strong>Preferred Intake:</strong> </td><td>" . $preferred_intake . "</td></tr>";
                      $message .= "<tr><td><strong>Level of Education:</strong> </td><td
                        >" . $level_of_education . "</td></tr>";
                        $message .= "<tr><td><strong>How did you hear about us:</strong> </td><td>" . $how_did_you_hear_about_us . "</td></tr>";
                        $message .= "</table>";
                        $message .= "</body></html>";

                        $headers = "From: $from_firstname $from_lastname <$from_email>" . "\r\n";
                        $headers .= "Reply-To: $from_email" . "\r\n";
                        $headers .= "MIME-Version: 1.0\r\n";
                        $headers .= "Content-Type: $this->content_type; charset=$this->charset\r\n";

                        if( $this->cc ) {
                          $headers .= "CC: " . implode(',', $this->cc) . "\r\n";
                        }

                        if( $this->bcc ) {
                          $headers .= "BCC: " . implode(',', $this->bcc) . "\r\n";
                        }

                        if( $this->attachments ) {
                          $boundary = md5(time());
                          $headers .= "Content-Type: multipart/mixed;boundary=" . $boundary . "\r\n";
                          $message = "--" . $boundary . "\r\n";
                          $message .= "Content-Type: text/html; charset='utf-8'\r\n";
                          $message .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
                          $message .= $message . "\r\n";
                          foreach( $this->attachments as $attachment ) {
                            $message .= "--" . $boundary . "\r\n";
                            $message .= "Content-Type: " . $attachment['type'] . "; name=\"" . $attachment['name'] . "\"\r\n";
                            $message .= "Content-Transfer-Encoding: base64\r\n";
                            $message .= "Content-Disposition: attachment\r\n";
                            $message .= chunk_split(base64_encode(file_get_contents($attachment['file']))) . "\r\n";
                          }
                          $message .= "--" . $boundary . "--";
                        }

                        if( $this->options ) {
                          foreach( $this->options as $key => $value ) {
                            $headers .= $key . ": " . $value . "\r\n";
                          }
                        }

                        if( mail($to, $message, $headers) ) {
                          return 'OK';
                        } else {
                          return 'Unable to send email!';
                        }
                        }
                        public function attach($file, $name, $type) {
                          $this->attachments[] = [
                            'file' => $file,
                            'name' => $name,
                            'type' => $type
                          ];
                        }
                        }

                        ?>