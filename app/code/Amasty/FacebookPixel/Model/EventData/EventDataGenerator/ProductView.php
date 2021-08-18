<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_FacebookPixel
 */


declare(strict_types=1);

namespace Amasty\FacebookPixel\Model\EventData\EventDataGenerator;

use Amasty\FacebookPixel\Model\ConfigProvider;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Registry;
use Magento\Store\Api\Data\StoreInterface;

class ProductView implements EventDataGeneratorInterface
{
    private const EVENT_ACTION = 'track';
    private const EVENT_TYPE = 'ViewContent';

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var StoreInterface
     */
    private $store;

    public function __construct(
        Registry $registry,
        ConfigProvider $configProvider,
        StoreInterface $store
    ) {
        $this->registry = $registry;
        $this->configProvider = $configProvider;
        $this->store = $store;
    }

    /**
     * @param null|ProductInterface $product
     * @return array|null
     */
    public function getEventData($product = null): ?array
    {
        $product = $product ?? $this->getCurrentProduct();
        if ($product === null) {
            return null;
        }

        $eventData = [
            'content_name' => $product->getName(),
            'content_ids' => [$product->getSku()],
            'content_type' => 'product',
            'value' => $product->getFinalPrice(),
            'currency' => $this->store->getCurrentCurrencyCode()
        ];

        if ($category = $this->getCurrentCategory()) {
            $eventData['content_category'] = $category->getName();
        }

        return $eventData;
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
     * @return bool
     */
    public function isEventEnabled(): bool
    {
        return $this->configProvider->isProductViewEventEnabled();
    }

    /**
     * @return ProductInterface|null
     */
    private function getCurrentProduct(): ?ProductInterface
    {
        return $this->registry->registry('current_product');
    }

    /**
     * @return CategoryInterface|null
     */
    private function getCurrentCategory(): ?CategoryInterface
    {
        return $this->registry->registry('current_category');
    }
}
