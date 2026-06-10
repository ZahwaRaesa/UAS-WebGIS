<?php

include "db.php";

$data=json_decode(file_get_contents("php://input"),true);


$id=$data['id'];

$geom=$data['geom']['geometry'];


$type=$data['type'];



if($type=="rusak"){

$x=$geom['coordinates'][0];
$y=$geom['coordinates'][1];


$sql="
UPDATE jalan_rusak
SET geom =
ST_GeomFromText('POINT($x $y)')
WHERE id=$id
";


}


if($type=="jalan"){


$list=[];

foreach($geom['coordinates'] as $c){

$list[]=$c[0]." ".$c[1];

}


$line="LINESTRING(".implode(",",$list).")";


$sql="
UPDATE jalan
SET geom =
ST_GeomFromText('$line')
WHERE id=$id
";


}



if($type=="parsil"){


$list=[];

foreach($geom['coordinates'][0] as $c){

$list[]=$c[0]." ".$c[1];

}


$poly="POLYGON((".implode(",",$list)."))";


$sql="
UPDATE parsil
SET geom =
ST_GeomFromText('$poly')
WHERE id=$id
";


}



if(mysqli_query($conn,$sql)){

echo "OK";

}else{

echo mysqli_error($conn);

}

?>