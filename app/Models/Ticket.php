<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
    'use_id',
    'cat_id',
    'pri_id',
    'pab_id',
    'tic_titulo',
    'tic_descripcion',
    'tic_archivo',
    'tic_estado',
    'tic_activo',
  ];

  public function prioridad()
  {
    return $this->belongsTo(Prioridad::class, 'pri_id');
  }

  public function user()
  {
    return $this->belongsTo(User::class, 'use_id');
  }

  public function categoria()
  {
    return $this->belongsTo(Categoria::class, 'cat_id');
  }

}
