<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ValidRewardType;
use Illuminate\Validation\Rule;

class UpdateRewardsTypeRequest extends FormRequest
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
            'rewards' => ['required', new ValidRewardType()], //TODO, exists:rewards_types,type - make rewards type migration table
            'keepCharacter' => 'required',
            'character_name' => [Rule::requiredIf($this->rewardsType == 'CHARACTER' && $this->keepCharacter == 'NEW'), 'string'],
        ];
    }
}
