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
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Sales\Model\Order;

class Purchase implements EventDataGeneratorInterface
{
    private const EVENT_ACTION = 'track';
    private const EVENT_TYPE = 'Purchase';

    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(
        CheckoutSession $checkoutSession,
        ConfigProvider $configProvider
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->configProvider = $configProvider;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @param null|Order $order
     * @return array|null
     */
    public function getEventData($order = null): ?array
    {
        $order = $order ?? $this->getOrder();
        if ($order === null) {
            return null;
        }

        $orderItems = $order->getAllVisibleItems();
        $itemContent = [];
        $itemCount = 0;
        foreach ($orderItems as $item) {
            $itemContent['contents'][] = [
                'id' => $item->getProduct()->getTypeId() == Type::TYPE_CODE
                    ? $item->getProduct()->getData('sku') : $item->getSku(),
                'name' => $item->getName(),
                'quantity' => (int)$item->getQtyOrdered(),
                'item_price' => $item->getPrice()
            ];

            if (empty($itemContent['content_ids'])) {
                $itemContent['content_ids'] = [];
            }

            $itemContent['content_ids'][] = $item->getProduct()->getTypeId() == Type::TYPE_CODE
                ? $item->getProduct()->getData('sku') : $item->getSku();
            $itemCount += $item->getQtyOrdered();
        }

        if ($itemContent) {
            return [
                'content_ids' => $itemContent['content_ids'],
                'contents' => $itemContent['contents'],
                'content_type' => 'product',
                'num_items' => $itemCount,
                'value' => $order->getGrandTotal(),
                'currency' => $order->getOrderCurrencyCode(),
            ];
        }

        return null;
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
        return $this->configProvider->isPurchaseEventEnabled();
    }

    /**
     * @return CategoryInterface
     */
    private function getOrder(): Order
    {
        return $this->checkoutSession->getLastRealOrder();
    }
}
