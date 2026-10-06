<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Country\Persistence\Propel\Mapper;

use ArrayObject;
use Generated\Shared\Transfer\CountryCollectionTransfer;
use Generated\Shared\Transfer\CountryTransfer;
use Generated\Shared\Transfer\RegionCollectionTransfer;
use Generated\Shared\Transfer\RegionTransfer;
use Orm\Zed\Country\Persistence\SpyCountry;
use Orm\Zed\Country\Persistence\SpyRegion;
use Propel\Runtime\Collection\Collection;

class CountryMapper
{
    public const string COLUMN_ID_COUNTRY = 'IdCountry';

    public const string COLUMN_POSTAL_CODE_MANDATORY = 'PostalCodeMandatory';

    public const string COLUMN_REGION_ID_REGION = 'RegionIdRegion';

    public const string COLUMN_REGION_NAME = 'RegionName';

    public const string COLUMN_REGION_ISO2_CODE = 'RegionIso2Code';

    /**
     * @param iterable<array<string, mixed>> $countryRegionRows
     */
    public function mapCountryRegionRowsToCountryCollectionTransfer(
        iterable $countryRegionRows,
        CountryCollectionTransfer $countryCollectionTransfer
    ): CountryCollectionTransfer {
        $countryTransfersIndexedByIdCountry = [];

        foreach ($countryRegionRows as $countryRegionRow) {
            $idCountry = $countryRegionRow[static::COLUMN_ID_COUNTRY];
            $countryTransfersIndexedByIdCountry[$idCountry] ??= $this->mapCountryRowToCountryTransfer($countryRegionRow, new CountryTransfer());

            if ($countryRegionRow[static::COLUMN_REGION_ID_REGION] === null) {
                continue;
            }

            $countryTransfersIndexedByIdCountry[$idCountry]->addRegion(
                $this->mapCountryRegionRowToRegionTransfer($countryRegionRow, new RegionTransfer()),
            );
        }

        return $countryCollectionTransfer->setCountries(new ArrayObject(array_values($countryTransfersIndexedByIdCountry)));
    }

    /**
     * @param array<string, mixed> $countryRow
     */
    protected function mapCountryRowToCountryTransfer(array $countryRow, CountryTransfer $countryTransfer): CountryTransfer
    {
        $countryTransfer->fromArray($countryRow, true);

        if ($countryRow[static::COLUMN_POSTAL_CODE_MANDATORY] !== null) {
            $countryTransfer->setPostalCodeMandatory((bool)$countryRow[static::COLUMN_POSTAL_CODE_MANDATORY]);
        }

        return $countryTransfer;
    }

    /**
     * @param array<string, mixed> $countryRegionRow
     */
    protected function mapCountryRegionRowToRegionTransfer(array $countryRegionRow, RegionTransfer $regionTransfer): RegionTransfer
    {
        return $regionTransfer
            ->setIdRegion($countryRegionRow[static::COLUMN_REGION_ID_REGION])
            ->setFkCountry($countryRegionRow[static::COLUMN_ID_COUNTRY])
            ->setName($countryRegionRow[static::COLUMN_REGION_NAME])
            ->setIso2Code($countryRegionRow[static::COLUMN_REGION_ISO2_CODE]);
    }

    /**
     * @param \Propel\Runtime\Collection\Collection<\Orm\Zed\Country\Persistence\SpyRegion> $regionEntities
     *
     * @return array<int, list<\Generated\Shared\Transfer\RegionTransfer>>
     */
    public function mapRegionEntitiesToRegionTransfersGroupedByIdCountry(Collection $regionEntities): array
    {
        $regionTransfersGroupedByIdCountry = [];

        foreach ($regionEntities as $regionEntity) {
            $regionTransfersGroupedByIdCountry[(int)$regionEntity->getFkCountry()][] = $this->mapRegionEntityToRegionTransfer(
                $regionEntity,
                new RegionTransfer(),
            );
        }

        return $regionTransfersGroupedByIdCountry;
    }

    public function mapRegionEntityToRegionTransfer(SpyRegion $regionEntity, RegionTransfer $regionTransfer): RegionTransfer
    {
        return $regionTransfer->fromArray($regionEntity->toArray(), true);
    }

    /**
     * @param iterable<\Orm\Zed\Country\Persistence\SpyRegion> $regionEntities
     */
    public function mapRegionEntitiesToRegionCollectionTransfer(
        iterable $regionEntities,
        RegionCollectionTransfer $regionCollectionTransfer
    ): RegionCollectionTransfer {
        foreach ($regionEntities as $regionEntity) {
            $regionCollectionTransfer->addRegion(
                $this->mapRegionEntityToRegionTransfer($regionEntity, new RegionTransfer()),
            );
        }

        return $regionCollectionTransfer;
    }

    /**
     * @param iterable<\Orm\Zed\Country\Persistence\SpyCountry> $countryEntities
     * @param \Generated\Shared\Transfer\CountryCollectionTransfer $countryCollectionTransfer
     *
     * @return \Generated\Shared\Transfer\CountryCollectionTransfer
     */
    public function mapCountryTransferCollection(iterable $countryEntities, CountryCollectionTransfer $countryCollectionTransfer): CountryCollectionTransfer
    {
        foreach ($countryEntities as $countryEntity) {
            $countryCollectionTransfer->addCountries(
                $this->mapCountryTransfer(
                    $countryEntity,
                    new CountryTransfer(),
                ),
            );
        }

        return $countryCollectionTransfer;
    }

    public function mapCountryTransfer(SpyCountry $countryEntity, CountryTransfer $countryTransfer): CountryTransfer
    {
        $countryTransfer = $countryTransfer
            ->fromArray($countryEntity->toArray(), true);

        foreach ($countryEntity->getSpyRegions() as $regionEntity) {
            $countryTransfer->addRegion(
                $this->mapRegionEntityToRegionTransfer($regionEntity, new RegionTransfer()),
            );
        }

        return $countryTransfer;
    }

    public function mapCountryTransferToCountryEntity(CountryTransfer $countryTransfer, SpyCountry $countryEntity): SpyCountry
    {
        return $countryEntity->setName($countryTransfer->getNameOrFail())
            ->setPostalCodeMandatory($countryTransfer->getPostalCodeMandatory())
            ->setPostalCodeRegex($countryTransfer->getPostalCodeRegex())
            ->setIso2Code($countryTransfer->getIso2CodeOrFail())
            ->setIso3Code($countryTransfer->getIso3CodeOrFail());
    }

    public function mapCountryEntityToCountryTransfer(SpyCountry $countryEntity, CountryTransfer $countryTransfer): CountryTransfer
    {
        return $countryTransfer->fromArray($countryEntity->toArray(), true);
    }

    public function mapRegionTransferToRegionEntity(RegionTransfer $regionTransfer, SpyRegion $regionEntity): SpyRegion
    {
        return $regionEntity
            ->setIso2Code($regionTransfer->getIso2CodeOrFail())
            ->setFkCountry($regionTransfer->getFkCountryOrFail())
            ->setName($regionTransfer->getNameOrFail());
    }

    /**
     * @param \Propel\Runtime\Collection\Collection<\Orm\Zed\Country\Persistence\SpyCountry> $countryEntities
     * @param \Generated\Shared\Transfer\CountryCollectionTransfer $countryCollectionTransfer
     *
     * @return \Generated\Shared\Transfer\CountryCollectionTransfer
     */
    public function mapCountryEntitiesToCountryCollectionTransfer(
        Collection $countryEntities,
        CountryCollectionTransfer $countryCollectionTransfer
    ): CountryCollectionTransfer {
        foreach ($countryEntities as $countryEntity) {
            $countryCollectionTransfer->addCountries(
                $this->mapCountryEntityToCountryTransfer($countryEntity, new CountryTransfer()),
            );
        }

        return $countryCollectionTransfer;
    }
}
