<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Country\Communication\Plugin\CheckoutRestApi;

use Generated\Shared\Transfer\CheckoutDataTransfer;
use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Spryker\Zed\CheckoutRestApiExtension\Dependency\Plugin\CheckoutDataValidatorPluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * @deprecated Use {@link \Spryker\Zed\Country\Communication\Plugin\CheckoutRestApi\CountriesCheckoutDataValidatorPlugin} instead.
 *
 * @method \Spryker\Zed\Country\Business\CountryFacadeInterface getFacade()
 * @method \Spryker\Zed\Country\CountryConfig getConfig()
 * @method \Spryker\Zed\Country\Communication\CountryCommunicationFactory getFactory()
 */
class CountryCheckoutDataValidatorPlugin extends AbstractPlugin implements CheckoutDataValidatorPluginInterface
{
    /**
     * {@inheritDoc}
     * - Verifies if countries can be found by countryIso2Codes given in billingAddress.
     * - Verifies if billingAddress is set.
     * - Verifies if countries can be found by countryIso2Codes given in CheckoutDataTransfer.shipments.shippingAddress.
     * - Returns CheckoutResponseTransfer with error if any check was failed.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\CheckoutDataTransfer $checkoutDataTransfer
     *
     * @return \Generated\Shared\Transfer\CheckoutResponseTransfer
     */
    public function validateCheckoutData(CheckoutDataTransfer $checkoutDataTransfer): CheckoutResponseTransfer
    {
        return $this->getFacade()->validateCountryCheckoutData($checkoutDataTransfer);
    }
}
