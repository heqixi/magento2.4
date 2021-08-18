<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_FacebookPixel
 */


declare(strict_types=1);

namespace Amasty\FacebookPixel\Model\EventData\EventDataGenerator;

interface EventDataGeneratorInterface
{
    /**
     * @param null $eventObject
     * @return array|null
     */
    public function getEventData($eventObject = null): ?array;

    /**
     * @return string
     */
    public function getEventAction(): string;

    /**
     * @return string
     */
    public function getEventType(): string;

    /**
     * @return bool
     */
    public function isEventEnabled(): bool;
}
