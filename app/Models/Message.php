<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;
    public function fromUser(){
        $this->belongsTo(User::class,'id','from');
    }
    public function toUser(){
        $this->belongsTo(User::class,'id','to');
    }
}
