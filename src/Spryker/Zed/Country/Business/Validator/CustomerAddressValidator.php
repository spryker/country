<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Country\Business\Validator;

use Generated\Shared\Transfer\AddressResponseTransfer;
use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\CustomerErrorTransfer;
use Generated\Shared\Transfer\RegionConditionsTransfer;
use Generated\Shared\Transfer\RegionCriteriaTransfer;
use Generated\Shared\Transfer\RegionTransfer;
use Spryker\Zed\Country\Business\Region\RegionReaderInterface;
use Spryker\Zed\Country\Persistence\CountryRepositoryInterface;

class CustomerAddressValidator implements CustomerAddressValidatorInterface
{
    protected const string ERROR_MESSAGE_COUNTRY_UNKNOWN = 'country.validation.unknown_country';

    protected const string ERROR_MESSAGE_REGION_UNKNOWN = 'country.validation.unknown_region';

    protected const string ERROR_MESSAGE_REGION_NOT_IN_COUNTRY = 'country.validation.region_not_in_country';

    public function __construct(
        protected CountryRepositoryInterface $countryRepository,
        protected RegionReaderInterface $regionReader,
    ) {
    }

    public function validateCustomerAddress(AddressTransfer $addressTransfer): AddressResponseTransfer
    {
        $addressResponseTransfer = (new AddressResponseTransfer())
            ->setAddress($addressTransfer)
            ->setIsSuccess(true);

        $iso2Code = $addressTransfer->getIso2Code();

        if (!$iso2Code) {
            return $this->validateRegionWithoutCountry($addressTransfer, $addressResponseTransfer);
        }

        $countryTransfer = $this->countryRepository->findCountryByIso2Code($iso2Code);

        if ($countryTransfer === null) {
            return $this->addError($addressResponseTransfer, static::ERROR_MESSAGE_COUNTRY_UNKNOWN, $iso2Code);
        }

        $regionCode = $addressTransfer->getRegion();

        if (!$regionCode) {
            return $addressResponseTransfer;
        }

        $regionTransfer = $this->findRegionByIso2Code($regionCode);

        if ($regionTransfer === null) {
            return $this->addError($addressResponseTransfer, static::ERROR_MESSAGE_REGION_UNKNOWN, $regionCode);
        }

        if ($regionTransfer->getFkCountry() !== $countryTransfer->getIdCountry()) {
            return $this->addError($addressResponseTransfer, static::ERROR_MESSAGE_REGION_NOT_IN_COUNTRY, $regionCode);
        }

        return $addressResponseTransfer;
    }

    protected function findRegionByIso2Code(string $iso2Code): ?RegionTransfer
    {
        $regionCriteriaTransfer = (new RegionCriteriaTransfer())
            ->setRegionConditions(
                (new RegionConditionsTransfer())->addIso2Code($iso2Code),
            );

        $regionTransfers = $this->regionReader
            ->getRegionCollection($regionCriteriaTransfer)
            ->getRegions();

        return $regionTransfers->count() > 0 ? $regionTransfers[0] : null;
    }

    protected function validateRegionWithoutCountry(
        AddressTransfer $addressTransfer,
        AddressResponseTransfer $addressResponseTransfer
    ): AddressResponseTransfer {
        $regionCode = $addressTransfer->getRegion();

        if (!$regionCode) {
            return $addressResponseTransfer;
        }

        return $this->addError($addressResponseTransfer, static::ERROR_MESSAGE_REGION_NOT_IN_COUNTRY, $regionCode);
    }

    protected function addError(
        AddressResponseTransfer $addressResponseTransfer,
        string $message,
        string $value
    ): AddressResponseTransfer {
        return $addressResponseTransfer
            ->setIsSuccess(false)
            ->addError(
                (new CustomerErrorTransfer())
                    ->setMessage($message)
                    ->setParameters(['%value%' => $value]),
            );
    }
}
