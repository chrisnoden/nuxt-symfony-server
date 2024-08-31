<?php

declare(strict_types=1);

namespace App\FormRequest;

use Somnambulist\Bundles\FormRequestBundle\Http\FormRequest;

class UserResetPasswordBeginFormRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email'    => 'sometimes|min:4|max:255|email',
        ];
    }
}
