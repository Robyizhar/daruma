### **Relasi Antar Tabel**

* **`users`** → bisa memiliki banyak **`orders`**
* **`orders`** → bisa memiliki banyak **`order_items`**
* **`products`** → bisa muncul di banyak **`order_items`** dan **`cart`**
* **`cart`** → berisi barang sementara sebelum checkout
* **`payments`** → menghubungkan pembayaran ke pesanan
