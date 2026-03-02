<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\Country\Helper;

use Codeception\Module;
use Generated\Shared\DataBuilder\CountryBuilder;
use Generated\Shared\Transfer\CountryTransfer;
use Orm\Zed\Country\Persistence\SpyCountryQuery;
use Orm\Zed\Country\Persistence\SpyCountryStoreQuery;
use Spryker\Zed\Country\Business\CountryFacadeInterface;
use SprykerTest\Shared\Testify\Helper\DataCleanupHelperTrait;
use SprykerTest\Shared\Testify\Helper\LocatorHelperTrait;

class CountryDataHelper extends Module
{
    use LocatorHelperTrait;
    use DataCleanupHelperTrait;

    public function haveCountry(array $seed = []): CountryTransfer
    {
        $countryTransferBuilder = new CountryBuilder($seed);
        $countryTransfer = $countryTransferBuilder->build();

        return $this->getCountryFacade()->getCountryByIso2Code(
            $countryTransfer->getIso2Code(),
        );
    }

    /**
     * @param array<string, mixed> $seed
     *
     * @return \Generated\Shared\Transfer\CountryTransfer
     */
    public function haveCountryTransfer(array $seed = []): CountryTransfer
    {
        $countryTransfer = (new CountryBuilder($seed))->build();

        $countryEntity = SpyCountryQuery::create()
            ->filterByIso2Code($countryTransfer->getIso2Code())
            ->findOneOrCreate();
        $countryEntity->fromArray($countryTransfer->modifiedToArray());
        $countryEntity->save();

        $this->getDataCleanupHelper()->_addCleanup(function () use ($countryEntity): void {
            $this->deleteCountry($countryEntity->getIdCountry());
        });

        return $countryTransfer->fromArray($countryEntity->toArray());
    }

    public function haveCountryStore(int $idStore, int $idCountry): int
    {
        $countryStoreEntity = $this->createCountryStorePropelQuery()
            ->filterByFkStore($idStore)
            ->filterByFkCountry($idCountry)
            ->findOneOrCreate();

        $countryStoreEntity->save();

        return $countryStoreEntity->getIdCountryStore();
    }

    public function countryStoreExists(int $idStore, int $idCountry): bool
    {
        return $this->createCountryStorePropelQuery()
            ->filterByFkStore($idStore)
            ->filterByFkCountry($idCountry)
            ->exists();
    }

    public function deleteCountryStore(int $idStore): void
    {
        $this->createCountryStorePropelQuery()
            ->filterByFkStore($idStore)
            ->delete();
    }

    protected function createCountryStorePropelQuery(): SpyCountryStoreQuery
    {
        return SpyCountryStoreQuery::create();
    }

    protected function deleteCountry(int $idCountry): void
    {
        $countryEntity = $this->getCountryQuery()->findOneByIdCountry($idCountry);

        if ($countryEntity) {
            $countryEntity->delete();
        }
    }

    protected function getCountryQuery(): SpyCountryQuery
    {
        return SpyCountryQuery::create();
    }

    protected function getCountryFacade(): CountryFacadeInterface
    {
        return $this->getLocator()->country()->facade();
    }

    public function ensureCountryStoreDatabaseTableIsEmpty(): void
    {
        $countryStoreQuery = $this->createCountryStorePropelQuery();
        $countryStoreQuery->deleteAll();
    }

    public function getCountryStoreRelationsCount(): int
    {
        return $this->createCountryStorePropelQuery()->count();
    }
}
