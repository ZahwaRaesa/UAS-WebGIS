<?php

include "db.php";


$data=json_decode(
file_get_contents("php://input"),
true
);


$id=$data['id'];

$type=$data['type'];



if($type=="jalan"){

$sql="
DELETE FROM jalan
WHERE id=$id
";

}


elseif($type=="parsil"){

$sql="
DELETE FROM parsil
WHERE id=$id
";

}



elseif($type=="rusak"){

$sql="
DELETE FROM jalan_rusak
WHERE id=$id
";

}




if(mysqli_query($conn,$sql)){

echo "Data berhasil dihapus";

}else{

echo mysqli_error($conn);

}


?>