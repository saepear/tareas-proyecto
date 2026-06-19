<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'status_id',
        'priority_id',
        'due_date',
        'completion_date',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'datetime',
            'completion_date' => 'datetime',
        ];
    }


public function status(){
    return $this->belongsTo(TaskState::class, 'status_id');
}

public function priority(){
    return $this->belongsTo(PriorityType::class, 'priority_id');
}

public function user(){
    return $this->belongsTo(User::class, 'user_id');
}

public function creator(){
    return $this->belongsTo(User::class, 'created_by');
}

public function updater(){
    return $this->belongsTo(User::class, 'updated_by');
}

public function deleter(){
    return $this->belongsTo(User::class, 'deleted_by');
}
}