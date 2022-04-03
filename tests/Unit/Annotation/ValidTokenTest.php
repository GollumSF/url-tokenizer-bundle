<?php
namespace Test\GollumSF\UrlTokenizerBundle\Unit\Annotation;

use GollumSF\UrlTokenizerBundle\Annotation\ValidToken;
use PHPUnit\Framework\TestCase;

class ValidTokenTest extends TestCase
{
	
	public function provideConstructLegacy() {
		return [
			[ [],  null, null, null ],
			[ [ 'lifeTime' => 4242 ],  4242, null, null ],
			[ [ 'key' => 'new_key' ],  null, 'new_key', null ],
			[ [ 'fullUrl' => true ],  null, null, true ],
			[ [ 'fullUrl' => false ],  null, null, false ],
		];
	}
	
	/**
	 * @dataProvider provideConstructLegacy
	 */
	public function testConstructLegacy($param, $lifeTime, $key, $fullUrl) {
		$annotation = new ValidToken($param);
		$this->assertEquals($annotation->getLifeTime(), $lifeTime);
		$this->assertEquals($annotation->getKey(), $key);
		$this->assertEquals($annotation->isFullUrl(), $fullUrl);
		$this->assertEquals($annotation->getAliasName(), ValidToken::ALIAS_NAME);
		$this->assertFalse($annotation->allowArray());
	}
	
	public function provideConstruct() {
		return [
			[ null, null, null ],
			[ 4242, null, null ],
			[ null, 'new_key', null ],
			[ null, null, true ],
			[ null, null, false ],
		];
	}
	
	/**
	 * @dataProvider provideConstruct
	 */
	public function testConstruct($lifeTime, $key, $fullUrl) {
		$annotation = new ValidToken($lifeTime, $fullUrl, $key);
		$this->assertEquals($annotation->getLifeTime(), $lifeTime);
		$this->assertEquals($annotation->getKey(), $key);
		$this->assertEquals($annotation->isFullUrl(), $fullUrl);
		$this->assertEquals($annotation->getAliasName(), ValidToken::ALIAS_NAME);
		$this->assertFalse($annotation->allowArray());
	}
	
}
