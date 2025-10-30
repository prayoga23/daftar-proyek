#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Script untuk Normalisasi Data Tabel Proyek
Memisahkan data yang tergabung dalam satu baris menjadi baris terpisah
"""

import pandas as pd
import re
from typing import List, Dict, Any

class DataNormalizer:
    def __init__(self):
        self.normalized_data = []
    
    def split_multiline_text(self, text: str) -> List[str]:
        """
        Memisahkan teks yang memiliki multiple lines
        """
        if not text or pd.isna(text):
            return ['']
        
        # Split berdasarkan newline dan bersihkan whitespace
        lines = [line.strip() for line in str(text).split('\n') if line.strip()]
        return lines if lines else ['']
    
    def normalize_row(self, row_data: Dict[str, Any]) -> List[Dict[str, Any]]:
        """
        Normalisasi satu baris data menjadi multiple baris
        """
        # Ambil data dari row
        no = row_data.get('NO', '')
        proyek = row_data.get('PROYEK', '')
        owner = row_data.get('OWNER', '')
        jenis_pekerjaan = row_data.get('JENIS PEKERJAAN', '')
        yang_mengerjakan = row_data.get('YANG MENGERJAKAN', '')
        status = row_data.get('STATUS PEKERJAAN', '')
        
        # Split setiap kolom yang bisa memiliki multiple values
        owners = self.split_multiline_text(owner)
        jenis_pekerjaans = self.split_multiline_text(jenis_pekerjaan)
        yang_mengerjakans = self.split_multiline_text(yang_mengerjakan)
        statuses = self.split_multiline_text(status)
        
        # Tentukan jumlah maksimal entries
        max_entries = max(len(owners), len(jenis_pekerjaans), 
                         len(yang_mengerjakans), len(statuses))
        
        normalized_rows = []
        
        for i in range(max_entries):
            # Ambil nilai untuk index i, jika tidak ada gunakan nilai pertama atau kosong
            current_owner = owners[i] if i < len(owners) else (owners[0] if owners else '')
            current_jenis = jenis_pekerjaans[i] if i < len(jenis_pekerjaans) else ''
            current_pelaku = yang_mengerjakans[i] if i < len(yang_mengerjakans) else ''
            current_status = statuses[i] if i < len(statuses) else ''
            
            normalized_row = {
                'NO': no,
                'PROYEK': proyek,
                'OWNER': current_owner,
                'JENIS_PEKERJAAN': current_jenis,
                'YANG_MENGERJAKAN': current_pelaku,
                'STATUS_PEKERJAAN': current_status
            }
            
            normalized_rows.append(normalized_row)
        
        return normalized_rows
    
    def normalize_dataframe(self, df: pd.DataFrame) -> pd.DataFrame:
        """
        Normalisasi seluruh dataframe
        """
        all_normalized_rows = []
        
        for index, row in df.iterrows():
            row_dict = row.to_dict()
            normalized_rows = self.normalize_row(row_dict)
            all_normalized_rows.extend(normalized_rows)
        
        # Buat dataframe baru dengan data yang sudah dinormalisasi
        normalized_df = pd.DataFrame(all_normalized_rows)
        
        # Reset index dan beri nomor urut baru
        normalized_df = normalized_df.reset_index(drop=True)
        normalized_df['NO'] = range(1, len(normalized_df) + 1)
        
        return normalized_df
    
    def process_excel_file(self, input_file: str, output_file: str = None):
        """
        Proses file Excel dan simpan hasil normalisasi
        """
        try:
            # Baca file Excel
            df = pd.read_excel(input_file)
            
            # Normalisasi data
            normalized_df = self.normalize_dataframe(df)
            
            # Simpan hasil
            if output_file is None:
                output_file = input_file.replace('.xlsx', '_normalized.xlsx')
            
            normalized_df.to_excel(output_file, index=False)
            
            print(f"✅ Data berhasil dinormalisasi!")
            print(f"📊 Input: {len(df)} baris")
            print(f"📊 Output: {len(normalized_df)} baris")
            print(f"💾 File tersimpan: {output_file}")
            
            return normalized_df
            
        except Exception as e:
            print(f"❌ Error: {str(e)}")
            return None

def main():
    """
    Contoh penggunaan
    """
    normalizer = DataNormalizer()
    
    # Contoh data untuk testing
    sample_data = {
        'NO': [5, 6, 7],
        'PROYEK': [
            'r. tinggal jl. teuku umar no.43',
            'r. tinggal jl. mataram gol. b', 
            'input pbg hotel jl.subuh'
        ],
        'OWNER': [
            'pak agung citata mtg',
            'p. bungaran matondang\np. valentino',
            'pak ben'
        ],
        'JENIS PEKERJAAN': [
            'irk\ncagar budaya\npbg',
            'irk\ncagar budaya',
            'gmbr arsitek\nstruktur\nmep\nska arsitek\nska str\nska mep'
        ],
        'YANG MENGERJAKAN': [
            'ptsp\npak danang\nptsp',
            'jambi / tika\nrenc. pak budi heri',
            'pak ben\npak ben\npak ben\npak ben\n?\n?'
        ],
        'STATUS PEKERJAAN': [
            'selesai. dibantu ucup, mame blm byr\nsudah selesai sidang\nMenunggu Dokumen SKRD dari PTSP.',
            'proses irk citata\nsedang proses sidang TAP Pak Budi Heri.',
            'sudah di kita\nsudah di kita\nblm di kirim dari pak ben\nudh ada dari pak ben\nrenc. dari kita\nblm jelas mau dari siapa'
        ]
    }
    
    # Buat DataFrame dari sample data
    df = pd.DataFrame(sample_data)
    
    print("📋 Data Asli:")
    print(df.to_string(index=False))
    print("\n" + "="*80 + "\n")
    
    # Normalisasi data
    normalized_df = normalizer.normalize_dataframe(df)
    
    print("📋 Data Setelah Normalisasi:")
    print(normalized_df.to_string(index=False))
    
    # Simpan ke Excel
    normalized_df.to_excel('data_normalized.xlsx', index=False)
    print(f"\n💾 Data tersimpan ke: data_normalized.xlsx")

if __name__ == "__main__":
    main()
