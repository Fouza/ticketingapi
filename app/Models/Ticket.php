<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Ticket extends Model
{
    use HasFactory;


    protected $fillable = [
        'title', 'description', 'notes', 'deadline', 'etat', 'user_id', 'createdBy'
    ];

    protected $casts = [
        'deadline' => 'date:d/m/Y',
    ];


    public function user(){
        return $this->hasOne(User::class);
    }

    public function isFinished(){
        return $this->attributes['etat'];
    }

}
