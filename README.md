📘 API Dokumentasi - Layanan Petani

Base URL: http://127.0.0.1:8000/api/petani

🔍 GET /api/petani
Deskripsi: Mengambil semua data petani.
Response: 200 OK
```json
[
  {
    "id": 1,
    "nama": "Azizah",
    "alamat": "Malang"
  }
]

📄 GET /api/petani/{id}
Deskripsi: Mengambil data petani berdasarkan ID.

➕ POST /api/petani
Deskripsi: Menambahkan data petani.
Body:
{
  "nama": "Azizah2",
  "alamat": "Surabaya"
}

✏ PUT /api/petani/{id}
Deskripsi: Memperbarui data petani berdasarkan ID.

❌ DELETE /api/petani/{id}
Deskripsi: Menghapus data petani berdasarkan ID.
