<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Reports
 */


declare(strict_types=1);

namespace Amasty\Reports\Model\ResourceModel\Notification;

use Amasty\Reports\Api\Data\NotificationInterface;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_setIdFieldName(NotificationInterface::ENTITY_ID);
        $this->_init(
            \Amasty\Reports\Model\Notification::class,
            \Amasty\Reports\Model\ResourceModel\Notification::class
        );
    }
}
