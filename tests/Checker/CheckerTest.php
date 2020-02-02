<?php
namespace GollumSF\UrlTokenizerBundle\Tests\Checkcer;


use GollumSF\UrlTokenizerBundle\Configuration\UrlTokenizerConfigurationInterface;
use GollumSF\UrlTokenizerBundle\Checker\Checker;
use GollumSF\UrlTokenizerBundle\Reflection\ControllerActionExtractor;
use GollumSF\UrlTokenizerBundle\Reflection\ControllerActionExtractorInterface;
use GollumSF\UrlTokenizerBundle\Tokenizer\Tokenizer;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\RequestStack;


/**
 * CheckcerTest
 *
 * @author Damien Duboeuf <smeagolworms4@gmail.com>
 */
class CheckcerTest extends TestCase {
	
	const ALGO = 'sha256';
	
	const TOKEN = 'SuperTestKey!é&95';
	
	const URL_TEST1 = 'http://www.urltokenizer.com/fakepath';
	const URL_TEST2 = 'http://www.urltokenizer.com/fakepath?';
	const URL_TEST3 = 'http://www.urltokenizer.com/fakepath?param1=ZZ&param2=hh';
	const URL_TEST4 = 'http://www.urltokenizer.com/fakepath?param1=ZZ&param2=hh&';
	
	/** @var Tokenizer */
	private $tokenizer;

	/**  @var Checker */
	private $checker;

	/**  @var RequestStack|MockObject */
	private $requestStack;
	
	
	protected function setUp (): void {
		$this->requestStack = $this->getMockBuilder(RequestStack::class)->disableOriginalConstructor()->getMock();
		
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
		$this->checker = new Checker($this->tokenizer, $this->requestStack);
	}
	
	public function provideCheckTokenOK() {
		return [
			[ self::URL_TEST1 ],
			[ self::URL_TEST2 ],
			[ self::URL_TEST3 ],
			[ self::URL_TEST4 ],
		];
	}
	
	public function provideCheckTokenKOAdd() {
		return [
			[ self::URL_TEST1, '&' ],
			[ self::URL_TEST2, '&' ],
			[ self::URL_TEST3, '&' ],
			[ self::URL_TEST4, '&' ],
		];
	}
	
	
	/**
	 * @dataProvider provideCheckTokenOK
	 */
	public  function testCheckTokenOK($url) {
		$urlTokenise = $this->tokenizer->generateUrl ($url);
		$this->assertTrue ($this->checker->checkToken($urlTokenise));
	}
	
	public  function testCheckTokenKOReplace() {
		$urlTokenise = $this->tokenizer->generateUrl (self::URL_TEST3);
		$urlChanged  = str_replace ('param2=hh', 'param2=HH', $urlTokenise);
		$this->assertTrue (!$this->checker->checkToken($urlChanged));
	}
	
	public  function testCheckTokenKORemove() {
		$urlTokenise = $this->tokenizer->generateUrl (self::URL_TEST3);
		$urlChanged  = str_replace ('&param2=hh', '', $urlTokenise);
		$this->assertTrue (!$this->checker->checkToken($urlChanged));
	}
	
	/**
	 * @dataProvider provideCheckTokenKOAdd
	 */
	public  function testCheckTokenKOAdd($url, $separator) {
		$urlTokenise = $this->tokenizer->generateUrl ($url);
		$urlChanged  = $url.$separator.'new_param=new123';
		$this->assertTrue (!$this->checker->checkToken($urlChanged));
	}
}