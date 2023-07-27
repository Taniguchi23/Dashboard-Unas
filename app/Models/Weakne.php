<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Weakne extends Model
{
    use HasFactory;
    public function weakdescriptions(){
        return $this->belongsTo(Weakdescription::class);
    }
}
