<?php
/**
 * Created by PhpStorm.
 * User: asao
 * Date: 2014/05/30
 * Time: 16:39
 */
namespace WScore\Validation;


/**
 * value transfer object.
 * Class ValueTO
 *
 * @package WScore\Validation
 */
interface ValueToInterface
{
    /**
     * @return mixed
     */
    public function getValue();

    /**
     * @return string
     */
    public function getType();

    /**
     * gets message regardless of the error state of this ValueTO.
     * use this message ONLY WHEN valueTO is error.
     *
     * @return string
     */
    public function message();

    /**
     * @return mixed
     */
    public function __toString();

    /**
     * @return bool
     */
    public function fails();
}