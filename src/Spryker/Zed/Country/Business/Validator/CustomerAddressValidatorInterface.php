<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Country\Business\Validator;

use Generated\Shared\Transfer\AddressResponseTransfer;
use Generated\Shared\Transfer\AddressTransfer;

interface CustomerAddressValidatorInterface
{
    public function validateCustomerAddress(AddressTransfer $addressTransfer): AddressResponseTransfer;
}
