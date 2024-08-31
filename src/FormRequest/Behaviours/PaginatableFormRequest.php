<?php

declare(strict_types=1);

namespace App\FormRequest\Behaviours;

use function array_merge;

trait PaginatableFormRequest
{
    public function rules(): array
    {
        return array_merge([
            'order'    => 'sometimes',
            'page'     => 'default:1|min:1|integer',
            'per_page' => 'default:100|min:1|max:1000|integer',
        ], $this->formRules());
    }

    public function getCriteria(): array
    {
        return array_merge(
            $this->data()->all(),
            [

            ]
        );
    }
}
