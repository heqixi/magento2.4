<?php
/**
 * @category    DragDropr
 * @package     DragDropr_Magento2
 * @author      Ricards Zalitis with THANKS to atipso GmbH <hello@dragdropr.com>
 * @copyright   2018 atipso GmbH
 * @license     http://license.mageshops.com/  Unlimited Commercial License
 */

namespace DragDropr\Magento2\Plugin\Magento\Framework\Data\Form\Element;

use \Magento\Framework\Data\Form\Element\Editor;
use \DragDropr\Magento2\Api\Wysiwyg\PluginInterface;

/**
 * Class EditorPlugin
 *
 * @package DragDropr\Magento2\Plugin\Component\Wysiwyg
 */
class EditorPlugin
{
    /**
     * @var PluginInterface[]
     */
    private $additionalPlugins;

    /**
     * ConfigPlugin constructor
     *
     * @param PluginInterface[] $additionalPlugins
     */
    public function __construct(array $additionalPlugins = [])
    {
        $this->additionalPlugins = $additionalPlugins;
    }

    /**
     * Changes additional plugin button visibility
     *
     * @param \DOMDocument $dom
     * @param bool $hideDefaultPlugins Whether hide all other plugins
     * @return bool Whether any plugin buttons were found
     */
    private function changePluginButtonVisibility(\DOMDocument $dom, $hideDefaultPlugins = false)
    {
        $buttonsFound = false;

        $buttons = $dom->getElementsByTagName('button');

        foreach ($buttons as $button) {
            $class = $button->getAttribute('class');

            foreach ($this->additionalPlugins as $plugin) {
                if (strpos($class, 'open-' . $plugin->getName() . ' PluginInterface') !== false) {
                    // Skip disabled plugins
                    if (! $plugin->getConfig()->isEnabled()) {
                        continue;
                    }

                    $styleAttribute = $button->getAttribute('style');
                    $styleAttribute = str_replace('display:none', '', $styleAttribute);
                    $button->setAttribute('style', $styleAttribute);
                    $buttonsFound = true;
                } elseif ($hideDefaultPlugins && strpos($class, 'plugin') !== false) {
                    $styleAttribute = $button->getAttribute('style');
                    $styleAttribute = str_replace('display:none', '', $styleAttribute);
                    $styleAttribute = $styleAttribute . 'display:none';
                    $button->setAttribute('style', $styleAttribute);
                }
            }
        }

        return $buttonsFound;
    }

    /**
     * Adds default button container
     *
     * @param Editor $subject
     * @param \DOMDocument $dom
     * @return \DOMDocument
     */
    private function addDefaultButtonContainer(Editor $subject, \DOMDocument $dom)
    {
        $method = new \ReflectionMethod($subject, '_getButtonsHtml');
        $method->setAccessible(true);
        $buttonsHTML = $method->invoke($subject);
        $buttonFragment = $dom->createDocumentFragment();
        $buttonFragment->appendXML($buttonsHTML);
        $reference = $dom->getElementById(__CLASS__);

        foreach ($reference->childNodes as $node) {
            $buttonFragment->appendChild($node);
        }

        $containerNode = $dom->createElement('div');
        $containerNode->setAttribute('class', 'admin__control-wysiwig');
        $containerNode->appendChild($buttonFragment);
        $reference->appendChild($containerNode);
        return $dom;
    }

    /**
     * Always show plugin button on frontend
     *
     * @param Editor $subject
     * @param $html
     * @return string
     */
    public function afterGetElementHtml(Editor $subject, $html)
    {
        $dom = new \DOMDocument();
        $html = "<!DOCTYPE html>
                 <html>
                     <head>
                        <meta charset=\"UTF-8\">
                         <title>". __CLASS__ . "</title>
                     </head>
                     <body>
                     <div id='" . __CLASS__ . "'>" . $html . "</div>
                     </body>
                 </html>";
        // remember current error reporting level
        $errorReporting = error_reporting();
        // set error reporting to 0 for html parsing to silence errors
        error_reporting(0);
        $dom->loadHTML($html);
        // set error reporting to the original value
        error_reporting($errorReporting);

        $buttonsFound = $this->changePluginButtonVisibility($dom);

        if (! $buttonsFound && ! $this->hasAdditionalFeatures($subject)) {
            // Attach the default button container only if an enabled plugin exists
            foreach ($this->additionalPlugins as $plugin) {
                if ($plugin->getConfig()->isEnabled()) {
                    $this->addDefaultButtonContainer($subject, $dom);
                    $this->changePluginButtonVisibility($dom, true);
                    break;
                }
            }
        }

        return $dom->saveHTML($dom->getElementById(__CLASS__));
    }

    /**
     * Whether given editor has additional features
     *
     * @param Editor $editor
     * @return bool
     */
    private function hasAdditionalFeatures(Editor $editor)
    {
        if (method_exists($editor, 'getPluginConfigOptions')) {
            return !! $editor->getPluginConfigOptions('magentowidget', 'window_url');
        }

        return !! $editor->getConfig('widget_window_url');
    }
}
