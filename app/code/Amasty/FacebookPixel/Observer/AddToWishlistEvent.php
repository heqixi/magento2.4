<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_FacebookPixel
 */


declare(strict_types=1);

namespace Amasty\FacebookPixel\Observer;

use Amasty\FacebookPixel\Model\EventData\EventDataGeneratorPool;
use Amasty\FacebookPixel\Model\EventSession;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;

class AddToWishlistEvent implements ObserverInterface
{
    /**
     * @var EventDataGeneratorPool
     */
    private $eventDataGeneratorPool;

    /**
     * @var EventSession
     */
    private $eventSession;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        EventDataGeneratorPool $eventDataGeneratorPool,
        EventSession $eventSession,
        LoggerInterface $logger
    ) {
        $this->eventDataGeneratorPool = $eventDataGeneratorPool;
        $this->eventSession = $eventSession;
        $this->logger = $logger;
        $this->eventDataGeneratorPool = $eventDataGeneratorPool;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer): void
    {
        $items = $observer->getData('items');
        foreach ($items as $item) {
            $wishlistItem = $item;
            break;
        }

        if (!empty($wishlistItem)) {
            try {
                $eventDataGenerator = $this->eventDataGeneratorPool->getDataGenerator('addToWishList');
                if (!$eventDataGenerator->isEventEnabled()) {
                    return;
                }

                $this->eventSession->setEvent(
                    [
                        'event_action' => $eventDataGenerator->getEventAction(),
                        'event_type' => $eventDataGenerator->getEventType(),
                        'event_data' => $eventDataGenerator->getEventData($wishlistItem)
                    ]
                );
            } catch (\Exception $e) {
                $this->logger->critical($e);
            }
        }
    }
}
