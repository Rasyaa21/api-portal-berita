<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comments extends Model
{
    protected $fillable = [
        'news_id',
        'comment',
        'user_id'
    ];

    public function news(){
        //1 comment dapat dimiliki oleh satu berita
        return $this->belongsTo(News::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
