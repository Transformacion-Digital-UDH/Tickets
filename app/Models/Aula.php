<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aula extends Model
{
    use HasFactory;

    protected $fillable = ['aul_numero', 'aul_activo', 'pab_id'];

    public function pabellon()
    {
        return $this->belongsTo(Pabellon::class, 'pab_id');
    }
}
