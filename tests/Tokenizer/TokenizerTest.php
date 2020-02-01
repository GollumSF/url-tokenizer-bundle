<?php
namespace GollumSF\UrlTokenizerBundle\Tests\Tokenizer;

use GollumSF\ReflectionPropertyTest\ReflectionPropertyTrait;
use GollumSF\UrlTokenizerBundle\Configuration\UrlTokenizerConfigurationInterface;
use GollumSF\UrlTokenizerBundle\Tokenizer\Tokenizer;
use PHPUnit\Framework\TestCase;


/**
 * TokenizerTest
 *
 * @author Damien Duboeuf <smeagolworms4@gmail.com>
 */
class TokenizerTest extends TestCase {
	
	use ReflectionPropertyTrait;

	const ALGO = 'sha256';
	
	const TOKEN = 'SuperTestKey!é&95';
	
	const URL_TEST1 = 'http://www.urltokenizer.com/fakepath';
	const URL_TEST2 = 'http://www.urltokenizer.com/fakepath?';
	const URL_TEST3 = 'http://www.urltokenizer.com/fakepath?param1=ZZ&param2=hh';
	const URL_TEST4 = 'http://www.urltokenizer.com/fakepath?param1=ZZ&param2=hh&';
	
	const FAKE_TOKEN           = 'a0123456789';
	const URL1_WITHTOKEN_TEST1 = 'http://www.urltokenizer.com/fakepath?t=a0123456789';
	const URL3_WITHTOKEN_TEST1 = 'http://www.urltokenizer.com/fakepath?t=a0123456789&param1=ZZ&param2=hh';
	const URL3_WITHTOKEN_TEST2 = 'http://www.urltokenizer.com/fakepath?param1=ZZ&t=a0123456789&param2=hh';
	
	/** @var Tokenizer */
	private $tokenizer;
	
	
	protected function setUp(): void {
		$configuration = $this->getMockForAbstractClass(UrlTokenizerConfigurationInterface::class);
		$configuration
			->method('getSecret')
			->willReturn(self::TOKEN.uniqid())
		;
		$configuration
			->method('getAlgo')
			->willReturn(self::ALGO)
		;
		$this->tokenizer = new Tokenizer($configuration);
	}

	public function provideGetSortedQuery() {
		return [
			[ 'http://www.urltokenizer.com/fakepath?aaa=1&bb=two&ccc=ad%40d', 'aaa=1&bb=two&ccc=ad%40d', ],
			[ 'http://www.urltokenizer.com/fakepath?bb=two&aaa=1&ccc=ad%40d', 'aaa=1&bb=two&ccc=ad%40d', ],
			[ 'http://www.urltokenizer.com/fakepath?ccc=ad%40d&bb=two&aaa=1', 'aaa=1&bb=two&ccc=ad%40d', ],
			[ 'http://www.urltokenizer.com/fakepath'                        , '', ],
			[ 'http://www.urltokenizer.com/fakepath?'                       , '', ],
		];
	}

	/**
	 * @dataProvider provideGetSortedQuery
	 */
	public function testGetSortedQuery($url, $result) {
		$ordered = $this->reflectionCallMethod($this->tokenizer, 'getSortedQuery', [ $url ]);
		
		$this->assertEquals($ordered, $result);
	}

	public function provideGenerateToken() {
		return [
			[ self::URL_TEST1, 'KeyTest_1!'.uniqid(), ],
			[ self::URL_TEST2, 'KeyTest_2!'.uniqid(), ],
			[ self::URL_TEST3, 'KeyTest_3!'.uniqid(), ],
			[ self::URL_TEST4, 'KeyTest_4!'.uniqid(), ],
		];
	}

	/**
	 * @depends testGetSortedQuery
	 * @dataProvider provideGenerateToken
	 */
	public function testGenerateToken($url, $key) {

		$sortedUrl = $this->reflectionCallMethod($this->tokenizer, 'getSortedQuery', [ $url ]);

		$token  = $this->tokenizer->generateToken ($url, false, $key);
		$result = hash_hmac(self::ALGO, $sortedUrl, $key);
		
		$this->assertNotNull($token);
		$this->assertNotEmpty($token);
		$this->assertEquals($token,$result);
	}

	/**
	 * depends testGetSortedQuery
	 * @dataProvider provideGenerateToken
	 */
	public function testGenerateTokenFullMatch($url, $key) {

		$sortedUrl = $this->reflectionCallMethod($this->tokenizer, 'getSortedQuery', [ $url ]);

		$token  = $this->tokenizer->generateToken ($url, true, $key);
		$result = hash_hmac(self::ALGO, 'http://www.urltokenizer.com/fakepath '.$sortedUrl, $key);

		$this->assertNotNull($token);
		$this->assertNotEmpty($token);
		$this->assertEquals($token,$result);
	}

	public function provideGenerateUrl() {
		return [
			[ self::URL_TEST1, '?' ],
			[ self::URL_TEST2, '&' ],
			[ self::URL_TEST3, '&' ],
			[ self::URL_TEST4, '&' ],
		];
	}
	
	/**
	 * @depends testGenerateToken
	 * @dataProvider provideGenerateUrl
	 */
	public function testGenerateUrl($urlOri, $separator) {
		$url   = $this->tokenizer->generateUrl   ($urlOri);
		$token = $this->tokenizer->generateToken ($urlOri);
		$this->assertTrue ($url == $urlOri.$separator."t=".$token);
	}
	
	/**
	 * @dataProvider provideRemoveTokenInUrl
	 */
	public  function testRemoveTokenInUrl($urlWithToken, $url) {
		$this->assertEquals($this->tokenizer->removeToken($urlWithToken), $url);
	}

	public function provideRemoveTokenInUrl() {
		return [
			[ self::URL1_WITHTOKEN_TEST1, self::URL_TEST1 ],
			[ self::URL3_WITHTOKEN_TEST1, self::URL_TEST3 ],
			[ self::URL3_WITHTOKEN_TEST2, self::URL_TEST3 ],
		];
	}

	public function provideGetTokenInUrl() {
		return [
			[ self::URL1_WITHTOKEN_TEST1, self::FAKE_TOKEN ],
			[ self::URL3_WITHTOKEN_TEST1, self::FAKE_TOKEN ],
			[ self::URL3_WITHTOKEN_TEST2, self::FAKE_TOKEN ],
		];
	}
	
	/**
	 * @dataProvider provideGetTokenInUrl
	 */
	public  function testGetTokenInUrl($url, $token) {
		$this->assertTrue ($this->tokenizer->getToken($url) == $token);
	}

	public function provideGetTokenNull() {
		return [
			[ self::URL_TEST1 ],
			[ self::URL_TEST2 ],
			[ self::URL_TEST3 ],
		];
	}

	/**
	 * @dataProvider provideGetTokenNull
	 */
	public  function testGetTokenNull($url) {
		$this->assertNull($this->tokenizer->getToken($url));
	}

	public function provideTokenOrder() {
		return [
			[ self::URL_TEST2.'param1=aaa&param2=ccc', self::URL_TEST2.'param2=ccc&param1=aaa' ],
			[ self::URL_TEST2.'param1=a%20aa&param2=ccc', self::URL_TEST2.'param2=ccc&param1=a%20aa' ],
			[ self::URL_TEST2.'param1=a+aa&param2=ccc', self::URL_TEST2.'param2=ccc&param1=a%20aa' ],
			[ self::URL_TEST2.'param1=aaa&&&&&&&param2=ccc', self::URL_TEST2.'param2=ccc&param1=aaa&&' ],
			[ self::URL_TEST2.'param1=aaa&param1=bbb&param2=ccc', self::URL_TEST2.'param1=aaa&param2=ccc&param1=bbb' ],
			[ self::URL_TEST2.'param1=aaa&=bbb&param2=ccc', self::URL_TEST2.'param1=aaa&param2=ccc&=bbb' ],
		];
	}
	
	/**
	 * @depends testGenerateUrl
	 * @depends testRemoveTokenInUrl
	 * @dataProvider provideTokenOrder
	 */
	public  function testTokenOrder($url1, $url2) {
		$token1 = $this->tokenizer->generateToken($url1);
		$token2 = $this->tokenizer->generateToken($url2);
		$this->assertTrue ($token1 == $token2);
	}

	public function provideQueryParameters() {
		return [
			[
				'http://domain.com',
				[
					'baseUrl' => 'http://domain.com',
					'listParams' => [],
				]
			],

			[
				'http://domain.com?',
				[
					'baseUrl' => 'http://domain.com',
					'listParams' => [],
				]
			],

			[
				'http://domain.com?param',
				[
					'baseUrl' => 'http://domain.com',
					'listParams' => [ [ 'param', '' ] ],
				]
			],

			[
				'http://domain.com?param&param1=a&param2=2',
				[
					'baseUrl' => 'http://domain.com',
					'listParams' => [ [ 'param', '' ], [ 'param1', 'a' ], [ 'param2', '2' ] ],
				]
			],
		];
	}
	
	/**
	 * @dataProvider provideQueryParameters
	 */
	public function testQueryParameters($url, $result) {
		$this->assertEquals(
			$this->reflectionCallMethod($this->tokenizer, 'getQueryParameters', [ $url ]), $result
		);
	}
	
}