<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerTest\Zed\Country\Business;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\CountryTransfer;
use Generated\Shared\Transfer\RegionTransfer;
use Spryker\Zed\Country\Communication\Plugin\Customer\CountryAddressValidatorPlugin;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group Country
 * @group Business
 * @group CustomerAddressValidatorTest
 * Add your own group annotations below this line
 */
class CustomerAddressValidatorTest extends Unit
{
    protected const string ISO_2_CODE = 'DE';

    protected const string ISO_2_CODE_OTHER = 'AT';

    protected const string ISO_2_CODE_NOT_A_COUNTRY = 'QQ';

    protected const string REGION_CODE = 'DE-BE';

    protected const string REGION_CODE_UNKNOWN = 'DE-XX';

    protected const string ERROR_MESSAGE_COUNTRY_UNKNOWN = 'country.validation.unknown_country';

    protected const string ERROR_MESSAGE_REGION_UNKNOWN = 'country.validation.unknown_region';

    protected const string ERROR_MESSAGE_REGION_NOT_IN_COUNTRY = 'country.validation.region_not_in_country';

    /**
     * @var \SprykerTest\Zed\Country\CountryBusinessTester
     */
    protected $tester;

    public function testAcceptsAnInstalledCountryWithoutARegion(): void
    {
        // Arrange
        $addressTransfer = (new AddressTransfer())->setIso2Code(static::ISO_2_CODE);

        // Act
        $addressResponseTransfer = (new CountryAddressValidatorPlugin())->validate($addressTransfer);

        // Assert
        $this->assertTrue($addressResponseTransfer->getIsSuccess());
        $this->assertCount(0, $addressResponseTransfer->getErrors());
    }

    public function testAcceptsAnAddressWithoutACountryCode(): void
    {
        // Arrange: the Customer module falls back to the shop's own country, so this is not a rejection.
        $addressTransfer = new AddressTransfer();

        // Act
        $addressResponseTransfer = (new CountryAddressValidatorPlugin())->validate($addressTransfer);

        // Assert
        $this->assertTrue($addressResponseTransfer->getIsSuccess());
    }

    public function testRejectsACountryThisShopDoesNotCarry(): void
    {
        // Arrange
        $addressTransfer = (new AddressTransfer())->setIso2Code(static::ISO_2_CODE_NOT_A_COUNTRY);

        // Act
        $addressResponseTransfer = (new CountryAddressValidatorPlugin())->validate($addressTransfer);

        // Assert
        $this->assertFalse($addressResponseTransfer->getIsSuccess());
        $this->assertSame(
            static::ERROR_MESSAGE_COUNTRY_UNKNOWN,
            $addressResponseTransfer->getErrors()->offsetGet(0)->getMessage(),
        );
    }

    public function testRejectsARegionNoCountryCarries(): void
    {
        // Arrange
        $addressTransfer = (new AddressTransfer())
            ->setIso2Code(static::ISO_2_CODE)
            ->setRegion(static::REGION_CODE_UNKNOWN);

        // Act
        $addressResponseTransfer = (new CountryAddressValidatorPlugin())->validate($addressTransfer);

        // Assert
        $this->assertFalse($addressResponseTransfer->getIsSuccess());
        $this->assertSame(
            static::ERROR_MESSAGE_REGION_UNKNOWN,
            $addressResponseTransfer->getErrors()->offsetGet(0)->getMessage(),
        );
    }

    public function testAcceptsARegionOfTheAddressCountry(): void
    {
        // Arrange
        $this->haveRegionInCountry(static::ISO_2_CODE, static::REGION_CODE);

        $addressTransfer = (new AddressTransfer())
            ->setIso2Code(static::ISO_2_CODE)
            ->setRegion(static::REGION_CODE);

        // Act
        $addressResponseTransfer = (new CountryAddressValidatorPlugin())->validate($addressTransfer);

        // Assert
        $this->assertTrue($addressResponseTransfer->getIsSuccess());
    }

    public function testRejectsARegionThatBelongsToAnotherCountry(): void
    {
        // Arrange: the region exists, so only the country comparison can catch it.
        $this->haveRegionInCountry(static::ISO_2_CODE, static::REGION_CODE);

        $addressTransfer = (new AddressTransfer())
            ->setIso2Code(static::ISO_2_CODE_OTHER)
            ->setRegion(static::REGION_CODE);

        // Act
        $addressResponseTransfer = (new CountryAddressValidatorPlugin())->validate($addressTransfer);

        // Assert
        $this->assertFalse($addressResponseTransfer->getIsSuccess());
        $this->assertSame(
            static::ERROR_MESSAGE_REGION_NOT_IN_COUNTRY,
            $addressResponseTransfer->getErrors()->offsetGet(0)->getMessage(),
        );
    }

    public function testRejectsARegionSentWithoutACountryToPlaceItIn(): void
    {
        // Arrange
        $addressTransfer = (new AddressTransfer())->setRegion(static::REGION_CODE);

        // Act
        $addressResponseTransfer = (new CountryAddressValidatorPlugin())->validate($addressTransfer);

        // Assert
        $this->assertFalse($addressResponseTransfer->getIsSuccess());
        $this->assertSame(
            static::ERROR_MESSAGE_REGION_NOT_IN_COUNTRY,
            $addressResponseTransfer->getErrors()->offsetGet(0)->getMessage(),
        );
    }

    protected function haveRegionInCountry(string $countryIso2Code, string $regionIso2Code): void
    {
        $countryTransfer = $this->tester->haveCountry([CountryTransfer::ISO2_CODE => $countryIso2Code]);

        $this->tester->haveRegion([
            RegionTransfer::ISO2_CODE => $regionIso2Code,
            RegionTransfer::FK_COUNTRY => $countryTransfer->getIdCountryOrFail(),
        ]);
    }
}
