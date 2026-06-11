<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>WebGIS Jalan Parsil Kerusakan</title>


<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet-draw/dist/leaflet.draw.css">


<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-draw/dist/leaflet.draw.js"></script>



<style>


*{
box-sizing:border-box;
font-family:Segoe UI,Arial;
}


body{

margin:0;
background:#020617;

}



#container{

display:flex;
height:100vh;

}



#sidebar{

width:360px;
background:#0f172a;
color:white;
padding:20px;
overflow:auto;

}


.logo{

font-size:22px;
font-weight:bold;
margin-bottom:20px;

}



.menu button{

width:100%;
padding:12px;
margin-bottom:8px;
border:none;
border-radius:10px;
background:#1e293b;
color:white;
cursor:pointer;

}


.menu button:hover{

background:#2563eb;

}



.card{

background:#1e293b;
padding:15px;
border-radius:15px;
margin-top:12px;

box-shadow:0 5px 15px #0005;

}


.card b{

color:#38bdf8;

}


#map{

flex:1;

}



#info{

background:#020617;
padding:10px;
color:#94a3b8;
border-radius:10px;
margin-top:10px;

}



</style>


</head>


<body>


<div id="container">


<div id="sidebar">


<div class="logo">
🗺 WebGIS
</div>


<div class="menu">

<button onclick="filterData('all')">
Semua Data
</button>

<button onclick="filterData('jalan')">
🛣 Jalan
</button>

<button onclick="filterData('parsil')">
🏠 Parsil
</button>

<button onclick="filterData('rusak')">
⚠ Kerusakan
</button>


</div>


<div id="info">
Memuat data...
</div>


<div id="list"></div>


</div>


<div id="map"></div>



</div>






<script>


const map=L.map('map')
.setView([-0.0263,109.3425],14);


L.tileLayer(
'https://tile.openstreetmap.org/{z}/{x}/{y}.png'
).addTo(map);



let group=L.featureGroup().addTo(map);

let dataGlobal=[];

let tempLayer=null;
let tempType=null;



// LOAD DATA

fetch("load.php")
.then(r=>r.json())
.then(data=>{


dataGlobal=data;


document.getElementById("info").innerHTML=
"Total data : "+data.length;


tampil(data);


})
.catch(e=>{

document.getElementById("info").innerHTML=
"Load error "+e;

});




// TAMPIL PETA

function tampil(data){


group.clearLayers();


let html="";


data.forEach((d,i)=>{


if(!d.geom)return;


let geo=JSON.parse(d.geom);


let layer=L.geoJSON(geo);



let popup="";



// JALAN

if(d.type=="jalan"){


layer.setStyle({

color:"#22c55e",
weight:6

});


popup=`

<h3>🛣 Jalan</h3>

<b>Nama Jalan:</b> ${d.nama_jalan}<br>

<b>Status:</b> ${d.status || '-'}<br>

<b>Panjang:</b> ${d.panjang || '-'} meter

<br><br>

<button onclick="editData(${d.id},'jalan')">
Edit
</button>

<button onclick="hapusData(${d.id},'jalan')">
Hapus
</button>

`;

}



// PARSIL

if(d.type=="parsil"){


layer.setStyle({

color:"#6366f1",

fillOpacity:.4

});


popup=`

<h3>🏠 Parsil</h3>

<b>Area:</b> ${d.nama_area}<br>

<b>Pemilik:</b> ${d.pemilik || '-'}<br>

<b>Status:</b> ${d.status || '-'}<br>

<b>Luas:</b> ${d.luas || 0} m²<br>

<b>Keterangan:</b> ${d.keterangan || '-'}

`;

}



// RUSAK

if(d.type=="rusak"){


popup=`

<h3>⚠ Kerusakan</h3>

<b>Lokasi:</b> ${d.nama_titik}<br>

<b>Keterangan:</b> ${d.keterangan}

`;

}



layer.bindPopup(popup);



layer.addTo(group);



html+=`

<div class="card"
onclick="zoomData(${i})">

<b>${d.type}</b>

<br>

${d.nama_jalan ||
d.nama_area ||
d.nama_titik}


</div>

`;


});



document.getElementById("list")
.innerHTML=html;



if(group.getLayers().length)
map.fitBounds(group.getBounds());


}





function zoomData(i){

let geo=JSON.parse(dataGlobal[i].geom);

map.fitBounds(
L.geoJSON(geo).getBounds()
);

}





function filterData(type){


if(type=="all"){

tampil(dataGlobal);

return;

}


tampil(
dataGlobal.filter(x=>x.type==type)
);


}






// ================= DRAW EDIT =================


const draw=new L.Control.Draw({

edit:{

featureGroup:group,

edit:true,

remove:false

},


draw:{

marker:true,

polyline:true,

polygon:true,

circle:false,

rectangle:false

}

});


map.addControl(draw);



// saat geometry digeser / diedit

map.on(
'draw:edited',
function(e){


e.layers.eachLayer(function(layer){


let geo=layer.toGeoJSON();



fetch("update.php",{

method:"POST",

headers:{
"Content-Type":"application/json"
},

body:JSON.stringify({

id:layer._id,

type:layer._type,

geom:geo

})


});


});


alert("Lokasi berhasil diperbarui");


});

// FORM INPUT

function openForm(){


let html="";



if(tempType=="rusak"){


html=`

<h3>Kerusakan Jalan</h3>

<input id="nama"
placeholder="Nama lokasi">

<br>

<textarea id="ket"
placeholder="Keterangan"></textarea>

`;

}



if(tempType=="jalan"){


html=`

<h3>Data Jalan</h3>


<input id="nama"
placeholder="Nama jalan">


<select id="status">

<option>Nasional</option>

<option>Provinsi</option>

<option>Kabupaten</option>

</select>

`;

}




if(tempType=="parsil"){


html=`

<h3>Data Parsil</h3>


<input id="nama"
placeholder="Nama area">


<input id="pemilik"
placeholder="Nama pemilik">


<select id="status">

<option>SHM</option>
<option>HGB</option>
<option>HGU</option>

</select>


<input id="luas"
placeholder="Luas tanah">


<textarea id="ket"
placeholder="Keterangan">

</textarea>


`;

}



let modal=document.createElement("div");


modal.id="inputBox";


modal.style.position="fixed";

modal.style.top="50%";

modal.style.left="50%";

modal.style.transform="translate(-50%,-50%)";

modal.style.background="white";

modal.style.padding="20px";

modal.style.zIndex="9999";

modal.innerHTML=
html+
`

<br>

<button onclick="simpanData()">
Simpan
</button>

<button onclick="batal()">
Batal
</button>

`;



document.body.appendChild(modal);


}




function batal(){

document.getElementById("inputBox").remove();

tempLayer.remove();

}




function simpanData(){


let geo=tempLayer.toGeoJSON();


let data={

type:tempType,

geom:geo

};



if(tempType=="rusak"){

data.nama_titik=
document.getElementById("nama").value;


data.keterangan=
document.getElementById("ket").value;

}



if(tempType=="jalan"){

data.nama=
document.getElementById("nama").value;


data.status=
document.getElementById("status").value;

}




if(tempType=="parsil"){


data.nama_area=
document.getElementById("nama").value;


data.pemilik=
document.getElementById("pemilik").value;


data.status=
document.getElementById("status").value;


data.luas=
document.getElementById("luas").value;


data.keterangan=
document.getElementById("ket").value;


}



fetch("save.php",{

method:"POST",

headers:{

"Content-Type":"application/json"

},

body:JSON.stringify(data)


})


.then(r=>r.text())

.then(r=>{


alert(r);


location.reload();


});


}
function hapusData(id,type){


if(!confirm("Yakin hapus data ini?"))
return;



fetch("delete.php",{

method:"POST",

headers:{
"Content-Type":"application/json"
},

body:JSON.stringify({

id:id,
type:type

})

})


.then(r=>r.text())

.then(r=>{

alert(r);

location.reload();

});


}

</script>



</body>
</html>