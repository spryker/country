<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Country\Business\Country;

use Generated\Shared\Transfer\CountryTransfer;
use Generated\Shared\Transfer\StoreResponseTransfer;
use Generated\Shared\Transfer\StoreTransfer;
use Spryker\Zed\Country\Business\Exception\CountryExistsException;
use Spryker\Zed\Country\Persistence\CountryEntityManagerInterface;

class CountryWriter implements CountryWriterInterface
{
    /**
     * @var \Spryker\Zed\Country\Persistence\CountryEntityManagerInterface
     */
    protected CountryEntityManagerInterface $entityManager;

    /**
     * @var \Spryker\Zed\Country\Business\Country\CountryReaderInterface
     */
    protected CountryReaderInterface $countryReader;

    public function __construct(
        CountryEntityManagerInterface $entityManager,
        CountryReaderInterface $countryReader
    ) {
        $this->entityManager = $entityManager;
        $this->countryReader = $countryReader;
    }

    public function createCountry(CountryTransfer $countryTransfer): CountryTransfer
    {
        $this->assertCountryDoesNotExist($countryTransfer->getIso2CodeOrFail());

        return $this->entityManager->createCountry($countryTransfer);
    }

    public function updateStoreCountries(StoreTransfer $storeTransfer): StoreResponseTransfer
    {
        $countryCollectionTransfer = $this->countryReader->getCountriesByIso2Codes($storeTransfer->getCountries());

        $countryIds = [];

        foreach ($countryCollectionTransfer->getCountries() as $countryTransfer) {
            $countryIds[] = $countryTransfer->getIdCountryOrFail();
        }

        $this->entityManager->updateStoresCountries($storeTransfer->getIdStoreOrFail(), $countryIds);

        return $this->getSuccessfulResponse($storeTransfer);
    }

    protected function getSuccessfulResponse(StoreTransfer $storeTransfer): StoreResponseTransfer
    {
        return (new StoreResponseTransfer())
            ->setStore($storeTransfer)
            ->setIsSuccessful(true);
    }

    /**
     * @param string $iso2code
     *
     * @throws \Spryker\Zed\Country\Business\Exception\CountryExistsException
     *
     * @return void
     */
    protected function assertCountryDoesNotExist(string $iso2code): void
    {
        if ($this->countryReader->countryExists($iso2code)) {
            throw new CountryExistsException();
        }
    }
}
