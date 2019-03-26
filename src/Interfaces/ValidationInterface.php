<?php

namespace WScore\Validation\Interfaces;

interface ValidationInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @param string|null $name
     * @return $this
     */
    public function setName(string $name);

    /**
     * @param string $message
     * @return void
     */
    public function setErrorMessage(string $message);

    /**
     * @param FilterInterface[] $filters
     * @return ValidationInterface
     */
    public function addFilters(FilterInterface ...$filters): ValidationInterface;

    /**
     * @param string $name
     * @param ValidationInterface $validation
     * @return ValidationInterface
     */
    public function addChild(string $name, ValidationInterface $validation): ValidationInterface;

    /**
     * TODO: initialize and validate methods should be internal methods.
     *
     * @param string|array $input
     * @return ResultInterface
     */
    public function initialize($input);

    /**
     * @param ResultInterface $result
     * @return ResultInterface
     */
    public function validate($result);


    /**
     * @param string|array $value
     * @return ResultInterface
     */
    public function verify($value);
}