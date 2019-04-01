<?php
declare(strict_types=1);

use WScore\Validation\Locale\Messages;
use PHPUnit\Framework\TestCase;
use WScore\Validation\Validators\Result;

class MessagesTest extends TestCase
{
    /**
     * @param string $locale
     * @return array
     */
    private function getMessages($locale = 'en')
    {
        return include __DIR__ . "/../../src/Locale/{$locale}/validation.message.php";
    }

    public function testCreate()
    {
        $messages = Messages::create('en');
        $this->assertEquals(Messages::class, get_class($messages));
        $this->assertEquals($this->getMessages()[Messages::class], $messages->getMessage(Messages::class));
    }

    public function testCreateJa()
    {
        $messages = Messages::create('ja');
        $this->assertEquals($this->getMessages('ja')[Messages::class], $messages->getMessage(Messages::class));
    }

    public function testGetMessage()
    {
        $messages = Messages::create('en');
        $this->assertEquals($this->getMessages()[Messages::class], $messages->getMessage(Messages::class));
    }

    public function testCreateUsingLocaleFile()
    {
        $messages = Messages::create(__DIR__ . '/locale-files');
        $this->assertEquals('test-locale-files', $messages->getMessage('test-me'));
    }

    public function testFallBackErrorMessage()
    {
        $result = new Result('test-value', 'test');
        $result->failed('no-such-name', []);
        $result->finalize(Messages::create('en'));

        $this->assertFalse($result->isValid());
        $fallbackMessage = $this->getMessages('en')[Messages::class];
        $this->assertEquals([$fallbackMessage], $result->getErrorMessage());
    }

}
