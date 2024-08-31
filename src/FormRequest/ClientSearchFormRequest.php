<?php declare(strict_types=1);

namespace App\FormRequest;

use App\FormRequest\Behaviours\PaginatableFormRequest;
use Somnambulist\Bundles\ApiBundle\Request\SearchFormRequest;

use function array_merge;

class ClientSearchFormRequest extends SearchFormRequest
{
    use PaginatableFormRequest;

    protected array $ignore = [];

    public function formRules(): array
    {
        /**
         * @see https://github.com/somnambulist-tech/validation#available-rules
         * @see https://github.com/somnambulist-tech/form-request-bundle#custom-rules
         */
        return array_merge(parent::rules(), [
            'enabled' => 'boolean',
            'name'    => 'string|min:3|max:100',
        ]);
    }
}
