<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class KeyAssignment extends Model
{
    protected $table = 'key_assignments';

    public function elmoKey() {
        return $this->belongsTo('\App\ElmoKey');
    }
}
