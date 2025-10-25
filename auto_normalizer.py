#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Auto Normalizer - Script Otomatis untuk Normalisasi Data
Membuat data sample dan langsung memproses normalisasi
"""

import pandas as pd
import os

def create_sample_data():
    """Membuat data sample yang berbeda dari data asli"""
    sample_data = {
        'NO': [1, 2, 3],
        'PROYEK': [
            'Gedung Perkantoran Sudirman Tower',
            'Rumah Sakit Umum Daerah Jakarta', 
            'Mall Shopping Center Bekasi'
        ],
        'OWNER': [
            'PT. Konstruksi Maju\nPT. Bangun Jaya',
            'Dinas Kesehatan DKI',
            'CV. Properti Sukses\nPT. Arsitek Modern\nPT. Interior Design'
        ],
        'JENIS PEKERJAAN': [
            'Struktur Beton\nInstalasi Listrik\nPlumbing System',
            'Renovasi Bangunan\nInstalasi Medis\nLandscaping',
            'Design Interior\nStruktur Baja\nAC Central\nFire Safety\nParking System\nSecurity System'
        ],
        'YANG MENGERJAKAN': [
            'Tim Struktur\nTim Elektrik\nTim Plumbing',
            'Kontraktor Utama\nTim Medis\nTim Taman',
            'Designer\nTim Baja\nTim AC\nTim Safety\nTim Parkir\nTim Security'
        ],
        'STATUS PEKERJAAN': [
            'Selesai 80%\nProgress 60%\nBelum Dimulai',
            'Sudah Selesai\nProgress 40%\nBelum Dimulai',
            'Design Selesai\nProgress 70%\nBelum Dimulai\nProgress 30%\nBelum Dimulai\nProgress 20%'
        ]
    }
    return pd.DataFrame(sample_data)

def normalize_data_auto():
    """Fungsi otomatis untuk normalisasi data"""
    print("🚀 Memulai Auto Normalizer...")
    print("=" * 50)
    
    # Buat data sample
    print("📝 Membuat data sample...")
    df = create_sample_data()
    
    print("\n📋 Data Asli:")
    print(df.to_string(index=False))
    print("\n" + "=" * 50)
    
    # Normalisasi data
    print("⚙️  Memproses normalisasi data...")
    normalized_rows = []
    
    for index, row in df.iterrows():
        # Split setiap kolom berdasarkan newline
        owners = [s.strip() for s in str(row['OWNER']).split('\n') if s.strip()]
        jenis_pekerjaans = [s.strip() for s in str(row['JENIS PEKERJAAN']).split('\n') if s.strip()]
        yang_mengerjakans = [s.strip() for s in str(row['YANG MENGERJAKAN']).split('\n') if s.strip()]
        statuses = [s.strip() for s in str(row['STATUS PEKERJAAN']).split('\n') if s.strip()]
        
        # Tentukan jumlah maksimal entries
        max_entries = max(len(owners), len(jenis_pekerjaans), len(yang_mengerjakans), len(statuses))
        
        for i in range(max_entries):
            normalized_row = {
                'NO': len(normalized_rows) + 1,
                'PROYEK': row['PROYEK'],
                'OWNER': owners[i] if i < len(owners) else (owners[0] if owners else ''),
                'JENIS_PEKERJAAN': jenis_pekerjaans[i] if i < len(jenis_pekerjaans) else '',
                'YANG_MENGERJAKAN': yang_mengerjakans[i] if i < len(yang_mengerjakans) else '',
                'STATUS_PEKERJAAN': statuses[i] if i < len(statuses) else ''
            }
            normalized_rows.append(normalized_row)
    
    # Buat DataFrame hasil
    normalized_df = pd.DataFrame(normalized_rows)
    
    print("✅ Normalisasi selesai!")
    print(f"📊 Input: {len(df)} baris")
    print(f"📊 Output: {len(normalized_df)} baris")
    
    print("\n📋 Data Setelah Normalisasi:")
    print(normalized_df.to_string(index=False))
    
    # Simpan ke Excel
    output_file = 'hasil_normalisasi_otomatis.xlsx'
    normalized_df.to_excel(output_file, index=False)
    
    print(f"\n💾 File tersimpan: {output_file}")
    print("🎉 Proses selesai!")
    
    return normalized_df

if __name__ == "__main__":
    try:
        result = normalize_data_auto()
        print(f"\n✨ Berhasil memproses {len(result)} baris data!")
    except Exception as e:
        print(f"❌ Error: {str(e)}")
