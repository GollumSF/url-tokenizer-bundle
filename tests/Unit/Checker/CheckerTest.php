<?php
namespace Test\GollumSF\UrlTokenizerBundle\Unit\Checkcer;


use GollumSF\UrlTokenizerBundle\Checker\Checker;
use GollumSF\UrlTokenizerBundle\Configuration\UrlTokenizerConfigurationInterface;
use GollumSF\UrlTokenizerBundle\Tokenizer\Tokenizer;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;


/**
 * CheckcerTest
 *
 * @author Damien Duboeuf <smeagolworms4@gmail.com>
 */
class CheckerTest extends TestCase {

	const ALGO = 'sha256';

	const TOKEN = 'SuperTestKey!é&95';

	/** @var Tokenizer */
	private $tokenizer;

	/**  @var Checker */
	private $checker;

	/**  @var RequestStack|MockObject */
	private $requestStack;

	protected function setUp (): void {
		$this->requestStack = $this->getMockBuilder(RequestStack::class)->disableOriginalConstructor()->getMock();

		$configuration = $this->createMock(UrlTokenizerConfigurationInterface::class);
		$configuration
			->method('getSecret')
			->willReturn(self::TOKEN.uniqid())
		;
		$configuration
			->method('getAlgo')
			->willReturn(self::ALGO)
		;
		$configuration
			->method('getTokenQueryName')
			->willReturn('t')
		;
		$configuration
			->method('getTokenTimeQueryName')
			->willReturn('d')
		;
		$configuration
			->method('getDefaultFullUrl')
			->willReturn(false)
		;

		$this->tokenizer = new Tokenizer($configuration);
		$this->checker = new Checker($this->tokenizer, $this->requestStack);
	}

	public static function provideCheckTokenOK() {
		return [
			[ 'http://www.urltokenizer.com/fakepath'                        ],
			[ 'http://www.urltokenizer.com/fakepath?'                       ],
			[ 'http://www.urltokenizer.com/fakepath?param1=ZZ&param2=hh'    ],
			[ 'http://www.urltokenizer.com/fakepath?param1=ZZ&param2=hh&'   ],
			[ 'http://www.urltokenizer.com/fakepath?param1=ZZ&param2=%20d&' ],
			[ 'http://www.urltokenizer.com/fakepath?param1=ZZ&param2=+d&'   ],
		];
	}

	#[DataProvider('provideCheckTokenOK')]
	public  function testCheckTokenOK($url) {
		$urlTokenise = $this->tokenizer->generateUrl ($url);
		$this->assertTrue($this->checker->checkToken($urlTokenise));
	}

	#[DataProvider('provideCheckTokenOK')]
	public  function testCheckTokenOKMasterRequest($url) {
		$urlTokenise = $this->tokenizer->generateUrl($url);
		$request = $this->getMockBuilder(Request::class)->disableOriginalConstructor()->getMock();
		$this->requestStack
			->expects($this->once())
			->method('getMainRequest')
			->willReturn($request)
		;
		$request
			->expects($this->once())
			->method('getUri')
			->willReturn($urlTokenise)
		;
		$this->assertTrue($this->checker->checkTokenMasterRequest());
	}

	public  function testCheckTokenKOReplace() {
		$urlTokenise = $this->tokenizer->generateUrl ('http://www.urltokenizer.com/fakepath?param1=ZZ&param2=hh');
		$urlChanged  = str_replace ('param2=hh', 'param2=HH', $urlTokenise);
		$this->assertFalse($this->checker->checkToken($urlChanged));
	}

	public static function provideCheckTokenKOAdd() {
		return [
			[ 'http://www.urltokenizer.com/fakepath'                       , '?' ],
			[ 'http://www.urltokenizer.com/fakepath?'                      , '&' ],
			[ 'http://www.urltokenizer.com/fakepath?param1=ZZ&param2=hh'   , '&' ],
			[ 'http://www.urltokenizer.com/fakepath?param1=ZZ&param2=hh&'  , '&' ],
			[ 'http://www.urltokenizer.com/fakepath?param1=ZZ&param2=%20d&', '&' ],
			[ 'http://www.urltokenizer.com/fakepath?param1=ZZ&param2=+d&'  , '&' ],
		];
	}

	#[DataProvider('provideCheckTokenKOAdd')]
	public  function testCheckTokenKOAdd($url, $separator) {
		$urlTokenise = $this->tokenizer->generateUrl ($url);
		$urlChanged  = $url.$separator.'new_param=new123';
		$this->assertTrue (!$this->checker->checkToken($urlChanged));
	}

	public  function testCheckTokenKORemove() {
		$urlTokenise = $this->tokenizer->generateUrl ('http://www.urltokenizer.com/fakepath?param1=ZZ&param2=hh');
		$urlChanged  = str_replace ('&param2=hh', '', $urlTokenise);
		$this->assertTrue (!$this->checker->checkToken($urlChanged));
	}

	public static function provideCheckTokenTime() {
		return [
			[ 'http://www.urltokenizer.com/fakepath'                        ],
			[ 'http://www.urltokenizer.com/fakepath?'                       ],
			[ 'http://www.urltokenizer.com/fakepath?param1=ZZ&param2=hh'    ],
			[ 'http://www.urltokenizer.com/fakepath?param1=ZZ&param2=hh&'   ],
			[ 'http://www.urltokenizer.com/fakepath?param1=ZZ&param2=%20d&' ],
			[ 'http://www.urltokenizer.com/fakepath?param1=ZZ&param2=+d&'   ],
		];
	}

	#[DataProvider('provideCheckTokenTime')]
	public  function testCheckTokenTimeOK($url) {
		$urlTokenise = $this->tokenizer->generateUrl ($url);
		$this->assertTrue($this->checker->checkTokenTime($urlTokenise, 10));
	}

	#[DataProvider('provideCheckTokenTime')]
	public  function testCheckTokenTimeOKMasterRequest($url) {


		$urlTokenise = $this->tokenizer->generateUrl ($url);
		$request = $this->getMockBuilder(Request::class)->disableOriginalConstructor()->getMock();
		$this->requestStack
			->expects($this->once())
			->method('getMainRequest')
			->willReturn($request)
		;
		$request
			->expects($this->once())
			->method('getUri')
			->willReturn($urlTokenise)
		;
		$this->assertTrue($this->checker->checkTokenTimeMasterRequest(10));
	}

	#[DataProvider('provideCheckTokenTime')]
	public  function testCheckTokenTimeKO($url) {
		$urlTokenise = $this->tokenizer->generateUrl ($url);
		sleep(1);
		$this->assertFalse($this->checker->checkTokenTime($urlTokenise, 0));
	}

	#[DataProvider('provideCheckTokenTime')]
	public  function testCheckTokenAndTokenTimeOK($url) {
		$urlTokenise = $this->tokenizer->generateUrl ($url);
		$this->assertTrue($this->checker->checkTokenAndTokenTime($urlTokenise, 10));
	}

	#[DataProvider('provideCheckTokenTime')]
	public  function testCheckTokenAndTokenTimeOKMasterRequest($url) {
		$urlTokenise = $this->tokenizer->generateUrl ($url);
		$request = $this->getMockBuilder(Request::class)->disableOriginalConstructor()->getMock();
		$this->requestStack
			->expects($this->once())
			->method('getMainRequest')
			->willReturn($request)
		;
		$request
			->expects($this->once())
			->method('getUri')
			->willReturn($urlTokenise)
		;
		$this->assertTrue($this->checker->checkTokenAndTokenTimeMasterRequest(10));
	}

	public  function testCheckTokenAndTokenTimeKOToken() {
		$urlTokenise = $this->tokenizer->generateUrl ('http://www.urltokenizer.com/fakepath?param1=ZZ&param2=hh');
		$urlChanged  = str_replace ('&param2=hh', '', $urlTokenise);
		$this->assertFalse($this->checker->checkTokenAndTokenTime($urlChanged, 10));
	}

	#[DataProvider('provideCheckTokenTime')]
	public  function testCheckTokenAndTokenTimeKOTokenTime($url) {
		$urlTokenise = $this->tokenizer->generateUrl ($url);
		sleep(1);
		$this->assertFalse($this->checker->checkTokenAndTokenTime($urlTokenise, 0));
	}
}
