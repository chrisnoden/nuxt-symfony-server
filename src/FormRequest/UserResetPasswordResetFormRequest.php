<?php

declare(strict_types=1);

namespace App\FormRequest;

use Somnambulist\Bundles\FormRequestBundle\Http\FormRequest;

class UserResetPasswordResetFormRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'token'       => 'sometimes|min:20|max:60',
            'password' => 'required|min:8|max:100',
        ];
    }
}
