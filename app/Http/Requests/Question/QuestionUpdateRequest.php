<?php

namespace App\Http\Requests\Question;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

class QuestionUpdateRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        $rules = QuestionRequestValidationRules::getRules($this);
        $rules['text'][] = Rule::unique('questions')->ignore($this->question->id);

        $rules['deletable_image_id'][] = 'integer';
        $rules['replace_image'][] = File::image()->max(5 * 1024);
        $rules['replace_image'][] = ['required_when:deletable_image_id'];

        return $rules;
    }
}
