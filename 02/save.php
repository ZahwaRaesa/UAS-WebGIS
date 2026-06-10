<?php

include "db.php";

header("Content-Type: text/plain");


$data=json_decode(file_get_contents("php://input"),true);



$type=$data['type'];

$geom=json_encode($data['geom']);

$coords=$data['geom']['geometry']['coordinates'];




// ================= JALAN =================

if($type=="jalan"){


$nama=mysqli_real_escape_string(
$conn,
$data['nama']
);


$status=mysqli_real_escape_string(
$conn,
$data['status']
);



$line="LINESTRING(";


foreach($coords as $c){

$line .= $c[0]." ".$c[1].",";

}


$line=rtrim($line,",").")";



$sql="
INSERT INTO jalan
(
nama_jalan,
geom
)
VALUES
(
'$nama',
ST_GeomFromText('$line')
)
";


}



// ================= PARSIL =================
elseif($type=="parsil"){


$nama=mysqli_real_escape_string(
$conn,
$data['nama_area']
);


$pemilik=mysqli_real_escape_string(
$conn,
$data['pemilik']
);


$status=mysqli_real_escape_string(
$conn,
$data['status']
);



$poly="POLYGON((";


foreach($coords[0] as $c){

$poly .= $c[0]." ".$c[1].",";

}


$poly=rtrim($poly,",")."))";



$sql="
INSERT INTO parsil
(
nama_area,
pemilik,
status,
geom
)
VALUES
(
'$nama',
'$pemilik',
'$status',
ST_GeomFromText('$poly')
)
";

}
// ================= RUSAK =================


elseif($type=="rusak"){



$nama=mysqli_real_escape_string(
$conn,
$data['nama_titik']
);


$ket=mysqli_real_escape_string(
$conn,
$data['keterangan']
);



$p=$coords;



$point =
"POINT(".$p[0]." ".$p[1].")";



$sql="
INSERT INTO jalan_rusak
(
nama_titik,
keterangan,
geom
)
VALUES
(
'$nama',
'$ket',
ST_GeomFromText('$point')
)
";



}





if(mysqli_query($conn,$sql)){


echo "Data berhasil disimpan";


}else{


echo "ERROR : ".mysqli_error($conn);


}



?>