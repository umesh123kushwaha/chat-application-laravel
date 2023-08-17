<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get last message sent by user
     */
    public function lastMessage()
    {
        $user = $this->id;
        return Message::where(function ($q1) use ($user) {
            $q1->where('from', $user)->where('to', auth()->user()->id);
        })->orWhere(function ($q2) use ($user) {
            $q2->where('from', auth()->user()->id)->where('to', $user);
        })->latest()->first();
    }

    /**
     * Get total unread messages
     */
    public function unreadMessagesFromCount()
    {
        $user = $this->id;
        return Message::where(function ($q2) use ($user) {
            $q2->where('from', $user)->where('to', auth()->user()->id);
        })->where('is_read',0)->count();
    }
    public function unreadMessagesToCount()
    {
        $user = $this->id;
        return Message::where(function ($q2) use ($user) {
            $q2->where('from', auth()->user())->where('to', $user);
        })->where('is_read',0)->count();
    }


}
