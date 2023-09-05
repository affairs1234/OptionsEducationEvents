<?php


  $receiving_email_address = 'abikoyeboluwatife@gmail.com';

  if( file_exists($php_email_form = 'php-email-form.php' )) {
    include( $php_email_form );
  } else {
    die( 'Unable to load the "PHP Email Form" Library!');
  }

    $contact = new PHP_Email_Form;
    $contact->ajax = true;

    $contact->to = $receiving_email_address;
    $contact->from_firstname = $_POST['firstname'];
    $contact->from_lastname = $_POST['lastname'];
    $contact->from_email = $_POST['email'];
    $contact->from_phone = $_POST['phone'];
    $contact->study_destination = $_POST['study_destination'];
    $contact->preferred_intake = $_POST['preferred_intake'];
    $contact->level_of_education = $_POST['level_of_education'];
    $contact->how_did_you_hear_about_us = $_POST['how_did_you_hear_about_us'];
    // $contact->add_message( $_POST['firstname'], 'From');
    // $contact->add_message( $_POST['lastname'], 'From');
    // $contact->add_message( $_POST['email'], 'Email');
    // $contact->add_message( $_POST['phone'], 'Phone');
    // $contact->add_message( $_POST['study_destination'], 'Preferred Study Destination');
    // $contact->add_message( $_POST['preferred_intake'], 'Preferred Intake');
    // $contact->add_message( $_POST['level_of_education'], 'Level of Education');
    // $contact->add_message( $_POST['how_did_you_hear_about_us'], 'How did you hear about us');
    echo $contact->send();
?>
