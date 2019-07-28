<?php
declare(strict_types=1);

namespace WScore\Validator\Validators;

use WScore\Validator\Interfaces\ResultInterface;
use WScore\Validator\Locale\Messages;

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
     * @param Messages|null $messages
     * @param string $final_error_message
     * @return  void
     */
    public function finalize(Messages $messages = null, $final_error_message = '')
    {
        $this->finalizeChildren($messages);
        $this->setValue($this->summarizeChildren('value'));
        foreach ($this->children as $name => $child) {
            if (!$child->isValid()) {
                $this->isValid = false;
            }
        }
        if (!$this->isValid() && $final_error_message) {
            $this->failed(__CLASS__, [], $final_error_message);
        }
        $this->populateMessages($messages);
    }

    /**
     * @param Messages|null $messages
     * @return void
     */
    private function finalizeChildren(Messages $messages = null): void
    {
        foreach ($this->children as $child) {
            $child->finalize($messages);
        }
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

    public function summarizeErrorMessages(): array
    {
        $messages = $this->summarizeChildren('getErrorMessage');
        return $messages;
    }
}