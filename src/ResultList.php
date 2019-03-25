<?php
namespace WScore\Validation;

use WScore\Validation\Interfaces\ResultInterface;

class ResultList extends AbstractResult
{
    /**
     * @param ResultInterface $result
     * @param null $name
     */
    public function addResult(ResultInterface $result, $name = null)
    {
        $result->setParent($this);
        $name = $name ?? $result->name();
        $this->children[$name] = $result;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        if ($this->isValid === false) return false;
        foreach ($this->children as $child) {
            if (!$child->isValid()) {
                $this->isValid = false;
            }
        }
        return $this->isValid;
    }

    /**
     * @return  void
     */
    public function finalize()
    {
        $this->summarizeChildren('finalize');
        $this->setValue($this->summarizeChildren('value'));
        $this->isValid = true;
        foreach ($this->children as $name => $child) {
            if (!$child->isValid()) {
                $this->isValid = false;
            }
        }
    }

    public function summarizeErrorMessages(): array
    {
        $messages = $this->summarizeChildren('getErrorMessage');
        $messages = array_merge($this->getErrorMessage(), $messages);
        return $messages;
    }

    /**
     * @param string $method
     * @return array
     */
    private function summarizeChildren(string $method): array
    {
        $values = [];
        foreach ($this->children as $name => $child) {
            $values[$name] = $child->$method();
        }
        return $values;
    }
}