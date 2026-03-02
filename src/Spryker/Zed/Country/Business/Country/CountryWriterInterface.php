<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Country\Business\Country;

use Generated\Shared\Transfer\CountryTransfer;
use Generated\Shared\Transfer\StoreResponseTransfer;
use Generated\Shared\Transfer\StoreTransfer;

interface CountryWriterInterface
{
    public function createCountry(CountryTransfer $countryTransfer): CountryTransfer;

    public function updateStoreCountries(StoreTransfer $storeTransfer): StoreResponseTransfer;
}
