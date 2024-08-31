<?php

declare(strict_types=1);

namespace App\Controller;

use App\Controller\Behaviours\StandardResponses;
use Somnambulist\Bundles\ApiBundle\Controllers\ApiController;

abstract class AbstractController extends ApiController
{
    use StandardResponses;
}
