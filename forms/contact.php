<?php

  $receiving_email_address = 'abikoyeboluwatife@gmail.com';

  if( file_exists($php_email_form = '../files/functionalities/php-email-form/php-email-form.php' )) {
    include( $php_email_form );
  } else {
    die( 'Unable to load the "PHP Email Form" Library!');
  }

  $register = new PHP_Email_Form;
  $register->ajax = true;
  
  $register->to = $receiving_email_address;
  $register->from_name = $_POST['name'];
  $register->from_email = $_POST['email'];
  $register->from_phone = $_POST['phone'];
  $register->from_destination = $_POST['destination'];
  $register->from_intake = $_POST['intake'];
  $register->from_qualification = $_POST['qualification'];
  $register->subject = 'New Registration';


  $register->add_message( $_POST['name'], 'From');
  $register->add_message( $_POST['email'], 'Email');
  $register->add_message( $_POST['phone'], 'Mobile no');
  $register->add_message( $_POST['destination'], 'Preferred Study Destination');
  $register->add_message( $_POST['intake'], 'Preferred Intake');
  $register->add_message( $_POST['qualification'], 'Qualificcation', 4);

  echo $contact->send();
?>
