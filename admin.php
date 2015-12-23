<?php

if (session_status() == PHP_SESSION_ACTIVE) {
  session_start();
}

include_once("config.php");
include_once('login.php'); // Includes Login Script

if(isset($_SESSION['login_user'])){
  header("location: administrare.php");
}

?>

<!DOCTYPE html>
<html>
<head>
  <title>Te rugam sa introduci datele de autentificare!</title>
  <link href="style/login_style.css" rel="stylesheet" type="text/css">
</head>
<body>
  <div id="main">
    <h1>Introdu datele de autentificare!</h1>
    <div id="login">
      <form action="" method="post">
        <label>Nume utilizator :</label>
        <input id="name" name="username" placeholder="username" type="text">
        <label>Parola :</label>
        <input id="password" name="password" placeholder="**********" type="password">
        <input name="submit" type="submit" value=" Login ">
        <span><?php echo $error; ?></span>
      </form>
    </div>
  </div>
</body>
</html>
