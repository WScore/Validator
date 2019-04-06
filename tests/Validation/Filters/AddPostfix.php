<?php
namespace tests\Validation\Filters;

use WScore\Validation\Filters\AbstractFilter;
use WScore\Validation\Interfaces\ResultInterface;

class AddPostfix extends AbstractFilter
{
    /**
     * @var string
     */
    private $prefix;

    /**
     * AddPostfix constructor.
     * @param string $prefix
     */
    public function __construct($prefix = '-tested')
    {
        $this->prefix = $prefix;
    }

    /**
     * @param $prefix
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
    }

    /**
     * @param ResultInterface $input
     * @param ResultInterface $allInputs
     * @return ResultInterface|null
     */
    public function __invoke(ResultInterface $input, ResultInterface $allInputs = null): ?ResultInterface
    {
        $value = $input->value();
        $input->setValue($value . $this->prefix);
        return null;
    }
}