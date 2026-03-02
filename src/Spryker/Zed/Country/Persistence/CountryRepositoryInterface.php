<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Country\Persistence;

use Generated\Shared\Transfer\CountryCollectionTransfer;
use Generated\Shared\Transfer\CountryCriteriaTransfer;
use Generated\Shared\Transfer\CountryTransfer;

interface CountryRepositoryInterface
{
    /**
     * @param array<string> $iso2Codes
     *
     * @return \Generated\Shared\Transfer\CountryCollectionTransfer
     */
    public function getCountriesByIso2Codes(array $iso2Codes): CountryCollectionTransfer;

    /**
     * Result format:
     * [
     *     $idStore => [
     *         'codes' => [$iso2Code, ...],
     *         'names' => [$countryName, ...]
     *     ],
     *     ...
     * ]
     *
     * @phpstan-return array<int, array<string, array<int, string>>>
     *
     * @param array<int> $storeIds
     *
     * @return array<int, array>
     */
    public function getCountryDataGroupedByIdStore(array $storeIds): array;

    public function countCountriesByIso2Code(string $iso2Code): int;

    public function getRegionsCountByIso2Code(string $iso2Code): int;

    public function getAvailableCountries(): CountryCollectionTransfer;

    public function findCountryByName(string $countryName): ?CountryTransfer;

    public function findCountryByIso2Code(string $iso2Code): ?CountryTransfer;

    public function findCountryByIso3Code(string $iso3Code): ?CountryTransfer;

    public function getCountryCollection(
        CountryCriteriaTransfer $countryCriteriaTransfer
    ): CountryCollectionTransfer;

    /**
     * @param list<int> $countryIds
     *
     * @return array<int, list<\Generated\Shared\Transfer\RegionTransfer>>
     */
    public function getRegionsGroupedByIdCountry(array $countryIds): array;
}
