<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_FacebookPixel
 */


declare(strict_types=1);

namespace Amasty\FacebookPixel\Model;

use Magento\Framework\Session\SessionManager;

class EventSession extends SessionManager
{
    /**
     * @param array data
     * @return $this
     */
    public function setEvent(array $data): self
    {
        if (!$eventsData = $this->getEvents(false)) {
            $eventsData = [];
        }

        $eventsData[] = $data;
        $this->setData('events', $eventsData);

        return $this;
    }

    /**
     * @param bool $clear
     * @return array|null
     */
    public function getEvents(bool $clear = true): ?array
    {
        return $this->getData('events', $clear);
    }
}
