<?php
session_start();

$id = $_POST['id_menu'];
$action = $_POST['action'];

foreach($_SESSION['cart'] as &$item){
    if($item['id_menu'] == $id){

        if($action == "plus"){
            $item['qty']++;
        }

        if($action == "minus"){
            $item['qty']--;
            if($item['qty'] <= 0){
                $item['qty'] = 1;
            }
        }
    }
}

header("Location: ../user/cart.php");