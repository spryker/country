<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Country\Communication\Plugin\Customer;

use Generated\Shared\Transfer\AddressResponseTransfer;
use Generated\Shared\Transfer\AddressTransfer;
use Spryker\Zed\CustomerExtension\Dependency\Plugin\AddressValidatorPluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * @method \Spryker\Zed\Country\Business\CountryBusinessFactory getBusinessFactory()
 * @method \Spryker\Zed\Country\Business\CountryFacadeInterface getFacade()
 * @method \Spryker\Zed\Country\CountryConfig getConfig()
 * @method \Spryker\Zed\Country\Communication\CountryCommunicationFactory getFactory()
 */
class CountryAddressValidatorPlugin extends AbstractPlugin implements AddressValidatorPluginInterface
{
    /**
     * {@inheritDoc}
     * - Rejects an `iso2Code` that names no installed country.
     * - Rejects a `region` that names no known region, or one belonging to a different country.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\AddressTransfer $addressTransfer
     *
     * @return \Generated\Shared\Transfer\AddressResponseTransfer
     */
    public function validate(AddressTransfer $addressTransfer): AddressResponseTransfer
    {
        return $this->getBusinessFactory()
            ->createCustomerAddressValidator()
            ->validateCustomerAddress($addressTransfer);
    }
}
