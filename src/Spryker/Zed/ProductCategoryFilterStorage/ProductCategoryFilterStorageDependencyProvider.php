<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductCategoryFilterStorage;

use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;
use Spryker\Zed\ProductCategoryFilterStorage\Dependency\Facade\ProductCategoryFilterStorageToEventBehaviorFacadeBridge;
use Spryker\Zed\ProductCategoryFilterStorage\Dependency\QueryContainer\ProductCategoryFilterStorageToProductCategoryFilterQueryContainerBridge;
use Spryker\Zed\ProductCategoryFilterStorage\Dependency\Service\ProductCategoryFilterStorageToUtilEncodingBridge;

/**
 * @method \Spryker\Zed\ProductCategoryFilterStorage\ProductCategoryFilterStorageConfig getConfig()
 */
class ProductCategoryFilterStorageDependencyProvider extends AbstractBundleDependencyProvider
{
    /**
     * @var string
     */
    public const FACADE_EVENT_BEHAVIOR = 'FACADE_EVENT_BEHAVIOR';

    /**
     * @var string
     */
    public const SERVICE_UTIL_ENCODING = 'SERVICE_UTIL_ENCODING';

    /**
     * @var string
     */
    public const QUERY_CONTAINER_PRODUCT_CATEGORY_FILTER = 'QUERY_CONTAINER_PRODUCT_CATEGORY_FILTER';

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideCommunicationLayerDependencies(Container $container)
    {
        $container->set(static::FACADE_EVENT_BEHAVIOR, function (Container $container) {
            return new ProductCategoryFilterStorageToEventBehaviorFacadeBridge($container->getLocator()->eventBehavior()->facade());
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideBusinessLayerDependencies(Container $container)
    {
        $container->set(static::SERVICE_UTIL_ENCODING, function (Container $container) {
            return new ProductCategoryFilterStorageToUtilEncodingBridge($container->getLocator()->utilEncoding()->service());
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function providePersistenceLayerDependencies(Container $container)
    {
        $container->set(static::QUERY_CONTAINER_PRODUCT_CATEGORY_FILTER, function (Container $container) {
            return new ProductCategoryFilterStorageToProductCategoryFilterQueryContainerBridge($container->getLocator()->productCategoryFilter()->queryContainer());
        });

        return $container;
    }
}
