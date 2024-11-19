<?php

declare(strict_types=1);

namespace App\FormRequest;

use App\Entity\TwoFactorStatusType;
use Somnambulist\Bundles\FormRequestBundle\Http\FormRequest;
use Somnambulist\Components\Validation\Rules\In;

class EnableTwoFactorFormRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'authCode' => 'sometimes|length:6',
            'method'   => 'required|' . In::make(TwoFactorStatusType::values()),
        ];
    }
}
