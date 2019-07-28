<?php
declare(strict_types=1);

namespace WScore\Validator\Validators;

use ArrayIterator;
use Traversable;
use WScore\Validator\Interfaces\FilterCollectionInterface;
use WScore\Validator\Interfaces\FilterInterface;

class FilterCollection implements FilterCollectionInterface
{
    /**
     * @var FilterInterface[]
     */
    private $filters = [];

    public function addFilters(array $filters): FilterCollectionInterface
    {
        foreach ($filters as $key => $filter) {
            $f = $this->buildFilter($key, $filter);
            $m = $f->getAddType();
            $this->$m($f);
        }
        return $this;
    }

    private function buildFilter($key, $filter): FilterInterface
    {
        if ($filter instanceof FilterInterface) {
            return $filter;
        } elseif (is_numeric($key) && is_string($filter)) {
            return new $filter();
        }
        return new $key($filter);
    }

    public function append(FilterInterface $filter): FilterCollectionInterface
    {
        $this->filters[get_class($filter)] = $filter;
        return $this;
    }

    public function prepend(FilterInterface $filter): FilterCollectionInterface
    {
        $this->filters = array_merge([get_class($filter) => $filter] + $this->filters);
        return $this;
    }

    public function has(string $name): bool
    {
        return array_key_exists($name, $this->filters);
    }

    public function get(string $name): FilterInterface
    {
        return $this->filters[$name];
    }

    public function remove(string $name): FilterCollectionInterface
    {
        unset($this->filters[$name]);
        return $this;
    }

    /**
     * @return Traversable|FilterInterface[]
     */
    public function getIterator()
    {
        return new ArrayIterator($this->filters);
    }
}
