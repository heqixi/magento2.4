<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_FacebookPixel
 */


declare(strict_types=1);

namespace Amasty\FacebookPixel\Controller\LogEvent;

use Amasty\FacebookPixel\Model\ConfigProvider;
use Amasty\FacebookPixel\Model\Logger\EventLogger;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Serialize\SerializerInterface;

class Log extends Action implements HttpPostActionInterface
{
    /**
     * @var EventLogger
     */
    private $eventLogger;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(
        Context $context,
        EventLogger $eventLogger,
        ConfigProvider $configProvider,
        SerializerInterface $serializer
    ) {
        parent::__construct($context);
        $this->eventLogger = $eventLogger;
        $this->configProvider = $configProvider;
        $this->serializer = $serializer;
    }

    /**
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        /** @var Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setHttpResponseCode(200);
        $events = $this->getRequest()->getParam('events', null);
        if (!$this->configProvider->isLoggingEnabled() || $events === null) {
            return $resultJson;
        }

        $this->eventLogger->logEvents($this->serializer->serialize($events));

        return $resultJson;
    }
}
