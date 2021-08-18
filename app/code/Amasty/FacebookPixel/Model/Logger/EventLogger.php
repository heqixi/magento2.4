<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_FacebookPixel
 */


declare(strict_types=1);

namespace Amasty\FacebookPixel\Model\Logger;

use Monolog\Logger;

class EventLogger extends Logger
{
    /**
     * @param string $eventData
     * @return void
     */
    public function logEvents(string $eventData): void
    {
        $this->debug($eventData);
    }
}
