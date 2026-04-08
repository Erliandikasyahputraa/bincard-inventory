# Import Produk - Use Case QA Manual

Dokumen ini dipakai admin untuk menguji fitur import produk (termasuk opsi skip/overwrite duplikat).

## Prasyarat

- Login sebagai user admin.
- Pastikan minimal ada 1 produk existing di database untuk skenario duplikat.
- Gunakan template resmi dari menu `Import Produk`.

## Use Case Pasti Berhasil

1. **Row baru valid**
   - Input: barcode unik, nama terisi, kolom opsional boleh kosong.
   - Ekspektasi: row sukses ter-import.
   - Catatan default otomatis:
     - `location = Lantai 1`
     - `min_stock = 1`
     - `max_stock = 10`
     - `current_stock = 0`

2. **Duplikat barcode + nama sama, pilih Skip**
   - Input: barcode dan nama sudah ada di DB.
   - Ekspektasi:
     - muncul panel preview duplikat.
     - saat pilih `Skip`, data lama tidak berubah.
     - row tercatat sebagai gagal/dilewati dengan alasan duplikat.

3. **Duplikat barcode + nama sama, pilih Overwrite**
   - Input: barcode dan nama sudah ada di DB, ada field inti yang berbeda.
   - Ekspektasi:
     - preview menampilkan field berubah (lama -> baru).
     - saat pilih `Overwrite`, data existing ditimpa dengan nilai baru.
     - proses selesai dengan hitungan sukses bertambah.

## Use Case Pasti Gagal

1. **Barcode kosong atau Nama kosong**
   - Ekspektasi: baris ditolak dengan alasan field wajib belum diisi.

2. **Barcode sama, nama berbeda**
   - Ekspektasi: baris ditolak karena konflik identitas produk.

3. **Supplier ID tidak valid**
   - Input: `supplier_id` tidak ada di tabel supplier.
   - Ekspektasi: baris ditolak dengan pesan supplier tidak dikenali.

4. **Format file tidak didukung**
   - Input: selain `xlsx`, `xls`, `csv`.
   - Ekspektasi: validasi upload gagal sebelum proses import.

5. **Ukuran file melebihi batas**
   - Input: file di atas 10MB.
   - Ekspektasi: validasi upload gagal dengan pesan batas ukuran.

## Validasi UI yang Wajib Dicek

- Saat proses `Mulai Import`, tombol disable dan menampilkan status loading.
- Saat ada duplikat, panel preview menampilkan:
  - barcode + nama produk
  - status `Akan Ditimpa` atau `Tidak Berubah`
  - daftar field inti yang berubah.
- Setelah proses selesai, popup sukses/gagal tampil dengan total sukses/gagal.
