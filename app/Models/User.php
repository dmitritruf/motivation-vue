<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Task;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'full_display_name',
        'display_picture',
        'rewards',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $with = ['characters'];

    public function characters(){
        return $this->hasMany('App\Models\Character');
    }

    public function taskLists(){
        return $this->hasMany('App\Models\TaskList');
    }

    public function tasks(){
        return $this->hasMany('App\Models\Task');
    }

    public function achievements(){
        return $this->belongsToMany('App\Models\Achievement', 'achievements_earned')->withPivot('earned');
    }

    public function friends(){
        return $this->belongsToMany('App\Models\User', 'friends', 'user_id', 'friend_id')->withTimestamps();
    }

    public function getTotalTasksCompleted(){
        $regularTasks = Task::where('user_id', $this->id)->where('completed', '!=', null)->count();
        $repeatableTasks = DB::table('repeatable_tasks_completed')->where('user_id', $this->id)->count();
        return $regularTasks + $repeatableTasks;
    }

    public function getRepeatableTaskMostCompleted(){
        $repeatable = DB::table('repeatable_tasks_completed')
            ->where('user_id', $this->id)
            ->select('task_id', DB::raw('count(*) as total'))
            ->groupBy('task_id')
            ->orderByDesc('total')
            ->first();
        $repeatable->task_name = Task::find($repeatable->task_id)->name;
        return $repeatable;
    }
}
