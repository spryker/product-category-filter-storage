<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductCategoryFilterStorage\Communication\Plugin\Event\Listener;

use Orm\Zed\ProductCategoryFilter\Persistence\Map\SpyProductCategoryFilterTableMap;
use Spryker\Zed\Event\Dependency\Plugin\EventBulkHandlerInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * @method \Spryker\Zed\ProductCategoryFilterStorage\Persistence\ProductCategoryFilterStorageQueryContainerInterface getQueryContainer()
 * @method \Spryker\Zed\ProductCategoryFilterStorage\Communication\ProductCategoryFilterStorageCommunicationFactory getFactory()
 * @method \Spryker\Zed\ProductCategoryFilterStorage\Business\ProductCategoryFilterStorageFacadeInterface getFacade()
 * @method \Spryker\Zed\ProductCategoryFilterStorage\ProductCategoryFilterStorageConfig getConfig()
 */
class ProductCategoryFilterPublishStorageListener extends AbstractPlugin implements EventBulkHandlerInterface
{
    /**
     * @api
     *
     * @param array<\Generated\Shared\Transfer\EventEntityTransfer> $eventEntityTransfers
     * @param string $eventName
     *
     * @return void
     */
    public function handleBulk(array $eventEntityTransfers, $eventName)
    {
        $categoryIds = $this->getFactory()->getEventBehaviorFacade()->getEventTransferForeignKeys($eventEntityTransfers, SpyProductCategoryFilterTableMap::COL_FK_CATEGORY);

        $this->getFacade()->publish($categoryIds);
    }
}
