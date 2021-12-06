<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Character;

class CharacterHandler {

    public static function toggleCharacterActive($userId, $characterIdToActive){
        $allCharacters = CharacterHandler::findAllCharactersByUser($userId);
        foreach($allCharacters as $char){
            if($char->id == $characterIdToActive){
                CharacterHandler::activateCharacter($char->id);
            } else {
                CharacterHandler::deactivateCharacter($char->id);
            }
        }
    }

    public static function activateCharacter($characterId){
        $character = Character::find($characterId);
        $character->active = true;
        $character->update();
    }

    public static function deactivateCharacter($characterId){
        $character = Character::find($characterId);
        $character->active = false;
        $character->update();
    }

    public static function createNewCharacterAndActivate($userId, $characterName){
        $newCharacter = Character::create([
            'name' => $characterName,
            'user_id' => $userId]);
        CharacterHandler::toggleCharacterActive($userId, $newCharacter->id);
    }

    public static function findActiveCharacter($userId){
        return Character::where('user_id', $userId)->where('active', true)->first();
    }

    public static function findAllCharactersByUser($userId){
        return Character::where('user_id', $userId)->get();
    }
}