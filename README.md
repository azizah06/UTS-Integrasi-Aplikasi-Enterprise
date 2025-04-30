📘 API Dokumentasi - Layanan Mitra
Base URL: http://127.0.0.1:8003/api/mitra

🔍 GET /api/mitra
Deskripsi: Mengambil semua data mitra.
Response: 200 OK
```json
[
  {
    "id": 1,
    "nama": "PT Pertanian Sejahtera",
    "alamat": "Jl. Raya Tani No.1"
  }
]

📄 GET /api/mitra/{id}
Deskripsi: Mengambil data mitra berdasarkan ID.

➕ POST /api/mitra
Deskripsi: Menambahkan data mitra.
Body:
{
  "nama": "PT Pertanian Sejahtera",
  "alamat": "Jl. Raya Tani No.1"
}

✏️ PUT /api/mitra/{id}
Deskripsi: Memperbarui data mitra berdasarkan ID.

❌ DELETE /api/mitra/{id}
Deskripsi: Menghapus data mitra berdasarkan ID.
