<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Helpers\RewardEnums;

class VariableHandler {

    public static function getExperienceTable(){
        if(!Cache::has('experienceTable')){
            Cache::remember('experienceTable', 60, function () {
                return DB::table('experience_points')->get();
            });
        }
        return Cache::get('experienceTable');
    }

    public static function getCharacterExpGain(String $type){
        return DB::table('character_exp_gain')
            ->where('task_type', $type)
            ->select('strength', 'agility', 'endurance', 'intelligence', 'charisma', 'level')->first();
    }
    public static function getVillageExpGain(String $type){
        return DB::table('village_exp_gain')
            ->where('task_type', $type)
            ->select('economy', 'labour', 'craft', 'art', 'community', 'level')->first();
    }
}