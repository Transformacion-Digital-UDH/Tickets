<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    use HasFactory;
    
    protected $fillable = ['cat_nombre', 'cat_activo'];

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'cat_id');
    }
}
