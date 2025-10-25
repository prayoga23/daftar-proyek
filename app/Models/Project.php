<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'nama_proyek',
        'owner',
        'jenis_pekerjaan',
        'yang_mengerjakan',
        'status_pekerjaan',
        'highlighted',
        'sort_order',
        // PBG fields
        'irk_pbg',
        'gambar_arsitek_pbg',
        'gambar_struktur_pbg',
        'gambar_mep_pbg',
        'ska_pbg',
        'proses_pbg',
        // SLF fields
        'pengukuran_slf',
        'irk_slf',
        'gambar_arsitek_slf',
        'gambar_struktur_slf',
        'gambar_mep_slf',
        'ska_slf',
        'kajian_slf',
        'proses_slf',
    ];
}
