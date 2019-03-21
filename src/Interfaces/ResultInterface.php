<?php
namespace WScore\FormModel\Validation;

interface ResultInterface extends \IteratorAggregate
{
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
     * @return string
     */
    public function label(): string ;

    /**
     * @return bool
     */
    public function isValid(): bool;

    /**
     * @return string|string[]|mixed
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
}