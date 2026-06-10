# WebGIS Pengentasan Kemiskinan — UAS 06 (FIXED)

## 📦 Struktur Folder WAJIB

```
webgis_uas06/            ← taruh di htdocs/
├── database_uas_06.sql  ← Import ke MySQL PERTAMA
├── koneksi.php
├── auth.php
├── ambil.php
├── index.php
├── login.php
├── uploads/             ← buat folder ini (chmod 777)
└── api/
    ├── dashboard.php
    ├── penduduk.php
    ├── bantuan.php
    ├── laporan.php
    ├── ibadah.php
    ├── user.php
    └── chat.php
```

> ⚠️ PENTING: File dashboard.php, penduduk.php, dll HARUS berada di folder `api/`
> bukan di root. Jika salah letak → semua halaman gagal memuat.

## 🚀 Cara Install

1. Import database: buka phpMyAdmin → Import → `database_uas_06.sql`
2. Copy folder `webgis_uas06/` ke `C:\xampp\htdocs\`
3. Pastikan folder `uploads/` ada di dalam webgis_uas06/
4. Akses: `http://localhost/webgis_uas06/login.php`

## 👤 Akun Demo (password: `password`)

| Role | Email |
|------|-------|
| Pengurus (Admin) | admin@uas.id |
| Pimpinan Daerah | pimpinan@uas.id |
| Masyarakat | warga@uas.id |

## 🐛 Bug yang Diperbaiki

- ✅ Dashboard gagal memuat → fixed null-check pada query result
- ✅ Penduduk gagal memuat & filter miskin/rentan tidak ada data → fixed
- ✅ Simpan penduduk baru gagal → fixed bind_param & NULL handling
- ✅ Edit penduduk gagal → fixed query UPDATE
- ✅ Laporan filter tidak berfungsi → fixed query WHERE
- ✅ Bantuan/histori gagal muat → fixed error handling
- ✅ Chat gagal (unread error saat belum login) → fixed null check user_id
- ✅ Ibadah & User gagal memuat → fixed error response
- ✅ Detail penduduk gagal → sudah benar, path api/ fixed
- ✅ Semua file API dipindah ke folder api/ sesuai path di index.php
