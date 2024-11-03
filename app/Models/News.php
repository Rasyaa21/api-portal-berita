<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $fillable = [
        'news_title',
        'news_description',
        'image',
        'user_id',
        'is_accepted'
    ];

    protected $casts = [
        'is_accepted' => 'boolean'
    ];

    protected $attributes = [
        'is_accepted' => false
    ];

    public function comments(){
        //satu berita memeiliki banyak comment
        return $this->hasMany(Comments::class);
    }

    public function user(){
        //satu berita hanya dimiliki oleh satu user
        return $this->belongsTo(User::class);
    }
}
