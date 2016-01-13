<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Spryker\Zed\Shipment\Persistence;

use Orm\Zed\Shipment\Persistence\SpyShipmentCarrierQuery;
use Spryker\Zed\Kernel\Persistence\AbstractQueryContainer;
use Orm\Zed\Shipment\Persistence\SpyShipmentMethodQuery;

class ShipmentQueryContainer extends AbstractQueryContainer implements ShipmentQueryContainerInterface
{

    /**
     * @return SpyShipmentCarrierQuery
     */
    public function queryCarriers()
    {
        return new SpyShipmentCarrierQuery();
    }

    /**
     * @return SpyShipmentCarrierQuery
     */
    public function queryActiveCarriers()
    {
        return $this->queryCarriers()->filterByIsActive(true);
    }

    /**
     * @return SpyShipmentMethodQuery
     */
    public function queryMethods()
    {
        return new SpyShipmentMethodQuery();
    }

    /**
     * @return SpyShipmentMethodQuery
     */
    public function queryActiveMethods()
    {
        return $this->queryMethods()->filterByIsActive(true);
    }

    /**
     * @param int $idMethod
     *
     * @return SpyShipmentMethodQuery
     */
    public function queryMethodByIdMethod($idMethod)
    {
        $query = $this->queryMethods();
        $query->filterByIdShipmentMethod($idMethod);

        return $query;
    }

}