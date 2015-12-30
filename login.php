<?php

session_start(); // Starting Session
include_once("config.php");

$error = ''; // Variable To Store Error Message

if(isset($_POST['submit'])) {
  if(empty($_POST['username']) || empty($_POST['password'])) {
    $error = "Username or Password is invalid";
  }
  else
  {
    // Define $username and $password
    $username = $_POST['username'];
    $password = hash('sha512',$_POST['password']);
    // Establishing Connection with Server by passing server_name, user_id and password as a parameter

    if($query = $mysqli->prepare("SELECT username FROM credentials WHERE username = ? AND password = ?")) {
      $query->bind_param('ss', $username, $password);
      $query->execute();
      $query->store_result();

      $num_row = $query->num_rows();
      $query->bind_result($username);

      $query->fetch();
      $query->close();
    }
    else {
      die("Failed to prepare query");
    }

    if($num_row == 1) {
      $_SESSION['login_user'] = $username; // Initializing Session
      header("location: administrare.php"); // Redirecting To Other Page
    }
    else {
      $error = "Nume utilizator sau parola gresita!";
    }
  }
}

?>
