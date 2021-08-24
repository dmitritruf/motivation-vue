<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UpdateUserSettingsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'full_display_name' => ['required' , 'string', Rule::unique('users')->ignore(Auth::user())],
            'rewards' => 'required', //TODO, exists:rewards_types,type - make rewards type migration table
            'show_character' => 'required|boolean',
            'show_achievements' => 'required|boolean',
            'show_friends' => 'required|boolean',
        ];
    }
}