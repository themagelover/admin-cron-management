<?php

namespace TheMageLover\AdminCronManagement\Ui\DataProvider;

use Magento\Framework\Api\Filter;
use Magento\Ui\DataProvider\AbstractDataProvider;
use TheMageLover\AdminCronManagement\Helper\Data;

/**
 * Class ExecutorProvider
 * Provider for Cron Executor
 */
class ExecutorProvider extends AbstractDataProvider
{
    /** @var int */
    protected $size = 20;

    /** @var int */
    protected $offset = 1;

    /** @var string */
    protected $sortField = 'name';

    /** @var string */
    protected $sortDirection = 'asc';

    /** @var array */
    protected $filterRegistry = [];

    /** @var array */
    protected $currentFilter;

    /** @var Data */
    protected $helper;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param Data $helper
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        Data $helper,
        array $meta = [],
        array $data = []
    ) {
        $this->helper = $helper;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * @inheritdoc
     */
    public function addFilter(Filter $filter)
    {
        $conditionType = $filter->getConditionType();
        $filterData = [
            'field' => $filter->getField(),
            'value' => $filter->getValue()
        ];
        switch ($conditionType) {
            case 'like':
                $filterData['filter'] = function ($job) {
                    $currentRegister = $this->currentFilter;
                    return strpos($job[$currentRegister['field']], $currentRegister['value']) !== false;
                };
                $filterData['value'] = trim($filterData['value'], "%");
                $this->filterRegistry[] = $filterData;
                break;
            case 'eq':
                $filterData['filter'] = function ($job) {
                    $currentRegister = $this->currentFilter;
                    return $job[$currentRegister['field']] === $currentRegister['value'];
                };
                $this->filterRegistry[] = $filterData;
                break;
            case 'in':
                $filterData['filter'] = function ($job) {
                    $currentRegister = $this->currentFilter;
                    return in_array($job[$currentRegister['field']], array_values($currentRegister['value']), true);
                };
                $this->filterRegistry[] = $filterData;
                break;
            default:
                break;
        }
    }

    /**
     * Add Order
     *
     * @param string $field
     * @param string $direction
     */
    public function addOrder($field, $direction)
    {
        $this->sortField = $field;
        $this->sortDirection = strtolower($direction);
    }

    /**
     * Set Limit
     *
     * @param int $offset
     * @param int $size
     */
    public function setLimit($offset, $size)
    {
        $this->size = $size;
        $this->offset = $offset;
    }

    /**
     * @inheritdoc
     */
    public function getData()
    {
        // Get all Jobs
        $items = $this->helper->getAllJobs();

        // Add filter
        $items = $this->addItemFilter($items);

        // Sort items
        $sortField = $this->sortField;
        $sortDirection = $this->sortDirection;
        usort($items, function ($a, $b) use ($sortDirection, $sortField) {
            if ($sortDirection == 'asc') {
                return strcmp($a[$sortField], $b[$sortField]);
            } elseif ($sortDirection == 'desc') {
                return (-1 * strcmp($a[$sortField], $b[$sortField]));
            }
            return 0;
        });

        $totalRecords = count($items);
        // Paging
        $items = array_slice($items, ($this->offset - 1) * $this->size, $this->size);

        return [
            'totalRecords' => $totalRecords,
            'items' => $items
        ];
    }

    /**
     * Add Item Filter
     *
     * @param array $items
     * @return array
     */
    public function addItemFilter($items)
    {
        foreach ($this->filterRegistry as $filter) {
            $this->currentFilter = $filter;
            $items = array_filter(
                $items,
                $filter['filter'],
                ARRAY_FILTER_USE_BOTH
            );
        }
        return $items;
    }
}
