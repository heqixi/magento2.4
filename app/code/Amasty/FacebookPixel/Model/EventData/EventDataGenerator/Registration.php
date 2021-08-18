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

class Registration implements EventDataGeneratorInterface
{
    private const EVENT_ACTION = 'track';
    private const EVENT_TYPE = 'CompleteRegistration';

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
     * @param null $eventObject
     * @return array|null
     */
    public function getEventData($eventObject = null): ?array
    {
        return [
            'success' => true,
            'value' => 0,
            'currency' => $this->store->getCurrentCurrencyCode()
        ];
    }

    /**
     * @return bool
     */
    public function isEventEnabled(): bool
    {
        return $this->configProvider->isRegistrationEventEnabled();
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
