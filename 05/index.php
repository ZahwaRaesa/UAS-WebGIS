<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>WebGIS Pengentasan Kemiskinan – UAS 06</title>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
<style>
:root{
  --bg:#0d1117;--surface:#161b22;--card:#1c2230;--card2:#212834;
  --border:#30363d;--border2:#3d444d;
  --text:#e6edf3;--muted:#8b949e;--muted2:#6e7681;
  --accent:#58a6ff;--accent2:#1f6feb;
  --green:#3fb950;--green2:#238636;
  --yellow:#d29922;--yellow2:#9e6a03;
  --red:#f85149;--red2:#da3633;
  --orange:#ff7b72;--purple:#bc8cff;
  --miskin:#f85149;--rentan:#d29922;--mampu:#3fb950;
  --r:10px;--sh:0 4px 24px rgba(0,0,0,.5);
}
*{box-sizing:border-box;margin:0;padding:0}
html,body{height:100%;overflow:hidden}
body{font-family:'Plus Jakarta Sans',sans-serif;background:var(--bg);color:var(--text);display:flex;flex-direction:column}

/* TOPBAR */
#topbar{background:var(--surface);border-bottom:1px solid var(--border);height:52px;display:flex;align-items:center;padding:0 16px;gap:12px;flex-shrink:0;z-index:1000}
.logo{font-weight:800;font-size:15px;color:var(--accent);display:flex;align-items:center;gap:7px;white-space:nowrap}
.logo i{font-size:18px}
.topnav{display:flex;gap:2px;flex:1;overflow-x:auto}
.topnav::-webkit-scrollbar{height:0}
.tnbtn{background:transparent;border:none;color:var(--muted);padding:6px 12px;border-radius:6px;cursor:pointer;font-size:12px;font-family:inherit;font-weight:600;display:flex;align-items:center;gap:5px;white-space:nowrap;transition:all .15s;position:relative}
.tnbtn:hover{background:rgba(88,166,255,.08);color:var(--accent)}
.tnbtn.active{background:rgba(88,166,255,.12);color:var(--accent)}
.tnbtn .badge-dot{position:absolute;top:4px;right:4px;width:7px;height:7px;background:var(--red);border-radius:50%;display:none}
#btn-qlapor{background:linear-gradient(135deg,var(--red),var(--orange));border:none;color:#fff;padding:7px 14px;border-radius:7px;cursor:pointer;font-size:12px;font-family:inherit;font-weight:700;display:flex;align-items:center;gap:5px;flex-shrink:0}
#ubadge{background:var(--card);border:1px solid var(--border);padding:5px 10px;border-radius:7px;font-size:11px;color:var(--muted);display:flex;align-items:center;gap:6px;flex-shrink:0;cursor:pointer}
#ubadge strong{color:var(--text)}

/* LAYOUT */
#main{display:flex;flex:1;overflow:hidden}
#sidebar{width:320px;background:var(--surface);border-right:1px solid var(--border);display:flex;flex-direction:column;overflow:hidden;flex-shrink:0;transition:width .2s}
#sidebar.collapsed{width:0;border:none}
#content{flex:1;position:relative;overflow:hidden}

/* SIDEBAR */
.ss{padding:12px 14px;border-bottom:1px solid var(--border);flex-shrink:0}
.ss-title{font-size:10px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--muted);margin-bottom:10px}
.sscroll{flex:1;overflow-y:auto;padding:12px 14px}
.sscroll::-webkit-scrollbar{width:3px}
.sscroll::-webkit-scrollbar-thumb{background:var(--border);border-radius:3px}

/* Filter pills */
.fp-row{display:flex;gap:5px;flex-wrap:wrap}
.fp{background:var(--card);border:1px solid var(--border);color:var(--muted);padding:4px 10px;border-radius:20px;cursor:pointer;font-size:11px;font-family:inherit;font-weight:600;transition:all .15s;user-select:none}
.fp:hover{border-color:var(--accent);color:var(--accent)}
.fp.on-miskin{background:rgba(248,81,73,.12);border-color:var(--miskin);color:var(--miskin)}
.fp.on-rentan{background:rgba(210,153,34,.12);border-color:var(--yellow);color:var(--yellow)}
.fp.on-mampu{background:rgba(63,185,80,.12);border-color:var(--green);color:var(--green)}
.fp.on-ibadah{background:rgba(88,166,255,.12);border-color:var(--accent);color:var(--accent)}
.fp.on-laporan{background:rgba(255,123,114,.12);border-color:var(--orange);color:var(--orange)}
.fp.on-sudah{background:rgba(63,185,80,.12);border-color:var(--green);color:var(--green)}
.fp.on-belum{background:rgba(210,153,34,.12);border-color:var(--yellow);color:var(--yellow)}

/* Search */
.sw{position:relative}
.sw i{position:absolute;left:9px;top:50%;transform:translateY(-50%);color:var(--muted);font-size:11px;pointer-events:none}
.sw input{width:100%;background:var(--card);border:1px solid var(--border);color:var(--text);padding:7px 10px 7px 28px;border-radius:7px;font-size:12px;font-family:inherit;outline:none;transition:border .15s}
.sw input:focus{border-color:var(--accent)}

/* Penduduk card */
.pc{background:var(--card);border:1px solid var(--border);border-radius:8px;padding:10px 12px;margin-bottom:7px;cursor:pointer;transition:all .15s}
.pc:hover{border-color:var(--accent);transform:translateX(2px)}
.pc-name{font-weight:700;font-size:13px;margin-bottom:3px;display:flex;align-items:center;gap:6px}
.pc-meta{font-size:11px;color:var(--muted);display:flex;gap:8px;flex-wrap:wrap}

/* Badge */
.bdg{display:inline-flex;align-items:center;padding:1px 7px;border-radius:20px;font-size:10px;font-weight:700;letter-spacing:.4px;text-transform:uppercase}
.bdg-miskin{background:rgba(248,81,73,.12);color:var(--miskin)}
.bdg-rentan{background:rgba(210,153,34,.12);color:var(--yellow)}
.bdg-mampu{background:rgba(63,185,80,.12);color:var(--green)}
.bdg-darurat{background:rgba(248,81,73,.18);color:var(--red)}
.bdg-tinggi{background:rgba(255,123,114,.12);color:var(--orange)}
.bdg-sedang{background:rgba(210,153,34,.12);color:var(--yellow)}
.bdg-rendah{background:rgba(63,185,80,.12);color:var(--green)}
.bdg-selesai{background:rgba(63,185,80,.12);color:var(--green)}
.bdg-diproses{background:rgba(88,166,255,.12);color:var(--accent)}
.bdg-diverifikasi{background:rgba(188,140,255,.12);color:var(--purple)}
.bdg-pending{background:rgba(210,153,34,.12);color:var(--yellow)}
.bdg-disalurkan{background:rgba(63,185,80,.12);color:var(--green)}
.bdg-ditolak{background:rgba(248,81,73,.12);color:var(--red)}

/* MAP */
#map{width:100%;height:100%}

/* Map legend */
#map-legend{position:absolute;bottom:80px;right:12px;background:rgba(22,27,34,.93);backdrop-filter:blur(8px);border:1px solid var(--border);border-radius:var(--r);padding:12px 14px;z-index:800;min-width:160px}
.legend-title{font-size:10px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--muted);margin-bottom:8px}
.legend-item{display:flex;align-items:center;gap:7px;margin-bottom:5px;font-size:11px}
.legend-dot{width:12px;height:12px;border-radius:50%;flex-shrink:0}

/* Map controls */
#map-ctrl{position:absolute;bottom:24px;left:12px;display:flex;gap:6px;z-index:800;flex-wrap:wrap}
.mc-btn{background:rgba(22,27,34,.92);backdrop-filter:blur(8px);border:1px solid var(--border);color:var(--muted);padding:6px 12px;border-radius:7px;cursor:pointer;font-family:inherit;font-size:11px;font-weight:600;display:flex;align-items:center;gap:5px;transition:all .15s}
.mc-btn:hover,.mc-btn.active{border-color:var(--accent);color:var(--accent);background:rgba(88,166,255,.08)}

/* Stats pills on map */
#map-stats{position:absolute;top:10px;right:12px;display:flex;flex-direction:column;gap:7px;z-index:800}
.ms-pill{background:rgba(22,27,34,.92);backdrop-filter:blur(8px);border:1px solid var(--border);border-radius:8px;padding:8px 12px;display:flex;align-items:center;gap:9px;min-width:120px}
.ms-ico{font-size:16px}
.ms-lbl{font-size:9px;color:var(--muted);font-weight:700;text-transform:uppercase;letter-spacing:.5px}
.ms-val{font-size:19px;font-weight:800;font-family:'JetBrains Mono',monospace}

/* PAGE overlays */
.page-overlay{display:none;position:absolute;inset:0;overflow-y:auto;background:var(--bg);z-index:500;padding:24px}
.page-overlay.active{display:block}

/* Hide map when not on peta page */
#content.hide-map #map,
#content.hide-map #map-stats,
#content.hide-map #map-legend,
#content.hide-map #map-ctrl{display:none!important}
.page-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:10px}
.page-head h2{font-size:20px;font-weight:800}

/* Grid cards for dashboard */
.dash-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:12px;margin-bottom:24px}
.dash-card{background:var(--card);border:1px solid var(--border);border-radius:var(--r);padding:16px 14px}
.dash-card .dc-ico{font-size:24px;margin-bottom:8px}
.dash-card .dc-val{font-size:28px;font-weight:800;font-family:'JetBrains Mono',monospace}
.dash-card .dc-lbl{font-size:10px;color:var(--muted);font-weight:700;text-transform:uppercase;letter-spacing:.5px;margin-top:3px}

/* Two column */
.two-col{display:grid;grid-template-columns:1fr 1fr;gap:20px}
@media(max-width:900px){.two-col{grid-template-columns:1fr}}

/* Section title */
.sec-title{font-size:10px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--muted);padding-bottom:8px;border-bottom:1px solid var(--border);margin-bottom:12px;display:flex;align-items:center;justify-content:space-between}

/* Table */
.tbl{width:100%;border-collapse:collapse;font-size:12px}
.tbl th{background:var(--card);color:var(--muted);font-weight:700;text-transform:uppercase;font-size:10px;letter-spacing:.5px;padding:8px 12px;text-align:left;border-bottom:1px solid var(--border);white-space:nowrap}
.tbl td{padding:9px 12px;border-bottom:1px solid var(--border);vertical-align:middle}
.tbl tr:hover td{background:rgba(255,255,255,.02)}
.tbl-wrap{border:1px solid var(--border);border-radius:var(--r);overflow:hidden;overflow-x:auto}

/* Filter bar */
.filter-bar{background:var(--card);border:1px solid var(--border);border-radius:var(--r);padding:12px 14px;margin-bottom:16px;display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end}
.filter-bar .fg{display:flex;flex-direction:column;gap:4px}
.filter-bar label{font-size:10px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.4px}
.filter-bar select,.filter-bar input[type=text],.filter-bar input[type=date],.filter-bar input[type=number]{background:var(--card2);border:1px solid var(--border);color:var(--text);padding:6px 10px;border-radius:6px;font-size:12px;font-family:inherit;outline:none;transition:border .15s}
.filter-bar select:focus,.filter-bar input:focus{border-color:var(--accent)}

/* Laporan card */
.lc{background:var(--card);border:1px solid var(--border);border-radius:var(--r);padding:14px;margin-bottom:10px}
.lc-top{display:flex;align-items:center;gap:8px;margin-bottom:6px;flex-wrap:wrap}
.lc-desc{font-size:13px;line-height:1.5;margin-bottom:8px}
.lc-meta{font-size:11px;color:var(--muted);display:flex;gap:12px;flex-wrap:wrap;align-items:center}
.lc-acts{margin-top:10px;display:flex;gap:6px;flex-wrap:wrap}

/* Chat */
#chat-box{display:flex;flex-direction:column;height:calc(100vh - 200px);min-height:400px}
#chat-msgs{flex:1;overflow-y:auto;padding:14px;background:var(--card);border:1px solid var(--border);border-radius:8px;margin-bottom:12px;display:flex;flex-direction:column;gap:10px}
#chat-msgs::-webkit-scrollbar{width:3px}
#chat-msgs::-webkit-scrollbar-thumb{background:var(--border);border-radius:3px}
.cm{max-width:75%}
.cm.me{align-self:flex-end}
.cm-who{font-size:10px;color:var(--muted);margin-bottom:3px}
.cm.me .cm-who{text-align:right}
.cm-bubble{background:var(--surface);border:1px solid var(--border);border-radius:10px;padding:8px 12px;font-size:13px;line-height:1.4;display:inline-block}
.cm.me .cm-bubble{background:rgba(88,166,255,.1);border-color:rgba(88,166,255,.25);color:var(--text)}
.chat-input-row{display:flex;gap:8px}
.chat-input-row input{flex:1}

/* Priority item */
.pi{display:flex;align-items:center;gap:10px;padding:9px 0;border-bottom:1px solid var(--border)}
.pi:last-child{border-bottom:none}
.pi-num{font-size:10px;color:var(--muted);font-weight:700;font-family:'JetBrains Mono',monospace;width:22px;flex-shrink:0}
.pi-info{flex:1}
.pi-info strong{font-size:12px;display:block}
.pi-info span{font-size:11px;color:var(--muted)}

/* Histori item */
.hi{background:var(--card2);border:1px solid var(--border);border-radius:7px;padding:9px 12px;margin-bottom:7px;display:flex;align-items:center;gap:10px}
.hi-info{flex:1}
.hi-name{font-weight:600;font-size:12px}
.hi-meta{font-size:11px;color:var(--muted)}

/* MODAL */
#overlay{position:fixed;inset:0;background:rgba(0,0,0,.65);z-index:2000;display:none;align-items:center;justify-content:center;padding:16px}
#overlay.show{display:flex}
#modal{background:var(--surface);border:1px solid var(--border);border-radius:14px;width:660px;max-width:100%;max-height:92vh;overflow-y:auto;box-shadow:0 8px 48px rgba(0,0,0,.7)}
#modal::-webkit-scrollbar{width:3px}
#modal::-webkit-scrollbar-thumb{background:var(--border);border-radius:3px}
.mh{padding:18px 22px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;background:var(--surface);z-index:1}
.mh h2{font-size:17px;font-weight:700}
.mclose{background:transparent;border:none;color:var(--muted);cursor:pointer;font-size:22px;line-height:1;padding:2px 6px;border-radius:6px}
.mclose:hover{color:var(--red);background:rgba(248,81,73,.08)}
.mbody{padding:22px}

/* FORM */
.fg{margin-bottom:14px}
.fg label{font-size:11px;font-weight:700;color:var(--muted);display:block;margin-bottom:5px;text-transform:uppercase;letter-spacing:.4px}
.fg input,.fg select,.fg textarea{width:100%;background:var(--card);border:1px solid var(--border);color:var(--text);padding:8px 11px;border-radius:7px;font-size:13px;font-family:inherit;outline:none;transition:border .15s}
.fg input:focus,.fg select:focus,.fg textarea:focus{border-color:var(--accent)}
.fg .err-msg{font-size:11px;color:var(--red);margin-top:3px;display:none}
.fg2{display:grid;grid-template-columns:1fr 1fr;gap:12px}
.fg3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px}

/* Tabs */
.tabs{display:flex;border-bottom:1px solid var(--border);margin-bottom:18px;gap:0}
.tb{background:transparent;border:none;border-bottom:2px solid transparent;color:var(--muted);padding:9px 16px;cursor:pointer;font-family:inherit;font-size:12px;font-weight:700;margin-bottom:-1px;transition:all .15s}
.tb:hover{color:var(--text)}
.tb.active{color:var(--accent);border-bottom-color:var(--accent)}
.tp{display:none}
.tp.active{display:block}

/* Buttons */
.btn{padding:8px 16px;border-radius:7px;border:none;cursor:pointer;font-family:inherit;font-size:12px;font-weight:700;display:inline-flex;align-items:center;gap:6px;transition:all .15s}
.btn-primary{background:var(--accent);color:#0d1117}
.btn-primary:hover{background:#79c0ff}
.btn-danger{background:rgba(248,81,73,.1);color:var(--red);border:1px solid rgba(248,81,73,.25)}
.btn-danger:hover{background:rgba(248,81,73,.2)}
.btn-success{background:rgba(63,185,80,.1);color:var(--green);border:1px solid rgba(63,185,80,.25)}
.btn-success:hover{background:rgba(63,185,80,.2)}
.btn-ghost{background:var(--card);color:var(--text);border:1px solid var(--border)}
.btn-ghost:hover{border-color:var(--accent);color:var(--accent)}
.btn-sm{padding:5px 10px;font-size:11px}
.btn-row{display:flex;gap:7px;flex-wrap:wrap;margin-top:16px}

/* Warning box */
.wbox{background:rgba(210,153,34,.08);border:1px solid rgba(210,153,34,.25);border-radius:7px;padding:9px 12px;font-size:12px;color:var(--yellow);display:flex;gap:7px;align-items:flex-start;margin-top:10px}

/* Detail grid */
.dg{display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:18px}
.di label{font-size:10px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.4px;display:block;margin-bottom:3px}
.di span{font-size:13px}

/* Bar chart (CSS) */
.bar-wrap{margin-bottom:8px}
.bar-label{display:flex;justify-content:space-between;font-size:11px;margin-bottom:3px}
.bar-track{background:var(--border);border-radius:4px;height:8px;overflow:hidden}
.bar-fill{height:100%;border-radius:4px;transition:width .4s}

/* Toast */
#toasts{position:fixed;bottom:20px;right:20px;z-index:9999;display:flex;flex-direction:column;gap:7px}
.toast{background:var(--card);border:1px solid var(--border);border-radius:9px;padding:11px 15px;min-width:240px;display:flex;align-items:center;gap:9px;box-shadow:var(--sh);animation:tsin .25s ease;font-size:13px}
.toast.ok{border-color:var(--green)}
.toast.err{border-color:var(--red)}
.toast.warn{border-color:var(--yellow)}
@keyframes tsin{from{transform:translateX(80px);opacity:0}to{transform:none;opacity:1}}

/* Leaflet popup dark */
.leaflet-popup-content-wrapper{background:var(--surface)!important;border:1px solid var(--border)!important;border-radius:10px!important;color:var(--text)!important;box-shadow:var(--sh)!important}
.leaflet-popup-tip{background:var(--surface)!important}
.leaflet-popup-content{margin:12px 14px!important;font-family:'Plus Jakarta Sans',sans-serif!important}
.pu-name{font-weight:700;font-size:13px;margin-bottom:4px}
.pu-meta{font-size:11px;color:var(--muted);line-height:1.6}
.pu-btn{margin-top:8px;font-size:11px;font-weight:700;color:var(--accent);cursor:pointer;display:inline-block;background:none;border:none;font-family:inherit;padding:0}
.pu-btn:hover{text-decoration:underline}

/* Wrap for tabel */
.section-card{background:var(--card);border:1px solid var(--border);border-radius:var(--r);padding:16px;margin-bottom:16px}

/* Chart canvas area */
#chart-area{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px}
.chart-box{background:var(--card);border:1px solid var(--border);border-radius:var(--r);padding:16px}
.chart-box h4{font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:12px}

/* Coordinate note */
.coord-note{font-size:11px;color:var(--muted);margin-top:4px;display:flex;align-items:center;gap:5px}
</style>
</head>
<body>

<!-- TOPBAR -->
<div id="topbar">
  <div class="logo"><i class="fas fa-map-marked-alt"></i>WebGIS Kemiskinan <span style="font-size:10px;color:var(--muted);font-weight:500">UAS 06</span></div>
  <nav class="topnav">
    <button class="tnbtn active" id="nav-peta" onclick="goPage('peta')"><i class="fas fa-map"></i> Peta</button>
    <button class="tnbtn" id="nav-dashboard" onclick="goPage('dashboard')"><i class="fas fa-chart-bar"></i> Dashboard</button>
    <button class="tnbtn" id="nav-penduduk" onclick="goPage('penduduk')"><i class="fas fa-users"></i> Penduduk</button>
    <button class="tnbtn" id="nav-bantuan" onclick="goPage('bantuan')"><i class="fas fa-gift"></i> Bantuan</button>
    <button class="tnbtn" id="nav-laporan" onclick="goPage('laporan')"><i class="fas fa-flag"></i> Laporan<span class="badge-dot" id="dot-laporan"></span></button>
    <button class="tnbtn" id="nav-ibadah" onclick="goPage('ibadah')"><i class="fas fa-mosque"></i> Ibadah</button>
    <button class="tnbtn" id="nav-user" onclick="goPage('user')"><i class="fas fa-user-shield"></i> User</button>
    <button class="tnbtn" id="nav-chat" onclick="goPage('chat')"><i class="fas fa-comments"></i> Chat<span class="badge-dot" id="dot-chat"></span></button>
  </nav>
  <button id="btn-qlapor" onclick="openLaporModal()"><i class="fas fa-exclamation-triangle"></i> Laporkan</button>
  <div id="ubadge" onclick="handleLogout()"><i class="fas fa-user-circle"></i><strong id="uname">–</strong><span id="urole" style="font-size:10px;opacity:.6"></span><i class="fas fa-sign-out-alt" style="margin-left:4px"></i></div>
</div>

<!-- MAIN -->
<div id="main">
  <!-- SIDEBAR (peta only) -->
  <div id="sidebar">
    <div class="ss">
      <div class="ss-title">Filter Layer Peta</div>
      <div class="fp-row">
        <span class="fp on-miskin" data-f="miskin" onclick="toggleFP(this)">🔴 Miskin</span>
        <span class="fp on-rentan" data-f="rentan" onclick="toggleFP(this)">🟡 Rentan</span>
        <span class="fp on-mampu" data-f="mampu" onclick="toggleFP(this)">🟢 Mampu</span>
        <span class="fp on-ibadah" data-f="ibadah" onclick="toggleFP(this)">🔵 Ibadah</span>
        <span class="fp on-laporan" data-f="laporan" onclick="toggleFP(this)">🟠 Laporan</span>
        <span class="fp on-sudah" data-f="sudah" onclick="toggleFP(this)">✅ Sudah Dibantu</span>
        <span class="fp on-belum" data-f="belum" onclick="toggleFP(this)">⏳ Belum Dibantu</span>
      </div>
    </div>
    <div class="ss">
      <div class="ss-title">Cari Penduduk</div>
      <div class="sw"><i class="fas fa-search"></i><input id="sb-search" type="text" placeholder="Nama atau NIK..." oninput="sbSearch(this.value)"></div>
    </div>
    <div class="ss">
      <div class="ss-title">Filter Tambahan</div>
      <div style="display:flex;flex-direction:column;gap:8px">
        <select id="sb-se" onchange="sbFilter()" style="background:var(--card);border:1px solid var(--border);color:var(--text);padding:6px 9px;border-radius:6px;font-size:12px;font-family:inherit;outline:none;width:100%">
          <option value="">Semua Status Ekonomi</option>
          <option value="miskin">Miskin</option>
          <option value="rentan">Rentan</option>
          <option value="mampu">Mampu</option>
        </select>
        <select id="sb-ban" onchange="sbFilter()" style="background:var(--card);border:1px solid var(--border);color:var(--text);padding:6px 9px;border-radius:6px;font-size:12px;font-family:inherit;outline:none;width:100%">
          <option value="">Semua Status Bantuan</option>
          <option value="sudah">Sudah Dibantu</option>
          <option value="belum">Belum Dibantu</option>
        </select>
      </div>
    </div>
    <div class="sscroll" id="sb-list"><p style="color:var(--muted);font-size:12px;text-align:center;margin-top:24px">Memuat data...</p></div>
  </div>

  <!-- CONTENT AREA -->
  <div id="content">
    <!-- MAP -->
    <div id="map"></div>

    <!-- Map stats -->
    <div id="map-stats">
      <div class="ms-pill"><span class="ms-ico" style="color:var(--red)">👥</span><div><div class="ms-lbl">Miskin</div><div class="ms-val" id="ms-miskin">–</div></div></div>
      <div class="ms-pill"><span class="ms-ico" style="color:var(--yellow)">⚠️</span><div><div class="ms-lbl">Rentan</div><div class="ms-val" id="ms-rentan">–</div></div></div>
      <div class="ms-pill"><span class="ms-ico" style="color:var(--orange)">🚨</span><div><div class="ms-lbl">Laporan</div><div class="ms-val" id="ms-laporan">–</div></div></div>
    </div>

    <!-- Map legend -->
    <div id="map-legend">
      <div class="legend-title">Legenda</div>
      <div class="legend-item"><div class="legend-dot" style="background:var(--miskin)"></div> Penduduk Miskin</div>
      <div class="legend-item"><div class="legend-dot" style="background:var(--yellow)"></div> Penduduk Rentan</div>
      <div class="legend-item"><div class="legend-dot" style="background:var(--green)"></div> Sudah Dibantu</div>
      <div class="legend-item"><div class="legend-dot" style="background:var(--accent)"></div> Rumah Ibadah</div>
      <div class="legend-item"><div class="legend-dot" style="background:var(--orange)"></div> Laporan Warga</div>
    </div>

    <!-- Map controls -->
    <div id="map-ctrl">
      <button class="mc-btn active" id="mc-street" onclick="setBase('street')"><i class="fas fa-map"></i> Street</button>
      <button class="mc-btn" id="mc-satellite" onclick="setBase('satellite')"><i class="fas fa-satellite"></i> Satelit</button>
      <button class="mc-btn" id="mc-dark" onclick="setBase('dark')"><i class="fas fa-moon"></i> Dark</button>
      <button class="mc-btn" onclick="openAddPenduduk()"><i class="fas fa-plus"></i> Tambah Penduduk</button>
    </div>

    <!-- ====== PAGE: DASHBOARD ====== -->
    <div class="page-overlay" id="page-dashboard">
      <div class="page-head"><h2>📊 Dashboard Monitoring</h2><button class="btn btn-ghost btn-sm" onclick="loadDashboard()"><i class="fas fa-sync"></i> Refresh</button></div>
      <div class="dash-grid" id="dg-cards"></div>
      <div id="chart-area">
        <div class="chart-box"><h4>Distribusi Status Ekonomi</h4><div id="chart-ekonomi"></div></div>
        <div class="chart-box"><h4>Statistik per Rumah Ibadah</h4><div id="chart-ibadah"></div></div>
      </div>
      <div class="two-col">
        <div>
          <div class="sec-title">🚨 Prioritas Penerima Bantuan</div>
          <div id="dg-prioritas"></div>
        </div>
        <div>
          <div class="sec-title">🕐 Aktivitas Terbaru</div>
          <div id="dg-aktivitas"></div>
        </div>
      </div>
    </div>

    <!-- ====== PAGE: PENDUDUK ====== -->
    <div class="page-overlay" id="page-penduduk">
      <div class="page-head">
        <h2>👥 Manajemen Penduduk</h2>
        <button class="btn btn-primary" onclick="openAddPenduduk()"><i class="fas fa-plus"></i> Tambah Penduduk</button>
      </div>
      <div class="filter-bar">
        <div class="fg"><label>Cari</label><input type="text" id="f-pend-q" placeholder="Nama / NIK" oninput="loadPenduduk()"></div>
        <div class="fg"><label>Status Ekonomi</label>
          <select id="f-pend-se" onchange="loadPenduduk()">
            <option value="">Semua</option><option value="miskin">Miskin</option><option value="rentan">Rentan</option><option value="mampu">Mampu</option>
          </select></div>
        <div class="fg"><label>Status Bantuan</label>
          <select id="f-pend-ban" onchange="loadPenduduk()">
            <option value="">Semua</option><option value="sudah">Sudah Dibantu</option><option value="belum">Belum Dibantu</option>
          </select></div>
        <div class="fg"><label>Penghasilan Min</label><input type="number" id="f-pend-pmin" placeholder="0" oninput="loadPenduduk()"></div>
        <div class="fg"><label>Penghasilan Maks</label><input type="number" id="f-pend-pmax" placeholder="∞" oninput="loadPenduduk()"></div>
        <div class="fg"><label>Wilayah</label><input type="text" id="f-pend-wil" placeholder="Kelurahan" oninput="loadPenduduk()"></div>
        <button class="btn btn-ghost btn-sm" style="align-self:flex-end" onclick="resetFilterPenduduk()"><i class="fas fa-times"></i> Reset</button>
      </div>
      <div class="tbl-wrap">
        <table class="tbl">
          <thead><tr><th>NIK</th><th>Nama</th><th>Umur</th><th>Status</th><th>Pekerjaan</th><th>Penghasilan</th><th>Wilayah</th><th>Bantuan</th><th>Aksi</th></tr></thead>
          <tbody id="pend-tbody"><tr><td colspan="9" style="text-align:center;color:var(--muted);padding:24px">Memuat...</td></tr></tbody>
        </table>
      </div>
      <div id="pend-pagination" style="margin-top:10px;font-size:12px;color:var(--muted);text-align:right"></div>
    </div>

    <!-- ====== PAGE: BANTUAN ====== -->
    <div class="page-overlay" id="page-bantuan">
      <div class="page-head"><h2>🎁 Manajemen Bantuan</h2></div>
      <div class="tabs">
        <button class="tb active" onclick="switchTab('ban-master',this)">Master Bantuan</button>
        <button class="tb" onclick="switchTab('ban-histori',this)">Riwayat Penyaluran</button>
        <button class="tb" onclick="switchTab('ban-belum',this)">Belum Dibantu</button>
        <button class="tb" onclick="switchTab('ban-stat',this)">Statistik</button>
      </div>
      <!-- Master Bantuan -->
      <div id="tp-ban-master" class="tp active">
        <div style="margin-bottom:12px"><button class="btn btn-primary btn-sm" onclick="openMasterBantuanModal()"><i class="fas fa-plus"></i> Tambah Jenis Bantuan</button></div>
        <div class="tbl-wrap"><table class="tbl"><thead><tr><th>Nama</th><th>Jenis</th><th>Sumber</th><th>Bentuk</th><th>Aksi</th></tr></thead><tbody id="master-ban-tbody"></tbody></table></div>
      </div>
      <!-- Histori -->
      <div id="tp-ban-histori" class="tp">
        <div class="filter-bar">
          <div class="fg"><label>Status</label><select id="f-his-status" onchange="loadHistori()"><option value="">Semua</option><option value="disalurkan">Disalurkan</option><option value="direncanakan">Direncanakan</option><option value="ditolak">Ditolak</option></select></div>
          <div class="fg"><label>Dari</label><input type="date" id="f-his-dari" onchange="loadHistori()"></div>
          <div class="fg"><label>Sampai</label><input type="date" id="f-his-sampai" onchange="loadHistori()"></div>
          <div class="fg"><label>Cari Nama</label><input type="text" id="f-his-q" placeholder="Nama penerima" oninput="loadHistori()"></div>
        </div>
        <div class="tbl-wrap"><table class="tbl"><thead><tr><th>NIK</th><th>Nama Penerima</th><th>Jenis Bantuan</th><th>Tanggal</th><th>Jumlah</th><th>Status</th><th>Aksi</th></tr></thead><tbody id="histori-tbody"></tbody></table></div>
      </div>
      <!-- Belum Dibantu -->
      <div id="tp-ban-belum" class="tp">
        <div id="belum-list" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:10px"></div>
      </div>
      <!-- Statistik -->
      <div id="tp-ban-stat" class="tp">
        <div class="tbl-wrap"><table class="tbl"><thead><tr><th>Jenis Bantuan</th><th>Jenis</th><th>Total Tersalur</th><th>Berhasil</th></tr></thead><tbody id="stat-ban-tbody"></tbody></table></div>
      </div>
    </div>

    <!-- ====== PAGE: LAPORAN ====== -->
    <div class="page-overlay" id="page-laporan">
      <div class="page-head">
        <h2>🚩 Laporan Masyarakat</h2>
        <button class="btn btn-primary" onclick="openLaporModal()"><i class="fas fa-plus"></i> Buat Laporan</button>
      </div>
      <div class="filter-bar">
        <div class="fg"><label>Status</label><select id="f-lap-status" onchange="loadLaporan()"><option value="">Semua</option><option value="pending">Pending</option><option value="diverifikasi">Diverifikasi</option><option value="diproses">Diproses</option><option value="selesai">Selesai</option></select></div>
        <div class="fg"><label>Urgensi</label><select id="f-lap-urgensi" onchange="loadLaporan()"><option value="">Semua</option><option value="darurat">Darurat</option><option value="tinggi">Tinggi</option><option value="sedang">Sedang</option><option value="rendah">Rendah</option></select></div>
        <div class="fg"><label>Cari</label><input type="text" id="f-lap-q" placeholder="Kata kunci..." oninput="loadLaporan()"></div>
      </div>
      <div id="laporan-list"></div>
    </div>

    <!-- ====== PAGE: IBADAH ====== -->
    <div class="page-overlay" id="page-ibadah">
      <div class="page-head">
        <h2>🕌 Rumah Ibadah</h2>
        <button class="btn btn-primary" onclick="openIbadahModal()"><i class="fas fa-plus"></i> Tambah</button>
      </div>
      <div class="tbl-wrap"><table class="tbl"><thead><tr><th>Nama</th><th>Jenis</th><th>Kontak</th><th>Alamat</th><th>Penduduk</th><th>Miskin</th><th>Aksi</th></tr></thead><tbody id="ibadah-tbody"></tbody></table></div>
    </div>

    <!-- ====== PAGE: USER ====== -->
    <div class="page-overlay" id="page-user">
      <div class="page-head">
        <h2>👤 Manajemen Pengguna</h2>
        <button class="btn btn-primary" onclick="openUserModal()"><i class="fas fa-plus"></i> Tambah User</button>
      </div>
      <div class="tbl-wrap"><table class="tbl"><thead><tr><th>Nama</th><th>Email</th><th>Role</th><th>Status</th><th>Dibuat</th><th>Aksi</th></tr></thead><tbody id="user-tbody"></tbody></table></div>
    </div>

    <!-- ====== PAGE: CHAT ====== -->
    <div class="page-overlay" id="page-chat">
      <div class="page-head"><h2>💬 Chat Pengurus</h2><span style="font-size:12px;color:var(--muted)">Pesan broadcast ke semua pengurus</span></div>
      <div id="chat-box">
        <div id="chat-msgs"><p style="color:var(--muted);font-size:12px;text-align:center;margin:auto">Memuat pesan...</p></div>
        <div class="chat-input-row">
          <input class="fg" id="chat-inp" type="text" placeholder="Tulis pesan..." onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();sendChat()}" style="flex:1;background:var(--card);border:1px solid var(--border);color:var(--text);padding:9px 12px;border-radius:7px;font-size:13px;font-family:inherit;outline:none">
          <button class="btn btn-primary" onclick="sendChat()"><i class="fas fa-paper-plane"></i> Kirim</button>
        </div>
      </div>
    </div>
  </div><!-- end #content -->
</div><!-- end #main -->

<!-- MODAL -->
<div id="overlay" onclick="if(event.target===this)closeModal()">
  <div id="modal"></div>
</div>

<!-- TOASTS -->
<div id="toasts"></div>

<script>
// =========================================================
// GLOBALS
// =========================================================
let map, baseLayers={}, markerGroups={miskin:[],rentan:[],mampu:[],ibadah:[],laporan:[],sudah:[]};
let filterState={miskin:true,rentan:true,mampu:true,ibadah:true,laporan:true,sudah:true,belum:true};
let allMapData=[], currentUser=null, chatInterval=null, unreadInterval=null;

// =========================================================
// INIT
// =========================================================
document.addEventListener('DOMContentLoaded',()=>{
  checkUser();
  initMap();
  loadMapData();
  loadMiniStats();
  startUnreadPoll();
});

// =========================================================
// AUTH
// =========================================================
function checkUser(){
  fetch('auth.php?aksi=cek').then(r=>r.json()).then(d=>{
    if(d.login){
      currentUser=d.user;
      document.getElementById('uname').textContent=d.user.nama;
      document.getElementById('urole').textContent=d.user.role_nama||d.user.role;
    } else {
      document.getElementById('uname').textContent='Demo';
      document.getElementById('urole').textContent='pengurus';
    }
  });
}

function handleLogout(){
  if(!confirm('Yakin ingin logout?')) return;
  fetch('auth.php',{method:'POST',body:new URLSearchParams({aksi:'logout'})})
    .then(()=>{ currentUser=null; window.location.href='login.php'; });
}

// =========================================================
// MAP
// =========================================================
function initMap(){
  map=L.map('map',{zoomControl:false,attributionControl:true}).setView([0.0262,109.342],14);
  L.control.zoom({position:'bottomright'}).addTo(map);
  baseLayers.street=L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{attribution:'© OpenStreetMap',maxZoom:19}).addTo(map);
  baseLayers.satellite=L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',{attribution:'© Esri',maxZoom:19});
  baseLayers.dark=L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png',{attribution:'© CARTO',maxZoom:19});
  // Klik kanan → set coord
  map.on('contextmenu',e=>{
    const lat=e.latlng.lat.toFixed(6),lng=e.latlng.lng.toFixed(6);
    ['lat','lng','p-lat','p-lng','lap-lat','lap-lng'].forEach(id=>{const el=document.getElementById(id);if(el){if(id.includes('lat')) el.value=lat; else el.value=lng;}});
    toast('Koordinat diset: '+lat+', '+lng,'ok');
  });
}

function setBase(b){
  Object.values(baseLayers).forEach(l=>map.removeLayer(l));
  baseLayers[b].addTo(map);
  document.querySelectorAll('.mc-btn').forEach(el=>el.classList.remove('active'));
  document.getElementById('mc-'+b).classList.add('active');
}

function makeIcon(color,emoji='●'){
  return L.divIcon({className:'',iconSize:[28,28],iconAnchor:[14,28],popupAnchor:[0,-30],
    html:`<div style="background:${color};width:28px;height:28px;border-radius:50% 50% 50% 0;transform:rotate(-45deg);border:2px solid rgba(255,255,255,.3);box-shadow:0 2px 8px rgba(0,0,0,.5);display:flex;align-items:center;justify-content:center"><span style="transform:rotate(45deg);font-size:11px">${emoji}</span></div>`
  });
}

const COLORS={miskin:'#f85149',rentan:'#d29922',mampu:'#3fb950',sudah:'#3fb950',ibadah:'#58a6ff',laporan:'#ff7b72'};

function loadMapData(){
  let url='ambil.php?tipe=semua';
  const se=document.getElementById('sb-se')?.value, ban=document.getElementById('sb-ban')?.value;
  if(se) url+='&status_ekonomi='+se;
  if(ban) url+='&status_bantuan='+ban;
  fetch(url).then(r=>r.json()).then(data=>{
    allMapData=data;
    clearMarkers();
    renderMarkers(data);
    renderSidebar(data.filter(d=>d.tipe==='penduduk'));
  }).catch(e=>toast('Gagal memuat data peta','err'));
}

function clearMarkers(){
  Object.values(markerGroups).flat().forEach(m=>map.removeLayer(m));
  Object.keys(markerGroups).forEach(k=>markerGroups[k]=[]);
}

function renderMarkers(data){
  data.forEach(d=>{
    const lat=parseFloat(d.lat), lng=parseFloat(d.lng);
    if(!lat||!lng) return;

    if(d.tipe==='ibadah'){
      const emoji={masjid:'🕌',gereja:'⛪',pura:'🛕',vihara:'🏯'}[d.jenis]||'🏛️';
      const m=L.marker([lat,lng],{icon:makeIcon(COLORS.ibadah,emoji)});
      m.bindPopup(`<div class="pu-name">${d.nama}</div>
        <div class="pu-meta">Jenis: ${d.jenis}<br>Penduduk: ${d.total_penduduk||0} · Miskin: ${d.total_miskin||0}</div>
        <button class="pu-btn" onclick="openIbadahDetail(${d.id})">Detail →</button>`);
      markerGroups.ibadah.push(m);
      if(filterState.ibadah) m.addTo(map);
      // Radius circle
      const c=L.circle([lat,lng],{radius:parseFloat(d.radius)||500,color:COLORS.ibadah,fillColor:COLORS.ibadah,fillOpacity:0.04,weight:1,dashArray:'5'});
      markerGroups.ibadah.push(c);
      if(filterState.ibadah) c.addTo(map);
    }

    if(d.tipe==='penduduk'){
      const sudah=(d.total_bantuan||0)>0;
      const ek=d.status_ekonomi||'rentan';
      const grp=sudah?'sudah':ek;
      const color=sudah?COLORS.sudah:COLORS[ek]||'#8b949e';
      const emoji=sudah?'✅':(d.jenis_kelamin==='L'?'👨':'👩');
      const m=L.marker([lat,lng],{icon:makeIcon(color,emoji)});
      m.bindPopup(`<div class="pu-name">${d.nama}</div>
        <div class="pu-meta">${d.jenis_kelamin==='L'?'Laki-laki':'Perempuan'}, ${d.umur||'?'} th<br>
        Status: <b>${ek}</b>${sudah?' · ✅ Sudah dibantu ('+d.total_bantuan+'x)':' · ⏳ Belum dibantu'}<br>
        Pekerjaan: ${d.pekerjaan||'-'}<br>Penghasilan: Rp ${Number(d.penghasilan||0).toLocaleString('id')}<br>
        Ibadah: ${d.nama_ibadah||'-'}</div>
        <button class="pu-btn" onclick="openDetailPenduduk(${d.id})">Detail →</button>`);
      // Tentukan grup untuk filter
      const filterGrp=sudah?'sudah':ek;
      markerGroups[filterGrp]?.push(m)||(markerGroups[ek]||(markerGroups[ek]=[])).push(m);
      const show = filterState[ek] && (sudah ? filterState.sudah : filterState.belum);
      if(show) m.addTo(map);
    }

    if(d.tipe==='laporan'){
      const emoji={darurat:'🆘',tinggi:'🚨',sedang:'⚠️',rendah:'ℹ️'}[d.urgensi]||'⚠️';
      const m=L.marker([lat,lng],{icon:makeIcon(COLORS.laporan,emoji)});
      m.bindPopup(`<div class="pu-name">Laporan ${(d.urgensi||'').toUpperCase()}</div>
        <div class="pu-meta">${(d.deskripsi||'').substring(0,100)}...<br>
        Pelapor: ${d.pelapor||'Anonim'} · Status: ${d.status}<br>
        ${new Date(d.created_at).toLocaleDateString('id')}</div>`);
      markerGroups.laporan.push(m);
      if(filterState.laporan) m.addTo(map);
    }
  });
}

function toggleFP(el){
  const f=el.dataset.f;
  filterState[f]=!filterState[f];
  el.classList.toggle('on-'+f);
  // update markers
  clearMarkers(); renderMarkers(allMapData);
}

function sbSearch(q){
  q=q.toLowerCase();
  const filtered=allMapData.filter(d=>d.tipe==='penduduk'&&(d.nama.toLowerCase().includes(q)||(d.nik||'').includes(q)));
  renderSidebar(filtered);
}
function sbFilter(){ loadMapData(); }

function renderSidebar(data){
  const el=document.getElementById('sb-list');
  if(!data.length){el.innerHTML='<p style="color:var(--muted);font-size:12px;text-align:center;margin-top:20px">Tidak ada data</p>';return;}
  el.innerHTML=data.slice(0,60).map(d=>`<div class="pc" onclick="openDetailPenduduk(${d.id})">
    <div class="pc-name">${d.nama} <span class="bdg bdg-${d.status_ekonomi}">${d.status_ekonomi}</span></div>
    <div class="pc-meta"><span>🎂${d.umur||'?'}th</span><span>💼${d.pekerjaan||'–'}</span><span>🕌${d.nama_ibadah||'–'}</span></div>
  </div>`).join('');
}

function loadMiniStats(){
  fetch('dashboard.php').then(r=>r.json()).then(d=>{
    document.getElementById('ms-miskin').textContent=d.total_miskin||0;
    document.getElementById('ms-rentan').textContent=d.total_rentan||0;
    document.getElementById('ms-laporan').textContent=d.total_laporan||0;
  });
}

// =========================================================
// PAGE NAVIGATION
// =========================================================
const PAGES=['peta','dashboard','penduduk','bantuan','laporan','ibadah','user','chat'];
function goPage(p){
  document.querySelectorAll('.tnbtn').forEach(b=>b.classList.remove('active'));
  document.getElementById('nav-'+p)?.classList.add('active');
  PAGES.forEach(pg=>{
    const el=document.getElementById('page-'+pg);
    if(el) el.classList.remove('active');
  });
  document.getElementById('sidebar').classList.toggle('collapsed',p!=='peta');
  document.getElementById('content').classList.toggle('hide-map',p!=='peta');
  if(p!=='peta'){
    const el=document.getElementById('page-'+p);
    if(el) el.classList.add('active');
  }
  if(p==='dashboard') loadDashboard();
  if(p==='penduduk') loadPenduduk();
  if(p==='bantuan') { loadMasterBantuan(); loadHistori(); loadBelumDibantu(); loadStatBantuan(); }
  if(p==='laporan') loadLaporan();
  if(p==='ibadah') loadIbadah();
  if(p==='user') loadUsers();
  if(p==='chat') { loadChat(); clearInterval(chatInterval); chatInterval=setInterval(loadChat,4000); }
  else clearInterval(chatInterval);
}

// =========================================================
// DASHBOARD
// =========================================================
function loadDashboard(){
  fetch('dashboard.php').then(r=>r.json()).then(d=>{
    const cards=[
      {ico:'👥',val:d.total_penduduk,lbl:'Total Penduduk',c:'var(--accent)'},
      {ico:'🔴',val:d.total_miskin,lbl:'Warga Miskin',c:'var(--red)'},
      {ico:'🟡',val:d.total_rentan,lbl:'Warga Rentan',c:'var(--yellow)'},
      {ico:'🟢',val:d.total_mampu,lbl:'Warga Mampu',c:'var(--green)'},
      {ico:'🏠',val:d.total_keluarga,lbl:'Jumlah KK',c:'var(--purple)'},
      {ico:'🚩',val:d.total_laporan,lbl:'Total Laporan',c:'var(--orange)'},
      {ico:'🆘',val:d.total_darurat,lbl:'Laporan Darurat',c:'var(--red)'},
      {ico:'⏳',val:d.laporan_pending,lbl:'Laporan Pending',c:'var(--yellow)'},
      {ico:'🎁',val:d.total_bantuan_salur,lbl:'Bantuan Tersalur',c:'var(--green)'},
      {ico:'⌛',val:d.belum_dibantu,lbl:'Belum Dibantu',c:'var(--yellow)'},
      {ico:'🏘️',val:d.total_wilayah,lbl:'Wilayah/Kelurahan',c:'var(--accent)'},
      {ico:'📍',val:d.total_rt_rw,lbl:'Total RT/RW',c:'var(--muted)'},
    ];
    document.getElementById('dg-cards').innerHTML=cards.map(c=>`
      <div class="dash-card"><div class="dc-ico">${c.ico}</div>
      <div class="dc-val" style="color:${c.c}">${c.val||0}</div>
      <div class="dc-lbl">${c.lbl}</div></div>`).join('');

    // Chart ekonomi (CSS bars)
    const tot=(d.total_miskin+d.total_rentan+d.total_mampu)||1;
    document.getElementById('chart-ekonomi').innerHTML=[
      {lbl:'Miskin',val:d.total_miskin,c:'var(--red)'},
      {lbl:'Rentan',val:d.total_rentan,c:'var(--yellow)'},
      {lbl:'Mampu',val:d.total_mampu,c:'var(--green)'},
    ].map(x=>`<div class="bar-wrap">
      <div class="bar-label"><span>${x.lbl}</span><span style="font-weight:700">${x.val||0}</span></div>
      <div class="bar-track"><div class="bar-fill" style="width:${Math.round((x.val/tot)*100)}%;background:${x.c}"></div></div>
    </div>`).join('');

    // Chart ibadah
    const ibadah=d.stat_ibadah||[];
    const maxIb=Math.max(...ibadah.map(x=>x.total_penduduk||0),1);
    document.getElementById('chart-ibadah').innerHTML=ibadah.length?ibadah.map(x=>`<div class="bar-wrap">
      <div class="bar-label"><span style="font-size:11px">${x.nama}</span><span style="font-weight:700">${x.total_penduduk||0}</span></div>
      <div class="bar-track"><div class="bar-fill" style="width:${Math.round(((x.total_penduduk||0)/maxIb)*100)}%;background:var(--accent)"></div></div>
    </div>`).join(''):'<p style="color:var(--muted);font-size:12px">Tidak ada data</p>';

    // Prioritas
    document.getElementById('dg-prioritas').innerHTML=(d.prioritas||[]).length?
      (d.prioritas||[]).map((p,i)=>`<div class="pi">
        <div class="pi-num">#${i+1}</div>
        <div class="pi-info"><strong>${p.nama}</strong><span>${p.umur}th · ${p.nama_ibadah||'–'} · Lap. darurat: ${p.lap_darurat||0}</span></div>
        <span class="bdg bdg-${p.status_ekonomi}">${p.status_ekonomi}</span>
      </div>`).join(''):
      '<p style="color:var(--muted);font-size:12px">Tidak ada prioritas</p>';

    // Aktivitas
    document.getElementById('dg-aktivitas').innerHTML=(d.aktivitas||[]).length?
      (d.aktivitas||[]).map(a=>`<div class="pi">
        <div class="pi-info"><strong>${a.aksi} · ${a.tabel} #${a.data_id||'–'}</strong><span>${a.nama_user||'Sistem'} · ${new Date(a.created_at).toLocaleString('id')}</span></div>
      </div>`).join(''):
      '<p style="color:var(--muted);font-size:12px">Tidak ada aktivitas</p>';
  }).catch(e=>toast('Gagal memuat dashboard','err'));
}

// =========================================================
// PENDUDUK
// =========================================================
function loadPenduduk(){
  const q=document.getElementById('f-pend-q')?.value||'';
  const se=document.getElementById('f-pend-se')?.value||'';
  const ban=document.getElementById('f-pend-ban')?.value||'';
  const pmin=document.getElementById('f-pend-pmin')?.value||'';
  const pmax=document.getElementById('f-pend-pmax')?.value||'';
  const wil=document.getElementById('f-pend-wil')?.value||'';
  const params=new URLSearchParams({aksi:'list'});
  if(q) params.append('q',q);
  if(se) params.append('status_ekonomi',se);
  if(ban) params.append('status_bantuan',ban);
  if(pmin) params.append('penghasilan_min',pmin);
  if(pmax) params.append('penghasilan_max',pmax);
  if(wil) params.append('kelurahan',wil);
  fetch('penduduk.php?'+params).then(r=>r.json()).then(data=>{
    const tbody=document.getElementById('pend-tbody');
    if(!data.length){tbody.innerHTML='<tr><td colspan="9" style="text-align:center;color:var(--muted);padding:20px">Tidak ada data</td></tr>';return;}
    tbody.innerHTML=data.map(d=>`<tr>
      <td style="font-family:monospace;font-size:11px">${d.nik}</td>
      <td><strong>${d.nama}</strong></td>
      <td>${d.umur||'?'}</td>
      <td><span class="bdg bdg-${d.status_ekonomi}">${d.status_ekonomi}</span></td>
      <td>${d.pekerjaan||'–'}</td>
      <td>Rp${Number(d.penghasilan||0).toLocaleString('id')}</td>
      <td style="font-size:11px">${d.kelurahan||'–'}${d.rt?', RT '+d.rt+'/'+d.rw:''}</td>
      <td><span class="bdg ${d.total_bantuan>0?'bdg-disalurkan':'bdg-pending'}">${d.total_bantuan>0?d.total_bantuan+'x':'Belum'}</span></td>
      <td>
        <button class="btn btn-ghost btn-sm" onclick="openDetailPenduduk(${d.id})" title="Detail"><i class="fas fa-eye"></i></button>
        <button class="btn btn-ghost btn-sm" onclick="openEditPenduduk(${d.id})" title="Edit"><i class="fas fa-edit"></i></button>
        <button class="btn btn-success btn-sm" onclick="openSalurModal(${d.id},'${esc(d.nama)}')" title="Salurkan"><i class="fas fa-gift"></i></button>
        <button class="btn btn-danger btn-sm" onclick="hapusPenduduk(${d.id})" title="Hapus"><i class="fas fa-trash"></i></button>
      </td></tr>`).join('');
    document.getElementById('pend-pagination').textContent=`Menampilkan ${data.length} data`;
  }).catch(e=>toast('Gagal memuat data penduduk','err'));
}

function resetFilterPenduduk(){
  ['f-pend-q','f-pend-se','f-pend-ban','f-pend-pmin','f-pend-pmax','f-pend-wil'].forEach(id=>{const el=document.getElementById(id);if(el)el.value='';});
  loadPenduduk();
}

function openAddPenduduk(){
  openModal('Tambah Data Penduduk',`
    <div class="tabs">
      <button class="tb active" onclick="switchTabM('tp-data',this)">Data Pokok</button>
      <button class="tb" onclick="switchTabM('tp-detail',this)">Detail</button>
      <button class="tb" onclick="switchTabM('tp-lokasi',this)">Lokasi</button>
    </div>
    <form id="form-add-pend" onsubmit="savePenduduk(event)">
    <div id="tp-data" class="tp active">
      <div class="fg2">
        <div class="fg"><label>NIK *</label><input name="nik" required maxlength="16" placeholder="16 digit NIK"></div>
        <div class="fg"><label>Nama Lengkap *</label><input name="nama" required placeholder="Nama lengkap"></div>
      </div>
      <div class="fg2">
        <div class="fg"><label>Jenis Kelamin</label><select name="jenis_kelamin"><option value="L">Laki-laki</option><option value="P">Perempuan</option></select></div>
        <div class="fg"><label>Tanggal Lahir *</label><input type="date" name="tanggal_lahir" required></div>
      </div>
      <div class="fg2">
        <div class="fg"><label>Status dalam KK</label><select name="status_keluarga"><option value="anggota">Anggota</option><option value="kepala_keluarga">Kepala Keluarga</option></select></div>
        <div class="fg"><label>Status Perkawinan</label><select name="status_perkawinan"><option value="belum_kawin">Belum Kawin</option><option value="kawin">Kawin</option><option value="cerai_hidup">Cerai Hidup</option><option value="cerai_mati">Cerai Mati</option></select></div>
      </div>
      <div class="fg"><label>No. HP</label><input name="no_hp" placeholder="08xxxxxxxxxx"></div>
    </div>
    <div id="tp-detail" class="tp">
      <div class="fg2">
        <div class="fg"><label>Pekerjaan</label><input name="pekerjaan" placeholder="Pekerjaan saat ini"></div>
        <div class="fg"><label>Penghasilan/bulan (Rp)</label><input type="number" name="penghasilan" value="0" min="0"></div>
      </div>
      <div class="fg2">
        <div class="fg"><label>Status Ekonomi *</label><select name="status_ekonomi"><option value="miskin">Miskin</option><option value="rentan" selected>Rentan</option><option value="mampu">Mampu</option></select></div>
        <div class="fg"><label>Pendidikan Terakhir</label><select name="pendidikan_terakhir"><option value="tidak_sekolah">Tidak Sekolah</option><option value="SD">SD</option><option value="SMP">SMP</option><option value="SMA">SMA</option><option value="D3">D3</option><option value="S1">S1</option><option value="S2">S2</option></select></div>
      </div>
      <div class="fg2">
        <div class="fg"><label>Status Pendidikan</label><select name="status_pendidikan"><option value="sekolah">Masih Sekolah</option><option value="lulus">Lulus</option><option value="tidak_sekolah">Tidak Sekolah</option></select></div>
        <div class="fg"><label>Jenis Bantuan Dibutuhkan</label><input name="jenis_bantuan" placeholder="Sembako, kesehatan, dll"></div>
      </div>
    </div>
    <div id="tp-lokasi" class="tp">
      <div class="fg2" style="margin-bottom:10px">
        <div class="fg">
          <label>Latitude</label>
          <input name="lat" id="p-lat" placeholder="0.0262" type="number" step="any" oninput="syncPickerFromInput()">
        </div>
        <div class="fg">
          <label>Longitude</label>
          <input name="lng" id="p-lng" placeholder="109.342" type="number" step="any" oninput="syncPickerFromInput()">
        </div>
      </div>
      <div style="position:relative;border-radius:10px;overflow:hidden;border:1px solid var(--border)">
        <div id="picker-map" style="height:300px;width:100%;background:#1a2332"></div>
        <div style="position:absolute;top:8px;left:50%;transform:translateX(-50%);z-index:1000;background:rgba(13,17,23,.85);color:#e6edf3;font-size:11px;padding:4px 10px;border-radius:20px;pointer-events:none;white-space:nowrap;border:1px solid var(--border)">
          <i class="fas fa-mouse-pointer"></i> Klik peta untuk menentukan lokasi
        </div>
        <div id="picker-coord-badge" style="position:absolute;bottom:8px;left:8px;z-index:1000;background:rgba(13,17,23,.88);color:#58a6ff;font-size:11px;padding:4px 10px;border-radius:6px;display:none;border:1px solid var(--border)"></div>
      </div>
      <div style="display:flex;gap:8px;margin-top:8px">
        <button type="button" class="btn btn-ghost btn-sm" style="font-size:11px" onclick="locateMeOnPicker()"><i class="fas fa-crosshairs"></i> Lokasi Saya</button>
        <button type="button" class="btn btn-ghost btn-sm" style="font-size:11px" onclick="resetPickerMarker()"><i class="fas fa-times"></i> Reset Titik</button>
      </div>
    </div>
    <div id="add-warning" class="wbox" style="display:none"><i class="fas fa-exclamation-triangle"></i><span id="add-warning-txt"></span></div>
    <div class="btn-row">
      <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
      <button type="button" class="btn btn-ghost" onclick="closeModal()">Batal</button>
    </div>
    </form>
  `);
}

function savePenduduk(e){
  e.preventDefault();
  const form=new FormData(e.target);
  form.set('aksi','simpan');
  const btn=e.target.querySelector('[type=submit]');
  if(btn){btn.disabled=true;btn.innerHTML='<i class="fas fa-spinner fa-spin"></i> Menyimpan...';}
  fetch('penduduk.php',{method:'POST',body:form})
    .then(r=>{
      if(!r.ok) return r.text().then(t=>{throw new Error('Server error '+r.status+': '+t.substring(0,200));});
      return r.json();
    })
    .then(d=>{
      if(btn){btn.disabled=false;btn.innerHTML='<i class="fas fa-save"></i> Simpan';}
      if(d.warning){document.getElementById('add-warning').style.display='flex';document.getElementById('add-warning-txt').textContent=d.warning;}
      if(d.error){toast(d.error,'err');return;}
      if(d.success){toast('Data penduduk berhasil disimpan! ✅');closeModal();loadPenduduk();loadMapData();loadMiniStats();}
    })
    .catch(err=>{
      if(btn){btn.disabled=false;btn.innerHTML='<i class="fas fa-save"></i> Simpan';}
      console.error('savePenduduk error:',err);
      toast('Gagal: '+err.message,'err');
    });
}

function openDetailPenduduk(id){
  fetch(`penduduk.php?aksi=detail&id=${id}`).then(r=>r.json()).then(d=>{
    if(d.error){toast(d.error,'err');return;}
    const histori=(d.histori_bantuan||[]).map(h=>`<div class="hi">
      <div class="hi-info"><div class="hi-name">${h.nama_bantuan}</div><div class="hi-meta">${h.tanggal} · ${h.jumlah||'–'}</div></div>
      <span class="bdg bdg-${h.status}">${h.status}</span></div>`).join('')||'<p style="color:var(--muted);font-size:12px">Belum pernah menerima bantuan</p>';
    const anggota=(d.anggota_keluarga||[]).map(a=>`<div class="pi">
      <div class="pi-info"><strong>${a.nama}</strong><span>${a.umur||'?'}th · ${a.status_keluarga} · ${a.pekerjaan||'–'}</span></div>
      <span class="bdg bdg-${a.status_ekonomi}">${a.status_ekonomi}</span></div>`).join('')||'<p style="color:var(--muted);font-size:12px">Tidak ada anggota lain</p>';
    openModal('Detail Penduduk',`
      <div class="tabs">
        <button class="tb active" onclick="switchTabM('tp-info',this)">Info Pribadi</button>
        <button class="tb" onclick="switchTabM('tp-kel',this)">Keluarga</button>
        <button class="tb" onclick="switchTabM('tp-ban',this)">Riwayat Bantuan</button>
      </div>
      <div id="tp-info" class="tp active">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:18px">
          <div style="width:44px;height:44px;background:var(--card2);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:22px;flex-shrink:0">${d.jenis_kelamin==='L'?'👨':'👩'}</div>
          <div><div style="font-size:18px;font-weight:800">${d.nama} <span class="bdg bdg-${d.status_ekonomi}">${d.status_ekonomi}</span></div>
          <div style="font-size:11px;color:var(--muted)">NIK: ${d.nik} · ${d.umur||'?'} tahun</div></div>
        </div>
        <div class="dg">
          <div class="di"><label>Pekerjaan</label><span>${d.pekerjaan||'–'}</span></div>
          <div class="di"><label>Penghasilan</label><span>Rp ${Number(d.penghasilan||0).toLocaleString('id')}</span></div>
          <div class="di"><label>Status Kawin</label><span>${d.status_perkawinan||'–'}</span></div>
          <div class="di"><label>Status Hidup</label><span>${d.status_hidup||'–'}</span></div>
          <div class="di"><label>Pendidikan</label><span>${d.pendidikan_terakhir||'–'}</span></div>
          <div class="di"><label>Status Pend.</label><span>${d.status_pendidikan||'–'}</span></div>
          <div class="di"><label>RT/RW</label><span>${d.rt||'–'}/${d.rw||'–'}</span></div>
          <div class="di"><label>Kelurahan</label><span>${d.kelurahan||'–'}</span></div>
          <div class="di"><label>No HP</label><span>${d.no_hp||'–'}</span></div>
          <div class="di"><label>Rumah Ibadah</label><span>${d.nama_ibadah||'–'}</span></div>
        </div>
        <div class="btn-row">
          <button class="btn btn-ghost btn-sm" onclick="openEditPenduduk(${d.id})"><i class="fas fa-edit"></i> Edit</button>
          <button class="btn btn-success btn-sm" onclick="closeModal();openSalurModal(${d.id},'${esc(d.nama)}')"><i class="fas fa-gift"></i> Salurkan Bantuan</button>
          <button class="btn btn-danger btn-sm" onclick="hapusPenduduk(${d.id})"><i class="fas fa-trash"></i> Hapus</button>
          ${d.lat?`<button class="btn btn-ghost btn-sm" onclick="closeModal();goPage('peta');setTimeout(()=>map.flyTo([${d.lat},${d.lng}],17),200)"><i class="fas fa-map-marker-alt"></i> Lihat di Peta</button>`:''}
        </div>
      </div>
      <div id="tp-kel" class="tp"><div class="sec-title">Anggota Keluarga (KK: ${d.no_kk||'–'})</div>${anggota}</div>
      <div id="tp-ban" class="tp"><div class="sec-title">Riwayat Bantuan (${(d.histori_bantuan||[]).length}x diterima)</div>${histori}</div>
    `);
  }).catch(e=>toast('Gagal memuat detail','err'));
}

function openEditPenduduk(id){
  fetch(`penduduk.php?aksi=detail&id=${id}`).then(r=>r.json()).then(d=>{
    openModal('Edit Data Penduduk',`
      <form onsubmit="updatePenduduk(event,${id})">
      <div class="fg2">
        <div class="fg"><label>Nama</label><input name="nama" value="${esc(d.nama)}" required></div>
        <div class="fg"><label>Jenis Kelamin</label><select name="jenis_kelamin"><option value="L" ${d.jenis_kelamin==='L'?'selected':''}>Laki-laki</option><option value="P" ${d.jenis_kelamin==='P'?'selected':''}>Perempuan</option></select></div>
      </div>
      <div class="fg2">
        <div class="fg"><label>Tanggal Lahir</label><input type="date" name="tanggal_lahir" value="${d.tanggal_lahir}"></div>
        <div class="fg"><label>Status Hidup</label><select name="status_hidup"><option value="hidup" ${d.status_hidup==='hidup'?'selected':''}>Hidup</option><option value="meninggal" ${d.status_hidup==='meninggal'?'selected':''}>Meninggal</option></select></div>
      </div>
      <div class="fg2">
        <div class="fg"><label>Status Kawin</label><select name="status_perkawinan"><option value="belum_kawin" ${d.status_perkawinan==='belum_kawin'?'selected':''}>Belum Kawin</option><option value="kawin" ${d.status_perkawinan==='kawin'?'selected':''}>Kawin</option><option value="cerai_hidup" ${d.status_perkawinan==='cerai_hidup'?'selected':''}>Cerai Hidup</option><option value="cerai_mati" ${d.status_perkawinan==='cerai_mati'?'selected':''}>Cerai Mati</option></select></div>
        <div class="fg"><label>Status Keluarga</label><select name="status_keluarga"><option value="anggota" ${d.status_keluarga==='anggota'?'selected':''}>Anggota</option><option value="kepala_keluarga" ${d.status_keluarga==='kepala_keluarga'?'selected':''}>Kepala Keluarga</option></select></div>
      </div>
      <div class="fg2">
        <div class="fg"><label>Pekerjaan</label><input name="pekerjaan" value="${esc(d.pekerjaan||'')}"></div>
        <div class="fg"><label>Penghasilan</label><input type="number" name="penghasilan" value="${d.penghasilan||0}"></div>
      </div>
      <div class="fg2">
        <div class="fg"><label>Status Ekonomi</label><select name="status_ekonomi"><option value="miskin" ${d.status_ekonomi==='miskin'?'selected':''}>Miskin</option><option value="rentan" ${d.status_ekonomi==='rentan'?'selected':''}>Rentan</option><option value="mampu" ${d.status_ekonomi==='mampu'?'selected':''}>Mampu</option></select></div>
        <div class="fg"><label>Pendidikan</label><select name="pendidikan_terakhir"><option value="tidak_sekolah" ${d.pendidikan_terakhir==='tidak_sekolah'?'selected':''}>Tidak Sekolah</option><option value="SD" ${d.pendidikan_terakhir==='SD'?'selected':''}>SD</option><option value="SMP" ${d.pendidikan_terakhir==='SMP'?'selected':''}>SMP</option><option value="SMA" ${d.pendidikan_terakhir==='SMA'?'selected':''}>SMA</option><option value="D3" ${d.pendidikan_terakhir==='D3'?'selected':''}>D3</option><option value="S1" ${d.pendidikan_terakhir==='S1'?'selected':''}>S1</option><option value="S2" ${d.pendidikan_terakhir==='S2'?'selected':''}>S2</option></select></div>
      </div>
      <div class="fg2">
        <div class="fg"><label>Status Pendidikan</label><select name="status_pendidikan"><option value="sekolah" ${d.status_pendidikan==='sekolah'?'selected':''}>Masih Sekolah</option><option value="lulus" ${d.status_pendidikan==='lulus'?'selected':''}>Lulus</option><option value="tidak_sekolah" ${d.status_pendidikan==='tidak_sekolah'?'selected':''}>Tidak Sekolah</option></select></div>
        <div class="fg"><label>No HP</label><input name="no_hp" value="${esc(d.no_hp||'')}"></div>
      </div>
      <div class="fg"><label>Jenis Bantuan Dibutuhkan</label><input name="jenis_bantuan" value="${esc(d.jenis_bantuan||'')}"></div>
      <div class="btn-row">
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Perubahan</button>
        <button type="button" class="btn btn-ghost" onclick="closeModal()">Batal</button>
      </div>
      </form>
    `);
  });
}

function updatePenduduk(e,id){
  e.preventDefault();
  const form=new FormData(e.target);
  form.append('aksi','edit'); form.append('id',id);
  fetch('penduduk.php',{method:'POST',body:form}).then(r=>r.json()).then(d=>{
    if(d.error){toast(d.error,'err');return;}
    if(d.success){toast('Data berhasil diperbarui ✅');closeModal();loadPenduduk();loadMapData();}
  }).catch(e=>toast('Gagal','err'));
}

function hapusPenduduk(id){
  if(!confirm('Yakin hapus penduduk ini beserta semua data terkait?')) return;
  fetch('penduduk.php',{method:'POST',body:new URLSearchParams({aksi:'hapus',id})}).then(r=>r.json()).then(d=>{
    if(d.success){toast('Data dihapus');closeModal();loadPenduduk();loadMapData();loadMiniStats();}
    else toast(d.error||'Gagal','err');
  });
}

// =========================================================
// BANTUAN
// =========================================================
function loadMasterBantuan(){
  fetch('bantuan.php?aksi=list_bantuan').then(r=>r.json()).then(data=>{
    document.getElementById('master-ban-tbody').innerHTML=data.length?data.map(b=>`<tr>
      <td><strong>${b.nama}</strong></td>
      <td><span class="bdg bdg-sedang">${b.jenis}</span></td>
      <td>${b.sumber||'–'}</td>
      <td>${b.bentuk||'–'}</td>
      <td>
        <button class="btn btn-ghost btn-sm" onclick="openEditMasterBantuan(${b.id},'${esc(b.nama)}','${b.jenis}','${esc(b.sumber||'')}','${esc(b.bentuk||'')}')"><i class="fas fa-edit"></i></button>
        <button class="btn btn-danger btn-sm" onclick="hapusMasterBantuan(${b.id})"><i class="fas fa-trash"></i></button>
      </td></tr>`).join('')
      :'<tr><td colspan="5" style="text-align:center;color:var(--muted);padding:20px">Tidak ada data</td></tr>';
  });
}

function openMasterBantuanModal(data=null){
  openModal(data?'Edit Jenis Bantuan':'Tambah Jenis Bantuan',`
    <form onsubmit="saveMasterBantuan(event,${data?data.id:'null'})">
    <div class="fg"><label>Nama Bantuan *</label><input name="nama" required value="${data?esc(data.nama):''}" placeholder="cth: Sembako Bulanan"></div>
    <div class="fg2">
      <div class="fg"><label>Jenis</label><select name="jenis">
        ${['sembako','pendidikan','kesehatan','ekonomi','perumahan','lainnya'].map(j=>`<option value="${j}" ${data&&data.jenis===j?'selected':''}>${j}</option>`).join('')}
      </select></div>
      <div class="fg"><label>Sumber</label><input name="sumber" value="${data?esc(data.sumber||''):''}" placeholder="Pemerintah/Donatur/Ibadah"></div>
    </div>
    <div class="fg"><label>Bentuk Bantuan</label><input name="bentuk" value="${data?esc(data.bentuk||''):''}" placeholder="cth: 10kg beras/bulan"></div>
    <div class="btn-row">
      <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
      <button type="button" class="btn btn-ghost" onclick="closeModal()">Batal</button>
    </div>
    </form>
  `);
}

function openEditMasterBantuan(id,nama,jenis,sumber,bentuk){
  openMasterBantuanModal({id,nama,jenis,sumber,bentuk});
}

function saveMasterBantuan(e,id){
  e.preventDefault();
  const form=new FormData(e.target);
  form.append('aksi', id?'edit_master':'simpan_master');
  if(id) form.append('id',id);
  fetch('bantuan.php',{method:'POST',body:form}).then(r=>r.json()).then(d=>{
    if(d.error){toast(d.error,'err');return;}
    toast('Jenis bantuan disimpan ✅');closeModal();loadMasterBantuan();
  });
}

function hapusMasterBantuan(id){
  if(!confirm('Hapus jenis bantuan ini? Semua histori penyalurannya juga akan terhapus!')) return;
  fetch('bantuan.php',{method:'POST',body:new URLSearchParams({aksi:'hapus_master',id})}).then(r=>r.json()).then(d=>{
    if(d.success){toast('Dihapus');loadMasterBantuan();} else toast(d.error||'Gagal','err');
  });
}

function loadHistori(){
  const params=new URLSearchParams({aksi:'histori'});
  const s=document.getElementById('f-his-status')?.value;
  const dari=document.getElementById('f-his-dari')?.value;
  const smp=document.getElementById('f-his-sampai')?.value;
  const q=document.getElementById('f-his-q')?.value;
  if(s) params.append('status',s);
  if(dari) params.append('tgl_dari',dari);
  if(smp) params.append('tgl_sampai',smp);
  fetch('bantuan.php?'+params).then(r=>r.json()).then(data=>{
    document.getElementById('histori-tbody').innerHTML=data.length?data.map(h=>`<tr>
      <td style="font-family:monospace;font-size:11px">${h.nik||'–'}</td>
      <td>${h.nama_penduduk||'–'}</td>
      <td>${h.nama_bantuan}</td>
      <td>${h.tanggal}</td>
      <td>${h.jumlah||'–'}</td>
      <td><span class="bdg bdg-${h.status}">${h.status}</span></td>
      <td>
        <button class="btn btn-danger btn-sm" onclick="hapusHistori(${h.id})"><i class="fas fa-trash"></i></button>
      </td></tr>`).join('')
      :'<tr><td colspan="7" style="text-align:center;color:var(--muted);padding:20px">Tidak ada data</td></tr>';
  });
}

function hapusHistori(id){
  if(!confirm('Hapus riwayat bantuan ini?')) return;
  fetch('bantuan.php',{method:'POST',body:new URLSearchParams({aksi:'hapus_histori',id})}).then(r=>r.json()).then(d=>{
    if(d.success){toast('Dihapus');loadHistori();} else toast(d.error||'Gagal','err');
  });
}

function loadBelumDibantu(){
  fetch('bantuan.php?aksi=belum_dibantu').then(r=>r.json()).then(data=>{
    document.getElementById('belum-list').innerHTML=data.length?data.map(d=>`
      <div class="pc" onclick="openSalurModal(${d.id},'${esc(d.nama)}')">
        <div class="pc-name">${d.nama} <span class="bdg bdg-${d.status_ekonomi}">${d.status_ekonomi}</span></div>
        <div class="pc-meta"><span>${d.umur||'?'}th</span><span>${d.nama_ibadah||'–'}</span><span>${d.kelurahan||'–'}</span></div>
        <div style="margin-top:6px;font-size:11px;color:var(--accent)"><i class="fas fa-gift"></i> Klik untuk salurkan bantuan</div>
      </div>`).join('')
      :'<p style="color:var(--muted);font-size:13px;text-align:center;margin-top:20px">🎉 Semua warga miskin/rentan sudah menerima bantuan!</p>';
  });
}

function loadStatBantuan(){
  fetch('bantuan.php?aksi=statistik').then(r=>r.json()).then(data=>{
    document.getElementById('stat-ban-tbody').innerHTML=data.length?data.map(d=>`<tr>
      <td><strong>${d.nama}</strong></td><td><span class="bdg bdg-sedang">${d.jenis}</span></td>
      <td>${d.total_salur||0}</td><td>${d.berhasil||0}</td></tr>`).join('')
      :'<tr><td colspan="4" style="text-align:center;color:var(--muted);padding:20px">Tidak ada data</td></tr>';
  });
}

function openSalurModal(pid,nama){
  fetch('bantuan.php?aksi=list_bantuan').then(r=>r.json()).then(list=>{
    openModal('Salurkan Bantuan',`
      <p style="margin-bottom:14px;font-size:14px">Penerima: <strong>${nama}</strong></p>
      <form onsubmit="saveSalur(event,${pid})">
      <div class="fg"><label>Jenis Bantuan *</label><select name="bantuan_id" required>
        ${list.map(b=>`<option value="${b.id}">${b.nama} (${b.jenis})</option>`).join('')}
      </select></div>
      <div class="fg2">
        <div class="fg"><label>Tanggal *</label><input type="date" name="tanggal" value="${new Date().toISOString().split('T')[0]}" required></div>
        <div class="fg"><label>Jumlah/Detail</label><input name="jumlah" placeholder="cth: 10kg beras"></div>
      </div>
      <div class="fg"><label>Keterangan</label><textarea name="keterangan" rows="2" placeholder="Catatan tambahan"></textarea></div>
      <div class="btn-row">
        <button type="submit" class="btn btn-success"><i class="fas fa-check"></i> Salurkan</button>
        <button type="button" class="btn btn-ghost" onclick="closeModal()">Batal</button>
      </div>
      </form>
    `);
  });
}

function saveSalur(e,pid){
  e.preventDefault();
  const form=new FormData(e.target);
  form.append('aksi','salurkan'); form.append('penduduk_id',pid);
  fetch('bantuan.php',{method:'POST',body:form}).then(r=>r.json()).then(d=>{
    if(d.error){toast(d.error,'err');return;}
    toast('Bantuan berhasil disalurkan! ✅');closeModal();loadHistori();loadBelumDibantu();loadStatBantuan();
  });
}

// =========================================================
// LAPORAN
// =========================================================
function loadLaporan(){
  const params=new URLSearchParams({aksi:'list'});
  const s=document.getElementById('f-lap-status')?.value;
  const u=document.getElementById('f-lap-urgensi')?.value;
  const q=document.getElementById('f-lap-q')?.value;
  if(s) params.append('status',s);
  if(u) params.append('urgensi',u);
  if(q) params.append('q',q);
  fetch('laporan.php?'+params).then(r=>r.json()).then(data=>{
    const el=document.getElementById('laporan-list');
    if(!data.length){el.innerHTML='<p style="color:var(--muted);text-align:center;margin-top:30px">Tidak ada laporan</p>';return;}
    el.innerHTML=data.map(l=>`<div class="lc">
      <div class="lc-top">
        <span class="bdg bdg-${l.urgensi}">${l.urgensi?.toUpperCase()}</span>
        <span class="bdg bdg-${l.status}">${l.status}</span>
        ${l.nama_penduduk?`<span style="font-size:11px;color:var(--muted)">📌 ${l.nama_penduduk}</span>`:''}
      </div>
      <div class="lc-desc">${l.deskripsi||''}</div>
      <div class="lc-meta">
        <span>👤 ${l.pelapor||'Anonim'}</span>
        <span>📅 ${new Date(l.created_at).toLocaleDateString('id')}</span>
        ${l.lat?`<span>📍 ${parseFloat(l.lat).toFixed(4)}, ${parseFloat(l.lng).toFixed(4)}</span>`:''}
      </div>
      <div class="lc-acts">
        <button class="btn btn-ghost btn-sm" onclick="openEditLaporan(${l.id})"><i class="fas fa-edit"></i> Edit</button>
        ${l.status!=='diverifikasi'&&l.status!=='selesai'?`<button class="btn btn-ghost btn-sm" onclick="setStatusLap(${l.id},'diverifikasi')">✅ Verifikasi</button>`:''}
        ${l.status!=='diproses'&&l.status!=='selesai'?`<button class="btn btn-ghost btn-sm" onclick="setStatusLap(${l.id},'diproses')">🔄 Proses</button>`:''}
        ${l.status!=='selesai'?`<button class="btn btn-success btn-sm" onclick="setStatusLap(${l.id},'selesai')">✔ Selesai</button>`:''}
        <button class="btn btn-danger btn-sm" onclick="hapusLaporan(${l.id})"><i class="fas fa-trash"></i></button>
        ${l.lat?`<button class="btn btn-ghost btn-sm" onclick="closeModal();goPage('peta');setTimeout(()=>map.flyTo([${l.lat},${l.lng}],17),200)"><i class="fas fa-map-marker-alt"></i> Peta</button>`:''}
      </div>
    </div>`).join('');
    // update badge
    const pending=data.filter(l=>l.status==='pending').length;
    document.getElementById('dot-laporan').style.display=pending?'block':'none';
  }).catch(e=>toast('Gagal memuat laporan','err'));
}

function openLaporModal(){
  openModal('Buat Laporan Kondisi',`
    <form onsubmit="saveLaporan(event)">
    <div class="fg"><label>Nama Pelapor</label><input name="pelapor" placeholder="Anonim jika dikosongkan"></div>
    <div class="fg"><label>Deskripsi Kondisi *</label><textarea name="deskripsi" rows="4" required placeholder="Jelaskan kondisi yang perlu dilaporkan secara detail..."></textarea></div>
    <div class="fg2">
      <div class="fg"><label>Tingkat Urgensi</label><select name="urgensi">
        <option value="rendah">🟢 Rendah</option><option value="sedang" selected>🟡 Sedang</option>
        <option value="tinggi">🔴 Tinggi</option><option value="darurat">🆘 DARURAT</option>
      </select></div>
      <div class="fg"><label>ID Penduduk Terkait</label><input type="number" name="penduduk_id" placeholder="Opsional"></div>
    </div>
    <div class="fg2">
      <div class="fg"><label>Latitude</label><input name="lat" id="lap-lat" placeholder="0.0262" type="number" step="any"></div>
      <div class="fg"><label>Longitude</label><input name="lng" id="lap-lng" placeholder="109.342" type="number" step="any"></div>
    </div>
    <p class="coord-note"><i class="fas fa-info-circle"></i> Klik kanan di peta (tutup modal dulu) untuk koordinat otomatis</p>
    <div class="btn-row">
      <button type="submit" class="btn btn-danger"><i class="fas fa-flag"></i> Kirim Laporan</button>
      <button type="button" class="btn btn-ghost" onclick="closeModal()">Batal</button>
    </div>
    </form>
  `);
}

function saveLaporan(e){
  e.preventDefault();
  const form=new FormData(e.target);
  form.append('aksi','kirim');
  fetch('laporan.php',{method:'POST',body:form}).then(r=>r.json()).then(d=>{
    if(d.error){toast(d.error,'err');return;}
    toast('Laporan terkirim! ✅');closeModal();loadLaporan();loadMapData();loadMiniStats();
  });
}

function openEditLaporan(id){
  fetch(`laporan.php?aksi=detail&id=${id}`).then(r=>r.json()).then(d=>{
    openModal('Edit Laporan',`
      <form onsubmit="updateLaporan(event,${id})">
      <div class="fg"><label>Nama Pelapor</label><input name="pelapor" value="${esc(d.pelapor||'')}"></div>
      <div class="fg"><label>Deskripsi *</label><textarea name="deskripsi" rows="4" required>${esc(d.deskripsi||'')}</textarea></div>
      <div class="fg2">
        <div class="fg"><label>Urgensi</label><select name="urgensi">
          ${['rendah','sedang','tinggi','darurat'].map(u=>`<option value="${u}" ${d.urgensi===u?'selected':''}>${u}</option>`).join('')}
        </select></div>
        <div class="fg"><label>Status</label><select name="status">
          ${['pending','diverifikasi','diproses','selesai'].map(s=>`<option value="${s}" ${d.status===s?'selected':''}>${s}</option>`).join('')}
        </select></div>
      </div>
      <div class="btn-row">
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
        <button type="button" class="btn btn-ghost" onclick="closeModal()">Batal</button>
      </div>
      </form>
    `);
  });
}

function updateLaporan(e,id){
  e.preventDefault();
  const form=new FormData(e.target);
  form.append('aksi','edit'); form.append('id',id);
  fetch('laporan.php',{method:'POST',body:form}).then(r=>r.json()).then(d=>{
    if(d.success){toast('Laporan diperbarui ✅');closeModal();loadLaporan();}
    else toast(d.error||'Gagal','err');
  });
}

function setStatusLap(id,status){
  fetch('laporan.php',{method:'POST',body:new URLSearchParams({aksi:'update_status',id,status})}).then(r=>r.json()).then(d=>{
    if(d.success){toast('Status diperbarui');loadLaporan();} else toast('Gagal','err');
  });
}

function hapusLaporan(id){
  if(!confirm('Hapus laporan ini?')) return;
  fetch('laporan.php',{method:'POST',body:new URLSearchParams({aksi:'hapus',id})}).then(r=>r.json()).then(d=>{
    if(d.success){toast('Laporan dihapus');loadLaporan();loadMapData();}
  });
}

// =========================================================
// RUMAH IBADAH
// =========================================================
function loadIbadah(){
  fetch('ibadah.php?aksi=list').then(r=>r.json()).then(data=>{
    document.getElementById('ibadah-tbody').innerHTML=data.length?data.map(d=>`<tr>
      <td><strong>${d.nama}</strong></td>
      <td>${d.jenis}</td><td>${d.kontak||'–'}</td><td>${d.alamat||'–'}</td>
      <td>${d.total_penduduk||0}</td><td>${d.total_miskin||0}</td>
      <td>
        <button class="btn btn-ghost btn-sm" onclick="openEditIbadah(${d.id},'${esc(d.nama)}','${d.jenis}','${esc(d.kontak||'')}','${esc(d.alamat||'')}',${d.radius||500})"><i class="fas fa-edit"></i></button>
        <button class="btn btn-danger btn-sm" onclick="hapusIbadah(${d.id})"><i class="fas fa-trash"></i></button>
        <button class="btn btn-ghost btn-sm" onclick="closeModal();goPage('peta');setTimeout(()=>map.flyTo([${d.lat},${d.lng}],16),200)"><i class="fas fa-map-marker-alt"></i></button>
      </td></tr>`).join('')
      :'<tr><td colspan="7" style="text-align:center;color:var(--muted);padding:20px">Tidak ada data</td></tr>';
  });
}

function openIbadahModal(data=null){
  openModal(data?'Edit Rumah Ibadah':'Tambah Rumah Ibadah',`
    <form onsubmit="saveIbadah(event,${data?data.id:'null'})">
    <div class="fg"><label>Nama *</label><input name="nama" required value="${data?esc(data.nama):''}" placeholder="Nama rumah ibadah"></div>
    <div class="fg2">
      <div class="fg"><label>Jenis</label><select name="jenis">
        ${['masjid','gereja','pura','vihara','klenteng','lainnya'].map(j=>`<option value="${j}" ${data&&data.jenis===j?'selected':''}>${j}</option>`).join('')}
      </select></div>
      <div class="fg"><label>Kontak</label><input name="kontak" value="${data?esc(data.kontak||''):''}" placeholder="No HP"></div>
    </div>
    <div class="fg"><label>Alamat</label><input name="alamat" value="${data?esc(data.alamat||''):''}" placeholder="Alamat lengkap"></div>
    ${!data?`<div class="fg2">
      <div class="fg"><label>Latitude *</label><input name="lat" id="ib-lat" required placeholder="0.0262" type="number" step="any" oninput="syncIbadahPickerFromInput()"></div>
      <div class="fg"><label>Longitude *</label><input name="lng" id="ib-lng" required placeholder="109.342" type="number" step="any" oninput="syncIbadahPickerFromInput()"></div>
    </div>
    <div style="position:relative;margin-bottom:14px">
      <div style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.4px;margin-bottom:6px">
        <i class="fas fa-map-marker-alt" style="color:var(--accent)"></i> Pilih Lokasi di Peta
      </div>
      <div id="ibadah-picker-map" style="height:260px;border-radius:8px;border:1px solid var(--border);overflow:hidden;background:var(--card)"></div>
      <div id="ibadah-picker-badge" style="display:none;position:absolute;bottom:36px;left:50%;transform:translateX(-50%);background:rgba(22,27,34,.92);backdrop-filter:blur(8px);border:1px solid var(--accent);border-radius:20px;padding:4px 12px;font-size:11px;font-weight:700;color:var(--accent);font-family:'JetBrains Mono',monospace;white-space:nowrap;pointer-events:none;z-index:800"></div>
      <div style="display:flex;gap:6px;margin-top:7px">
        <button type="button" class="btn btn-ghost btn-sm" onclick="locateMeOnIbadahPicker()"><i class="fas fa-crosshairs"></i> Lokasi Saya</button>
        <button type="button" class="btn btn-ghost btn-sm" onclick="resetIbadahPickerMarker()"><i class="fas fa-times"></i> Reset Titik</button>
        <span style="font-size:11px;color:var(--muted);align-self:center;margin-left:4px"><i class="fas fa-info-circle"></i> Klik peta atau drag marker</span>
      </div>
    </div>`:''}
    <div class="fg"><label>Radius Wilayah (meter)</label><input type="number" name="radius" value="${data?data.radius:500}" min="100" max="5000"></div>
    <div class="btn-row">
      <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
      <button type="button" class="btn btn-ghost" onclick="closeModal()">Batal</button>
    </div>
    </form>
  `);
  if(!data) setTimeout(()=>initIbadahPickerMap(),80);
}

function openEditIbadah(id,nama,jenis,kontak,alamat,radius){
  openIbadahModal({id,nama,jenis,kontak,alamat,radius});
}

function saveIbadah(e,id){
  e.preventDefault();
  const form=new FormData(e.target);
  form.append('aksi',id?'edit':'simpan');
  if(id) form.append('id',id);
  fetch('ibadah.php',{method:'POST',body:form}).then(r=>r.json()).then(d=>{
    if(d.error){toast(d.error,'err');return;}
    toast('Rumah ibadah disimpan ✅');closeModal();loadIbadah();loadMapData();
  });
}

function hapusIbadah(id){
  if(!confirm('Hapus rumah ibadah ini?')) return;
  fetch('ibadah.php',{method:'POST',body:new URLSearchParams({aksi:'hapus',id})}).then(r=>r.json()).then(d=>{
    if(d.success){toast('Dihapus');loadIbadah();loadMapData();}
  });
}

// ── IBADAH PICKER MAP FUNCTIONS ───────────────────────────
function initIbadahPickerMap(){
  const el=document.getElementById('ibadah-picker-map');
  if(!el) return;
  if(ibadahPickerMap){ ibadahPickerMap.remove(); ibadahPickerMap=null; ibadahPickerMarker=null; }
  const defLat=parseFloat(document.getElementById('ib-lat')?.value)||0.0262;
  const defLng=parseFloat(document.getElementById('ib-lng')?.value)||109.3423;
  const zoom=(document.getElementById('ib-lat')?.value&&document.getElementById('ib-lat').value!=='')?16:13;
  ibadahPickerMap=L.map('ibadah-picker-map',{zoomControl:true,attributionControl:false}).setView([defLat,defLng],zoom);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{maxZoom:19}).addTo(ibadahPickerMap);
  if(document.getElementById('ib-lat')?.value && document.getElementById('ib-lat').value!==''){
    ibadahPickerMarker=L.marker([defLat,defLng],{draggable:true}).addTo(ibadahPickerMap);
    ibadahPickerMarker.on('dragend',()=>updateIbadahPickerCoord(ibadahPickerMarker.getLatLng()));
    showIbadahPickerBadge(defLat,defLng);
  }
  ibadahPickerMap.on('click',e=>{
    const {lat,lng}=e.latlng;
    if(ibadahPickerMarker) ibadahPickerMarker.setLatLng([lat,lng]);
    else { ibadahPickerMarker=L.marker([lat,lng],{draggable:true}).addTo(ibadahPickerMap); ibadahPickerMarker.on('dragend',()=>updateIbadahPickerCoord(ibadahPickerMarker.getLatLng())); }
    updateIbadahPickerCoord({lat,lng});
  });
  setTimeout(()=>ibadahPickerMap.invalidateSize(),120);
}
function updateIbadahPickerCoord(latlng){
  const lat=parseFloat(latlng.lat).toFixed(6);
  const lng=parseFloat(latlng.lng).toFixed(6);
  const elLat=document.getElementById('ib-lat');
  const elLng=document.getElementById('ib-lng');
  if(elLat) elLat.value=lat;
  if(elLng) elLng.value=lng;
  showIbadahPickerBadge(lat,lng);
}
function showIbadahPickerBadge(lat,lng){
  const b=document.getElementById('ibadah-picker-badge');
  if(b){b.style.display='block';b.textContent=parseFloat(lat).toFixed(5)+', '+parseFloat(lng).toFixed(5);}
}
function syncIbadahPickerFromInput(){
  if(!ibadahPickerMap) return;
  const lat=parseFloat(document.getElementById('ib-lat')?.value);
  const lng=parseFloat(document.getElementById('ib-lng')?.value);
  if(!lat||!lng) return;
  if(ibadahPickerMarker) ibadahPickerMarker.setLatLng([lat,lng]);
  else { ibadahPickerMarker=L.marker([lat,lng],{draggable:true}).addTo(ibadahPickerMap); ibadahPickerMarker.on('dragend',()=>updateIbadahPickerCoord(ibadahPickerMarker.getLatLng())); }
  ibadahPickerMap.setView([lat,lng],16);
  showIbadahPickerBadge(lat,lng);
}
function locateMeOnIbadahPicker(){
  if(!ibadahPickerMap){toast('Peta belum siap','warn');return;}
  if(!navigator.geolocation){toast('Browser tidak mendukung geolokasi','err');return;}
  navigator.geolocation.getCurrentPosition(pos=>{
    const lat=pos.coords.latitude, lng=pos.coords.longitude;
    ibadahPickerMap.setView([lat,lng],17);
    if(ibadahPickerMarker) ibadahPickerMarker.setLatLng([lat,lng]);
    else { ibadahPickerMarker=L.marker([lat,lng],{draggable:true}).addTo(ibadahPickerMap); ibadahPickerMarker.on('dragend',()=>updateIbadahPickerCoord(ibadahPickerMarker.getLatLng())); }
    updateIbadahPickerCoord({lat,lng});
    toast('Lokasi ditemukan','ok');
  },()=>toast('Gagal mendapatkan lokasi','err'));
}
function resetIbadahPickerMarker(){
  if(ibadahPickerMarker&&ibadahPickerMap){ibadahPickerMap.removeLayer(ibadahPickerMarker);ibadahPickerMarker=null;}
  const b=document.getElementById('ibadah-picker-badge');
  if(b) b.style.display='none';
  const elLat=document.getElementById('ib-lat');
  const elLng=document.getElementById('ib-lng');
  if(elLat) elLat.value='';
  if(elLng) elLng.value='';
}

// =========================================================
// USER
// =========================================================
function loadUsers(){
  fetch('user.php?aksi=list').then(r=>r.json()).then(data=>{
    document.getElementById('user-tbody').innerHTML=data.length?data.map(u=>`<tr>
      <td><strong>${u.nama}</strong></td>
      <td>${u.email}</td>
      <td><span class="bdg bdg-${u.role_nama==='pengurus'?'diproses':u.role_nama==='pimpinan'?'sedang':'pending'}">${u.role_nama}</span></td>
      <td><span class="bdg ${u.aktif?'bdg-disalurkan':'bdg-ditolak'}">${u.aktif?'Aktif':'Nonaktif'}</span></td>
      <td>${new Date(u.created_at).toLocaleDateString('id')}</td>
      <td>
        <button class="btn btn-ghost btn-sm" onclick="openEditUser(${u.id},'${esc(u.nama)}','${u.email}',${u.role_id},${u.aktif})"><i class="fas fa-edit"></i></button>
        <button class="btn btn-danger btn-sm" onclick="hapusUser(${u.id})"><i class="fas fa-trash"></i></button>
      </td></tr>`).join('')
      :'<tr><td colspan="6" style="text-align:center;color:var(--muted);padding:20px">Tidak ada user</td></tr>';
  });
}

function openUserModal(){
  openModal('Tambah Pengguna',`
    <form onsubmit="saveUser(event,null)">
    <div class="fg2">
      <div class="fg"><label>Nama Lengkap *</label><input name="nama" required placeholder="Nama lengkap"></div>
      <div class="fg"><label>Email *</label><input type="email" name="email" required placeholder="email@domain.com"></div>
    </div>
    <div class="fg2">
      <div class="fg"><label>Password *</label><input type="password" name="password" required placeholder="Minimal 8 karakter"></div>
      <div class="fg"><label>Role</label><select name="role_id">
        <option value="1">Masyarakat</option><option value="2" selected>Pengurus</option><option value="3">Pimpinan Daerah</option>
      </select></div>
    </div>
    <div class="btn-row">
      <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Tambah</button>
      <button type="button" class="btn btn-ghost" onclick="closeModal()">Batal</button>
    </div>
    </form>
  `);
}

function openEditUser(id,nama,email,role_id,aktif){
  openModal('Edit Pengguna',`
    <form onsubmit="saveUser(event,${id})">
    <div class="fg2">
      <div class="fg"><label>Nama</label><input name="nama" value="${esc(nama)}" required></div>
      <div class="fg"><label>Email</label><input type="email" name="email" value="${email}" required></div>
    </div>
    <div class="fg2">
      <div class="fg"><label>Password Baru</label><input type="password" name="password" placeholder="Kosongkan jika tidak diubah"></div>
      <div class="fg"><label>Role</label><select name="role_id">
        <option value="1" ${role_id==1?'selected':''}>Masyarakat</option>
        <option value="2" ${role_id==2?'selected':''}>Pengurus</option>
        <option value="3" ${role_id==3?'selected':''}>Pimpinan Daerah</option>
      </select></div>
    </div>
    <div class="fg"><label>Status</label><select name="aktif"><option value="1" ${aktif?'selected':''}>Aktif</option><option value="0" ${!aktif?'selected':''}>Nonaktif</option></select></div>
    <div class="btn-row">
      <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
      <button type="button" class="btn btn-ghost" onclick="closeModal()">Batal</button>
    </div>
    </form>
  `);
}

function saveUser(e,id){
  e.preventDefault();
  const form=new FormData(e.target);
  form.append('aksi',id?'edit':'simpan');
  if(id) form.append('id',id);
  fetch('user.php',{method:'POST',body:form}).then(r=>r.json()).then(d=>{
    if(d.error){toast(d.error,'err');return;}
    toast('User disimpan ✅');closeModal();loadUsers();
  });
}

function hapusUser(id){
  if(!confirm('Hapus user ini?')) return;
  fetch('user.php',{method:'POST',body:new URLSearchParams({aksi:'hapus',id})}).then(r=>r.json()).then(d=>{
    if(d.success){toast('User dihapus');loadUsers();}
  });
}

// =========================================================
// CHAT
// =========================================================
let lastChatId=0;
function loadChat(){
  fetch('chat.php?aksi=inbox').then(r=>r.json()).then(msgs=>{
    const box=document.getElementById('chat-msgs');
    if(!msgs.length){box.innerHTML='<p style="color:var(--muted);font-size:12px;text-align:center;margin:auto">Belum ada pesan. Mulai diskusi!</p>';return;}
    const uid=currentUser?.id;
    box.innerHTML=msgs.map(m=>{
      const isMe=uid&&m.dari_user==uid;
      return `<div class="cm ${isMe?'me':''}">
        <div class="cm-who">${m.nama_pengirim} · ${new Date(m.created_at).toLocaleString('id',{hour:'2-digit',minute:'2-digit',day:'numeric',month:'short'})}</div>
        <div class="cm-bubble">${m.isi}</div>
      </div>`;
    }).join('');
    box.scrollTop=box.scrollHeight;
  }).catch(e=>console.warn('Chat error',e));
}

function sendChat(){
  const inp=document.getElementById('chat-inp');
  const isi=inp.value.trim();
  if(!isi) return;
  fetch('chat.php',{method:'POST',body:new URLSearchParams({aksi:'kirim',isi})}).then(r=>r.json()).then(d=>{
    if(d.success){inp.value='';loadChat();}
    else toast(d.error||'Gagal kirim pesan','err');
  });
}

function startUnreadPoll(){
  setInterval(()=>{
    fetch('chat.php?aksi=unread').then(r=>r.json()).then(d=>{
      document.getElementById('dot-chat').style.display=(d.count>0)?'block':'none';
    }).catch(()=>{});
    fetch('laporan.php?aksi=list&status=pending').then(r=>r.json()).then(d=>{
      document.getElementById('dot-laporan').style.display=(d.length>0)?'block':'none';
    }).catch(()=>{});
  },10000);
}

// =========================================================
// TAB HELPERS
// =========================================================
function switchTab(id,btn){
  // bantuan tabs
  const parent=btn.closest('.page-overlay')||document.getElementById('page-bantuan');
  parent.querySelectorAll('.tb').forEach(b=>b.classList.remove('active'));
  ['ban-master','ban-histori','ban-belum','ban-stat'].forEach(t=>{
    const el=document.getElementById('tp-'+t);
    if(el) el.classList.remove('active');
  });
  btn.classList.add('active');
  const el=document.getElementById('tp-'+id);
  if(el) el.classList.add('active');
}

function switchTabM(id,btn){
  // modal tabs
  btn.closest('.tabs').querySelectorAll('.tb').forEach(b=>b.classList.remove('active'));
  document.querySelectorAll('#modal .tp').forEach(p=>p.classList.remove('active'));
  btn.classList.add('active');
  document.getElementById(id)?.classList.add('active');
  if(id==='tp-lokasi') initPickerMap();
}

// ── PICKER MAP (mini peta di modal penduduk) ──────────────
let pickerMap=null, pickerMarker=null;

// ── IBADAH PICKER MAP ─────────────────────────────────────
let ibadahPickerMap=null, ibadahPickerMarker=null;
function initPickerMap(){
  const el=document.getElementById('picker-map');
  if(!el) return;
  if(pickerMap){ pickerMap.remove(); pickerMap=null; pickerMarker=null; }
  const defLat=parseFloat(document.getElementById('p-lat')?.value)||0.0262;
  const defLng=parseFloat(document.getElementById('p-lng')?.value)||109.3423;
  const zoom=(document.getElementById('p-lat')?.value&&document.getElementById('p-lat').value!=='')?16:13;
  pickerMap=L.map('picker-map',{zoomControl:true,attributionControl:false}).setView([defLat,defLng],zoom);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{maxZoom:19}).addTo(pickerMap);
  if(document.getElementById('p-lat')?.value && document.getElementById('p-lat').value!==''){
    pickerMarker=L.marker([defLat,defLng],{draggable:true}).addTo(pickerMap);
    pickerMarker.on('dragend',()=>updatePickerCoord(pickerMarker.getLatLng()));
    showPickerBadge(defLat,defLng);
  }
  pickerMap.on('click',e=>{
    const {lat,lng}=e.latlng;
    if(pickerMarker) pickerMarker.setLatLng([lat,lng]);
    else { pickerMarker=L.marker([lat,lng],{draggable:true}).addTo(pickerMap); pickerMarker.on('dragend',()=>updatePickerCoord(pickerMarker.getLatLng())); }
    updatePickerCoord({lat,lng});
  });
  setTimeout(()=>pickerMap.invalidateSize(),120);
}
function updatePickerCoord(latlng){
  const lat=parseFloat(latlng.lat).toFixed(6);
  const lng=parseFloat(latlng.lng).toFixed(6);
  const elLat=document.getElementById('p-lat');
  const elLng=document.getElementById('p-lng');
  if(elLat) elLat.value=lat;
  if(elLng) elLng.value=lng;
  showPickerBadge(lat,lng);
}
function showPickerBadge(lat,lng){
  const b=document.getElementById('picker-coord-badge');
  if(b){b.style.display='block';b.textContent=parseFloat(lat).toFixed(5)+', '+parseFloat(lng).toFixed(5);}
}
function syncPickerFromInput(){
  if(!pickerMap) return;
  const lat=parseFloat(document.getElementById('p-lat')?.value);
  const lng=parseFloat(document.getElementById('p-lng')?.value);
  if(!lat||!lng) return;
  if(pickerMarker) pickerMarker.setLatLng([lat,lng]);
  else { pickerMarker=L.marker([lat,lng],{draggable:true}).addTo(pickerMap); pickerMarker.on('dragend',()=>updatePickerCoord(pickerMarker.getLatLng())); }
  pickerMap.setView([lat,lng],16);
  showPickerBadge(lat,lng);
}
function locateMeOnPicker(){
  if(!pickerMap){toast('Buka tab Lokasi dulu','warn');return;}
  if(!navigator.geolocation){toast('Browser tidak mendukung geolokasi','err');return;}
  navigator.geolocation.getCurrentPosition(pos=>{
    const lat=pos.coords.latitude, lng=pos.coords.longitude;
    pickerMap.setView([lat,lng],17);
    if(pickerMarker) pickerMarker.setLatLng([lat,lng]);
    else { pickerMarker=L.marker([lat,lng],{draggable:true}).addTo(pickerMap); pickerMarker.on('dragend',()=>updatePickerCoord(pickerMarker.getLatLng())); }
    updatePickerCoord({lat,lng});
    toast('Lokasi ditemukan','ok');
  },()=>toast('Gagal mendapatkan lokasi','err'));
}
function resetPickerMarker(){
  if(pickerMarker&&pickerMap){pickerMap.removeLayer(pickerMarker);pickerMarker=null;}
  const b=document.getElementById('picker-coord-badge');
  if(b) b.style.display='none';
  const elLat=document.getElementById('p-lat');
  const elLng=document.getElementById('p-lng');
  if(elLat) elLat.value='';
  if(elLng) elLng.value='';
}

// =========================================================
// MODAL
// =========================================================
function openModal(title,body){
  document.getElementById('modal').innerHTML=`
    <div class="mh"><h2>${title}</h2><button class="mclose" onclick="closeModal()">×</button></div>
    <div class="mbody">${body}</div>`;
  document.getElementById('overlay').classList.add('show');
}
function closeModal(){
  document.getElementById('overlay').classList.remove('show');
  if(pickerMap){ pickerMap.remove(); pickerMap=null; pickerMarker=null; }
  if(ibadahPickerMap){ ibadahPickerMap.remove(); ibadahPickerMap=null; ibadahPickerMarker=null; }
}
document.addEventListener('keydown',e=>{if(e.key==='Escape')closeModal();});

// =========================================================
// TOAST
// =========================================================
function toast(msg,type='ok'){
  const icons={ok:'✅',err:'❌',warn:'⚠️'};
  const t=document.createElement('div');
  t.className=`toast ${type}`;
  t.innerHTML=`<span>${icons[type]||'ℹ️'}</span><span>${msg}</span>`;
  document.getElementById('toasts').appendChild(t);
  setTimeout(()=>t.remove(),3500);
}

// =========================================================
// UTIL
// =========================================================
function esc(s){return String(s||'').replace(/'/g,"\\'").replace(/"/g,'&quot;');}

// expose ibadah detail helper
function openIbadahDetail(id){}
</script>
</body>
</html>