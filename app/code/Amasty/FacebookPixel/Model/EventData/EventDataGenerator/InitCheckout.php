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
use Magento\Quote\Model\Quote;
use Magento\Store\Api\Data\StoreInterface;

class InitCheckout implements EventDataGeneratorInterface
{
    private const EVENT_ACTION = 'track';
    private const EVENT_TYPE = 'InitiateCheckout';

    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var StoreInterface
     */
    private $store;

    public function __construct(
        CheckoutSession $checkoutSession,
        ConfigProvider $configProvider,
        StoreInterface $store
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->configProvider = $configProvider;
        $this->store = $store;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @param null|Quote $quote
     * @return array|null
     */
    public function getEventData($quote = null): ?array
    {
        $quote = $quote ?? $this->getQuote();
        if ($quote === null) {
            return null;
        }

        $items = $quote->getAllVisibleItems();
        $quoteContent = [];
        $itemCount = 0;
        foreach ($items as $item) {
            $quoteContent['contents'][] = [
                'id' => $item->getProduct()->getTypeId() == Type::TYPE_CODE
                    ? $item->getProduct()->getData('sku') : $item->getSku(),
                'name' => $item->getName(),
                'quantity' => $item->getQty(),
                'item_price' => $item->getRowTotal() / $item->getQty()
            ];

            if (empty($quoteContent['content_ids'])) {
                $quoteContent['content_ids'] = [];
            }

            $quoteContent['content_ids'][] = $item->getProduct()->getTypeId() == Type::TYPE_CODE
                ? $item->getProduct()->getData('sku') : $item->getSku();
            $itemCount += $item->getQty();
        }

        if ($quoteContent) {
            return [
                'content_ids' => $quoteContent['content_ids'],
                'contents' => $quoteContent['contents'],
                'content_type' => 'product',
                'num_items' => $itemCount,
                'value' => $quote->getData('subtotal'),
                'currency' => $this->store->getCurrentCurrencyCode(),
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
        return $this->configProvider->isInitCheckoutEventEnabled();
    }

    /**
     * @return CategoryInterface|null
     */
    private function getQuote(): ?Quote
    {
        return $this->checkoutSession->getQuote();
    }
}
