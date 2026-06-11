<?php

include "db.php";

header("Content-Type: application/json; charset=UTF-8");

$data = [];


// cek koneksi
if(!$conn){
    echo json_encode([
        "error"=>"Database gagal konek"
    ]);
    exit;
}



// ================= JALAN =================

$q = mysqli_query($conn,"
SELECT 
id,
nama_jalan,
status,
ST_AsGeoJSON(geom) AS geom
FROM jalan
");


if($q){

while($row=mysqli_fetch_assoc($q)){

$data[]=[

"id"=>(int)$row['id'],

"type"=>"jalan",

"nama_jalan"=>$row['nama_jalan'],

"status"=>"Kabupaten",

"panjang"=>0,

"geom"=>$row['geom']

];

}

}





// ================= PARSIL =================

$q = mysqli_query($conn,"
SELECT 
id,
nama_area,
ST_AsGeoJSON(geom) AS geom
FROM parsil
");


if($q){

while($row=mysqli_fetch_assoc($q)){


$data[]=[

"id"=>(int)$row['id'],

"type"=>"parsil",

"pemilik"=>$row['nama_area'],

"status"=>"SHM",

"luas"=>0,

"geom"=>$row['geom']

];


}

}





// ================= KERUSAKAN =================

$q = mysqli_query($conn,"
SELECT
id,
nama_titik,
keterangan,
ST_AsGeoJSON(geom) AS geom
FROM jalan_rusak
");


if($q){

while($row=mysqli_fetch_assoc($q)){


$data[]=[

"id"=>(int)$row['id'],

"type"=>"rusak",

"nama_titik"=>$row['nama_titik'],

"keterangan"=>$row['keterangan'],

"geom"=>$row['geom']

];


}

}



echo json_encode($data);

?>