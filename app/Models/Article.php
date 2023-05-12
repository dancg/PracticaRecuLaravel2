<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Article extends Model
{
    use HasFactory;
    protected $fillable = ['titulo', 'contenido', 'estado', 'imagen', 'user_id'];
    //Relacion 1:N entre Article y User (en este caso belongsTo)
    public function user() :BelongsTo{
        return $this->belongsTo(User::class);
    }
}
