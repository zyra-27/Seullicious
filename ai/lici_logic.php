<?php

function liciResponse($message){

$message = strtolower($message);

$conn = mysqli_connect("localhost","root","","seoullicious_pos");

$query = "SELECT * FROM menu WHERE 1=1";

# spicy
if(strpos($message,"spicy") !== false){
$query .= " AND level_pedas='spicy'";
}

# sweet
if(strpos($message,"sweet") !== false){
$query .= " AND rasa='sweet'";
}

# vegetarian
if(strpos($message,"vegetarian") !== false){
$query .= " AND is_vegetarian=1";
}

# drink
if(strpos($message,"drink") !== false){
$query .= " AND kategori='drink'";
}

# light meal
if(strpos($message,"light") !== false){
$query .= " AND tipe='light'";
}

# budget detect
if(preg_match('/([0-9]+)/',$message,$match)){
$budget = $match[1]*1000;
$query .= " AND harga <= $budget";
}

$query .= " ORDER BY RAND() LIMIT 3";

$result = mysqli_query($conn,$query);

$response = "✨ Here are my recommendations:\n\n";

while($row = mysqli_fetch_assoc($result)){
$response .= "🍜 ".$row['nama_menu']." - Rp".$row['harga']."\n";
}

if(mysqli_num_rows($result)==0){
$response = "Hmm... I couldn't find a perfect match. Try asking for spicy, sweet, drinks, vegetarian, or budget!";
}

return $response;

}
?>