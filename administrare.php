<?php

session_start(); // Starting Session
include_once("config.php");

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

    <span class="link" id="link"><a href="logout.php"> logout </a></span>
    <span class="home" id="home"><a href="index.php"> home </a></span>
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

        $query = $mysqli->prepare("SELECT category_name FROM categories WHERE 1");
        $query->execute();
        $query->bind_result($nume_categorie);

        $categorii = array();

        while ($query->fetch()) {
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
            echo '<td> <a href ="?sterge_categorie=' . $nume_categorie . '"> [sterge] </a></td>';
            echo '</tr>';
        }

        echo '</tbody></table>';

        $current_url = urlencode($url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);

        echo '</div>';

        // verificare daca s-a selectat vreo categorie de sters.
        if(isset($_GET['sterge_categorie'])) {
            if(!is_null($_GET['sterge_categorie'])) {
                $categorie = $_GET['sterge_categorie'];

                $query = $mysqli->prepare("DELETE FROM categories WHERE category_name = ?");
                $query->bind_param('s', $categorie);
                $query->execute();

                header("location: administrare.php");

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

        echo '<br><br>';
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
            $query = $mysqli->prepare("INSERT INTO categories (category_name) VALUES (?)");
            $query->bind_param('s', $_POST['nume_categorie']);
            $query->execute();

            header("location: administrare.php");
        }

        /*!
         *  Stergere produse dintr-o categorie...
         */

         echo '<br><br>';

         $query = $mysqli->prepare("SELECT category_name FROM categories WHERE 1");
         $query->execute();
         $query->bind_result($nume_categorie);

         $categorii = array();

         while ($query->fetch()) {
             array_push($categorii, $nume_categorie);
         }

         echo '<table class="pure-table"><thead><tr>
                <th>Selecteaza categoria de produse</th></tr></thead>
             <tbody>
                 <tr>
                     <td>
                         <form action="administrare.php" method="post">';
                         if(!empty($categorii)) {
                             echo '<select name="categorie">';
                                        foreach($categorii as $categorie) {
                                            echo '<option value='.$categorie.'>'.$categorie.'</option>';
                                        }
                             echo '</select>
                             <input type="submit" name="afiseaza_categorie" value="Selecteaza"/>
                             </form>';
                          }
                    echo '</td>
                 </tr>
             </tbody>
             </table>';

            if(isset($_POST['categorie'])) {
                $query = $mysqli->prepare("SELECT product_code, product_name FROM products WHERE product_category = ?");
                $query->bind_param('s', $_POST['categorie']);
                $query->execute();
                $query->bind_result($codProdus, $numeProdus);

                $produse = array();

                while ($query->fetch()) {
                    $produse[$codProdus] = $numeProdus;
                }

                if(!empty($produse)) {
                    echo '<br><br><table class="pure-table"><thead><tr>
                          <th>#</th>
                          <th>Cod produs</th>
                          <th>Nume produs</th>
                          <th>Optiune</th>
                          </tr></thead><tbody>';

                    $b = 0;

                    foreach($produse as $cod => $produs) {
                        echo '<tr class="pure-table-odd">';
                        echo '<td>' . ++$b . '</td>';
                        echo '<td>' . $cod . '</td>';
                        echo '<td>' . $produs . '</td>';
                        echo '<td> <a href ="?sterge_produs=' . $cod . '"> [sterge] </a></td>';
                        echo '</tr>';
                    }

                    echo '</tbody></table>';
                }
            }

            //! verificare daca s-a selectat vreun produs pentru stergere.
            if(isset($_GET['sterge_produs']) && !empty($_GET['sterge_produs'])) {
                $query = $mysqli->prepare("DELETE FROM products WHERE product_code = ?");
                $query->bind_param('s', $_GET['sterge_produs']);
                $query->execute();
            }
    }
    else {
        echo 'Iesi';
    }
