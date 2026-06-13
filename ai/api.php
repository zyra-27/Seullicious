<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

session_start();
include "../config/koneksi.php";

$message = strtolower(trim($_POST['message'] ?? ''));

if (empty($message)) {
    echo json_encode(['error' => 'Message is required']);
    exit;
}

/* SIMPAN HISTORY */
$_SESSION['lici_history'][] = $message;

/* ========================= */
/* RESPONSE HUMAN STYLE */
/* ========================= */

function randomText($arr){
    return $arr[array_rand($arr)];
}

/* GREETING */
if(preg_match("/halo|hai|hi/",$message)){
    echo json_encode([
        'response' => "Halo juga 😄 kamu lagi pengen makan apa hari ini?",
        'recommendations' => []
    ]);
    exit;
}

/* DETECT INTENT */
$intent = [
    "murah" => preg_match("/murah|hemat/",$message),
    "mahal" => preg_match("/mahal|premium/",$message),
    "minuman" => preg_match("/minum|drink|milk|coffee|tea/",$message),
    "pedas" => preg_match("/pedas|spicy/",$message),
    "manis" => preg_match("/manis|sweet/",$message),
    "lapar" => preg_match("/lapar|makan|rekom/",$message),
];

/* ========================= */
/* RESPON NATURAL */
/* ========================= */

$intro = randomText([
    "Oke aku ngerti 😄",
    "Siap, aku bantu ya 👌",
    "Hmm menarik 🤔",
    "Wah ini cocok sih 🔥"
]);

/* ========================= */
/* QUERY */
/* ========================= */

$where=[];

if($intent["minuman"]){
    $where[]="nama_menu LIKE '%milk%' OR nama_menu LIKE '%tea%' OR nama_menu LIKE '%coffee%'";
}

if($intent["pedas"]){
    $where[]="nama_menu LIKE '%ramen%' OR nama_menu LIKE '%tteok%' OR nama_menu LIKE '%nakji%'";
}

if($intent["manis"]){
    $where[]="nama_menu LIKE '%milk%' OR nama_menu LIKE '%yakult%' OR nama_menu LIKE '%coffee%'";
}

$query="SELECT * FROM menu";

if(count($where)){
    $query.=" WHERE ".implode(" AND ",$where);
}

/* SORT */
if($intent["murah"]){
    $query.=" ORDER BY harga ASC";
}else{
    $query.=" ORDER BY RAND()";
}

$query.=" LIMIT 3";

$res=mysqli_query($koneksi,$query);

$recommendations = [];
if(mysqli_num_rows($res)>0){
    while($d=mysqli_fetch_assoc($res)){
        $recommendations[] = [
            'id' => $d['id_menu'],
            'name' => $d['nama_menu'],
            'price' => (int)$d['harga'],
            'image' => $d['gambar']
        ];
    }
    $response = $intro." aku rekomendasiin ini ya 👇";
    $follow_up = "Kamu mau aku tambahin ke cart langsung atau mau lihat yang lain? 😄";
}else{
    $response = "Hmm aku belum nemu 😅 coba kata lain ya";
    $follow_up = "";
}

echo json_encode([
    'response' => $response,
    'recommendations' => $recommendations,
    'follow_up' => $follow_up
]);
?>