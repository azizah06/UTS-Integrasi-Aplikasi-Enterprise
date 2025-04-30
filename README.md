📘 API Dokumentasi - Layanan Transaksi
Base URL: http://127.0.0.1:8002/api/transaksi

🔍 GET /api/transaksi
Deskripsi: Mengambil semua transaksi.
Response: 200 OK
```json
[
  {
    "id": 1,
    "id_petani": 1,
    "id_produk": 2,
    "jumlah": 10,
    "total_harga": 150000
  }
]

📄 GET /api/transaksi/{id}
Deskripsi: Mengambil data transaksi berdasarkan ID.

➕ POST /api/transaksi
Deskripsi: Menambahkan transaksi baru.
Body:
{
  "id_petani": 1,
  "id_produk": 2,
  "jumlah": 10,
  "total_harga": 150000
}

✏ PUT /api/transaksi/{id}
Deskripsi: Memperbarui transaksi berdasarkan ID.

❌ DELETE /api/transaksi/{id}
Deskripsi: Menghapus transaksi berdasarkan ID.
