📘 API Dokumentasi - Layanan Produk
Base URL: http://127.0.0.1:8001/api/produk

🔍 GET /api/produk
Deskripsi: Mengambil semua data produk.
Response: 200 OK
```json
[
  {
    "id": 1,
    "nama_produk": "Padi",
    "harga": 15000,
    "stok": 100
  }
]

📄 GET /api/produk/{id}
Deskripsi: Mengambil data produk berdasarkan ID.

➕ POST /api/produk
Deskripsi: Menambahkan produk baru.
Body:
{
  "nama_produk": "Padi",
  "harga": 15000,
  "stok": 100
}

✏ PUT /api/produk/{id}
Deskripsi: Memperbarui data produk berdasarkan ID.

❌ DELETE /api/produk/{id}
Deskripsi: Menghapus produk berdasarkan ID.
