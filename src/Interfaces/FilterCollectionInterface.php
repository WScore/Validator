<?php
declare(strict_types=1);

namespace WScore\Validator\Interfaces;

use IteratorAggregate;

interface FilterCollectionInterface extends IteratorAggregate
{
    /**
     * @param array $filters
     * @return FilterCollectionInterface
     */
    public function addFilters(array $filters): FilterCollectionInterface;

    /**
     * @param FilterInterface $filter
     * @return FilterCollectionInterface
     */
    public function append(FilterInterface $filter): FilterCollectionInterface;

    /**
     * @param FilterInterface $filter
     * @return FilterCollectionInterface
     */
    public function prepend(FilterInterface $filter): FilterCollectionInterface;

    /**
     * @param string $name
     * @return bool
     */
    public function has(string $name): bool;

    /**
     * @param string $name
     * @return FilterInterface
     */
    public function get(string $name): FilterInterface;

    /**
     * @param string $name
     * @return FilterCollectionInterface
     */
    public function remove(string $name): FilterCollectionInterface;
}
