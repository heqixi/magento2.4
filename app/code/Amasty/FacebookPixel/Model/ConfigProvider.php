<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_FacebookPixel
 */


declare(strict_types=1);

namespace Amasty\FacebookPixel\Model;

use Amasty\Base\Model\ConfigProviderAbstract;

class ConfigProvider extends ConfigProviderAbstract
{
    /**
     * xpath prefix of module (section)
     */
    protected $pathPrefix = 'amasty_facebook_pixel/';

    const GENERAL_BLOCK = 'general/';
    const EVENTS_BLOCK = 'events/';

    const PIXEL_ENABLED = 'is_enabled';
    const PIXEL_ID = 'pixel_id';
    const LOGGING_ENABLED = 'is_logging_enabled';
    const CATEGORY_VIEW_EVENT = 'category_view';
    const PRODUCT_VIEW_EVENT = 'product_view';
    const INIT_CHECKOUT_EVENT = 'init_checkout';
    const PURCHASE_EVENT = 'purchase';
    const PRODUCT_SEARCH_EVENT = 'product_search';
    const ADD_TO_CART_EVENT = 'add_to_cart';
    const ADD_TO_WISHLIST_EVENT = 'add_to_wishlist';
    const REGISTRATION_EVENT = 'customer_registration';

    /**
     * @return bool
     */
    public function isFacebookPixelEnabled(): bool
    {
        return $this->isSetFlag(self::GENERAL_BLOCK . self::PIXEL_ENABLED);
    }

    /**
     * @return string|null
     */
    public function getPixelId(): ?string
    {
        return $this->getValue(self::GENERAL_BLOCK . self::PIXEL_ID);
    }

    /**
     * @return bool
     */
    public function isLoggingEnabled(): bool
    {
        return $this->isSetFlag(self::GENERAL_BLOCK . self::LOGGING_ENABLED);
    }

    /**
     * @return bool
     */
    public function isCategoryViewEventEnabled(): bool
    {
        return $this->isSetFlag(self::EVENTS_BLOCK . self::CATEGORY_VIEW_EVENT);
    }

    /**
     * @return bool
     */
    public function isProductViewEventEnabled(): bool
    {
        return $this->isSetFlag(self::EVENTS_BLOCK . self::PRODUCT_VIEW_EVENT);
    }

    /**
     * @return bool
     */
    public function isInitCheckoutEventEnabled(): bool
    {
        return $this->isSetFlag(self::EVENTS_BLOCK . self::INIT_CHECKOUT_EVENT);
    }

    /**
     * @return bool
     */
    public function isPurchaseEventEnabled(): bool
    {
        return $this->isSetFlag(self::EVENTS_BLOCK . self::PURCHASE_EVENT);
    }

    /**
     * @return bool
     */
    public function isProductSearchEventEnabled(): bool
    {
        return $this->isSetFlag(self::EVENTS_BLOCK . self::PRODUCT_SEARCH_EVENT);
    }

    /**
     * @return bool
     */
    public function isAddToCartEventEnabled(): bool
    {
        return $this->isSetFlag(self::EVENTS_BLOCK . self::ADD_TO_CART_EVENT);
    }

    /**
     * @return bool
     */
    public function isAddToWishlistEventEnabled(): bool
    {
        return $this->isSetFlag(self::EVENTS_BLOCK . self::ADD_TO_WISHLIST_EVENT);
    }

    /**
     * @return bool
     */
    public function isRegistrationEventEnabled(): bool
    {
        return $this->isSetFlag(self::EVENTS_BLOCK . self::REGISTRATION_EVENT);
    }
}
