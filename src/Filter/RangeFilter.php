<?php

namespace Merkeleon\Table\Filter;

use Merkeleon\Table\Filter;


class RangeFilter extends Filter
{

    protected $viewPath   = 'filters.range';
    protected $multiplier = 1;

    public function params($params)
    {
        if (($multiplier = array_get($params, 'multiplier')) !== null)
        {
            $this->multiplier = $multiplier;
        }

        return parent::params($params);
    }

    protected function prepare()
    {
        $value = request('f_' . $this->name);
        if ($from = array_get($value, 'from'))
        {
            $this->value['from'] = $from;
        }
        if ($to = array_get($value, 'to'))
        {
            $this->value['to'] = $to;
        }
    }

    protected function applyEloquentFilter($dataSource)
    {
        if ($from = array_get($this->value, 'from'))
        {
            $dataSource = $dataSource->where($dataSource->getModel()
                                                        ->getTable() . '.' . $this->name, '>=', $from * $this->multiplier);
        }

        if ($to = array_get($this->value, 'to'))
        {
            $dataSource = $dataSource->where($dataSource->getModel()
                                                        ->getTable() . '.' . $this->name, '<=', $to * $this->multiplier);
        }

        return $dataSource;
    }

    protected function applyCollectionFilter($dataSource)
    {
        if ($from = array_get($this->value, 'from'))
        {
            $dataSource = $dataSource->filter(function ($item, $key) use ($from) {
                return strtotime($item->{$this->name}) >= $from * $this->multiplier;
            });
        }

        if ($to = array_get($this->value, 'to'))
        {
            $dataSource = $dataSource->filter(function ($item, $key) use ($to) {
                return strtotime($item->{$this->name}) <= $to * $this->multiplier;
            });
        }

        return $dataSource;
    }
}