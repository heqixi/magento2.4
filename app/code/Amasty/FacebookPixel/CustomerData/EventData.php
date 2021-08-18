<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_FacebookPixel
 */


declare(strict_types=1);

namespace Amasty\FacebookPixel\CustomerData;

use Amasty\FacebookPixel\Model\ConfigProvider;
use Amasty\FacebookPixel\Model\EventSession;
use Magento\Customer\CustomerData\SectionSourceInterface;
use Magento\Framework\Serialize\SerializerInterface;

class EventData implements SectionSourceInterface
{
    /**
     * @var EventSession
     */
    private $eventSession;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(
        EventSession $eventSession,
        ConfigProvider $configProvider,
        SerializerInterface $serializer
    ) {
        $this->eventSession = $eventSession;
        $this->configProvider = $configProvider;
        $this->serializer = $serializer;
    }

    /**
     * @return array
     */
    public function getSectionData(): array
    {
        $eventsData = $this->eventSession->getEvents();

        return $eventsData ? ['events' => $eventsData] : [];
    }
}
