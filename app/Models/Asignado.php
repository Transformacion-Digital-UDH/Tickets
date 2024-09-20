<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asignado extends Model
{
    use HasFactory;

    protected $fillable = ['tic_id', 'use_id', 'es_asignado'];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'tic_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'use_id');
    }
}
