<?php
namespace Test\GollumSF\UrlTokenizerBundle\Unit\Annotation;

use GollumSF\UrlTokenizerBundle\Annotation\ValidToken;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ValidTokenTest extends TestCase
{

	public static function provideConstruct() {
		return [
			[ null, null, null ],
			[ 4242, null, null ],
			[ null, 'new_key', null ],
			[ null, null, true ],
			[ null, null, false ],
		];
	}

	#[DataProvider('provideConstruct')]
	public function testConstruct($lifeTime, $key, $fullUrl) {
		$annotation = new ValidToken($lifeTime, $fullUrl, $key);
		$this->assertEquals($annotation->getLifeTime(), $lifeTime);
		$this->assertEquals($annotation->getKey(), $key);
		$this->assertEquals($annotation->isFullUrl(), $fullUrl);
	}

}
