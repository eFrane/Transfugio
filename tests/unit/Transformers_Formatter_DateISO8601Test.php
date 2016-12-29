<?php

use Carbon\Carbon;
use EFrane\Transfugio\Transformers\Formatter\DateTimeISO8601;

class Transformers_Formatter_DateISO8601Test extends \Codeception\TestCase\Test
{
  /**
   * @var \UnitTester
   */
  protected $tester;

  public function testInstantiate()
  {
    $this->assertInstanceOf('EFrane\Transfugio\Transformers\Formatter\DateISO8601', new DateTimeISO8601());
  }

  public function testIsFormatHelper()
  {
    $this->assertInstanceOf('EFrane\Transfugio\Transformers\Formatter\FormatHelper', new DateTimeISO8601());
  }

  public function testFormat()
  {
    $formatter = new DateTimeISO8601();

    $this->assertEquals('2016-01-01', $formatter->format(Carbon::parse('2016-01-01')));
  }

  /**
   * @expectedException \InvalidArgumentException
   */
  public function testFormatNonCarbon()
  {
    $formatter = new DateTimeISO8601();

    $this->assertEquals('', $formatter->format('some non-date value'));
  }
}