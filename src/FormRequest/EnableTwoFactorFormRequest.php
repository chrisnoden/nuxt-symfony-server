<?php

declare(strict_types=1);

namespace App\FormRequest;

use Somnambulist\Bundles\FormRequestBundle\Http\FormRequest;

class EnableTwoFactorFormRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'authCode' => 'required|length:6',
        ];
    }
}
