<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Login – WebGIS Kemiskinan UAS 06</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
<style>
:root{--bg:#0d1117;--surface:#161b22;--card:#1c2230;--border:#30363d;--text:#e6edf3;--muted:#8b949e;--accent:#58a6ff;--red:#f85149;--green:#3fb950}
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Plus Jakarta Sans',sans-serif;background:var(--bg);color:var(--text);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px}
.wrap{width:100%;max-width:900px;display:grid;grid-template-columns:1fr 1fr;gap:0;border-radius:16px;overflow:hidden;box-shadow:0 8px 48px rgba(0,0,0,.7);border:1px solid var(--border)}
.left{background:linear-gradient(135deg,#0d1117 0%,#1a2332 100%);padding:48px 40px;display:flex;flex-direction:column;justify-content:center;position:relative;overflow:hidden}
.left::before{content:'';position:absolute;top:-60px;right:-60px;width:240px;height:240px;background:rgba(88,166,255,.06);border-radius:50%}
.left::after{content:'';position:absolute;bottom:-40px;left:-40px;width:180px;height:180px;background:rgba(63,185,80,.05);border-radius:50%}
.left-ico{font-size:40px;margin-bottom:20px;display:block}
.left h1{font-size:26px;font-weight:800;letter-spacing:-0.5px;margin-bottom:10px;line-height:1.2}
.left p{font-size:13px;color:var(--muted);line-height:1.6;margin-bottom:28px}
.feat{display:flex;align-items:center;gap:10px;margin-bottom:10px;font-size:12px;color:var(--muted)}
.feat i{color:var(--accent);width:16px}
.right{background:var(--surface);padding:48px 40px}
.right h2{font-size:20px;font-weight:800;margin-bottom:6px}
.right .sub{font-size:12px;color:var(--muted);margin-bottom:28px}
.fg{margin-bottom:16px}
.fg label{font-size:11px;font-weight:700;color:var(--muted);display:block;margin-bottom:5px;text-transform:uppercase;letter-spacing:.4px}
.fg input{width:100%;background:var(--card);border:1px solid var(--border);color:var(--text);padding:10px 13px;border-radius:8px;font-size:13px;font-family:inherit;outline:none;transition:border .15s}
.fg input:focus{border-color:var(--accent)}
.btn-login{width:100%;background:var(--accent);color:#0d1117;border:none;padding:11px;border-radius:8px;font-size:14px;font-weight:800;font-family:inherit;cursor:pointer;transition:background .15s;margin-top:4px}
.btn-login:hover{background:#79c0ff}
.btn-login:disabled{opacity:.6;cursor:not-allowed}
.err{background:rgba(248,81,73,.08);border:1px solid rgba(248,81,73,.25);border-radius:7px;padding:9px 12px;font-size:12px;color:var(--red);margin-bottom:14px;display:none;align-items:center;gap:7px}
.demo-box{background:var(--card);border:1px solid var(--border);border-radius:8px;padding:14px;margin-top:20px}
.demo-box strong{display:block;font-size:10px;color:var(--muted);text-transform:uppercase;letter-spacing:1px;margin-bottom:10px}
.demo-row{display:flex;align-items:center;justify-content:space-between;padding:5px 0;border-bottom:1px solid var(--border);font-size:12px}
.demo-row:last-child{border:none}
.demo-row .role{color:var(--muted);font-size:11px}
.demo-row .creds{font-size:11px;color:var(--accent);cursor:pointer}
.demo-row .creds:hover{text-decoration:underline}
@media(max-width:650px){.wrap{grid-template-columns:1fr}.left{display:none}}
</style>
</head>
<body>
<div class="wrap">
  <div class="left">
    <span class="left-ico">🗺️</span>
    <h1>WebGIS Pengentasan Kemiskinan</h1>
    <p>Sistem informasi geografis berbasis masyarakat untuk monitoring dan pengentasan kemiskinan yang terintegrasi dengan rumah ibadah.</p>
    <div class="feat"><i class="fas fa-map-marked-alt"></i> Peta sebaran penduduk real-time</div>
    <div class="feat"><i class="fas fa-gift"></i> Manajemen bantuan sosial</div>
    <div class="feat"><i class="fas fa-flag"></i> Sistem laporan masyarakat</div>
    <div class="feat"><i class="fas fa-chart-bar"></i> Dashboard statistik kemiskinan</div>
    <div class="feat"><i class="fas fa-comments"></i> Komunikasi antar pengurus</div>
    <div class="feat"><i class="fas fa-user-shield"></i> Sistem multi-role & autentikasi</div>
  </div>
  <div class="right">
    <h2>Masuk ke Sistem</h2>
    <p class="sub">Silakan login dengan akun yang telah terdaftar</p>
    <div class="err" id="err"><i class="fas fa-exclamation-circle"></i><span id="err-txt">–</span></div>
    <form onsubmit="doLogin(event)">
      <div class="fg"><label>Email</label><input type="email" id="email" placeholder="email@domain.com" required autofocus></div>
      <div class="fg"><label>Password</label><input type="password" id="pass" placeholder="••••••••" required></div>
      <button type="submit" class="btn-login" id="btn-submit"><i class="fas fa-sign-in-alt"></i> Masuk</button>
    </form>
    <div class="demo-box">
      <strong>Akun Demo — klik untuk isi otomatis</strong>
      <div class="demo-row">
        <div><div style="font-weight:700">Admin/Pengurus</div><div class="role">Akses penuh input & verifikasi</div></div>
        <span class="creds" onclick="fillDemo('admin@uas.id','password')">Gunakan →</span>
      </div>
      <div class="demo-row">
        <div><div style="font-weight:700">Pimpinan Daerah</div><div class="role">Read-only + dashboard</div></div>
        <span class="creds" onclick="fillDemo('pimpinan@uas.id','password')">Gunakan →</span>
      </div>
      <div class="demo-row">
        <div><div style="font-weight:700">Masyarakat</div><div class="role">Hanya lihat & laporan</div></div>
        <span class="creds" onclick="fillDemo('warga@uas.id','password')">Gunakan →</span>
      </div>
    </div>
  </div>
</div>
<script>
function fillDemo(e,p){
  document.getElementById('email').value=e;
  document.getElementById('pass').value=p;
  document.getElementById('email').focus();
}
function doLogin(e){
  e.preventDefault();
  const btn=document.getElementById('btn-submit');
  btn.disabled=true; btn.innerHTML='<i class="fas fa-spinner fa-spin"></i> Memverifikasi...';
  document.getElementById('err').style.display='none';
  const body=new URLSearchParams({aksi:'login',email:document.getElementById('email').value,password:document.getElementById('pass').value});
  fetch('auth.php',{method:'POST',body}).then(r=>r.json()).then(d=>{
    if(d.login){ window.location.href='index.php'; }
    else {
      const el=document.getElementById('err');
      document.getElementById('err-txt').textContent=d.error||'Login gagal';
      el.style.display='flex';
      btn.disabled=false; btn.innerHTML='<i class="fas fa-sign-in-alt"></i> Masuk';
    }
  }).catch(()=>{
    document.getElementById('err-txt').textContent='Tidak dapat terhubung ke server';
    document.getElementById('err').style.display='flex';
    btn.disabled=false; btn.innerHTML='<i class="fas fa-sign-in-alt"></i> Masuk';
  });
}
</script>
</body>
</html>
