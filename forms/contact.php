<?php

  $receiving_email_address = 'abikoyeboluwatife@gmail.com';
//send form data to receiving email address
  if(isset($_POST['submit'])){
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $destination = $_POST['destination'];
    $intake = $_POST['intake'];
    $qualification = $_POST['qualification'];

    $mailTo = "abikoyeboluwatife@gmail.com";
    $headers = "From: ".$email;
    $txt = "You have received an email from ".$name;

    mail($mailTo, $email, $phone, $destination, $intake, $qualification, $txt, $headers);
    header("Location: index.php?mailsend");
  }
?>
