<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerTest\Zed\Country\Business\Facade;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\CountryTransfer;
use Generated\Shared\Transfer\RegionConditionsTransfer;
use Generated\Shared\Transfer\RegionCriteriaTransfer;
use Generated\Shared\Transfer\RegionTransfer;
use SprykerTest\Zed\Country\CountryBusinessTester;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group Country
 * @group Business
 * @group Facade
 * @group GetRegionCollectionTest
 * Add your own group annotations below this line
 */
class GetRegionCollectionTest extends Unit
{
    protected const string COUNTRY_ISO2_CODE = '02';

    protected const string COUNTRY_ISO3_CODE = '002';

    protected const string REGION_ISO2_CODE = '02-AA';

    protected const string REGION_ISO2_CODE_SECOND = '02-BB';

    protected const string REGION_ISO2_CODE_UNKNOWN = '02-ZZ';

    /**
     * @var \SprykerTest\Zed\Country\CountryBusinessTester
     */
    protected CountryBusinessTester $tester;

    public function testReturnsRegionByIso2Code(): void
    {
        // Arrange
        $countryTransfer = $this->haveCountry();
        $this->haveRegionInCountry($countryTransfer, static::REGION_ISO2_CODE_SECOND);
        $regionTransfer = $this->haveRegionInCountry($countryTransfer, static::REGION_ISO2_CODE);
        $regionCriteriaTransfer = (new RegionCriteriaTransfer())->setRegionConditions(
            (new RegionConditionsTransfer())->addIso2Code(static::REGION_ISO2_CODE),
        );

        // Act
        $regionCollectionTransfer = $this->tester->getFacade()->getRegionCollection($regionCriteriaTransfer);

        // Assert
        $this->assertCount(1, $regionCollectionTransfer->getRegions());
        /** @var \Generated\Shared\Transfer\RegionTransfer $retrievedRegionTransfer */
        $retrievedRegionTransfer = $regionCollectionTransfer->getRegions()->getIterator()->current();
        $this->assertSame(static::REGION_ISO2_CODE, $retrievedRegionTransfer->getIso2Code());
        $this->assertSame($regionTransfer->getIdRegion(), $retrievedRegionTransfer->getIdRegion());
        $this->assertSame($countryTransfer->getIdCountry(), $retrievedRegionTransfer->getFkCountry());
    }

    public function testReturnsEveryRegionOfEveryRequestedIso2Code(): void
    {
        // Arrange
        $countryTransfer = $this->haveCountry();
        $this->haveRegionInCountry($countryTransfer, static::REGION_ISO2_CODE);
        $this->haveRegionInCountry($countryTransfer, static::REGION_ISO2_CODE_SECOND);
        $regionCriteriaTransfer = (new RegionCriteriaTransfer())->setRegionConditions(
            (new RegionConditionsTransfer())
                ->addIso2Code(static::REGION_ISO2_CODE)
                ->addIso2Code(static::REGION_ISO2_CODE_SECOND),
        );

        // Act
        $regionCollectionTransfer = $this->tester->getFacade()->getRegionCollection($regionCriteriaTransfer);

        // Assert
        $this->assertCount(2, $regionCollectionTransfer->getRegions());
    }

    public function testReturnsAnEmptyCollectionForACodeNoRegionCarries(): void
    {
        // Arrange
        $this->haveRegionInCountry($this->haveCountry(), static::REGION_ISO2_CODE);
        $regionCriteriaTransfer = (new RegionCriteriaTransfer())->setRegionConditions(
            (new RegionConditionsTransfer())->addIso2Code(static::REGION_ISO2_CODE_UNKNOWN),
        );

        // Act
        $regionCollectionTransfer = $this->tester->getFacade()->getRegionCollection($regionCriteriaTransfer);

        // Assert
        $this->assertCount(0, $regionCollectionTransfer->getRegions());
    }

    public function testAppliesNoFilterWhenNoConditionsAreGiven(): void
    {
        // Arrange: matches `getCountryCollection()`, where absent conditions mean "unfiltered".
        $this->haveRegionInCountry($this->haveCountry(), static::REGION_ISO2_CODE);

        // Act
        $regionCollectionTransfer = $this->tester->getFacade()->getRegionCollection(new RegionCriteriaTransfer());

        // Assert
        $regionIso2Codes = [];

        foreach ($regionCollectionTransfer->getRegions() as $regionTransfer) {
            $regionIso2Codes[] = $regionTransfer->getIso2Code();
        }

        $this->assertContains(static::REGION_ISO2_CODE, $regionIso2Codes);
    }

    protected function haveCountry(): CountryTransfer
    {
        return $this->tester->haveCountryTransfer([
            CountryTransfer::ISO2_CODE => static::COUNTRY_ISO2_CODE,
            CountryTransfer::ISO3_CODE => static::COUNTRY_ISO3_CODE,
        ]);
    }

    protected function haveRegionInCountry(CountryTransfer $countryTransfer, string $regionIso2Code): RegionTransfer
    {
        return $this->tester->haveRegion([
            RegionTransfer::ISO2_CODE => $regionIso2Code,
            RegionTransfer::FK_COUNTRY => $countryTransfer->getIdCountryOrFail(),
        ]);
    }
}
