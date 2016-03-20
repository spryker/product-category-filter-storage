<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductOptionDiscountConnector\Communication\Plugin\OrderAmountAggregator;

use Generated\Shared\Transfer\OrderTransfer;
use Spryker\Zed\Discount\Communication\Plugin\AbstractDiscountPlugin;
use Spryker\Zed\SalesAggregator\Dependency\Plugin\OrderTotalsAggregatePluginInterface;

/**
 * @method \Spryker\Zed\ProductOptionDiscountConnector\Business\ProductOptionDiscountConnectorFacade getFacade()
 */
class DiscountTotalAmountWithProductOptionsAggregatorPlugin extends AbstractDiscountPlugin implements OrderTotalsAggregatePluginInterface
{

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return void
     */
    public function aggregate(OrderTransfer $orderTransfer)
    {
        $this->getFacade()->aggregateOrderTotalDiscountAmount($orderTransfer);
    }

}