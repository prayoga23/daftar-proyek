# Panduan Formula Excel/Google Sheets untuk Normalisasi Data

## Metode 1: Menggunakan Power Query (Excel) / Data Connector (Google Sheets)

### Excel Power Query:
1. **Buka Data → Get Data → From Other Sources → Blank Query**
2. **Masukkan kode M berikut:**

```m
let
    // Ganti "Sheet1" dengan nama sheet Anda
    Source = Excel.CurrentWorkbook(){[Name="Sheet1"]}[Content],
    
    // Split kolom yang memiliki multiple values
    SplitOwner = Table.ExpandListColumn(
        Table.TransformColumns(Source, {
            {"OWNER", Splitter.SplitTextByDelimiter("#(lf)", QuoteStyle.None), let itemType = (type nullable text) meta [Serialized.Text = true] in type {itemType}}
        }), "OWNER"),
    
    SplitJenisPekerjaan = Table.ExpandListColumn(
        Table.TransformColumns(SplitOwner, {
            {"JENIS PEKERJAAN", Splitter.SplitTextByDelimiter("#(lf)", QuoteStyle.None), let itemType = (type nullable text) meta [Serialized.Text = true] in type {itemType}}
        }), "JENIS PEKERJAAN"),
    
    SplitYangMengerjakan = Table.ExpandListColumn(
        Table.TransformColumns(SplitJenisPekerjaan, {
            {"YANG MENGERJAKAN", Splitter.SplitTextByDelimiter("#(lf)", QuoteStyle.None), let itemType = (type nullable text) meta [Serialized.Text = true] in type {itemType}}
        }), "YANG MENGERJAKAN"),
    
    SplitStatus = Table.ExpandListColumn(
        Table.TransformColumns(SplitYangMengerjakan, {
            {"STATUS PEKERJAAN", Splitter.SplitTextByDelimiter("#(lf)", QuoteStyle.None), let itemType = (type nullable text) meta [Serialized.Text = true] in type {itemType}}
        }), "STATUS PEKERJAAN"),
    
    // Filter baris kosong
    FilteredRows = Table.SelectRows(SplitStatus, each 
        [OWNER] <> null and [OWNER] <> "" and
        [JENIS PEKERJAAN] <> null and [JENIS PEKERJAAN] <> "" and
        [YANG MENGERJAKAN] <> null and [YANG MENGERJAKAN] <> "" and
        [STATUS PEKERJAAN] <> null and [STATUS PEKERJAAN] <> ""
    ),
    
    // Reset nomor urut
    AddIndex = Table.AddIndexColumn(FilteredRows, "NO_NEW", 1, 1),
    RemoveOldNo = Table.RemoveColumns(AddIndex,{"NO"}),
    RenameColumn = Table.RenameColumns(RemoveOldNo,{{"NO_NEW", "NO"}})
in
    RenameColumn
```

3. **Klik Close & Load**

## Metode 2: Formula Excel Manual

### Step 1: Buat Helper Columns

**Kolom A (NO):** `=ROW()-1`

**Kolom B (PROYEK):** 
```excel
=IF(ROW()<=COUNTA(Data!$B$2:$B$1000),INDEX(Data!$B$2:$B$1000,MATCH(ROW(),Data!$A$2:$A$1000,0)),"")
```

**Kolom C (OWNER):**
```excel
=IF(ROW()<=COUNTA(Data!$C$2:$C$1000),INDEX(Data!$C$2:$C$1000,MATCH(ROW(),Data!$A$2:$A$1000,0)),"")
```

### Step 2: Split Text dengan Formula

**Untuk memisahkan text berdasarkan newline:**

```excel
=TRIM(MID(SUBSTITUTE($C2,CHAR(10),REPT(" ",100)),(COLUMN()-3)*100+1,100))
```

**Formula lengkap untuk setiap kolom:**

```excel
// Kolom D (OWNER Split 1)
=IF(ISERROR(FIND(CHAR(10),$C2)),$C2,TRIM(LEFT($C2,FIND(CHAR(10),$C2)-1)))

// Kolom E (OWNER Split 2) 
=IF(ISERROR(FIND(CHAR(10),$C2)),"",TRIM(MID($C2,FIND(CHAR(10),$C2)+1,LEN($C2))))

// Kolom F (JENIS PEKERJAAN Split 1)
=IF(ISERROR(FIND(CHAR(10),$D2)),$D2,TRIM(LEFT($D2,FIND(CHAR(10),$D2)-1)))

// Kolom G (JENIS PEKERJAAN Split 2)
=IF(ISERROR(FIND(CHAR(10),$D2)),"",TRIM(MID($D2,FIND(CHAR(10),$D2)+1,LEN($D2))))

// Dan seterusnya untuk kolom lainnya...
```

## Metode 3: Google Sheets dengan Apps Script

### Script Apps Script:

```javascript
function normalizeData() {
  const sheet = SpreadsheetApp.getActiveSheet();
  const data = sheet.getDataRange().getValues();
  
  const normalizedData = [];
  
  for (let i = 1; i < data.length; i++) { // Skip header
    const row = data[i];
    const no = row[0];
    const proyek = row[1];
    const owner = row[2];
    const jenisPekerjaan = row[3];
    const yangMengerjakan = row[4];
    const status = row[5];
    
    // Split berdasarkan newline
    const owners = owner ? owner.split('\n').map(s => s.trim()) : [''];
    const jenisPekerjaans = jenisPekerjaan ? jenisPekerjaan.split('\n').map(s => s.trim()) : [''];
    const yangMengerjakans = yangMengerjakan ? yangMengerjakan.split('\n').map(s => s.trim()) : [''];
    const statuses = status ? status.split('\n').map(s => s.trim()) : [''];
    
    // Tentukan jumlah maksimal entries
    const maxEntries = Math.max(owners.length, jenisPekerjaans.length, yangMengerjakans.length, statuses.length);
    
    for (let j = 0; j < maxEntries; j++) {
      normalizedData.push([
        normalizedData.length + 1, // NO
        proyek,
        owners[j] || owners[0] || '',
        jenisPekerjaans[j] || '',
        yangMengerjakans[j] || '',
        statuses[j] || ''
      ]);
    }
  }
  
  // Buat sheet baru untuk hasil
  const newSheet = SpreadsheetApp.getActiveSpreadsheet().insertSheet('Normalized Data');
  newSheet.getRange(1, 1, 1, 6).setValues([['NO', 'PROYEK', 'OWNER', 'JENIS PEKERJAAN', 'YANG MENGERJAKAN', 'STATUS PEKERJAAN']]);
  newSheet.getRange(2, 1, normalizedData.length, 6).setValues(normalizedData);
}
```

## Metode 4: Formula Google Sheets dengan SPLIT

```excel
// Untuk memisahkan text berdasarkan newline di Google Sheets
=SPLIT(A2,CHAR(10))

// Atau menggunakan TRANSPOSE untuk membuat vertikal
=TRANSPOSE(SPLIT(A2,CHAR(10)))
```

## Cara Penggunaan:

1. **Copy data asli ke sheet terpisah**
2. **Pilih metode yang sesuai dengan kebutuhan**
3. **Jalankan script/formula**
4. **Verifikasi hasil normalisasi**
5. **Simpan hasil ke file baru**

## Tips:

- **Backup data asli** sebelum melakukan normalisasi
- **Test dengan sample data kecil** terlebih dahulu
- **Verifikasi hasil** untuk memastikan data tidak hilang
- **Gunakan filter** untuk memeriksa data yang sudah dinormalisasi
