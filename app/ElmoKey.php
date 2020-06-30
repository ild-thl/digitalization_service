<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ElmoKey extends Model
{
    protected $table = 'elmo_keys';

    public function keyAssignment() {
        return $this->hasMany('\App\KeyAssignment');
    }
}
