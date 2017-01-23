<?php
namespace GollumSF\UrlTokenizerBundle\Tests\Tokenizer;


use GollumSF\CoreBundle\Test\AbstractWebTestCase;
use GollumSF\UrlTokenizerBundle\Tokenizer\Tokenizer;


/**
 * TokenizerTest
 *
 * @author Damien Duboeuf <smeagolworms4@gmail.com>
 */
class TokenizerTest extends AbstractWebTestCase {
	
	const TOKEN = 'SuperTestKey!é&95';
	
	const URL_TEST1 = 'http://www.urltokenizer.com/fakepath';
	const URL_TEST2 = 'http://www.urltokenizer.com/fakepath?';
	const URL_TEST3 = 'http://www.urltokenizer.com/fakepath?param1=ZZ&param2=hh';
	const URL_TEST4 = 'http://www.urltokenizer.com/fakepath?param1=ZZ&param2=hh&';
	
	const FAKE_TOKEN           = 'a0123456789';
	const URL1_WITHTOKEN_TEST1 = 'http://www.urltokenizer.com/fakepath?t=a0123456789';
	const URL3_WITHTOKEN_TEST1 = 'http://www.urltokenizer.com/fakepath?t=a0123456789&param1=ZZ&param2=hh';
	const URL3_WITHTOKEN_TEST2 = 'http://www.urltokenizer.com/fakepath?param1=ZZ&t=a0123456789&param2=hh';
	
	/**
	 * @var Tokenizer
	 */
	private $tokenizer;
	
	
	/**
	 * Call before the test
	 */
	protected function setUp () {
		parent::setUp ();
		$this->tokenizer = new Tokenizer(self::TOKEN.uniqid());
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
	
	public function provideGenerateToken() {
		return [
			[ self::URL_TEST1, 'KeyTest_1!'.uniqid(), ],
			[ self::URL_TEST2, 'KeyTest_2!'.uniqid(), ],
			[ self::URL_TEST3, 'KeyTest_3!'.uniqid(), ],
			[ self::URL_TEST4, 'KeyTest_4!'.uniqid(), ],
		];
	}
	
	public function provideGenerateUrl() {
		return [
			[ self::URL_TEST1, '?' ],
			[ self::URL_TEST2, '&' ],
			[ self::URL_TEST3, '&' ],
			[ self::URL_TEST4, '&' ],
		];
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
	 * @covers GollumSF\UrlTokenizerBundle\Tokenizer\Tokenizer::getSortedQuery
	 * @dataProvider provideGetSortedQuery
	 */
	public function testGetSortedQuery($url, $result) {
		$ordered = $this->invokeMethod($this->tokenizer, 'getSortedQuery', $url);
		
		$this->assertEquals($ordered, $result);
	}
	
	
	/**
	 * @depends testGetSortedQuery
	 * @covers GollumSF\UrlTokenizerBundle\Tokenizer\Tokenizer::generateToken
	 * @dataProvider provideGenerateToken
	 */
	public function testGenerateToken($url, $key) {
		
		$sortedUrl = $this->invokeMethod($this->tokenizer, 'getSortedQuery', $url);
		
		$token  = $this->tokenizer->generateToken ($url, $key);
		$result = hash_hmac("sha1", $sortedUrl, $key);
		
		$this->assertNotNull($token);
		$this->assertNotEmpty($token);
		$this->assertEquals($token,$result);
	}
	
	/**
	 * @depends testGenerateToken
	 * @covers GollumSF\UrlTokenizerBundle\Tokenizer\Tokenizer::generateUrl
	 * @dataProvider provideGenerateUrl
	 */
	public function testGenerateUrl($urlOri, $separator) {
		$url   = $this->tokenizer->generateUrl   ($urlOri);
		$token = $this->tokenizer->generateToken ($urlOri);
		$this->assertTrue ($url == $urlOri.$separator."t=".$token);
	}
	
	/**
	 * @covers GollumSF\UrlTokenizerBundle\Tokenizer\Tokenizer::removeToken
	 * @dataProvider provideRemoveTokenInUrl
	 */
	public  function testRemoveTokenInUrl($urlWithToken, $url) {
		$this->assertTrue ($this->tokenizer->removeToken($urlWithToken) == $url);
	}
	
	/**
	 * @covers GollumSF\UrlTokenizerBundle\Tokenizer\Tokenizer::getToken
	 * @dataProvider provideGetTokenInUrl
	 */
	public  function testGetTokenInUrl($url, $token) {
		$this->assertTrue ($this->tokenizer->getToken($url) == $token);
	}
	
	/**
	 * @depends testGenerateUrl
	 * @depends testRemoveTokenInUrl
	 * @covers GollumSF\UrlTokenizerBundle\Tokenizer\Tokenizer::generateToken
	 * @dataProvider provideTokenOrder
	 */
	public  function testTokenOrder($url1, $url2) {
		$token1 = $this->tokenizer->generateToken($url1);
		$token2 = $this->tokenizer->generateToken($url2);
		$this->assertTrue ($token1 == $token2);
	}
	
}