<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_FacebookPixel
 */


declare(strict_types=1);

namespace Amasty\FacebookPixel\Model;

use Magento\Framework\App\Request\Http;

class PageViewEventResolver
{
    /**
     * @var Http
     */
    private $request;

    /**
     * @var array
     */
    private $events;

    public function __construct(
        Http $request,
        $events = []
    ) {
        $this->events = $events;
        $this->request = $request;
    }

    /**
     * @return string|null
     */
    public function getEventKey(): ?string
    {
        $actionName = $this->request->getFullActionName();

        return $this->events[$actionName] ?? null;
    }
}
