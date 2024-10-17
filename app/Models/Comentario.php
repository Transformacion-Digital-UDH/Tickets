<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comentario extends Model
{
    use HasFactory;

    protected $fillable = ['tic_id', 'use_id', 'com_texto', 'com_adjunto', 'com_activo'];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'tic_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'use_id');
    }
}
