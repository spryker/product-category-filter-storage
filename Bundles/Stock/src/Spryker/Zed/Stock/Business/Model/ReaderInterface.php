<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Spryker\Zed\Stock\Business\Model;

use Spryker\Zed\Product\Business\Exception\MissingProductException;
use Spryker\Zed\Stock\Business\Exception\StockProductAlreadyExistsException;
use Spryker\Zed\Stock\Business\Exception\StockProductNotFoundException;
use Orm\Zed\Stock\Persistence\SpyStockProduct;

interface ReaderInterface
{

    /**
     * @return array
     */
    public function getStockTypes();

    /**
     * @param string $sku
     *
     * @return bool
     */
    public function isNeverOutOfStock($sku);

    /**
     * @param string $sku
     *
     * @return array|\PropelObjectCollection
     */
    public function getStocksProduct($sku);

    /**
     * @param string $sku
     * @param string $stockType
     *
     * @return bool
     */
    public function hasStockProduct($sku, $stockType);

    /**
     * @param string $sku
     * @param string $stockType
     *
     * @return int
     */
    public function getIdStockProduct($sku, $stockType);

    /**
     * @param string $stockType
     *
     * @return int
     */
    public function getStockTypeIdByName($stockType);

    /**
     * @param string $sku
     *
     * @throws MissingProductException
     *
     * @return int
     */
    public function getProductAbstractIdBySku($sku);

    /**
     * @param string $sku
     *
     * @throws MissingProductException
     *
     * @return int
     */
    public function getProductConcreteIdBySku($sku);

    /**
     * @param int $idStockType
     * @param int $idProduct
     *
     * @throws StockProductAlreadyExistsException
     */
    public function checkStockDoesNotExist($idStockType, $idProduct);

    /**
     * @param int $idStockProduct
     *
     * @throws StockProductNotFoundException
     *
     * @return SpyStockProduct
     */
    public function getStockProductById($idStockProduct);

}