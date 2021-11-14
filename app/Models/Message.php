<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Ticket;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_ticket', 'notes', 'read', 'fichier', 'send_to'
    ];

    public function ticket(){
        return $this->hasOne(Ticket::class);
    }


}
