# Panduan Normalisasi Data Tabel Proyek

## 🎯 Tujuan
Memisahkan data yang tergabung dalam satu baris menjadi baris terpisah untuk setiap kombinasi unik dari Owner, Jenis Pekerjaan, dan Yang Mengerjakan.

## 📊 Contoh Data Sebelum Normalisasi

| NO | PROYEK | OWNER | JENIS PEKERJAAN | YANG MENGERJAKAN | STATUS PEKERJAAN |
|----|--------|-------|-----------------|------------------|------------------|
| 5 | r. tinggal jl. teuku umar no.43 | pak agung citata mtg | irk<br>cagar budaya<br>pbg | ptsp<br>pak danang<br>ptsp | selesai. dibantu ucup, mame blm byr<br>sudah selesai sidang<br>Menunggu Dokumen SKRD dari PTSP. |
| 6 | r. tinggal jl. mataram gol. b | p. bungaran matondang<br>p. valentino | irk<br>cagar budaya | jambi / tika<br>renc. pak budi heri | proses irk citata<br>sedang proses sidang TAP Pak Budi Heri. |
| 7 | input pbg hotel jl.subuh | pak ben | gmbr arsitek<br>struktur<br>mep<br>ska arsitek<br>ska str<br>ska mep | pak ben<br>pak ben<br>pak ben<br>pak ben<br>?<br>? | sudah di kita<br>sudah di kita<br>blm di kirim dari pak ben<br>udh ada dari pak ben<br>renc. dari kita<br>blm jelas mau dari siapa |

## 📋 Data Setelah Normalisasi

| NO | PROYEK | OWNER | JENIS_PEKERJAAN | YANG_MENGERJAKAN | STATUS_PEKERJAAN |
|----|--------|-------|-----------------|------------------|------------------|
| 1 | r. tinggal jl. teuku umar no.43 | pak agung citata mtg | irk | ptsp | selesai. dibantu ucup, mame blm byr |
| 2 | r. tinggal jl. teuku umar no.43 | pak agung citata mtg | cagar budaya | pak danang | sudah selesai sidang |
| 3 | r. tinggal jl. teuku umar no.43 | pak agung citata mtg | pbg | ptsp | Menunggu Dokumen SKRD dari PTSP. |
| 4 | r. tinggal jl. mataram gol. b | p. bungaran matondang | irk | jambi / tika | proses irk citata |
| 5 | r. tinggal jl. mataram gol. b | p. valentino | cagar budaya | renc. pak budi heri | sedang proses sidang TAP Pak Budi Heri. |
| 6 | input pbg hotel jl.subuh | pak ben | gmbr arsitek | pak ben | sudah di kita |
| 7 | input pbg hotel jl.subuh | pak ben | struktur | pak ben | sudah di kita |
| 8 | input pbg hotel jl.subuh | pak ben | mep | pak ben | blm di kirim dari pak ben |
| 9 | input pbg hotel jl.subuh | pak ben | ska arsitek | pak ben | udh ada dari pak ben |
| 10 | input pbg hotel jl.subuh | pak ben | ska str | ? | renc. dari kita |
| 11 | input pbg hotel jl.subuh | pak ben | ska mep | ? | blm jelas mau dari siapa |

## 🛠️ Cara Penggunaan

### 1. Script Python (Otomatis)
```bash
# Install dependencies
pip3 install pandas openpyxl

# Jalankan script
python3 data_normalizer.py
```

### 2. Excel/Google Sheets
Lihat file `excel_formula_guide.md` untuk panduan lengkap menggunakan:
- Power Query (Excel)
- Formula manual
- Apps Script (Google Sheets)

## 📈 Keuntungan Normalisasi

1. **Konsistensi Data**: Setiap baris mewakili satu entitas pekerjaan
2. **Mudah Filtering**: Bisa filter berdasarkan owner, jenis pekerjaan, atau status
3. **Analisis Lebih Baik**: Bisa hitung progress per jenis pekerjaan
4. **Reporting**: Lebih mudah generate laporan per owner atau per proyek
5. **Database Ready**: Data siap untuk di-import ke database

## 🔍 Aturan Normalisasi

1. **Identifikasi Entitas**: Setiap kombinasi unik dari PROYEK + OWNER + JENIS PEKERJAAN + YANG MENGERJAKAN + STATUS
2. **Split Multi-line Text**: Pisahkan text yang dipisahkan newline (`\n`)
3. **Handle Missing Values**: Jika ada kolom yang kosong, gunakan nilai default atau kosong
4. **Preserve Relationships**: Pastikan relasi antar data tetap terjaga

## 📁 File yang Dihasilkan

- `data_normalized.xlsx`: File Excel dengan data yang sudah dinormalisasi
- `data_normalizer.py`: Script Python untuk normalisasi otomatis
- `excel_formula_guide.md`: Panduan formula Excel/Google Sheets

## ⚠️ Tips Penting

1. **Backup Data Asli**: Selalu backup data sebelum melakukan normalisasi
2. **Test dengan Sample**: Test dengan data kecil terlebih dahulu
3. **Verifikasi Hasil**: Periksa apakah semua data sudah terpisah dengan benar
4. **Handle Edge Cases**: Perhatikan data yang memiliki format tidak standar

## 🚀 Langkah Selanjutnya

Setelah data dinormalisasi, Anda bisa:
1. Import ke database
2. Buat dashboard analisis
3. Generate laporan otomatis
4. Setup monitoring progress proyek
