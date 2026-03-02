<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Country\Persistence\Propel\Mapper;

use Generated\Shared\Transfer\CountryCollectionTransfer;
use Generated\Shared\Transfer\CountryTransfer;
use Generated\Shared\Transfer\RegionTransfer;
use Orm\Zed\Country\Persistence\SpyCountry;
use Orm\Zed\Country\Persistence\SpyRegion;
use Propel\Runtime\Collection\Collection;

class CountryMapper
{
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
