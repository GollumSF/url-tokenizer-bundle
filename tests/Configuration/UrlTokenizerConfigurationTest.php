<?php

namespace Test\GollumSF\UrlTokenizerBundle\Configuration;

use GollumSF\UrlTokenizerBundle\Configuration\UrlTokenizerConfiguration;
use PHPUnit\Framework\TestCase;

class UrlTokenizerConfigurationTest extends TestCase {

	public function testConstructor() {
		$apiDocConfiguration1 = new UrlTokenizerConfiguration(
			'SECRET'
		);
		$this->assertEquals($apiDocConfiguration1->getSecret(), 'SECRET');
	}
}