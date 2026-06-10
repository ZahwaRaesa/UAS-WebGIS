<!DOCTYPE html>
<html>
<head>
    <title>Peta SPBU</title>

    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <style>
        #map { height: 100vh; }
    </style>
</head>

<body>

<div id="map"></div>

<script>
const map = L.map('map').setView([-0.02, 109.34], 13);

L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19
}).addTo(map);

let layer = L.layerGroup().addTo(map);
let data = [];

// =====================
// NOTIFIKASI POPUP
// =====================
function showNotif(text) {
    let popup = L.popup()
        .setLatLng(map.getCenter())
        .setContent(`<div style="padding:6px;">${text}</div>`)
        .openOn(map);

    setTimeout(() => {
        map.closePopup(popup);
    }, 1500);
}

// =====================
// LOAD DATA
// =====================
function load() {
    fetch('ambil_data.php')
    .then(res => res.json())
    .then(res => {
        data = res;
        render();
    });
}

// =====================
// RENDER MARKER
// =====================
function render() {
    layer.clearLayers();

    data.forEach(d => {

        let marker = L.marker([d.lat, d.lng], {
            draggable: true
        }).addTo(layer);

        marker.bindPopup(`
            <b>${d.nama_spbu}</b><br><br>

            <button onclick="edit(${d.id}, '${d.nama_spbu}', ${d.lat}, ${d.lng})">
                Edit
            </button>

            <button onclick="hapus(${d.id})">
                Hapus
            </button>
        `);

        // =====================
        // DRAG UPDATE
        // =====================
        marker.on('dragend', function(e){
            let pos = e.target.getLatLng();

            fetch('update.php', {
                method: 'POST',
                body: new URLSearchParams({
                    id: d.id,
                    nama: d.nama_spbu,
                    lat: pos.lat,
                    lng: pos.lng
                })
            })
            .then(res => res.text())
            .then(res => {
                if(res.trim() === "success"){
                    showNotif("📍 Lokasi berhasil diupdate");
                    load();
                } else {
                    showNotif("❌ Gagal update");
                }
            });
        });
    });
}

load();

// =====================
// TAMBAH DATA (klik peta)
// =====================
map.on('click', function(e){

    let lat = e.latlng.lat;
    let lng = e.latlng.lng;

    let popup = L.popup()
    .setLatLng(e.latlng)
    .setContent(`
        <form id="form">
            <input name="nama" placeholder="Nama SPBU" required>
            <button type="submit">Simpan</button>
        </form>
    `)
    .openOn(map);

    setTimeout(() => {
        document.getElementById("form").onsubmit = function(ev){
            ev.preventDefault();

            let fd = new FormData(this);
            fd.append("lat", lat);
            fd.append("lng", lng);

            fetch('simpan.php', {
                method: 'POST',
                body: fd
            })
            .then(res => res.text())
            .then(res => {
                if(res.trim() === "success"){
                    showNotif("✅ SPBU berhasil ditambahkan");
                    load();
                    map.closePopup();
                } else {
                    showNotif("❌ Gagal menyimpan");
                }
            });
        }
    }, 100);
});

// =====================
// EDIT DATA
// =====================
function edit(id, nama, lat, lng){
    let newNama = prompt("Nama SPBU:", nama);

    if(newNama){
        fetch('update.php', {
            method: 'POST',
            body: new URLSearchParams({
                id,
                nama: newNama,
                lat,
                lng
            })
        })
        .then(res => res.text())
        .then(res => {
            if(res.trim() === "success"){
                showNotif("✏️ Data berhasil diupdate");
                load();
            } else {
                showNotif("❌ Gagal update");
            }
        });
    }
}

// =====================
// HAPUS DATA
// =====================
function hapus(id){
    if(confirm("Yakin mau hapus data ini?")){
        fetch('hapus.php', {
            method: 'POST',
            body: new URLSearchParams({id})
        })
        .then(res => res.text())
        .then(res => {
            if(res.trim() === "success"){
                showNotif("🗑️ Data berhasil dihapus");
                load();
            } else {
                showNotif("❌ Gagal hapus");
            }
        });
    }
}
</script>

</body>
</html>