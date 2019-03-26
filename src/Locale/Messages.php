<?php
declare(strict_types=1);

namespace WScore\Validation\Locale;

class Messages
{
    /**
     * @var array
     */
    private $messages;

    public function __construct(array $messages)
    {
        $this->messages = $messages;
    }

    /**
     * @param string $locale
     * @return Messages
     */
    public static function create($locale = 'en'): Messages
    {
        $message_dir = strlen($locale) === 2
            ? __DIR__ . DIRECTORY_SEPARATOR . $locale
            : $locale;
        if (!is_dir($message_dir)) {
            throw new \InvalidArgumentException('message directory not found: ' . $message_dir);
        }
        $message_file = $message_dir . DIRECTORY_SEPARATOR . 'validation.message.php';
        if (!file_exists($message_file)) {
            throw new \InvalidArgumentException('message file not found: ' . $message_file);
        }
        /** @noinspection PhpIncludeInspection */
        $messages = include($message_file);
        $self = new self($messages);

        return $self;
    }

    /**
     * @param string $name
     * @param array $options
     * @return string
     */
    public function getMessage($name, $options = []): string
    {
        $message = isset($this->messages[$name]) ? $this->messages[$name]: $this->messages[__CLASS__];
        $search = [];
        $replace = [];
        foreach ($options as $key => $value) {
            $search[] = "\{{$key}\}";
            $replace[] = $value;
        }
        $message = str_replace($search, $replace, $message);

        return $message;
    }
}