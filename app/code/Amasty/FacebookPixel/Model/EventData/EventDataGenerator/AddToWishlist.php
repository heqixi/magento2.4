<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_FacebookPixel
 */


declare(strict_types=1);

namespace Amasty\FacebookPixel\Model\EventData\EventDataGenerator;

use Amasty\FacebookPixel\Model\ConfigProvider;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Wishlist\Model\Item;

class AddToWishlist implements EventDataGeneratorInterface
{
    private const EVENT_ACTION = 'track';
    private const EVENT_TYPE = 'AddToWishlist';

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var StoreInterface
     */
    private $store;

    public function __construct(
        ConfigProvider $configProvider,
        StoreInterface $store
    ) {
        $this->configProvider = $configProvider;
        $this->store = $store;
    }

    /**
     * @param null|Item $item
     * @return array|null
     */
    public function getEventData($item = null): ?array
    {
        if ($item === null) {
            return null;
        }

        $contents = [
            'id' => $item->getProduct()->getSku(),
        ];
        if ($item->getQty()) {
            $contents['quantity'] = $item->getQty();
        }

        return [
            'content_name' => $item->getProduct()->getName(),
            'content_ids' => [$item->getProduct()->getSku()],
            'content_type' => 'product',
            'contents' => [$contents],
            'currency' => $this->store->getCurrentCurrencyCode(),
            'value' => 0
        ];
    }

    /**
     * @return bool
     */
    public function isEventEnabled(): bool
    {
        return $this->configProvider->isAddToWishlistEventEnabled();
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
}
