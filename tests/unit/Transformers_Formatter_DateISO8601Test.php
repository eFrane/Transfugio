<?php

use Carbon\Carbon;
use EFrane\Transfugio\Transformers\Formatter\DateISO8601;

class Transformers_Formatter_DateISO8601Test extends \Codeception\TestCase\Test
{
  /**
   * @var \UnitTester
   */
  protected $tester;

  public function testIsFormatHelper()
  {
    $this->assertInstanceOf('EFrane\Transfugio\Transformers\Formatter\FormatHelper', new DateISO8601());
  }

  public function testInstantiate()
  {
    $this->assertInstanceOf('EFrane\Transfugio\Transformers\Formatter\DateISO8601', $instance);
  }

  public function testFormat()
  {
    $carbon = Carbon::now();
    $formatter = new DateISO8601();

    $this->assertEquals($carbon->toIso8601String(), $formatter->format($carbon));
  }

  /**
   * @expectedException \InvalidArgumentException
   */
  public function testFormatNonCarbon()
  {
    $formatter = new DateISO8601();

    $this->assertEquals('', $formatter->format('some non-date value'));
  }
}