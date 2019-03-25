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
     * @param callable[]|FilterInterface[] $filters
     * @return void
     */
    public function addFilters(callable ...$filters);

    /**
     * @param string $name
     * @param ValidationInterface $validation
     * @return void
     */
    public function addChild(string $name, ValidationInterface $validation);

    /**
     * @param string|array $input
     * @return ResultInterface
     */
    public function initialize($input);

    /**
     * @param ResultInterface $result
     * @param ResultInterface $rootResults
     * @return ResultInterface
     */
    public function validate($result, $rootResults = null);


    /**
     * @param string|array $value
     * @return ResultInterface
     */
    public function verify($value);
}