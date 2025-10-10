<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property string|int $role
 */
class UserStoreRequest extends FormRequest
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
    public function rules(): array
    {
        $rules = UserRequestValidationRules::getRules($this);
        $rules['email'][] = Rule::unique('users', 'email'); // @phpstan-ignore-line

        return $rules;
    }

    protected function prepareForValidation(): void
    {
        UserRequestValidationRules::prepareData($this);
    }
}
