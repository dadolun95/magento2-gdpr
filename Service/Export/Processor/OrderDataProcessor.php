<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Export\Processor;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Opengento\Gdpr\Service\Export\Processor\Entity\DataCollectorInterface;
use Opengento\Gdpr\Service\Export\ProcessorInterface;

/**
 * Class QuoteDataProcessor
 */
final class OrderDataProcessor implements ProcessorInterface
{
    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var \Opengento\Gdpr\Service\Export\Processor\Entity\DataCollectorInterface
     */
    private $dataCollector;

    /**
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Opengento\Gdpr\Service\Export\Processor\Entity\DataCollectorInterface $dataCollector
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        DataCollectorInterface $dataCollector
    ) {
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->dataCollector = $dataCollector;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(int $customerId, array $data): array
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter(OrderInterface::CUSTOMER_ID, $customerId);
        $orderList = $this->orderRepository->getList($searchCriteria->create());

        /** @var \Magento\Sales\Api\Data\OrderInterface $entity */
        foreach ($orderList->getItems() as $entity) {
            $data['orders']['order_id_' . $entity->getEntityId()] = $this->dataCollector->collect($entity);
        }

        return $data;
    }
}
