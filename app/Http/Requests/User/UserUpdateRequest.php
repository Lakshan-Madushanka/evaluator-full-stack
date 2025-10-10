<?php

namespace App\Http\Requests\User;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property User $user
 */
class UserUpdateRequest extends FormRequest
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
        $rules = UserRequestValidationRules::getRules($this);

        $rules['password'][] = 'sometimes'; // @phpstan-ignore-line
        $rules['email'][] = Rule::unique('users', 'email')->ignore($this->user->id); // @phpstan-ignore-line

        return $rules;
    }
}
