<?php

declare(strict_types=1);

namespace App\Application\Actions\Brand;

use App\Domain\Interfaces\DAO\IBrandDAO;
use App\Domain\Services\Brand\ServiceFetchBrands;
use App\Domain\Services\BrandService;
use App\Domain\Services\Service;
use App\Domain\DAO\BrandDAO;
use Cratia\ORM\DQL\Query;
use Cratia\Rest\Actions\Action;

/**
 * Class Get
 * @package App\Application\Actions\Brand
 */
class Get extends Action
{
    /**
     * @return \App\Domain\DTO\Brand[]|array|object
     */
    protected function action()
    {
        return (new ServiceFetchBrands($this->getContainer()->get(IBrandDAO::class)))->execute();
    }
}
