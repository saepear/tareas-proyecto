<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class PriorityType extends Model
{
    use SoftDeletes;

    protected $fillable =
    [
        'name',
    ];

    public function tasks()
    {
        return $this->hasMany(Task::class, 'priority_id');
    }
    
}
