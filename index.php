<?php

session_start();
include_once("config.php");

//current URL of the Page. cart_update.php redirects back to this URL
$current_url = urlencode($url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);

?>

<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Magazin Virtual</title>
		<link href="style/style.css" rel="stylesheet" type="text/css">
	</head>
	<body>

	<span class="link" id="link"><a href="admin.php"> pagina administrator </a></span>
	<h1 align="center">Produse...</h1>

<!-- View Cart Box Start -->
<?php
if (isset($_SESSION["cart_products"]) && count($_SESSION["cart_products"]) > 0) {
    echo '<div class="cart-view-table-front" id="view-cart">';
    echo '<h3>Cosul tau de cumparaturi...</h3>';
    echo '<form method="post" action="cart_update.php">';
    echo '<table width="100%"  cellpadding="6" cellspacing="0">';
    echo '<tbody>';

    $total = 0;
    $b     = 0;
    foreach ($_SESSION["cart_products"] as $cart_itm) {
        $product_name  = $cart_itm["product_name"];
        $product_qty   = $cart_itm["product_qty"];
        $product_price = $cart_itm["product_price"];
        $product_code  = $cart_itm["product_code"];
        $product_color = $cart_itm["product_color"];
        $bg_color      = ($b++ % 2 == 1) ? 'odd' : 'even'; //zebra stripe
        echo '<tr class="' . $bg_color . '">';
        echo '<td>Nr. prod. <input type="text" size="2" maxlength="2" name="product_qty[' . $product_code . ']" value="' . $product_qty . '" /></td>';
        echo '<td>' . $product_name . '</td>';
        echo '<td><input type="checkbox" name="remove_code[]" value="' . $product_code . '" /> Sterge</td>';
        echo '</tr>';
        $subtotal = ($product_price * $product_qty);
        $total    = ($total + $subtotal);
    }
    echo '<td colspan="4">';
    echo '<button type="submit">Update</button><a href="view_cart.php" class="button">Plateste</a>';
    echo '</td>';
    echo '</tbody>';
    echo '</table>';

    $current_url = urlencode($url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);

    echo '<input type="hidden" name="return_url" value="' . $current_url . '" />';
    echo '</form>';
    echo '</div>';
}

?>
<!-- View Cart Box End -->

<!-- Selectare categorie.. -->

<?php

echo '<div class="categories-front" id="cat">';
echo '<h3>Categorii de produse...</h3>';
echo '<table width="100%"  cellpadding="6" cellspacing="0">';
echo '<tbody>';

$mysql_fetch_categorii = $mysqli->prepare("SELECT category_name FROM categories WHERE 1");
$mysql_fetch_categorii->execute();
$mysql_fetch_categorii->bind_result($nume_categorie);

$b = 0;
while ($mysql_fetch_categorii->fetch()) {
    $bg_color = ($b++ % 2 == 1) ? 'odd' : 'even'; //zebra stripe
    echo '<tr class="' . $bg_color . '">';
    $str_lower = strtolower($nume_categorie);
    $str_lower = substr_replace($str_lower, strtoupper(substr($str_lower, 0, 1)), 0, 1);
    echo '<td><a href ="?categorie=' . $nume_categorie . '">' . $str_lower . '</a></td>';
    echo '</tr>';
}

echo '</tbody>';
echo '</table>';

$current_url = urlencode($url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);

echo '<input type="hidden" name="return_url" value="' . $current_url . '" />';
//echo '</form>';
echo '</div>';

?>

<!-- Selectare categorie.. -->

<!-- Products List Start -->
<?php

// implicit, se vor afisa toate datele din db.
if (!isset($_GET['categorie'])) {
    $_GET['categorie'] = "all";
}

$results = $mysqli->query("SELECT product_code, product_category, product_name, product_desc, product_img_name, price FROM products ORDER BY id ASC");

if ($results) {
    if (isset($_GET['categorie']) and !empty($_GET['categorie'])) {
        $selected_category = $_GET['categorie'];
        $products_item     = '<ul class="products">';
        //fetch results set as object and output HTML
        while ($obj = $results->fetch_object()) {
            if ($selected_category == $obj->product_category || $selected_category == "all") {
                $products_item .= <<<EOT
	<li class="product">
	<form method="post" action="cart_update.php">
	<div class="product-content"><h3>{$obj->product_name}</h3>
	<div class="product-thumb"><img src="images/{$obj->product_img_name}"></div>
	<div class="product-desc">{$obj->product_desc}</div>
	<div class="product-info">
	Pret {$currency}{$obj->price}<br>
	Categorie: {$obj->product_category}

	<fieldset>

	<label>
		<span>Culoare</span>
		<select name="product_color">
		<option value="Black">Negru</option>
		<option value="Silver">Argintiu</option>
		<option value="White">Alb</option>
		</select>
	</label>

	<label>
		<span>Cantitate</span>
		<input type="text" size="2" maxlength="2" name="product_qty" value="1" />
	</label>

	</fieldset>
	<input type="hidden" name="product_code" value="{$obj->product_code}" />
	<input type="hidden" name="type" value="add" />
	<input type="hidden" name="return_url" value="{$current_url}" />
	<div align="center"><button type="submit" class="add_to_cart">Adauga</button></div>
	</div></div>
	</form>
	</li>
EOT;
            }
        }
        $products_item .= '</ul>';
        echo $products_item;
    } else {
        echo "<center><b>Meh, se pare ca n-avem asa ceva!<br>Pls, pleaca.</center></b>";
    }
}
?>
<!-- Products List End -->
</body>
</html>
