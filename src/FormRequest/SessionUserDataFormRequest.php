<?php

declare(strict_types=1);

namespace App\FormRequest;

use Somnambulist\Bundles\FormRequestBundle\Http\FormRequest;

class SessionUserDataFormRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name'        => 'sometimes|min:6|max:100',
            'email'       => 'sometimes|min:4|max:255|email',
            'password'    => 'required|min:8|max:100',
            'newPassword' => 'sometimes|min:8|max:100',
        ];
    }
}
