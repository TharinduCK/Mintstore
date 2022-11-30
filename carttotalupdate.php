<?php
session_start();

require "connection.php";

$total = "0";
$subtotal = "0";
$shipping = "0";

if (isset($_SESSION["user"])) {

    $total = 0;

    $cartrs = Database::search("SELECT * FROM `cart` WHERE `user_id` = '" . $_SESSION["user"]["email"] . "';");
    $cnum = $cartrs->num_rows;

    $orderID = uniqid();

    for ($x = 0; $x < $cnum; $x++) {

        $totalpr = $cartrs->fetch_assoc();

        $catqty[$x] = (int)$totalpr["qty"];

        // echo $catqty[$x]."///////////";

        $product = Database::search("SELECT * FROM `product` WHERE `id` = '" . $totalpr["product_id"] . "' ");
        $pr = $product->fetch_assoc();

        $price=$pr["price"];

        $dc[$x] = $pr["delivery_fee_colombo"];

        $do[$x] = $pr["delivery_fee_other"];

        $arr[$x] = $catqty[$x] * $price;

      
    }

    $total =  array_sum($arr);

    $umail = $_SESSION["user"]["email"];

    $address_rs = Database::search("SELECT * FROM `user_has_address` WHERE `user_email` = '" . $umail . "' ");
    $cn = $address_rs->num_rows;

    if ($cn == 1) {

        $ar = $address_rs->fetch_assoc();

        // location , district , province
        $rs1 = Database::search("SELECT `location`.`id`, `province_id` , `district_id`, `city_id`, 
        city.id AS `cid`, city.name AS `c_name`, 
        city.postal_code, 
        district.id AS `did`, district.name AS `d_name`, 
        province.id AS `pid`, province.name AS `p_name` 
        FROM `location` JOIN `city` ON city.id=location.city_id
        JOIN `district` ON district.id=location.district_id
        JOIN `province` ON province.id=location.province_id WHERE `location`.id='" . $ar["location_id"] . "';");

        $locr = $rs1->fetch_assoc();

        $delivery = "0";

        if ($locr["district_id"] == "3") {
            $delivery = array_sum($dc);
        } else {
            $delivery = array_sum($do);
        }

        $amount = $total + (int)$delivery;

        echo "Rs.";
        echo $amount;
        echo ".00";
}
}