<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_FacebookPixel
 */


declare(strict_types=1);

namespace Amasty\FacebookPixel\Model\EventData\EventDataGenerator;

use Amasty\FacebookPixel\Model\ConfigProvider;
use Magento\Bundle\Model\Product\Type;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Framework\Pricing\Helper\Data;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\GroupedProduct\Model\Product\Type\Grouped;
use Magento\Quote\Model\Quote\Item;
use Magento\Store\Api\Data\StoreInterface;

class AddToCart implements EventDataGeneratorInterface
{
    private const EVENT_ACTION = 'track';
    private const EVENT_TYPE = 'AddToCart';

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var StoreInterface
     */
    private $store;
    /**
     * @var Data
     */
    private $pricingHelper;

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(
        ConfigProvider $configProvider,
        StoreInterface $store,
        Data $pricingHelper,
        SerializerInterface $serializer,
        ProductRepositoryInterface $productRepository,
        PriceCurrencyInterface $priceCurrency
    ) {
        $this->configProvider = $configProvider;
        $this->store = $store;
        $this->pricingHelper = $pricingHelper;
        $this->priceCurrency = $priceCurrency;
        $this->productRepository = $productRepository;
        $this->serializer = $serializer;
    }

    /**
     * @param null|Item[] $item
     * @return array|null
     */
    public function getEventData($items = null): ?array
    {
        if (!$items) {
            return null;
        }

        $eventData = [
            'contents' => [],
            'content_ids' => [],
            'value' => 0,
        ];
        $productName = '';
        foreach ($items as $item) {
            if (!$productName) {
                $productName = $item->getProductType() == Grouped::TYPE_CODE
                    ? $this->getGroupedProductName($item) :$item->getName();
            }

            if ($item->getProduct()->getTypeId() == Configurable::TYPE_CODE) {
                continue;
            }

            if ($item->getParentItem()) {
                if ($item->getParentItem()->getProductType() == Configurable::TYPE_CODE) {
                    $eventData['contents'][] = [
                        'id' => $item->getSku(),
                        'quantity' => $item->getParentItem()->getQtyToAdd(),
                    ];
                    $eventData['value'] += $this->priceCurrency->roundPrice(
                        $this->pricingHelper->currency(
                            $item->getProduct()->getFinalPrice() * $item->getParentItem()->getQtyToAdd(),
                            false,
                            false
                        )
                    );
                } else {
                    $eventData['contents'][] = [
                        'id' => $item->getSku(),
                        'quantity' => $item->getData('qty')
                    ];
                }
            } else {
                $eventData['contents'][] = [
                    'id' => $item->getProduct()->getTypeId() == Type::TYPE_CODE
                        ? $item->getProduct()->getData('sku') : $item->getSku(),
                    'quantity' => $item->getQtyToAdd()
                ];
                $eventData['value'] += $this->priceCurrency->roundPrice(
                    $this->pricingHelper->currency(
                        $item->getProduct()->getFinalPrice() * $item->getQtyToAdd(),
                        false,
                        false
                    )
                );
            }

            $eventData['content_ids'][] = $item->getProduct()->getTypeId() == Type::TYPE_CODE
                ? $item->getProduct()->getData('sku') : $item->getSku();
        }

        return [
            'content_name' => $productName,
            'content_ids' => $eventData['content_ids'],
            'content_type' => 'product',
            'contents' => $eventData['contents'],
            'currency' => $this->store->getCurrentCurrencyCode(),
            'value' => $eventData['value']
        ];
    }

    /**
     * @return bool
     */
    public function isEventEnabled(): bool
    {
        return $this->configProvider->isAddToCartEventEnabled();
    }

    /**
     * @return string
     */
    public function getEventAction(): string
    {
        return self::EVENT_ACTION;
    }

    /**
     * @return string
     */
    public function getEventType(): string
    {
        return self::EVENT_TYPE;
    }

    /**
     * @param Item $item
     * @return string
     */
    private function getGroupedProductName(Item $item): string
    {
        $infoButRequest = $item->getOptionByCode('info_buyRequest');
        if (!$infoButRequest) {
            return '';
        }

        $infoButRequest = $this->serializer->unserialize($infoButRequest->getValue());

        if (!empty($infoButRequest['super_product_config']['product_id'])) {
            $product = $this->productRepository->getById($infoButRequest['super_product_config']['product_id']);

            return $product && $product->getId() ? $product->getName() : '';
        }

        return '';
    }
}
