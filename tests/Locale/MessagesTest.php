<?php
/**
 * Created by PhpStorm.
 * User: wsjp
 * Date: 2019/03/25
 * Time: 20:46
 */

use WScore\Validation\Locale\Messages;
use PHPUnit\Framework\TestCase;

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
        $messages = Messages::create(__DIR__ . '/locale-files/validation.message.php');
        $this->assertEquals('test-locale-files', $messages->getMessage('test-me'));
    }

}
