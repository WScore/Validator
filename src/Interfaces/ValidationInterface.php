<?php
declare(strict_types=1);

namespace WScore\Validation\Interfaces;

use WScore\Validation\Validators\ValidationList;

interface ValidationInterface
{
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
     * @param FilterInterface[] $filters
     * @return ValidationList
     */
    public function addPreparationFilters(FilterInterface ...$filters): ValidationInterface;

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
     * @return ResultInterface
     */
    public function verify($value);

    /**
     * @internal
     * @param string|array $value
     * @param string|null $name
     * @param ResultInterface|null $parentResult
     * @return mixed
     */
    public function callVerify($value, $name = null, ResultInterface $parentResult = null);
}