<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Support;

class DataTable
{

    private $data;

    private $columns = [];
    private $orderColumns = [];
    private $actions = [];
    private $bulkActions = [];
    private $bulkActionsId = null;
    private $sortable = null;
    private $sortableId = null;
    private $sortableData = null;

    public function __construct(\App\Src\Orm\Collection $data)
    {
        $this->data = $data;
    }

    public function addColumns(array $columns)
    {
        $this->columns = array_merge($this->columns, $columns);

        return $this;
    }

    public function orderColumns(array $orderColumns)
    {
        $this->orderColumns = array_merge($this->orderColumns, $orderColumns);

        return $this;
    }

    public function addActions(array $actions)
    {
        $this->actions = array_merge($this->actions, $actions);

        return $this;
    }

    public function addBulkActions(array $bulkActions)
    {
        $this->bulkActions = array_merge($this->bulkActions, $bulkActions);

        return $this;
    }

    public function setBulkActionsId(\Closure $bulkActionsId)
    {
        $this->bulkActionsId = $bulkActionsId;

        return $this;
    }

    public function setSortable($source)
    {
        $this->sortable = $source;

        return $this;
    }

    public function setSortableId(\Closure $sortableId)
    {
        $this->sortableId = $sortableId;

        return $this;
    }

    public function setSortableData($data)
    {
        $this->sortableData = $data;

        return $this;
    }

    public function render($template = 'misc/datatable')
    {
        return view($template, [
            'columns' => $this->columns,
            'orderColumns' => $this->orderColumns,
            'actions' => $this->actions,
            'bulkActions' => $this->bulkActions,
            'bulkActionsId' => $this->bulkActionsId,
            'sortable' => $this->sortable,
            'sortableId' => $this->sortableId,
            'sortableData' => $this->sortableData,
            'data' => $this->data,
        ]);
    }

    public function __toString()
    {
        return $this->render();
    }

}
