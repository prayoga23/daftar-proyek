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
        'status_pekerjaan'
    ];
}
