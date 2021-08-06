<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Reports
 */


declare(strict_types=1);

namespace Amasty\Reports\Model\Email;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\File\WriteInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Ui\Component\MassAction\Filter;

class CsvGenerator
{
    /**
     * @var Filesystem\Directory\WriteInterface
     */
    private $directory;

    /**
     * @var UiComponentFactory
     */
    private $factory;

    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var array
     */
    private $metaDataProviders;

    public function __construct(
        Filesystem $filesystem,
        UiComponentFactory $factory,
        Filter $filter,
        array $metaDataProviders = []
    ) {
        $this->directory = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $this->factory = $factory;
        $this->filter = $filter;
        $this->metaDataProviders = $metaDataProviders;
    }

    public function getCsvContent(string $report, string $reportListing): string
    {
        $file = $this->createCsvFile($report, $reportListing);

        $content = $this->directory->readFile($file);
        $this->directory->delete($file);

        return $content;
    }

    private function createCsvFile(string $report, string $reportListing): string
    {
        $file = sprintf('export/%s.csv', $report);
        $this->directory->create('export');
        $stream = $this->directory->openFile($file, 'w+');
        $stream->lock();

        $this->addFieldsToFile($this->prepareComponent($report, $reportListing), $stream, $report);

        $stream->unlock();
        $stream->close();

        return $file;
    }

    private function prepareComponent(string $report, string $reportListing): UiComponentInterface
    {
        $component = $this->factory->create($reportListing);
        $this->filter->prepareComponent($component);
        $this->filter->applySelectionOnTargetProvider();

        return $component;
    }

    private function addFieldsToFile(UiComponentInterface $component, WriteInterface $stream, string $report)
    {
        $metaDataProvider = $this->metaDataProviders[$report] ?? $this->metaDataProviders['default'];
        $fields = $metaDataProvider->getFields($component);
        $options = $metaDataProvider->getOptions();
        $dataProvider = $component->getContext()->getDataProvider();
        $items = $dataProvider->getSearchResult()->getItems();

        $stream->writeCsv($metaDataProvider->getHeaders($component));
        foreach ($items as $item) {
            $metaDataProvider->convertDate($item, $component->getName());
            $stream->writeCsv($metaDataProvider->getRowData($item, $fields, $options));
        }
    }
}
