<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evenement extends Model
{
    protected $table = 'evenements';
    protected $primaryKey = 'code_evenement';
    public $incrementing = false;
    protected $keyType = 'string';
}
