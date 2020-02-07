<?php
namespace Test\GollumSF\UrlTokenizerBundle\Calendar;

use GollumSF\UrlTokenizerBundle\Calendar\Calendar;
use PHPUnit\Framework\TestCase;

class CalendarTest extends TestCase {
	public function testTime() {
		$calendar = new Calendar();
		$time = time();
		$this->assertTrue($calendar->time() >= $time && $calendar->time() <= $time + 1);
	}
}