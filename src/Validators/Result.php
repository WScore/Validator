<?php
namespace WScore\Validation\Validators;

use WScore\Validation\Locale\Messages;

class Result extends AbstractResult
{
    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->isValid;
    }

    /**
     * @param Messages|null $messages
     * @return  void
     */
        public function finalize(Messages $messages = null)
        {
            $this->populateMessages($messages);
        }
}