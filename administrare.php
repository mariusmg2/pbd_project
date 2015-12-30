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

    //! Daca avem un user logat...
	if(isset($_SESSION['login_user'])) {

        /*!
         *  Stergere categorie + produse din categorie.
         */

        echo '<table class="pure-table"><thead><tr>
              <th>#</th>
              <th>Categorie</th>
              <th>Optiune</th>
              </tr></thead><tbody>';

        //! Extragere toate categoriile disponibile din db.
        $query = $mysqli->prepare("SELECT category_name FROM categories WHERE 1");
        $query->execute();

        //! Rezultatul (categoriile) va fi disponibil accesand variabila `$nume_categorie`.
        $query->bind_result($nume_categorie);

        $categorii = array();

        //! Salvam numele categoriilor intr-un array.
        while ($query->fetch()) {
            array_push($categorii, $nume_categorie);
        }

        //! Sortam categoriile (alfabetic, crescator).
        sort($categorii);

        $b = 0;

        //! Parcurgere array, si afisarea elementelor intr-un table (html).
        foreach($categorii as $nume_categorie) {
            //! Conversii pentru a avea categorii cu prima litera mare.
            $str_lower = strtolower($nume_categorie);
            $str_lower = substr_replace($str_lower, strtoupper(substr($str_lower, 0, 1)), 0, 1);

            echo '<tr class="pure-table-odd">';
            echo '<td>' . ++$b . '</td>';
            echo '<td>' . $nume_categorie . '</td>';

            //! Generare hyperlink pentru a sterge o anumita categorie.
            echo '<td> <a href ="?sterge_categorie=' . $nume_categorie . '"> [sterge] </a></td>';
            echo '</tr>';
        }

        echo '</tbody></table>';

        $current_url = urlencode($url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);

        echo '</div>';

        // Verificare daca s-a selectat vreo categorie de sters.
        if(isset($_GET['sterge_categorie'])) {

            //! Daca categoria selectata este nenula:
            if(!is_null($_GET['sterge_categorie'])) {
                $categorie = $_GET['sterge_categorie'];

                //! Generare query sql pentru stergerea categoriei selectate.
                $queryCategorie = $mysqli->prepare("DELETE FROM categories WHERE category_name = ?");
                $queryCategorie->bind_param('s', $categorie);
                $queryCategorie->execute();

                //! Generare query sql pentru stergerea tuturor produselor din categoria tocmai aleasa si stearsa.
                $queryProduse = $mysqli->prepare("DELETE FROM products WHERE product_category = ?");
                $queryProduse->bind_param('s', $categorie);
                $queryProduse->execute();

                //! Redirectionare catre pagina administrare.php (refrash).
                //sleep(2);
                header("location: administrare.php");
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

        //! Daca s-a selectat optiunea de adaugare a unei noi categorii, iar categoria selectata este nenula.
        if(isset($_POST['nume_categorie']) && !empty($_POST['nume_categorie'])) {

            //! Creare query sql pentru a adauga o noua inregistrare in tabela `categories`.
            $query = $mysqli->prepare("INSERT INTO categories (category_name) VALUES (?)");
            $query->bind_param('s', $_POST['nume_categorie']);
            $query->execute(); //! Executare query sql.

            //! Refrash pagina.
            header("location: administrare.php");
        }

        /*!
         *  Stergere produse dintr-o categorie...
         */

         echo '<br><br>';

         //! Preluare categorii din baza de date.
         $query = $mysqli->prepare("SELECT category_name FROM categories WHERE 1");
         $query->execute();
         $query->bind_result($nume_categorie);

         $categorii = array();

         //! Salvarea lor in arrayul `$categorii`.
         while ($query->fetch()) {
             array_push($categorii, $nume_categorie);
         }

         //! Afisare cod html pentru o tabela.
         echo '<table class="pure-table"><thead><tr>
                <th>Selecteaza categoria de produse</th></tr></thead>
             <tbody>
                 <tr>
                     <td>
                         <form action="administrare.php" method="post">';
                         if(!empty($categorii)) {
                             echo '<select name="categorie">';

                                        //! Populare combobox cu toate categoriile disponibile.
                                        foreach($categorii as $categorie) {
                                            echo '<option value='.$categorie.'>'.$categorie.'</option>';
                                        }
                            /*!
                             * Salvarea (in cazul in care utilizatorul da click pe buton) categoriei
                             * tocmai selectate in arrayul special $_POST.
                             */
                            echo '</select>
                            <input type="submit" name="afiseaza_categorie" value="Selecteaza"/>
                            </form>';
                          }
                    echo '</td>
                 </tr>
             </tbody>
             </table>';

            //! Daca utilizatorul a selectat o categorie de produse:
            if(isset($_POST['categorie'])) {

                //! Query pentru selectarea tuturor produselor din categoria tocmai selectata:
                $query = $mysqli->prepare("SELECT product_code, product_name FROM products WHERE product_category = ?");
                $query->bind_param('s', $_POST['categorie']);
                $query->execute(); //! Executare query.

                //! Rezultatele queryului (codul produsului si numele acestuia) vor fi dispobibile in variabilele
                //! pasate functiei bind_result().
                $query->bind_result($codProdus, $numeProdus);

                $produse = array();

                /*!
                 * Cat timp mai exista produse ce indeplinesc conditia in db, aceste sunt
                 * introduse intr-un array asociativ, unde indexul unui produs reprezinta defapt
                 * codul acestuia.
                 */
                while ($query->fetch()) {
                    $produse[$codProdus] = $numeProdus;
                }

                //! Daca exista cel putin un produs, va fi afisat frumos intr-un tabel.
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

            /*!
             *  Adaugare produse in baza de date a magazinului.
             */

            echo '<br><br><table class="pure-table"><thead><tr>
                   <th>Nume produs</th>
                   <th> Categorie</th>
                   <th>Descriere</th>
                   <th>Pret</th>
                   <th>Actiune</th>
                   </tr></thead><tbody>';
            echo '<form action="administrare.php" method="post"> <tr class="pure-table-odd">';
            echo '<td> <input type="text" size="30" maxlength="20" name="nume_produs_adaugare" value=""> </td>';

            echo '<td>';
            echo '<select name="categorie_adaugare">';
            foreach($categorii as $categorie) {
                echo '<option value='.$categorie.'>'.$categorie.'</option>';
            }
            echo '</select>';

            echo '<td> <textarea name="descriere_adaugare" rows="4" cols="65"></textarea> </td>';
            echo '<td> <input type="number" name="pret_adaugare" min="1" max="9999"> </td>';
            echo '<td> <button type="submit" class="pure-button pure-button-primary">Adauga!</button> </td> </form>';
            echo '</tr>';
            echo '</tbody></table><br><br><br>';

            if(isset($_POST['descriere_adaugare']) && isset($_POST['pret_adaugare']) &&
                isset($_POST['nume_produs_adaugare']) && isset($_POST['categorie_adaugare'])) {
                if(empty($_POST['descriere_adaugare']) || empty($_POST['pret_adaugare']) || empty($_POST['nume_produs_adaugare'])) {
                    echo '<div class="isa_error">Adaugare produs: toate campurile trebuie sa contina valori valide!</div>';
                }
                else {
                    $categorie = strtolower($_POST['categorie_adaugare']);
                    $nume = ucfirst($_POST['nume_produs_adaugare']);
                    $descriere = ucfirst($_POST['descriere_adaugare']);
                    $img = 'unknown.jpg';
                    $pret = (float)$_POST['pret_adaugare'];
                    $cod = strtoupper(substr($categorie, 0, 3)).substr(hash('sha256', substr(hash('sha512', $nume.$categorie.$descriere), 10, 32)), 3, 10);

                    $query = $mysqli->prepare("INSERT INTO products (product_code, product_category, product_name,
                                               product_desc, product_img_name, price) VALUES (?, ?, ?, ?, ?, ?)");
                    $query->bind_param('sssssd', $cod, $categorie, $nume, $descriere, $img, $pret);

                    if($query->execute()) {
                        echo '<div class="isa_success">Adaugare produs: adaugat cu succes!</div>';
                    }
                    else {
                        echo '<div class="isa_warning">Adaugare produs: eroare adaugare produs!</div>';
                    }
                }
            }
    }
    else {
        /*!
         * Daca utilizatorul a ajuns pe pagina `administrare.php` in mod direct
         * (fara sa fi trecut si autentificat prin `admin.php`), acesta va fi redirectionat
         * catre pagina principala.
         */
        header("location: index.php");
    }
