<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerTest\Zed\Country\Business\Facade;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\CountryCollectionTransfer;
use Generated\Shared\Transfer\CountryTransfer;
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
 * @group GetAvailableCountriesTest
 * Add your own group annotations below this line
 */
class GetAvailableCountriesTest extends Unit
{
    protected const string COUNTRY_ISO2_CODE = 'Q1';

    protected const string COUNTRY_ISO3_CODE = 'Q01';

    protected const string COUNTRY_NAME = 'Zzz Test Country';

    protected const string COUNTRY_ISO2_CODE_SECOND = 'Q2';

    protected const string COUNTRY_ISO3_CODE_SECOND = 'Q02';

    protected const string COUNTRY_NAME_SECOND = 'Aaa Test Country';

    protected const string POSTAL_CODE_REGEX = '\d{5}';

    protected const string REGION_ISO2_CODE = 'Q1-AA';

    protected const string REGION_NAME = 'First Test Region';

    protected const string REGION_ISO2_CODE_SECOND = 'Q1-BB';

    protected const string REGION_NAME_SECOND = 'Second Test Region';

    protected const string REGION_ISO2_CODE_OTHER_COUNTRY = 'Q2-AA';

    /**
     * @var \SprykerTest\Zed\Country\CountryBusinessTester
     */
    protected CountryBusinessTester $tester;

    /**
     * @dataProvider getReturnsCountryWithEveryPropertyMappedDataProvider
     */
    public function testReturnsCountryWithEveryPropertyMapped(bool $isPostalCodeMandatory, ?string $postalCodeRegex): void
    {
        // Arrange
        $countryTransfer = $this->tester->haveCountryTransfer([
            CountryTransfer::ISO2_CODE => static::COUNTRY_ISO2_CODE,
            CountryTransfer::ISO3_CODE => static::COUNTRY_ISO3_CODE,
            CountryTransfer::NAME => static::COUNTRY_NAME,
            CountryTransfer::POSTAL_CODE_MANDATORY => $isPostalCodeMandatory,
            CountryTransfer::POSTAL_CODE_REGEX => $postalCodeRegex,
        ]);

        // Act
        $countryCollectionTransfer = $this->tester->getFacade()->getAvailableCountries();

        // Assert
        $retrievedCountryTransfer = $this->findCountryByIso2Code($countryCollectionTransfer, static::COUNTRY_ISO2_CODE);
        $this->assertNotNull($retrievedCountryTransfer);
        $this->assertSame($countryTransfer->getIdCountryOrFail(), $retrievedCountryTransfer->getIdCountry());
        $this->assertSame(static::COUNTRY_ISO3_CODE, $retrievedCountryTransfer->getIso3Code());
        $this->assertSame(static::COUNTRY_NAME, $retrievedCountryTransfer->getName());
        $this->assertSame($isPostalCodeMandatory, $retrievedCountryTransfer->getPostalCodeMandatory());
        $this->assertSame($postalCodeRegex, $retrievedCountryTransfer->getPostalCodeRegex());
    }

    /**
     * @return array<string, array{bool, string|null}>
     */
    protected function getReturnsCountryWithEveryPropertyMappedDataProvider(): array
    {
        return [
            'postal code mandatory with regex' => [true, static::POSTAL_CODE_REGEX],
            'postal code optional without regex' => [false, null],
        ];
    }

    public function testReturnsCountryWithoutRegionsWithEmptyRegionCollection(): void
    {
        // Arrange
        $this->haveCountry();

        // Act
        $countryCollectionTransfer = $this->tester->getFacade()->getAvailableCountries();

        // Assert
        $retrievedCountryTransfer = $this->findCountryByIso2Code($countryCollectionTransfer, static::COUNTRY_ISO2_CODE);
        $this->assertNotNull($retrievedCountryTransfer);
        $this->assertCount(0, $retrievedCountryTransfer->getRegions());
    }

    public function testReturnsEveryRegionOfCountryWithEveryPropertyMapped(): void
    {
        // Arrange
        $countryTransfer = $this->haveCountry();
        $regionTransfer = $this->haveRegionInCountry($countryTransfer, static::REGION_ISO2_CODE, static::REGION_NAME);
        $secondRegionTransfer = $this->haveRegionInCountry($countryTransfer, static::REGION_ISO2_CODE_SECOND, static::REGION_NAME_SECOND);

        // Act
        $countryCollectionTransfer = $this->tester->getFacade()->getAvailableCountries();

        // Assert
        $retrievedCountryTransfer = $this->findCountryByIso2Code($countryCollectionTransfer, static::COUNTRY_ISO2_CODE);
        $this->assertNotNull($retrievedCountryTransfer);

        $retrievedRegionTransfers = $this->getRegionTransfersIndexedByIso2Code($retrievedCountryTransfer);
        $this->assertCount(2, $retrievedRegionTransfers);
        $this->assertRegionTransfer($regionTransfer, $retrievedRegionTransfers[static::REGION_ISO2_CODE]);
        $this->assertRegionTransfer($secondRegionTransfer, $retrievedRegionTransfers[static::REGION_ISO2_CODE_SECOND]);
    }

    public function testReturnsRegionsOnlyUnderTheirOwnCountry(): void
    {
        // Arrange
        $countryTransfer = $this->haveCountry();
        $otherCountryTransfer = $this->haveSecondCountry();
        $this->haveRegionInCountry($countryTransfer, static::REGION_ISO2_CODE, static::REGION_NAME);
        $this->haveRegionInCountry($otherCountryTransfer, static::REGION_ISO2_CODE_OTHER_COUNTRY, static::REGION_NAME_SECOND);

        // Act
        $countryCollectionTransfer = $this->tester->getFacade()->getAvailableCountries();

        // Assert
        $retrievedCountryTransfer = $this->findCountryByIso2Code($countryCollectionTransfer, static::COUNTRY_ISO2_CODE);
        $retrievedOtherCountryTransfer = $this->findCountryByIso2Code($countryCollectionTransfer, static::COUNTRY_ISO2_CODE_SECOND);
        $this->assertNotNull($retrievedCountryTransfer);
        $this->assertNotNull($retrievedOtherCountryTransfer);
        $this->assertSame([static::REGION_ISO2_CODE], array_keys($this->getRegionTransfersIndexedByIso2Code($retrievedCountryTransfer)));
        $this->assertSame([static::REGION_ISO2_CODE_OTHER_COUNTRY], array_keys($this->getRegionTransfersIndexedByIso2Code($retrievedOtherCountryTransfer)));
    }

    public function testReturnsCountriesOrderedByName(): void
    {
        // Arrange
        $this->haveCountry();
        $this->haveSecondCountry();

        // Act
        $countryCollectionTransfer = $this->tester->getFacade()->getAvailableCountries();

        // Assert
        $iso2Codes = [];
        foreach ($countryCollectionTransfer->getCountries() as $countryTransfer) {
            $iso2Codes[] = $countryTransfer->getIso2Code();
        }

        $this->assertLessThan(
            array_search(static::COUNTRY_ISO2_CODE, $iso2Codes, true),
            array_search(static::COUNTRY_ISO2_CODE_SECOND, $iso2Codes, true),
        );
    }

    public function testReturnsEveryCountryOnlyOnce(): void
    {
        // Arrange
        $countryTransfer = $this->haveCountry();
        $this->haveRegionInCountry($countryTransfer, static::REGION_ISO2_CODE, static::REGION_NAME);
        $this->haveRegionInCountry($countryTransfer, static::REGION_ISO2_CODE_SECOND, static::REGION_NAME_SECOND);

        // Act
        $countryCollectionTransfer = $this->tester->getFacade()->getAvailableCountries();

        // Assert
        $idCountries = [];
        foreach ($countryCollectionTransfer->getCountries() as $retrievedCountryTransfer) {
            $idCountries[] = $retrievedCountryTransfer->getIdCountry();
        }

        $this->assertSame(array_values(array_unique($idCountries)), $idCountries);
    }

    protected function haveCountry(): CountryTransfer
    {
        return $this->tester->haveCountryTransfer([
            CountryTransfer::ISO2_CODE => static::COUNTRY_ISO2_CODE,
            CountryTransfer::ISO3_CODE => static::COUNTRY_ISO3_CODE,
            CountryTransfer::NAME => static::COUNTRY_NAME,
        ]);
    }

    protected function haveSecondCountry(): CountryTransfer
    {
        return $this->tester->haveCountryTransfer([
            CountryTransfer::ISO2_CODE => static::COUNTRY_ISO2_CODE_SECOND,
            CountryTransfer::ISO3_CODE => static::COUNTRY_ISO3_CODE_SECOND,
            CountryTransfer::NAME => static::COUNTRY_NAME_SECOND,
        ]);
    }

    protected function haveRegionInCountry(CountryTransfer $countryTransfer, string $regionIso2Code, string $regionName): RegionTransfer
    {
        return $this->tester->haveRegion([
            RegionTransfer::ISO2_CODE => $regionIso2Code,
            RegionTransfer::NAME => $regionName,
            RegionTransfer::FK_COUNTRY => $countryTransfer->getIdCountryOrFail(),
        ]);
    }

    protected function findCountryByIso2Code(CountryCollectionTransfer $countryCollectionTransfer, string $iso2Code): ?CountryTransfer
    {
        foreach ($countryCollectionTransfer->getCountries() as $countryTransfer) {
            if ($countryTransfer->getIso2Code() === $iso2Code) {
                return $countryTransfer;
            }
        }

        return null;
    }

    /**
     * @return array<string, \Generated\Shared\Transfer\RegionTransfer>
     */
    protected function getRegionTransfersIndexedByIso2Code(CountryTransfer $countryTransfer): array
    {
        $regionTransfers = [];
        foreach ($countryTransfer->getRegions() as $regionTransfer) {
            $regionTransfers[$regionTransfer->getIso2CodeOrFail()] = $regionTransfer;
        }

        return $regionTransfers;
    }

    protected function assertRegionTransfer(RegionTransfer $expectedRegionTransfer, RegionTransfer $regionTransfer): void
    {
        $this->assertSame($expectedRegionTransfer->getIdRegionOrFail(), $regionTransfer->getIdRegion());
        $this->assertSame($expectedRegionTransfer->getFkCountryOrFail(), $regionTransfer->getFkCountry());
        $this->assertSame($expectedRegionTransfer->getNameOrFail(), $regionTransfer->getName());
        $this->assertSame($expectedRegionTransfer->getIso2CodeOrFail(), $regionTransfer->getIso2Code());
    }
}
