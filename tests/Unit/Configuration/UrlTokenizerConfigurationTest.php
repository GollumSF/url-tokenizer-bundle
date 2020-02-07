<?php
namespace Test\GollumSF\UrlTokenizerBundle\Unit\Configuration;

use GollumSF\UrlTokenizerBundle\Configuration\UrlTokenizerConfiguration;
use PHPUnit\Framework\TestCase;

class UrlTokenizerConfigurationTest extends TestCase {

	public function testConstructor() {
		$apiDocConfiguration1 = new UrlTokenizerConfiguration(
			'SECRET',
			true,
			'ALGO',
			'TOKEN_QUERY_NAME',
			'TOKEN_TIME_QUERY_NAME'
		);
		$this->assertEquals($apiDocConfiguration1->getSecret(), 'SECRET');
		$this->assertTrue($apiDocConfiguration1->getDefaultFullUrl());
		$this->assertEquals($apiDocConfiguration1->getAlgo(), 'ALGO');
		$this->assertEquals($apiDocConfiguration1->getTokenQueryName(), 'TOKEN_QUERY_NAME');
		$this->assertEquals($apiDocConfiguration1->getTokenTimeQueryName(), 'TOKEN_TIME_QUERY_NAME');
	}
}