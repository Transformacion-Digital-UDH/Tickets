<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prioridad extends Model
{
    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'pri_id');
    }
    
    use HasFactory;
}
