<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_FacebookPixel
 */


declare(strict_types=1);

namespace Amasty\FacebookPixel\Model\EventData\EventDataGenerator;

use Amasty\FacebookPixel\Model\ConfigProvider;
use Magento\CatalogSearch\Helper\Data;
use Magento\Store\Api\Data\StoreInterface;

class Search implements EventDataGeneratorInterface
{
    private const EVENT_ACTION = 'track';
    private const EVENT_TYPE = 'Search';

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var StoreInterface
     */
    private $store;

    /**
     * @var Data
     */
    private $catalogSearch;

    public function __construct(
        ConfigProvider $configProvider,
        StoreInterface $store,
        Data $catalogSearch
    ) {
        $this->configProvider = $configProvider;
        $this->store = $store;
        $this->catalogSearch = $catalogSearch;
    }

    /**
     * @param null|string $searchString
     * @return array|null
     */
    public function getEventData($searchString = null): ?array
    {
        $searchString = $searchString ?? $this->getSearchString();
        if ($searchString === null) {
            return null;
        }

        return [
            'search_string' => $searchString,
        ];
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

    /**
     * @return bool
     */
    public function isEventEnabled(): bool
    {
        return $this->configProvider->isProductSearchEventEnabled();
    }

    /**
     * @return string|null
     */
    private function getSearchString(): ?string
    {
        return $this->catalogSearch->getEscapedQueryText();
    }
}
