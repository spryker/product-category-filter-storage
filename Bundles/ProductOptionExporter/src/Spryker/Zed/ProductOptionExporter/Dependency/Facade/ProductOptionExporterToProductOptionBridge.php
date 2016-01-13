<?php
/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Spryker\Zed\ProductOptionExporter\Dependency\Facade;

use Spryker\Zed\ProductOption\Business\ProductOptionFacade;

class ProductOptionExporterToProductOptionBridge implements ProductOptionExporterToProductOptionInterface
{

    /**
     * @var ProductOptionFacade
     */
    protected $productOptionFacade;

    /**
     * ProductOptionExporterToProductOptionBridge constructor.
     *
     * @param ProductOptionFacade $productOptionFacade
     */
    public function __construct($productOptionFacade)
    {
        $this->productOptionFacade = $productOptionFacade;
    }

    /**
     * @param int $idProduct
     * @param int $idLocale
     *
     * @return array
     */
    public function getTypeUsagesForProductConcrete($idProduct, $idLocale)
    {
        return $this->productOptionFacade->getTypeUsagesForProductConcrete($idProduct, $idLocale);
    }

    /**
     * @param int $idTypeUsage
     * @param int $idLocale
     *
     * @return array
     */
    public function getValueUsagesForTypeUsage($idTypeUsage, $idLocale)
    {
        return $this->productOptionFacade->getValueUsagesForTypeUsage($idTypeUsage, $idLocale);
    }

    /**
     * @param int $idTypeUsage
     *
     * @return array
     */
    public function getTypeExclusionsForTypeUsage($idTypeUsage)
    {
        return $this->productOptionFacade->getTypeExclusionsForTypeUsage($idTypeUsage);
    }

    /**
     * @param int $idValueUsage
     *
     * @return array
     */
    public function getValueConstraintsForValueUsage($idValueUsage)
    {
        return $this->productOptionFacade->getValueConstraintsForValueUsage($idValueUsage);
    }

    /**
     * @param int $idValueUsage
     * @param string $operator
     *
     * @return array
     */
    public function getValueConstraintsForValueUsageByOperator($idValueUsage, $operator)
    {
        return $this->productOptionFacade->getValueConstraintsForValueUsageByOperator($idValueUsage, $operator);
    }

    /**
     * @param int $idProduct
     *
     * @return array
     */
    public function getConfigPresetsForProductConcrete($idProduct)
    {
        return $this->productOptionFacade->getConfigPresetsForProductConcrete($idProduct);
    }

    /**
     * @param int $idConfigPreset
     *
     * @return array
     */
    public function getValueUsagesForConfigPreset($idConfigPreset)
    {
        return $this->productOptionFacade->getValueUsagesForConfigPreset($idConfigPreset);
    }

    /**
     * @param int $idTypeUsage
     *
     * @return string|null
     */
    public function getEffectiveTaxRateForTypeUsage($idTypeUsage)
    {
        return $this->productOptionFacade->getEffectiveTaxRateForTypeUsage($idTypeUsage);
    }

}