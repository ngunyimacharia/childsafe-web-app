<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChildLog extends Model
{
    protected $fillable = ['url','initiator','timestamp'];
}
