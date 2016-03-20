<?php

use EFrane\Transfugio\Transformers\Formatter\HttpURI;

class Transformers_Formatter_HttpURITest extends \Codeception\TestCase\Test
{
  /**
   * @var \UnitTester
   */
  protected $tester;

  // tests
  public function testIsFormatHelper()
  {
    $this->assertInstanceOf('EFrane\Transfugio\Transformers\Formatter\FormatHelper', new HttpURI());
  }

  public function testInstantiate()
  {
    $this->assertInstanceOf('EFrane\Transfugio\Transformers\Formatter\HttpURI', new HttpURI());
  }

  public function testValidate()
  {
    $url = new HttpURI();

    $this->assertTrue($url->validate('google.com'));
    $this->assertTrue($url->validate('http://example.tld'));

    $this->assertFalse($url->validate('Random String'));
    $this->assertFalse($url->validate('Rand/om.url'));
  }
}