<?php declare(strict_types=1);

namespace App\FormRequest;

use App\FormRequest\Behaviours\PaginatableFormRequest;
use Somnambulist\Bundles\ApiBundle\Request\SearchFormRequest;

use function array_merge;

class UserSearchFormRequest extends SearchFormRequest
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
            'client'  => 'integer|min:1',
            'email'   => 'string|min:3|max:255',
            'enabled' => 'boolean',
            'name'    => 'string|min:3|max:100',
            'role'    => 'string|max:40',
        ]);
    }
}
