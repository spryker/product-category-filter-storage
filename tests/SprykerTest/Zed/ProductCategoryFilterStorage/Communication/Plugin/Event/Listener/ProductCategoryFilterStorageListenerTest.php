<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\ProductCategoryFilterStorage\Communication\Plugin\Event\Listener;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\EventEntityTransfer;
use Generated\Shared\Transfer\ProductCategoryFilterTransfer;
use Orm\Zed\ProductCategoryFilter\Persistence\Map\SpyProductCategoryFilterTableMap;
use Orm\Zed\ProductCategoryFilterStorage\Persistence\SpyProductCategoryFilterStorageQuery;
use Spryker\Zed\ProductCategoryFilter\Business\ProductCategoryFilterFacade;
use Spryker\Zed\ProductCategoryFilter\Dependency\ProductCategoryFilterEvents;
use Spryker\Zed\ProductCategoryFilterStorage\Business\ProductCategoryFilterStorageBusinessFactory;
use Spryker\Zed\ProductCategoryFilterStorage\Business\ProductCategoryFilterStorageFacade;
use Spryker\Zed\ProductCategoryFilterStorage\Communication\Plugin\Event\Listener\ProductCategoryFilterPublishStorageListener;
use SprykerTest\Zed\ProductCategoryFilterStorage\ProductCategoryFilterStorageCommunicationTester;
use SprykerTest\Zed\ProductCategoryFilterStorage\ProductCategoryFilterStorageConfigMock;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group ProductCategoryFilterStorage
 * @group Communication
 * @group Plugin
 * @group Event
 * @group Listener
 * @group ProductCategoryFilterStorageListenerTest
 * Add your own group annotations below this line
 */
class ProductCategoryFilterStorageListenerTest extends Unit
{
    protected const int ID_CATEGORY_FIRST = 1;

    protected const int ID_CATEGORY_SECOND = 2;

    // Event carries only the foreign key - the shape produced by a live Back Office save.
    protected const string EVENT_SOURCE_FOREIGN_KEY = 'foreign_key';

    // Event carries only the primary key - the shape produced by `publish:trigger-events`.
    protected const string EVENT_SOURCE_PRIMARY_KEY = 'primary_key';

    // Event carries both keys - the shape of a fully populated live entity event.
    protected const string EVENT_SOURCE_BOTH = 'both';

    protected ProductCategoryFilterStorageCommunicationTester $tester;

    /**
     * @return array<string, array{eventSources: array<array{idCategory: int, source: string}>, expectedCategoryIds: array<int>}>
     */
    public function getEventSourceScenarios(): array
    {
        return [
            'foreign key only (Back Office save) publishes the category' => [
                'eventSources' => [
                    ['idCategory' => static::ID_CATEGORY_FIRST, 'source' => static::EVENT_SOURCE_FOREIGN_KEY],
                ],
                'expectedCategoryIds' => [static::ID_CATEGORY_FIRST],
            ],
            'primary key only (publish:trigger-events) publishes the category' => [
                'eventSources' => [
                    ['idCategory' => static::ID_CATEGORY_FIRST, 'source' => static::EVENT_SOURCE_PRIMARY_KEY],
                ],
                'expectedCategoryIds' => [static::ID_CATEGORY_FIRST],
            ],
            'mixed batch with one foreign key and one primary key publishes both categories' => [
                'eventSources' => [
                    ['idCategory' => static::ID_CATEGORY_FIRST, 'source' => static::EVENT_SOURCE_PRIMARY_KEY],
                    ['idCategory' => static::ID_CATEGORY_SECOND, 'source' => static::EVENT_SOURCE_FOREIGN_KEY],
                ],
                'expectedCategoryIds' => [static::ID_CATEGORY_FIRST, static::ID_CATEGORY_SECOND],
            ],
            'event carrying both keys for the same category publishes it once' => [
                'eventSources' => [
                    ['idCategory' => static::ID_CATEGORY_FIRST, 'source' => static::EVENT_SOURCE_BOTH],
                ],
                'expectedCategoryIds' => [static::ID_CATEGORY_FIRST],
            ],
        ];
    }

    /**
     * @dataProvider getEventSourceScenarios
     *
     * @param array<array{idCategory: int, source: string}> $eventSources
     * @param array<int> $expectedCategoryIds
     *
     * @return void
     */
    public function testHandleBulkPublishesStorageForCategoriesResolvedFromBothEventShapes(
        array $eventSources,
        array $expectedCategoryIds
    ): void {
        // Arrange
        $productCategoryFilterIdsByIdCategory = $this->seedProductCategoryFilters($eventSources);
        $eventEntityTransfers = $this->buildEventEntityTransfers($eventSources, $productCategoryFilterIdsByIdCategory);

        // Act
        $this->createListener()->handleBulk($eventEntityTransfers, ProductCategoryFilterEvents::ENTITY_SPY_PRODUCT_CATEGORY_FILTER_CREATE);

        // Assert
        foreach ($expectedCategoryIds as $idCategory) {
            $this->assertSame(
                1,
                SpyProductCategoryFilterStorageQuery::create()->filterByFkCategory($idCategory)->count(),
                sprintf('Expected exactly one storage entry for category %d.', $idCategory),
            );
        }
    }

    /**
     * @param array<array{idCategory: int, source: string}> $eventSources
     *
     * @return array<int, int>
     */
    protected function seedProductCategoryFilters(array $eventSources): array
    {
        $productCategoryFilterFacade = new ProductCategoryFilterFacade();
        $productCategoryFilterIdsByIdCategory = [];

        foreach ($this->extractIdCategories($eventSources) as $idCategory) {
            $this->deleteExistingProductCategoryFilter($productCategoryFilterFacade, $idCategory);
            SpyProductCategoryFilterStorageQuery::create()->filterByFkCategory($idCategory)->delete();

            $productCategoryFilterTransfer = $productCategoryFilterFacade->createProductCategoryFilter(
                (new ProductCategoryFilterTransfer())->setFkCategory($idCategory),
            );
            $productCategoryFilterIdsByIdCategory[$idCategory] = $productCategoryFilterTransfer->getIdProductCategoryFilter();
        }

        return $productCategoryFilterIdsByIdCategory;
    }

    /**
     * @param array<array{idCategory: int, source: string}> $eventSources
     *
     * @return array<int>
     */
    protected function extractIdCategories(array $eventSources): array
    {
        return array_values(array_unique(array_column($eventSources, 'idCategory')));
    }

    protected function deleteExistingProductCategoryFilter(ProductCategoryFilterFacade $productCategoryFilterFacade, int $idCategory): void
    {
        $productCategoryFilterTransfer = $productCategoryFilterFacade->findProductCategoryFilterByCategoryId($idCategory);

        if (!$productCategoryFilterTransfer->getIdProductCategoryFilter()) {
            return;
        }

        $productCategoryFilterFacade->deleteProductCategoryFilterByCategoryId($idCategory);
    }

    /**
     * @param array<array{idCategory: int, source: string}> $eventSources
     * @param array<int, int> $productCategoryFilterIdsByIdCategory
     *
     * @return array<\Generated\Shared\Transfer\EventEntityTransfer>
     */
    protected function buildEventEntityTransfers(array $eventSources, array $productCategoryFilterIdsByIdCategory): array
    {
        $eventEntityTransfers = [];
        foreach ($eventSources as $eventSource) {
            $eventEntityTransfers[] = $this->buildEventEntityTransfer(
                $eventSource['source'],
                $eventSource['idCategory'],
                $productCategoryFilterIdsByIdCategory[$eventSource['idCategory']],
            );
        }

        return $eventEntityTransfers;
    }

    protected function buildEventEntityTransfer(string $source, int $idCategory, int $idProductCategoryFilter): EventEntityTransfer
    {
        $eventEntityTransfer = new EventEntityTransfer();

        if ($source !== static::EVENT_SOURCE_PRIMARY_KEY) {
            $eventEntityTransfer->setForeignKeys([SpyProductCategoryFilterTableMap::COL_FK_CATEGORY => $idCategory]);
        }

        if ($source !== static::EVENT_SOURCE_FOREIGN_KEY) {
            $eventEntityTransfer->setId($idProductCategoryFilter);
        }

        return $eventEntityTransfer;
    }

    protected function createListener(): ProductCategoryFilterPublishStorageListener
    {
        $productCategoryFilterPublishStorageListener = new ProductCategoryFilterPublishStorageListener();
        $productCategoryFilterPublishStorageListener->setFacade($this->getProductCategoryFilterStorageFacade());

        return $productCategoryFilterPublishStorageListener;
    }

    protected function getProductCategoryFilterStorageFacade(): ProductCategoryFilterStorageFacade
    {
        $factory = new ProductCategoryFilterStorageBusinessFactory();
        $factory->setConfig(new ProductCategoryFilterStorageConfigMock());

        $facade = new ProductCategoryFilterStorageFacade();
        $facade->setFactory($factory);

        return $facade;
    }
}
