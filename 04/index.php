<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Peta SPBU</title>

<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    font-family:'Segoe UI',sans-serif;
}

#map{
    height:100vh;
}

/* ========================= */
/* POPUP UMUM */
/* ========================= */

.leaflet-popup-content-wrapper{
    border-radius:20px;
    overflow:hidden;
    padding:0;
}

.leaflet-popup-content{
    margin:0;
    width:290px !important;
}

.leaflet-popup-tip{
    background:white;
}

/* ========================= */
/* POPUP INFO SPBU */
/* ========================= */

.spbu-card{
    padding:14px;
    background:#fff;
}

.spbu-header{
    background:linear-gradient(135deg,#ff9800,#ff5b00);
    color:white;
    padding:16px;
    border-radius:16px;
    text-align:center;
    font-size:20px;
    font-weight:800;
    margin-bottom:12px;
}

.spbu-info{
    background:#f4f4f4;
    border-radius:14px;
    padding:14px;
    margin-bottom:10px;
    font-size:16px;
    display:flex;
    gap:8px;
    align-items:center;
}

.status-open{
    color:#18a84c;
    font-weight:bold;
}

.status-close{
    color:#ff2020;
    font-weight:bold;
}

.spbu-actions{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:10px;
}

.btn{
    border:none;
    padding:12px;
    border-radius:14px;
    font-size:15px;
    font-weight:700;
    color:white;
    cursor:pointer;
}

.btn-edit{
    background:#2995f0;
}

.btn-delete{
    background:#ff4336;
}

/* ========================= */
/* FORM */
/* ========================= */

.form-box{
    padding:16px;
    background:white;
}

.form-title{
    text-align:center;
    font-size:19px;
    font-weight:800;
    color:#ff7300;
    margin-bottom:14px;
}

.form-group{
    margin-bottom:12px;
}

.form-group label{
    display:block;
    margin-bottom:6px;
    font-size:14px;
    font-weight:700;
    color:#444;
}

.form-control{
    width:100%;
    padding:10px 12px;
    border:1px solid #ddd;
    border-radius:12px;
    font-size:14px;
    outline:none;
}

.form-control:focus{
    border-color:#ff9800;
}

.btn-save{
    width:100%;
    margin-top:4px;
    border:none;
    background:linear-gradient(135deg,#ff9800,#ff5b00);
    color:white;
    padding:12px;
    border-radius:14px;
    font-size:15px;
    font-weight:800;
    cursor:pointer;
}

/* layer control */
.leaflet-control-layers{
    border-radius:14px !important;
    overflow:hidden;
}
</style>
</head>

<body>
<div id="map"></div>

<script>
const map = L.map('map').setView([-0.02,109.34],13);

L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png',{
    maxZoom:19
}).addTo(map);

/* layer */
let buka24 = L.layerGroup().addTo(map);
let tidak24 = L.layerGroup().addTo(map);

/* marker icon */
function markerIcon(color){
    return L.icon({
        iconUrl:`https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-${color}.png`,
        shadowUrl:'https://unpkg.com/leaflet@1.7.1/dist/images/marker-shadow.png',
        iconSize:[25,41],
        iconAnchor:[12,41]
    });
}

/* ========================= */
/* LOAD DATA */
/* ========================= */

fetch('ambil_data.php')
.then(r=>r.json())
.then(data=>{

data.forEach(d=>{

let warna = d.status === 'yes' ? 'green' : 'red';

let marker = L.marker(
    [d.latitude,d.longitude],
    {
        icon:markerIcon(warna),
        draggable:true
    }
);

marker.bindPopup(`
<div class="spbu-card">

<div class="spbu-header">
⛽ ${d.nama_spbu}
</div>

<div class="spbu-info">
📞 WA : ${d.no_wa}
</div>

<div class="spbu-info">
🕒 Status :
<span class="${d.status=='yes' ? 'status-open':'status-close'}">
${d.status=='yes' ? 'Buka 24 Jam':'Tidak 24 Jam'}
</span>
</div>

<div class="spbu-actions">

<button class="btn btn-edit"
onclick="editData(${d.id},'${d.nama_spbu}','${d.no_wa}','${d.status}',${d.latitude},${d.longitude})">
✏️ Edit
</button>

<button class="btn btn-delete"
onclick="hapusData(${d.id})">
🗑 Hapus
</button>

</div>

</div>
`);

marker.on('dragend',function(e){

let pos = e.target.getLatLng();

fetch('update.php',{
method:'POST',
body:new URLSearchParams({
id:d.id,
nama:d.nama_spbu,
wa:d.no_wa,
status:d.status,
lat:pos.lat,
lng:pos.lng
})
})
.then(r=>r.text())
.then(res=>{
if(res=="success"){
alert("Lokasi berhasil diupdate");
location.reload();
}
});

});

if(d.status=="yes"){
marker.addTo(buka24);
}else{
marker.addTo(tidak24);
}

});

});

/* layer control */
L.control.layers(null,{
"🟢 SPBU Buka 24 Jam": buka24,
"🔴 SPBU Tidak Buka 24 Jam": tidak24
},{collapsed:false}).addTo(map);

/* ========================= */
/* TAMBAH DATA */
/* ========================= */

map.on('click',function(e){

let lat = e.latlng.lat;
let lng = e.latlng.lng;

L.popup({maxWidth:310})
.setLatLng(e.latlng)
.setContent(`
<div class="form-box">

<div class="form-title">Tambah SPBU</div>

<form id="formTambah">

<div class="form-group">
<label>Nama SPBU</label>
<input class="form-control" type="text" name="nama" required>
</div>

<div class="form-group">
<label>No WhatsApp</label>
<input class="form-control" type="text" name="wa" required>
</div>

<div class="form-group">
<label>Status 24 Jam</label>
<select class="form-control" name="status">
<option value="yes">Yes</option>
<option value="no">No</option>
</select>
</div>

<button class="btn-save">💾 Simpan</button>

</form>
</div>
`)
.openOn(map);

setTimeout(()=>{

document.getElementById("formTambah").onsubmit=function(ev){
ev.preventDefault();

let fd = new FormData(this);
fd.append("lat",lat);
fd.append("lng",lng);

fetch('simpan.php',{
method:'POST',
body:fd
})
.then(r=>r.text())
.then(res=>{
if(res=="success"){
alert("Data berhasil disimpan");
location.reload();
}else{
alert("Gagal menyimpan");
}
});

}

},100);

});

/* ========================= */
/* EDIT DATA */
/* ========================= */

function editData(id,nama,wa,status,lat,lng){

L.popup({maxWidth:310})
.setLatLng([lat,lng])
.setContent(`
<div class="form-box">

<div class="form-title">Edit SPBU</div>

<form id="formEdit">

<div class="form-group">
<label>Nama SPBU</label>
<input class="form-control" type="text" name="nama" value="${nama}" required>
</div>

<div class="form-group">
<label>No WhatsApp</label>
<input class="form-control" type="text" name="wa" value="${wa}" required>
</div>

<div class="form-group">
<label>Status 24 Jam</label>
<select class="form-control" name="status">
<option value="yes" ${status=='yes'?'selected':''}>Yes</option>
<option value="no" ${status=='no'?'selected':''}>No</option>
</select>
</div>

<button class="btn-save">💾 Update</button>

</form>
</div>
`)
.openOn(map);

setTimeout(()=>{

document.getElementById("formEdit").onsubmit=function(ev){
ev.preventDefault();

let fd = new FormData(this);

fetch('update.php',{
method:'POST',
body:new URLSearchParams({
id:id,
nama:fd.get("nama"),
wa:fd.get("wa"),
status:fd.get("status"),
lat:lat,
lng:lng
})
})
.then(r=>r.text())
.then(res=>{
if(res=="success"){
alert("Data berhasil diupdate");
location.reload();
}else{
alert("Gagal update");
}
});

}

},100);

}

/* ========================= */
/* HAPUS DATA */
/* ========================= */

function hapusData(id){

if(confirm("Yakin ingin hapus data ini?")){

fetch('hapus.php',{
method:'POST',
body:new URLSearchParams({
id:id
})
})
.then(r=>r.text())
.then(res=>{
if(res=="success"){
alert("Data berhasil dihapus");
location.reload();
}
});

}

}
</script>
</body>
</html>