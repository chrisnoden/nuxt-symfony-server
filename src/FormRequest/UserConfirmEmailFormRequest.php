<?php

declare(strict_types=1);

namespace App\FormRequest;

use Somnambulist\Bundles\FormRequestBundle\Http\FormRequest;

class UserConfirmEmailFormRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'token' => 'required|length:36',
        ];
    }
}
