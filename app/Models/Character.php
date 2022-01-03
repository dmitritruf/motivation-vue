<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\RewardHandler;
use App\Helpers\LevelHandler;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\CharacterResource;

class Character extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'user_id',
        'strength_exp',
        'agility_exp',
        'endurance_exp',
        'intelligence_exp',
        'charisma_exp',
        'experience',
        'strength',
        'agility',
        'endurance',
        'intelligence',
        'charisma',
        'level',
    ];

    public function user(){
        return $this->belongsTo('App\Models\User');
    }

    /**
     * Applies a reward from a completed task to a character
     *
     * @param Task $task
     * @return Object
     */
    public function applyReward(Task $task){
        $parsedReward = RewardHandler::calculateReward($task->type, $task->difficulty, 'CHARACTER');
        $returnValue = LevelHandler::addCharacterExperience($this->toArray(), $parsedReward);
        $this->update($returnValue->activeReward);
        $returnValue->activeReward = new CharacterResource($this);
        return $returnValue;
    }

    public function experienceTable(){
        return DB::table('experience_points')->get();
    }
}
