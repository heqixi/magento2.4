<?php
namespace Magento\Tax\Controller\Adminhtml\Rule\Save;

/**
 * Interceptor class for @see \Magento\Tax\Controller\Adminhtml\Rule\Save
 */
class Interceptor extends \Magento\Tax\Controller\Adminhtml\Rule\Save implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Framework\Registry $coreRegistry, \Magento\Tax\Api\TaxRuleRepositoryInterface $ruleService, \Magento\Tax\Api\Data\TaxRuleInterfaceFactory $taxRuleDataObjectFactory)
    {
        $this->___init();
        parent::__construct($context, $coreRegistry, $ruleService, $taxRuleDataObjectFactory);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'execute');
        return $pluginInfo ? $this->___callPlugins('execute', func_get_args(), $pluginInfo) : parent::execute();
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch(\Magento\Framework\App\RequestInterface $request)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'dispatch');
        return $pluginInfo ? $this->___callPlugins('dispatch', func_get_args(), $pluginInfo) : parent::dispatch($request);
    }
}
