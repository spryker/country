<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Country\Business\Region;

use Spryker\Zed\Country\Persistence\CountryRepositoryInterface;

class RegionReader implements RegionReaderInterface
{
    /**
     * @var \Spryker\Zed\Country\Persistence\CountryRepositoryInterface
     */
    protected $repository;

    public function __construct(CountryRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function regionExists(string $isoCode): bool
    {
        return $this->repository->getRegionsCountByIso2Code($isoCode) > 0;
    }
}
