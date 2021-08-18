<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_FacebookPixel
 */


declare(strict_types=1);

namespace Amasty\FacebookPixel\Model\EventData;

use Amasty\FacebookPixel\Model\EventData\EventDataGenerator\EventDataGeneratorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\ObjectManagerInterface;

class EventDataGeneratorPool
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var string[]
     */
    private $eventDataGenerators;

    /**
     * @var EventDataGeneratorInterface[]
     */
    private $initializedGenerators = [];

    public function __construct(
        ObjectManagerInterface $objectManager,
        $eventDataGenerators = []
    ) {
        $this->eventDataGenerators = $eventDataGenerators;
        $this->objectManager = $objectManager;
    }

    /**
     * @return EventDataGeneratorInterface[]
     * @throws LocalizedException
     */
    public function getDataGenerators(): array
    {
        foreach ($this->eventDataGenerators as $generatorCode) {
            if (!isset($this->initializedGenerators[$generatorCode])) {
                $this->initDataGenerator($generatorCode);
            }
        }

        return $this->initializedGenerators;
    }

    /**
     * @param string $code
     * @return EventDataGeneratorInterface
     * @throws LocalizedException
     */
    public function getDataGenerator(string $code): EventDataGeneratorInterface
    {
        if (!isset($this->initializedGenerators[$code])) {
            $this->initDataGenerator($code);
        }

        return $this->initializedGenerators[$code];
    }

    /**
     * @param string $code
     * @throws LocalizedException
     * @return void
     */
    private function initDataGenerator(string $code): void
    {
        if (!empty($this->eventDataGenerators[$code])) {
            $generator = $this->objectManager->get($this->eventDataGenerators[$code]);
            if (!$generator instanceof EventDataGeneratorInterface) {
                throw new LocalizedException(
                    __('Event Data Generator %1 must implement %2 interface', $code, EventDataGeneratorInterface::class)
                );
            }

            $this->initializedGenerators[$code] = $generator;
        } else {
            throw new LocalizedException(__('Event Data Generator with %1 code is not declared', $code));
        }
    }
}
