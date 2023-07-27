<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cve extends Model
{
    use HasFactory;

    public function descriptions(){
        return $this->hasMany(Description::class);
    }

    public function metrics(){
        return $this->hasMany(Metric::class);
    }


    public function weaknes(){
        return $this->hasMany(Weakne::class);
    }

}
