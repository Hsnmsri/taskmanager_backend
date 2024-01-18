<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function task_categories()
    {
        return $this->hasMany(TaskCategories::class);
    }

    public function access_tokens()
    {
        return $this->hasMany(AccessToken::class);
    }

    public function recovery_tokens()
    {
        return $this->hasMany(RecoveryToken::class);
    }
}
