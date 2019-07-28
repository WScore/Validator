<?php
declare(strict_types=1);

namespace WScore\Validator\Validators;

use WScore\Validator\Locale\Messages;

class Result extends AbstractResult
{
    /**
     * @param Messages|null $messages
     * @param string $final_error_message
     * @return  void
     */
    public function finalize(Messages $messages = null, $final_error_message = '')
    {
        if (!$this->isValid() && $final_error_message) {
            $this->failed(__CLASS__, [], $final_error_message);
        }
        $this->populateMessages($messages);
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->isValid;
    }
}