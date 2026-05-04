<?php
session_start();
echo json_encode(array_values($_SESSION['cart'] ?? []));