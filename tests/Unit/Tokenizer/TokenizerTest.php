<?php
namespace Test\GollumSF\UrlTokenizerBundle\Unit\Tokenizer;

use GollumSF\ReflectionPropertyTest\ReflectionPropertyTrait;
use GollumSF\UrlTokenizerBundle\Calendar\Calendar;
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
	
	/** @var Tokenizer */
	private $tokenizer;
	
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
		$configuration = $this->getMockForAbstractClass(UrlTokenizerConfigurationInterface::class);
		$tokenizer = new Tokenizer($configuration);
		
		$this->assertEquals(
			$this->reflectionCallMethod($tokenizer, 'getQueryParameters', [ $url ]), $result
		);
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
	 * @depends testQueryParameters
	 * @dataProvider provideGetSortedQuery
	 */
	public function testGetSortedQuery($url, $result) {
		$configuration = $this->getMockForAbstractClass(UrlTokenizerConfigurationInterface::class);
		$tokenizer = new Tokenizer($configuration);
		
		$ordered = $this->reflectionCallMethod($tokenizer, 'getSortedQuery', [ $url ]);
		$this->assertEquals($ordered, $result);
	}

	public function provideGenerateTokenOnlyParam() {
		return [
			[ false, 'sha256'   , 'http://www.urltokenizer.com/fakepath'                     , ''                    , 'KeyTest_1!'.uniqid(), ],
			[ false, 'sha256'   , 'http://www.urltokenizer.com/fakepath?'                    , ''                    , 'KeyTest_2!'.uniqid(), ],
			[ false, 'sha256'   , 'http://www.urltokenizer.com/fakepath?param1=ZZ&param2=hh' , 'param1=ZZ&param2=hh' , 'KeyTest_3!'.uniqid(), ],
			[ false, 'sha256'   , 'http://www.urltokenizer.com/fakepath?param1=ZZ&param2=hh&', 'param1=ZZ&param2=hh' , 'KeyTest_4!'.uniqid(), ],
			[ false, 'sha256'   , 'http://www.urltokenizer.com/fakepath?param2=ZZ&param1=hh&', 'param1=hh&param2=ZZ' , 'KeyTest_5!'.uniqid(), ],
			[ false, 'sha256'   , 'http://www.urltokenizer.com/fakepath?param1=ZZ&param2=%40', 'param1=ZZ&param2=%40', 'KeyTest_6!'.uniqid(), ],
			[ false, 'sha256'   , 'http://www.urltokenizer.com/fakepath'                     , ''                    , null, ],
			[ false, 'sha256'   , 'http://www.urltokenizer.com/fakepath?'                    , ''                    , null, ],
			[ false, 'sha256'   , 'http://www.urltokenizer.com/fakepath?param1=ZZ&param2=hh' , 'param1=ZZ&param2=hh' , null, ],
			[ false, 'sha256'   , 'http://www.urltokenizer.com/fakepath?param1=ZZ&param2=hh&', 'param1=ZZ&param2=hh' , null, ],
			[ false, 'sha256'   , 'http://www.urltokenizer.com/fakepath?param2=ZZ&param1=hh&', 'param1=hh&param2=ZZ' , null, ],
			[ false, 'sha256'   , 'http://www.urltokenizer.com/fakepath?param1=ZZ&param2=%40', 'param1=ZZ&param2=%40', null, ],
			[ null , 'sha256'   , 'http://www.urltokenizer.com/fakepath?param1=ZZ&param2=hh' , 'param1=ZZ&param2=hh' , 'KeyTest!'.uniqid(), ],
			[ false, 'sha1'     , 'http://www.urltokenizer.com/fakepath?param1=ZZ&param2=hh' , 'param1=ZZ&param2=hh' , 'KeyTest!'.uniqid(), ],
			[ false, 'md5'      , 'http://www.urltokenizer.com/fakepath?param1=ZZ&param2=hh' , 'param1=ZZ&param2=hh' , 'KeyTest!'.uniqid(), ],
			[ false, 'sha512'   , 'http://www.urltokenizer.com/fakepath?param1=ZZ&param2=hh' , 'param1=ZZ&param2=hh' , 'KeyTest!'.uniqid(), ],
			[ false, 'ripemd128', 'http://www.urltokenizer.com/fakepath?param1=ZZ&param2=hh' , 'param1=ZZ&param2=hh' , 'KeyTest!'.uniqid(), ],
		];
	}

	/**
	 * @dataProvider provideGenerateTokenOnlyParam
	 */
	public function testGenerateTokenOnlyParam($fullURL, $algo, $url, $sortedUrl, $key) {
		
		$defaultToken = self::TOKEN.uniqid();
		$configuration = $this->getMockForAbstractClass(UrlTokenizerConfigurationInterface::class);
		$configuration
			->method('getSecret')
			->willReturn($defaultToken)
		;
		$configuration
			->expects($this->once())
			->method('getAlgo')
			->willReturn($algo)
		;
		
		if ($fullURL !== null) {
			$configuration
				->expects($this->never())
				->method('getDefaultFullUrl')
			;			
		} else {
			$configuration
				->expects($this->once())
				->method('getDefaultFullUrl')
				->willReturn(false)
			;
		}
		
		$tokenizer = new Tokenizer($configuration);

		$token  = $tokenizer->generateToken($url, $fullURL, $key);
		$result = hash_hmac($algo, $sortedUrl, $key ? $key : $defaultToken);

		$this->assertNotNull($token);
		$this->assertNotEmpty($token);
		$this->assertEquals($token,$result);
	}


	public function provideGenerateTokenFullMatch() {
		return [
			[ true, 'sha256'   , 'http://www.urltokenizer.com/fakepath'                     , ''                    , 'KeyTest_1!'.uniqid(), ],
			[ true, 'sha256'   , 'http://www.urltokenizer.com/fakepath?'                    , ''                    , 'KeyTest_2!'.uniqid(), ],
			[ true, 'sha256'   , 'http://www.urltokenizer.com/fakepath?param1=ZZ&param2=hh' , 'param1=ZZ&param2=hh' , 'KeyTest_3!'.uniqid(), ],
			[ true, 'sha256'   , 'http://www.urltokenizer.com/fakepath?param1=ZZ&param2=hh&', 'param1=ZZ&param2=hh' , 'KeyTest_4!'.uniqid(), ],
			[ true, 'sha256'   , 'http://www.urltokenizer.com/fakepath?param2=ZZ&param1=hh&', 'param1=hh&param2=ZZ' , 'KeyTest_5!'.uniqid(), ],
			[ true, 'sha256'   , 'http://www.urltokenizer.com/fakepath?param1=ZZ&param2=%40', 'param1=ZZ&param2=%40', 'KeyTest_6!'.uniqid(), ],
			[ true, 'sha256'   , 'http://www.urltokenizer.com/fakepath'                     , ''                    , null, ],
			[ true, 'sha256'   , 'http://www.urltokenizer.com/fakepath?'                    , ''                    , null, ],
			[ true, 'sha256'   , 'http://www.urltokenizer.com/fakepath?param1=ZZ&param2=hh' , 'param1=ZZ&param2=hh' , null, ],
			[ true, 'sha256'   , 'http://www.urltokenizer.com/fakepath?param1=ZZ&param2=hh&', 'param1=ZZ&param2=hh' , null, ],
			[ true, 'sha256'   , 'http://www.urltokenizer.com/fakepath?param2=ZZ&param1=hh&', 'param1=hh&param2=ZZ' , null, ],
			[ true, 'sha256'   , 'http://www.urltokenizer.com/fakepath?param1=ZZ&param2=%40', 'param1=ZZ&param2=%40', null, ],
			[ null, 'sha256'   , 'http://www.urltokenizer.com/fakepath?param1=ZZ&param2=hh' , 'param1=ZZ&param2=hh' , 'KeyTest!'.uniqid(), ],
			[ true, 'sha1'     , 'http://www.urltokenizer.com/fakepath?param1=ZZ&param2=hh' , 'param1=ZZ&param2=hh' , 'KeyTest!'.uniqid(), ],
			[ true, 'md5'      , 'http://www.urltokenizer.com/fakepath?param1=ZZ&param2=hh' , 'param1=ZZ&param2=hh' , 'KeyTest!'.uniqid(), ],
			[ true, 'sha512'   , 'http://www.urltokenizer.com/fakepath?param1=ZZ&param2=hh' , 'param1=ZZ&param2=hh' , 'KeyTest!'.uniqid(), ],
			[ true, 'ripemd128', 'http://www.urltokenizer.com/fakepath?param1=ZZ&param2=hh' , 'param1=ZZ&param2=hh' , 'KeyTest!'.uniqid(), ],
		];
	}

	/**
	 * @dataProvider provideGenerateTokenFullMatch
	 */
	public function testGenerateTokenFullMatch($fullURL, $algo, $url, $sortedUrl, $key) {

		$defaultToken = self::TOKEN.uniqid();
		$configuration = $this->getMockForAbstractClass(UrlTokenizerConfigurationInterface::class);
		$configuration
			->method('getSecret')
			->willReturn($defaultToken)
		;
		$configuration
			->expects($this->once())
			->method('getAlgo')
			->willReturn($algo)
		;

		if ($fullURL !== null) {
			$configuration
				->expects($this->never())
				->method('getDefaultFullUrl')
			;
		} else {
			$configuration
				->expects($this->once())
				->method('getDefaultFullUrl')
				->willReturn(true)
			;
		}

		$tokenizer = new Tokenizer($configuration);
		$token  = $tokenizer->generateToken ($url, $fullURL, $key);
		$result = hash_hmac($algo, 'http://www.urltokenizer.com/fakepath '.$sortedUrl, $key ? $key : $defaultToken);

		$this->assertNotNull($token);
		$this->assertNotEmpty($token);
		$this->assertEquals($token, $result);
	}
	
	public function provideGenerateUrl() {
		return [
			[ 'd', 't', 'http://www.urltokenizer.com/fakepath'                     , '?' ],
			[ 'd', 't', 'http://www.urltokenizer.com/fakepath?'                    , '&' ],
			[ 'd', 't', 'http://www.urltokenizer.com/fakepath?param1=ZZ&param2=hh' , '&' ],
			[ 'd', 't', 'http://www.urltokenizer.com/fakepath?param1=ZZ&param2=hh&', '&' ],
			[ 'd', 't', 'http://www.urltokenizer.com/fakepath?param2=ZZ&param1=hh&', '&' ],
			[ 'd', 't', 'http://www.urltokenizer.com/fakepath?param1=ZZ&param2=%40', '&' ],
			[ 'AAAA', 'EEEE', 'http://www.urltokenizer.com/fakepath?param1=ZZ&param2=%40', '&' ],
		];
	}

	/**
	 * @depends testGenerateTokenOnlyParam
	 * @depends testGenerateTokenFullMatch
	 * @dataProvider provideGenerateUrl
	 */
	public function testGenerateUrl($d, $t, $urlOri, $separator) {

		$defaultToken = self::TOKEN.uniqid();
		$configuration = $this->getMockForAbstractClass(UrlTokenizerConfigurationInterface::class);
		$configuration
			->method('getSecret')
			->willReturn($defaultToken)
		;
		$configuration
			->expects($this->exactly(2))
			->method('getAlgo')
			->willReturn(self::ALGO)
		;
		$configuration
			->expects($this->exactly(2))
			->method('getDefaultFullUrl')
			->willReturn(false)
		;
		$configuration
			->expects($this->once())
			->method('getTokenQueryName')
			->willReturn($t)
		;
		$configuration
			->expects($this->once())
			->method('getTokenTimeQueryName')
			->willReturn($d)
		;
		
		$calendar = $this->getMockBuilder(Calendar::class)->getMock();
		$time = time();
		$calendar
			->expects($this->once())
			->method('time')
			->willReturn($time)
		;

		$tokenizer = new Tokenizer($configuration, $calendar);
		
		$url   = $tokenizer->generateUrl   ($urlOri);
		$token = $tokenizer->generateToken ($urlOri.$separator.$d.'='.$time);
		$this->assertEquals($url, $urlOri.$separator.$d.'='.$time.'&'.$t.'='.$token);
	}

	public function provideTokenOrder() {
		return [
			[ 'http://www.urltokenizer.com/fakepath?param1=aaa&param2=ccc'           , 'http://www.urltokenizer.com/fakepath?param2=ccc&param1=aaa' ],
			[ 'http://www.urltokenizer.com/fakepath?param1=a%20aa&param2=ccc'        , 'http://www.urltokenizer.com/fakepath?param2=ccc&param1=a%20aa' ],
			[ 'http://www.urltokenizer.com/fakepath?param1=a+aa&param2=ccc'          , 'http://www.urltokenizer.com/fakepath?param2=ccc&param1=a%2Baa' ],
			[ 'http://www.urltokenizer.com/fakepath?param1=a%20aa&param2=ccc'        , 'http://www.urltokenizer.com/fakepath?param2=ccc&param1=a aa' ],
			[ 'http://www.urltokenizer.com/fakepath?param1=aaa&&&&&&&param2=ccc'     , 'http://www.urltokenizer.com/fakepath?param2=ccc&param1=aaa&&' ],
			[ 'http://www.urltokenizer.com/fakepath?param1=aaa&param1=bbb&param2=ccc', 'http://www.urltokenizer.com/fakepath?param1=aaa&param2=ccc&param1=bbb' ],
			[ 'http://www.urltokenizer.com/fakepath?param1=aaa&=bbb&param2=ccc'      , 'http://www.urltokenizer.com/fakepath?param1=aaa&param2=ccc&=bbb' ],
		];
	}
	
	/**
	 * @depends testGenerateUrl
	 * @depends testRemoveTokenInUrl
	 * @dataProvider provideTokenOrder
	 */
	public  function testTokenOrder($url1, $url2) {
		$defaultToken = self::TOKEN.uniqid();
		$configuration = $this->getMockForAbstractClass(UrlTokenizerConfigurationInterface::class);
		$configuration
			->method('getSecret')
			->willReturn($defaultToken)
		;
		$configuration
			->method('getAlgo')
			->willReturn(self::ALGO)
		;
		$configuration
			->method('getDefaultFullUrl')
			->willReturn(true)
		;
		$tokenizer = new Tokenizer($configuration);
		
		$token1 = $tokenizer->generateToken($url1);
		$token2 = $tokenizer->generateToken($url2);
		$this->assertEquals($token1, $token2);
	}

	public function provideRemoveTokenInUrl() {
		return [
			[ 't', 'http://www.urltokenizer.com/fakepath?param1=aaa&param2=ccc&t=0123456'                 , 'http://www.urltokenizer.com/fakepath?param1=aaa&param2=ccc'                  ],
			[ 't', 'http://www.urltokenizer.com/fakepath?param1=a%20aa&param2=ccc&t=0123456'              , 'http://www.urltokenizer.com/fakepath?param1=a%20aa&param2=ccc'               ],
			[ 't', 'http://www.urltokenizer.com/fakepath?param1=a+aa&param2=ccc&t=0123456'                , 'http://www.urltokenizer.com/fakepath?param1=a%2Baa&param2=ccc'               ],
			[ 't', 'http://www.urltokenizer.com/fakepath?param1=a+aa&param2=ccc&t=0123456'                , 'http://www.urltokenizer.com/fakepath?param1=a%2Baa&param2=ccc'               ],
			[ 't', 'http://www.urltokenizer.com/fakepath?param1=aaa&param2=ccc&t=0123456&d=5010231123'    , 'http://www.urltokenizer.com/fakepath?param1=aaa&param2=ccc&d=5010231123'     ],
			[ 't', 'http://www.urltokenizer.com/fakepath?param1=a%20aa&param2=ccc&t=0123456&d=5010231123' , 'http://www.urltokenizer.com/fakepath?param1=a%20aa&param2=ccc&d=5010231123'  ],
			[ 't', 'http://www.urltokenizer.com/fakepath?param1=a+aa&param2=ccc&t=0123456&d=5010231123'   , 'http://www.urltokenizer.com/fakepath?param1=a%2Baa&param2=ccc&d=5010231123'  ],
			[ 'BB', 'http://www.urltokenizer.com/fakepath?param1=a+aa&param2=ccc&BB=0123456&AA=5010231123', 'http://www.urltokenizer.com/fakepath?param1=a%2Baa&param2=ccc&AA=5010231123' ],
		];
	}

	/**
	 * @dataProvider provideRemoveTokenInUrl
	 */
	public  function testRemoveTokenInUrl($t, $urlWithToken, $url) {
		$configuration = $this->getMockForAbstractClass(UrlTokenizerConfigurationInterface::class);
		$configuration
			->expects($this->once())
			->method('getTokenQueryName')
			->willReturn($t)
		;
		$tokenizer = new Tokenizer($configuration);
		$this->assertEquals($tokenizer->removeToken($urlWithToken), $url);
	}

	public function provideGetTokenInUrl() {
		return [
			[ 't', 'http://www.urltokenizer.com/fakepath?t=a0123456789'                       , 'a0123456789' ],
			[ 't', 'http://www.urltokenizer.com/fakepath?t=a0123456789&param1=ZZ&param2=hh'   , 'a0123456789' ],
			[ 't', 'http://www.urltokenizer.com/fakepath?param1=ZZ&t=a0123456789&param2=hh'   , 'a0123456789' ],
			[ 't', 'http://www.urltokenizer.com/fakepath?param1=ZZ&t=a0123456789%40&param2=hh', 'a0123456789@' ],
			[ 'AA', 'http://www.urltokenizer.com/fakepath?AA=a0123456789'                       , 'a0123456789' ],
		];
	}

	/**
	 * @dataProvider provideGetTokenInUrl
	 */
	public  function testGetTokenInUrl($t, $url, $token) {
		$configuration = $this->getMockForAbstractClass(UrlTokenizerConfigurationInterface::class);
		$configuration
			->expects($this->once())
			->method('getTokenQueryName')
			->willReturn($t)
		;
		$tokenizer = new Tokenizer($configuration);
		$this->assertTrue ($tokenizer->getToken($url) == $token);
	}

	public function provideGetTokenNull() {
		return [
			[ 't', 'http://www.urltokenizer.com/fakepath'                                     ],
			[ 't', 'http://www.urltokenizer.com/fakepath?param1=ZZ&param2=hh'                 ],
			[ 't', 'http://www.urltokenizer.com/fakepath?param1=ZZ'                           ],
			[ 'AAA', 'http://www.urltokenizer.com/fakepath?param1=ZZ&t=a0123456789&param2=hh' ],
		];
	}

	/**
	 * @dataProvider provideGetTokenNull
	 */
	public  function testGetTokenNull($t, $url) {
		$configuration = $this->getMockForAbstractClass(UrlTokenizerConfigurationInterface::class);
		$configuration
			->expects($this->once())
			->method('getTokenQueryName')
			->willReturn($t)
		;
		$tokenizer = new Tokenizer($configuration);
		$this->assertNull($tokenizer->getToken($url));
	}


	public function provideGetTokenTimeInUrl() {
		return [
			[ 'd', 'http://www.urltokenizer.com/fakepath?d=0123456'                       , 123456    ],
			[ 'd', 'http://www.urltokenizer.com/fakepath?d=55555&param1=ZZ&param2=hh'     , 55555     ],
			[ 'd', 'http://www.urltokenizer.com/fakepath?param1=ZZ&d=9999&param2=hh'      , 9999      ],
			[ 'd', 'http://www.urltokenizer.com/fakepath?param1=ZZ&d=654321%40&param2=hh' , 654321    ],
			[ 'AA', 'http://www.urltokenizer.com/fakepath?AA=333333333'                   , 333333333 ],
			[ 'd', 'http://www.urltokenizer.com/fakepath'                                 , null      ],
			[ 'd', 'http://www.urltokenizer.com/fakepath?d=ead'                           , null      ],
		];
	}

	/**
	 * @dataProvider provideGetTokenTimeInUrl
	 */
	public  function testGetTokenTimeInUrl($d, $url, $time) {
		$configuration = $this->getMockForAbstractClass(UrlTokenizerConfigurationInterface::class);
		$configuration
			->expects($this->once())
			->method('getTokenTimeQueryName')
			->willReturn($d)
		;
		$tokenizer = new Tokenizer($configuration);
		$this->assertEquals($tokenizer->getTokenTime($url), $time);
	}



	
}