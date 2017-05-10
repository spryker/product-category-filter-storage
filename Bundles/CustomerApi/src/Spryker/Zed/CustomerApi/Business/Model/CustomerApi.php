<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CustomerApi\Business\Model;

use Generated\Shared\Transfer\ApiDataTransfer;
use Generated\Shared\Transfer\ApiQueryBuilderQueryTransfer;
use Generated\Shared\Transfer\ApiRequestTransfer;
use Generated\Shared\Transfer\CustomerApiTransfer;
use Generated\Shared\Transfer\PropelQueryBuilderColumnSelectionTransfer;
use Generated\Shared\Transfer\PropelQueryBuilderColumnTransfer;
use Orm\Zed\Customer\Persistence\Map\SpyCustomerTableMap;
use Orm\Zed\Customer\Persistence\SpyCustomer;
use Propel\Runtime\Map\TableMap;
use Spryker\Zed\Api\Business\Exception\EntityNotFoundException;
use Spryker\Zed\CustomerApi\Business\Mapper\EntityMapperInterface;
use Spryker\Zed\CustomerApi\Business\Mapper\TransferMapperInterface;
use Spryker\Zed\CustomerApi\Dependency\QueryContainer\CustomerApiToApiInterface;
use Spryker\Zed\CustomerApi\Dependency\QueryContainer\CustomerApiToApiQueryBuilderInterface;
use Spryker\Zed\CustomerApi\Persistence\CustomerApiQueryContainerInterface;

class CustomerApi implements CustomerApiInterface
{

    /**
     * @var \Spryker\Zed\CustomerApi\Dependency\QueryContainer\CustomerApiToApiInterface
     */
    protected $apiQueryContainer;

    /**
     * @var \Spryker\Zed\CustomerApi\Dependency\QueryContainer\CustomerApiToApiQueryBuilderInterface
     */
    protected $apiQueryBuilderQueryContainer;

    /**
     * @var \Spryker\Zed\CustomerApi\Persistence\CustomerApiQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @var \Spryker\Zed\CustomerApi\Business\Mapper\EntityMapperInterface
     */
    protected $entityMapper;

    /**
     * @var \Spryker\Zed\CustomerApi\Business\Mapper\TransferMapperInterface
     */
    protected $transferMapper;

    /**
     * @param \Spryker\Zed\CustomerApi\Dependency\QueryContainer\CustomerApiToApiInterface $apiQueryContainer
     * @param \Spryker\Zed\CustomerApi\Dependency\QueryContainer\CustomerApiToApiQueryBuilderInterface $apiQueryBuilderQueryContainer
     * @param \Spryker\Zed\CustomerApi\Persistence\CustomerApiQueryContainerInterface $queryContainer
     * @param \Spryker\Zed\CustomerApi\Business\Mapper\EntityMapperInterface $entityMapper
     * @param \Spryker\Zed\CustomerApi\Business\Mapper\TransferMapperInterface $transferMapper
     */
    public function __construct(
        CustomerApiToApiInterface $apiQueryContainer,
        CustomerApiToApiQueryBuilderInterface $apiQueryBuilderQueryContainer,
        CustomerApiQueryContainerInterface $queryContainer,
        EntityMapperInterface $entityMapper,
        TransferMapperInterface $transferMapper
    ) {
        $this->apiQueryContainer = $apiQueryContainer;
        $this->apiQueryBuilderQueryContainer = $apiQueryBuilderQueryContainer;
        $this->queryContainer = $queryContainer;
        $this->entityMapper = $entityMapper;
        $this->transferMapper = $transferMapper;
    }

    /**
     * @param \Generated\Shared\Transfer\ApiDataTransfer $apiDataTransfer
     *
     * @return \Generated\Shared\Transfer\ApiItemTransfer
     */
    public function add(ApiDataTransfer $apiDataTransfer)
    {
        $customerEntity = $this->entityMapper->toEntity($apiDataTransfer->getData());
        $customerApiTransfer = $this->persist($customerEntity);

        return $this->apiQueryContainer->createApiItem($customerApiTransfer, $customerApiTransfer->getIdCustomer());
    }

    /**
     * @param int $idCustomer
     *
     * @return \Generated\Shared\Transfer\ApiItemTransfer
     */
    public function get($idCustomer)
    {
        $customerData = $this->getCustomerData($idCustomer);
        $customerApiTransfer = $this->transferMapper->toTransfer($customerData);

        return $this->apiQueryContainer->createApiItem($customerApiTransfer, $customerApiTransfer->getIdCustomer());
    }

    /**
     * @param int $idCustomer
     * @param \Generated\Shared\Transfer\ApiDataTransfer $apiDataTransfer
     *
     * @throws \Spryker\Zed\Api\Business\Exception\EntityNotFoundException
     *
     * @return \Generated\Shared\Transfer\ApiItemTransfer
     */
    public function update($idCustomer, ApiDataTransfer $apiDataTransfer)
    {
        $entityToUpdate = $this->queryContainer
            ->queryFind()
            ->filterByIdCustomer($idCustomer)
            ->findOne();

        if (!$entityToUpdate) {
            throw new EntityNotFoundException(sprintf('Customer not found: %s', $idCustomer));
        }

        $data = (array)$apiDataTransfer->getData();
        $entityToUpdate->fromArray($data);

        $customerApiTransfer = $this->persist($entityToUpdate);

        return $this->apiQueryContainer->createApiItem($customerApiTransfer, $customerApiTransfer->getIdCustomer());
    }

    /**
     * @param int $idCustomer
     *
     * @return \Generated\Shared\Transfer\ApiItemTransfer
     */
    public function remove($idCustomer)
    {
        $deletedRows = $this->queryContainer
            ->queryRemove($idCustomer)
            ->delete();

        $customerApiTransfer = new CustomerApiTransfer();

        if ($deletedRows > 0) {
            $customerApiTransfer->setIdCustomer($idCustomer);
        }

        return $this->apiQueryContainer->createApiItem($customerApiTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\ApiRequestTransfer $apiRequestTransfer
     *
     * @return \Generated\Shared\Transfer\ApiCollectionTransfer
     */
    public function find(ApiRequestTransfer $apiRequestTransfer)
    {
        $query = $this->buildQuery($apiRequestTransfer);

        $collection = $this->transferMapper->toTransferCollection(
            $query->find()->toArray()
        );

        return $this->apiQueryContainer->createApiCollection($collection);
    }

    /**
     * @param \Generated\Shared\Transfer\ApiRequestTransfer $apiRequestTransfer
     *
     * @return \Orm\Zed\Product\Persistence\SpyProductQuery|\Propel\Runtime\ActiveQuery\ModelCriteria
     */
    protected function buildQuery(ApiRequestTransfer $apiRequestTransfer)
    {
        $apiQueryBuilderQueryTransfer = $this->buildApiQueryBuilderQuery($apiRequestTransfer);

        $query = $this->queryContainer->queryFind();
        $query = $this->apiQueryBuilderQueryContainer->buildQueryFromRequest($query, $apiQueryBuilderQueryTransfer);

        return $query;
    }

    /**
     * @param \Generated\Shared\Transfer\ApiRequestTransfer $apiRequestTransfer
     *
     * @return \Generated\Shared\Transfer\ApiQueryBuilderQueryTransfer
     */
    protected function buildApiQueryBuilderQuery(ApiRequestTransfer $apiRequestTransfer)
    {
        $columnSelectionTransfer = $this->buildColumnSelection();

        $apiQueryBuilderQueryTransfer = new ApiQueryBuilderQueryTransfer();
        $apiQueryBuilderQueryTransfer->setApiRequest($apiRequestTransfer);
        $apiQueryBuilderQueryTransfer->setColumnSelection($columnSelectionTransfer);

        return $apiQueryBuilderQueryTransfer;
    }

    /**
     * @return \Generated\Shared\Transfer\PropelQueryBuilderColumnSelectionTransfer
     */
    protected function buildColumnSelection()
    {
        $columnSelectionTransfer = new PropelQueryBuilderColumnSelectionTransfer();
        $tableColumns = SpyCustomerTableMap::getFieldNames(TableMap::TYPE_FIELDNAME);

        foreach ($tableColumns as $columnAlias) {
            $columnTransfer = new PropelQueryBuilderColumnTransfer();
            $columnTransfer->setName(SpyCustomerTableMap::TABLE_NAME . '.' . $columnAlias);
            $columnTransfer->setAlias($columnAlias);

            $columnSelectionTransfer->addTableColumn($columnTransfer);
        }

        return $columnSelectionTransfer;
    }

    /**
     * @param \Orm\Zed\Customer\Persistence\SpyCustomer $entity
     *
     * @return \Generated\Shared\Transfer\CustomerApiTransfer
     */
    protected function persist(SpyCustomer $entity)
    {
        $entity->save();

        return $this->transferMapper->toTransfer($entity->toArray());
    }

    /**
     * @param int $idCustomer
     *
     * @throws \Spryker\Zed\Api\Business\Exception\EntityNotFoundException
     *
     * @return array
     */
    protected function getCustomerData($idCustomer)
    {
        $customerEntity = (array)$this->queryContainer
            ->queryGet($idCustomer)
            ->findOne()
            ->toArray();

        if (!$customerEntity) {
            throw new EntityNotFoundException(sprintf('Customer not found: %s', $idCustomer));
        }

        return $customerEntity;
    }

}