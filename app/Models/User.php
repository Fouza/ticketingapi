<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use App\Models\Ticket;
use App\Models\Message;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    //User Types : Admin, Agent, Customer
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'lastname',
        'email',
        'password',
        'type'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function tickets(){
        return $this->hasMany(Ticket::class);
    }

    public function doneTickets(){
        Ticket::where('user_id',$this->attributes['id'])->where('etat','done')->get();
    }

    public function toDoTickets(){
        Ticket::where('user_id',$this->attributes['id'])->where('etat','todo')->get();
    }

    public function getMessages(){
        Message::where($this->attributes['type'],'customer')->where('user_id',$this->attributes['id'])->get();
    }


}
