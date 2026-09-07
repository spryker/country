<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Country\Business\Region;

use Generated\Shared\Transfer\RegionCollectionTransfer;
use Generated\Shared\Transfer\RegionCriteriaTransfer;

interface RegionReaderInterface
{
    public function regionExists(string $isoCode): bool;

    public function getRegionCollection(RegionCriteriaTransfer $regionCriteriaTransfer): RegionCollectionTransfer;
}
