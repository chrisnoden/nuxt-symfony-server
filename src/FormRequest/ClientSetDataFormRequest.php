<?php declare(strict_types=1);

namespace App\FormRequest;

use Somnambulist\Bundles\FormRequestBundle\Http\FormRequest;

class ClientSetDataFormRequest extends FormRequest
{
    public function rules(): array
    {
        /**
         * @see https://github.com/somnambulist-tech/validation#available-rules
         * @see https://github.com/somnambulist-tech/form-request-bundle#custom-rules
         */
        return [
            'enabled'     => 'boolean',
            'companyName' => 'required|string|min:3|max:100',
        ];
    }
}
