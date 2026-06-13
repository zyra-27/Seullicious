<?php
session_start();

$id = $_POST['id_menu'];

foreach($_SESSION['cart'] as $key => $item){
    if($item['id_menu'] == $id){
        unset($_SESSION['cart'][$key]);
    }
}

header("Location: ../user/cart.php");