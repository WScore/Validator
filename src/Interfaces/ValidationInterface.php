<?php
/**
 * Created by PhpStorm.
 * User: wsjp
 * Date: 2019/03/21
 * Time: 15:31
 */

namespace WScore\FormModel\Validation;

interface ValidationInterface
{
    /**
     * @param callable[]|FilterInterface[] $filters
     * @return $this
     */
    public function setInputFilter(callable ...$filters);

    /**
     * @param callable[]|ValidatorInterface[] $validators
     * @return $this
     */
    public function setValidator(callable ...$validators);

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
     * @return ResultInterface|null
     */
    public function validate($result, $rootResults = null);
}