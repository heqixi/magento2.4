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
use Magento\Framework\Registry;
use Magento\Store\Api\Data\StoreInterface;

class CategoryView implements EventDataGeneratorInterface
{
    private const EVENT_ACTION = 'trackCustom';
    private const EVENT_TYPE = 'ViewCategory';

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
     * @param null|CategoryInterface $category
     * @return array|null
     */
    public function getEventData($category = null): ?array
    {
        $category = $category ?? $this->getCurrentCategory();
        if ($category === null) {
            return null;
        }

        $eventData = [
            'content_name' => $category->getName(),
            'content_ids' => [$category->getId()],
            'content_type' => 'product_group',
            'value' => 0,
            'currency' => $this->store->getCurrentCurrencyCode()
        ];

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
        return $this->configProvider->isCategoryViewEventEnabled();
    }

    /**
     * @return CategoryInterface|null
     */
    private function getCurrentCategory(): ?CategoryInterface
    {
        return $this->registry->registry('current_category');
    }
}
