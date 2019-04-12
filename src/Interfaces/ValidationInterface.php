<?php
declare(strict_types=1);

namespace WScore\Validation\Interfaces;

interface ValidationInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @param string|null $name
     * @return ValidationInterface|$this
     */
    public function setName(string $name);

    /**
     * @param string $message
     * @return void
     */
    public function setErrorMessage(string $message);

    /**
     * @param FilterInterface[] $filters
     * @return ValidationInterface|$this
     */
    public function addFilters(array $filters): ValidationInterface;

    /**
     * @param string $name
     * @return bool
     */
    public function hasFilter(string $name): bool;

    /**
     * @param string $name
     * @return FilterInterface
     */
    public function getFilter(string $name): FilterInterface;

    /**
     * @param string $name
     * @return ValidationInterface|$this
     */
    public function removeFilter(string $name): ValidationInterface;

    /**
     * @param string $name
     * @param ValidationInterface $validation
     * @return ValidationInterface|$this
     */
    public function add(string $name, ValidationInterface $validation): ValidationInterface;

    /**
     * @param string $name
     * @return ValidationInterface
     */
    public function get(string $name): ?ValidationInterface;

    /**
     * @param string $name
     * @return bool
     */
    public function has(string $name): bool;

    /**
     * @param string $name
     * @return ValidationInterface|$this
     */
    public function remove(string $name): ValidationInterface;

    /**
     * @return ValidationInterface[]
     */
    public function all(): array;

    /**
     * @param string|array $value
     * @param ResultInterface $parentResult
     * @return ResultInterface
     */
    public function verify($value, ResultInterface $parentResult = null);
}