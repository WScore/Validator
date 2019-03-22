<?php
namespace WScore\Validation\Interfaces;

use WScore\Validation\ResultList;

interface ResultInterface extends \IteratorAggregate
{
    /**
     * @param string $message
     * @return $this
     */
    public function failed(string $message): ResultInterface;

    /**
     * @return string|string[]|mixed
     */
    public function value();

    /**
     * @param mixed $value
     */
    public function setValue($value);

    /**
     * @return string
     */
    public function name(): string ;

    /**
     * @return bool
     */
    public function isValid(): bool;

    /**
     * @return string[]
     */
    public function getErrorMessage();

    /**
     * @param string $name
     * @return bool
     */
    public function hasChild(string $name): bool;

    /**
     * @param string $name
     * @return ResultInterface
     */
    public function getChild(string $name): ?ResultInterface;

    /**
     * @return self[]
     */
    public function getIterator();

    /**
     * @return bool
     */
    public function hasChildren(): bool;

    /**
     * @return self[]
     */
    public function getChildren();

    /**
     * @return ResultList|null
     */
    public function getParent(): ?ResultList;

    /**
     * @param ResultList $param
     * @return void
     */
    public function setParent(ResultList $param): void;
}