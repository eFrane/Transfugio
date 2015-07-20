<?php

use EFrane\Transfugio\Transformers\Formatter\EMailURI;

class Transformers_Formatter_EMailURITest extends \Codeception\TestCase\Test
{
  /**
   * @var \UnitTester
   */
  protected $tester;

  // tests
  public function testIsFormatHelper()
  {
    $this->assertInstanceOf('EFrane\Transfugio\Transformers\Formatter\FormatHelper', new EMailURI());
  }

  public function testInstantiate()
  {
    $this->assertInstanceOf('EFrane\Transfugio\Transformers\Formatter\EMailURI', new EMailURI());
  }

  public function testValidate()
  {
    $email = new EMailURI();

    $this->assertTrue($email->validate('valid@email.com'));
    $this->assertFalse($email->validate('inval id@email.com'));
  }

  public function testFormat()
  {
    $email = new EMailURI();

    $this->assertEquals('mailto:valid@email.com', $email->format('valid@email.com'));
  }

  /**
   * @expectedException \InvalidArgumentException
   */
  public function testFormatInvalidAddress()
  {
    $email = new EMailURI();
    $email->format('in valid@email.com');
  }
}