<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_FacebookPixel
 */


declare(strict_types=1);

namespace Amasty\FacebookPixel\ViewModel;

use Amasty\FacebookPixel\Model\ConfigProvider;
use Amasty\FacebookPixel\Model\EventData\EventDataGeneratorPool;
use Amasty\FacebookPixel\Model\PageViewEventResolver;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Psr\Log\LoggerInterface;

class FacebookPixel implements ArgumentInterface
{
    /**
     * @var PageViewEventResolver
     */
    private $pageViewEventResolver;

    /**
     * @var EventDataGeneratorPool
     */
    private $eventGeneratorPool;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(
        PageViewEventResolver $pageViewEventResolver,
        EventDataGeneratorPool $eventGeneratorPool,
        ConfigProvider $configProvider,
        SerializerInterface $serializer,
        UrlInterface $url,
        LoggerInterface $logger
    ) {
        $this->pageViewEventResolver = $pageViewEventResolver;
        $this->eventGeneratorPool = $eventGeneratorPool;
        $this->logger = $logger;
        $this->configProvider = $configProvider;
        $this->serializer = $serializer;
        $this->url = $url;
    }

    /**
     * @return string|null
     */
    public function getEventDataJson(): ?string
    {
        $eventKey = $this->pageViewEventResolver->getEventKey();
        if (!$eventKey) {
            return null;
        }

        $eventConfig = [];
        try {
            $eventDataGenerator = $this->eventGeneratorPool->getDataGenerator($eventKey);
            if (!$eventDataGenerator->isEventEnabled()) {
                return null;
            }

            $eventConfig[] = [
                'event_action' => $eventDataGenerator->getEventAction(),
                'event_type' => $eventDataGenerator->getEventType(),
                'event_data' => $eventDataGenerator->getEventData()
            ];
        } catch (LocalizedException $e) {
            return null;
        } catch (\Exception $e) {
            $this->logger->critical($e);
            return null;
        }

        return $this->serializer->serialize($eventConfig);
    }

    /**
     * @return bool
     */
    public function isFaceBookPixelEnabled(): bool
    {
        return $this->configProvider->isFacebookPixelEnabled() && $this->getPixelId();
    }

    /**
     * @return string
     */
    public function getPixelId(): ?string
    {
        return $this->configProvider->getPixelId();
    }

    /**
     * @return bool
     */
    public function isEventLoggingEnabled(): bool
    {
        return $this->configProvider->isLoggingEnabled();
    }

    /**
     * @return string
     */
    public function getLoggingUrl(): string
    {
        return $this->url->getUrl('amasty_fbpixel/logEvent/log');
    }
}
