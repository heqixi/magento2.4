<?php
/**
 * @category    DragDropr
 * @package     DragDropr_Magento2
 * @author      Ricards Zalitis with THANKS to atipso GmbH <hello@dragdropr.com>
 * @copyright   2018 atipso GmbH
 * @license     http://license.mageshops.com/  Unlimited Commercial License
 */

namespace DragDropr\Magento2\Block;

use \Magento\Backend\Block\Template;
use \DragDropr\Magento2\Api\Wysiwyg\Plugin\DragDropr\ConfigInterface;

class DataSource extends Template
{
    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var string
     */
    private $template = 'DragDropr_Magento2::data_source.phtml';

    /**
     * DataSource constructor
     *
     * @param Template\Context $context
     * @param ConfigInterface $config
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        ConfigInterface $config,
        array $data = []
    ) {
        $this->config = $config;
        parent::__construct($context, $data);
    }

    /**
     * Get plugin config
     *
     * @return ConfigInterface
     */
    public function config()
    {
        return $this->config;
    }

    /**
     * Get data source config
     *
     * @return array
     */
    public function dataSource()
    {
        $integration = $this->config->getIntegration();

        return [
            'integration' => [
                'active' => $integration->getStatus() === $integration::STATUS_ACTIVE,
                'url' => $this->getUrl('adminhtml/integration')
            ],
            'storeUrl' => str_replace(['index.php/', 'index.php'], '', $this->_storeManager->getDefaultStoreView()->getBaseUrl()),
            'apiKey' => $this->config->getApiKey(),
            'cms_page' => [
                'endpoint' => $this->escapeUrlProtocol(
                    $this->config->getPageEndpoint($integration->getEnvironment())
                ),
                'new_entity' => $this->escapeUrlProtocol($this->getUrl(
                    'cms/page/edit',
                    [
                        'page_id' => ':pageId',
                        '_escape_params' => false
                    ]
                ))
            ],
            'catalog_category' => [
                'endpoint' => $this->escapeUrlProtocol(
                    $this->config->getCategoryEndpoint($integration->getEnvironment())
                ),
                'new_entity' => $this->escapeUrlProtocol($this->getUrl(
                    'catalog/category/edit',
                    [
                        'id' => ':categoryId',
                        'store' => ':storeId',
                        '_escape_params' => false
                    ]
                ))
            ],
            'default' => [
                'endpoint' => $this->escapeUrlProtocol(
                    $this->config->getDefaultEndpoint($integration->getEnvironment())
                )
            ],
            'type' => \DragDropr\Magento2\Api\Data\IntegrationInterface::INTEGRATION_TYPE
        ];
    }

    /**
     * Escapes url protocol so the given url can be correctly used in js with url pattern
     *
     * @param string $url
     * @return string
     */
    public function escapeUrlProtocol($url)
    {
        return str_replace('https://', 'https\://', str_replace('http://', 'http\://', $url));
    }
}
