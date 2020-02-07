<?php
namespace Test\GollumSF\UrlTokenizerBundle\Unit\Annotation;

use GollumSF\UrlTokenizerBundle\Annotation\ValidToken;
use PHPUnit\Framework\TestCase;

class ValidTokenTest extends TestCase
{
	
	public function provideConstruct() {
		return [
			[ [],  null, null, null ],
			[ [ 'lifeTime' => 4242 ],  4242, null, null ],
			[ [ 'key' => 'new_key' ],  null, 'new_key', null ],
			[ [ 'fullUrl' => true ],  null, null, true ],
			[ [ 'fullUrl' => false ],  null, null, false ],
		];
	}

	/**
	 * @dataProvider provideConstruct
	 */
	public function testConstruct($param, $lifeTime, $key, $fullUrl) {
		$annotation = new ValidToken($param);
		$this->assertEquals($annotation->getLifeTime(), $lifeTime);
		$this->assertEquals($annotation->getKey(), $key);
		$this->assertEquals($annotation->isFullUrl(), $fullUrl);
		$this->assertEquals($annotation->getAliasName(), ValidToken::ALIAS_NAME);
		$this->assertFalse($annotation->allowArray());
	}
	
}