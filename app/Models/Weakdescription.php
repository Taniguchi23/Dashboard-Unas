<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Weakdescription extends Model
{
    use HasFactory;
    public function weakne(){
        return $this->belongsTo(Weakne::class);
    }
}
