<?php

declare(strict_types=1);

namespace App\FormRequest;

use Somnambulist\Bundles\FormRequestBundle\Http\FormRequest;

class DisableTwoFactorFormRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'password' => 'required|min:8|max:100',
        ];
    }
}
