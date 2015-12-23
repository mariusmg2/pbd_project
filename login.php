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
    $password = $_POST['password'];
    // Establishing Connection with Server by passing server_name, user_id and password as a parameter

    if($mysql_fetch_utilizator = $mysqli->prepare("SELECT username FROM credentials WHERE username = ? AND password = ?")) {
      $mysql_fetch_utilizator->bind_param('ss', $username, $password);
      $mysql_fetch_utilizator->execute();
      $mysql_fetch_utilizator->store_result();

      $num_row = $mysql_fetch_utilizator->num_rows();
      $mysql_fetch_utilizator->bind_result($username);

      $mysql_fetch_utilizator->fetch();
      $mysql_fetch_utilizator->close();
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
