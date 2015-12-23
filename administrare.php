<?php

session_start(); // Starting Session
include_once("config.php");

echo '$_SESSION: ', var_dump($_SESSION);
echo "<br>\$_GET: ", var_dump($_GET);

?>

<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Administrare magazin</title>
		<link href="style/style.css" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="style/tables.css">
	</head>
	<body>
	<h1 align="center">Pagina administrator!</h1>

  <?php

	if(isset($_SESSION['login_user'])) {

        /*!
         *  Stergere categorie + produse din categorie.
         */
        echo '<table class="pure-table"><thead><tr>
              <th>#</th>
              <th>Categorie</th>
              <th>Optiune</th>
              </tr></thead><tbody>';

        $mysql_fetch_categorii = $mysqli->prepare("SELECT category_name FROM categories WHERE 1");
        $mysql_fetch_categorii->execute();
        $mysql_fetch_categorii->bind_result($nume_categorie);

        $categorii = array();

        while ($mysql_fetch_categorii->fetch()) {
            array_push($categorii, $nume_categorie);
        }

        sort($categorii);

        $b = 0;
        foreach($categorii as $nume_categorie) {
            $str_lower = strtolower($nume_categorie);
            $str_lower = substr_replace($str_lower, strtoupper(substr($str_lower, 0, 1)), 0, 1);

            echo '<tr class="pure-table-odd">';
            echo '<td>' . ++$b . '</td>';
            echo '<td>' . $nume_categorie . '</td>';
            echo '<td> <a href ="?sterge_categorie=' . $nume_categorie . '"> sterge </a></td>';
            echo '</tr>';
        }

        echo '</tbody></table>';

        $current_url = urlencode($url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);

        echo '</div>';

        // verificare daca s-a selectat vreo categorie de sters.
        if(isset($_GET['sterge_categorie'])) {
            if(!is_null($_GET['sterge_categorie'])) {
                $categorie = $_GET['sterge_categorie'];

                $mysql_fetch_categorii = $mysqli->prepare("DELETE FROM categories WHERE category_name = ?");
                $mysql_fetch_categorii->bind_param('s', $categorie);
                $mysql_fetch_categorii->execute();

                // nu merge verificarea...
                if($mysqli->query("DELETE FROM categories WHERE category_name = $categorie")) {
                    echo 'Sters cu succes!';
                }
                else {
                    echo 'Esuat sa sterg...';
                }
            }
        }

        /*!
         *  Adaugare categorie noua.
         */

        echo '<br><br><br>';
        echo '<table class="pure-table"><thead><tr>
               <th>Adauga categorie noua...</th></tr></thead>
            <tbody>
                <tr>
                    <td>
                        <form action="administrare.php" method="post">
                        Nume categorie: <input type="text" size="6" maxlength="10" name="nume_categorie" value="">

                        <button type="submit" class="pure-button pure-button-primary">Adauga categorie!</button>
                        </form>
                    </td>
                </tr>
            </tbody>
            </table>';

        if(isset($_POST['nume_categorie']) && !empty($_POST['nume_categorie'])) {
            echo 'haha';
        }
    }
    else {
        echo 'Iesi';
    }
