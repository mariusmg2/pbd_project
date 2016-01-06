<?php

//! start sesiune.
session_start();

//! includere "config.php" pentru acces la interfata mysql.
include_once("config.php");

$error = '';

//! daca s-au trimis datele catre server.
if(isset($_POST['submit'])) {
  //! daca campurile 'utilizator' sau 'password' sunt vide, seteaza mesaj eroare corespunzator.
  if(empty($_POST['username']) || empty($_POST['password'])) {
    $error = "Username or Password is invalid";
  }
  else {
    //! salveaza numele utilizatorului si parola in niste variabile.
    $username = $_POST['username'];
    $password = hash('sha512',$_POST['password']);

    //! preparare query pentru obtinerea inregistrarii ce corespunde cu datele introduse de utilizator (user si parola).
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
      die("Eroare executare query...");
    }

    //! daca queryul tocmai executat a returnat o inregistare
    //! (inseamna ca s-a gasit combinatia de user + parola introduse de utilizator).
    if($num_row == 1) {
      $_SESSION['login_user'] = $username; //! initializare variabila sesiune.
      header("location: administrare.php"); //! redirectionare utilizator catre pagina de administrare.
    }
    else {
      //! altfel, daca queryul executat nu a returnat nicio inregistrare, inseamna ca [,] combinatia de user + parola
      //! introduse de utilizator nu exista-n db.
      $error = "Nume utilizator sau parola gresita!";
    }
  }
}

?>
