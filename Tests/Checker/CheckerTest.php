<?php
namespace GollumSF\UrlTokenizerBundle\Tests\Checkcer;


use GollumSF\CoreBundle\Test\AbstractWebTestCase;
use GollumSF\UrlTokenizerBundle\Checker\Checker;
use GollumSF\UrlTokenizerBundle\Tokenizer\Tokenizer;


/**
 * CheckcerTest
 *
 * @author Damien Duboeuf <smeagolworms4@gmail.com>
 */
class CheckcerTest extends AbstractWebTestCase {
	
	const TOKEN = 'SuperTestKey!é&95';
	
	const URL_TEST1 = 'http://www.urltokenizer.com/fakepath';
	const URL_TEST2 = 'http://www.urltokenizer.com/fakepath?';
	const URL_TEST3 = 'http://www.urltokenizer.com/fakepath?param1=ZZ&param2=hh';
	const URL_TEST4 = 'http://www.urltokenizer.com/fakepath?param1=ZZ&param2=hh&';
	
	/**
	 * @var Tokenizer
	 */
	private $tokenizer;
	
	/**
	 * @var Checker
	 */
	private $checker;
	
	
	/**
	 * Call before the test
	 */
	protected function setUp () {
		parent::setUp ();
		$this->tokenizer = new Tokenizer(self::TOKEN.uniqid());
		$this->checker = new Checker($this->tokenizer);
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
	 * @covers GollumSF\UrlTokenizerBundle\Checker\Checker::checkToken
	 */
	public  function testCheckTokenOK($url) {
		$urlTokenise = $this->tokenizer->generateUrl ($url);
		$this->assertTrue ($this->checker->checkToken($urlTokenise));
	}
	
	/**
	 * @covers GollumSF\UrlTokenizerBundle\Checker\Checker::checkToken
	 */
	public  function testCheckTokenKOReplace() {
		$urlTokenise = $this->tokenizer->generateUrl (self::URL_TEST3);
		$urlChanged  = str_replace ('param2=hh', 'param2=HH', $urlTokenise);
		$this->assertTrue (!$this->checker->checkToken($urlChanged));
	}
	
	/**
	 * @covers GollumSF\UrlTokenizerBundle\Checker\Checker::checkToken
	 */
	public  function testCheckTokenKORemove() {
		$urlTokenise = $this->tokenizer->generateUrl (self::URL_TEST3);
		$urlChanged  = str_replace ('&param2=hh', '', $urlTokenise);
		$this->assertTrue (!$this->checker->checkToken($urlChanged));
	}
	
	/**
	 * @dataProvider provideCheckTokenKOAdd
	 * @covers GollumSF\UrlTokenizerBundle\Checker\Checker::checkToken
	 */
	public  function testCheckTokenKOAdd($url, $separator) {
		$urlTokenise = $this->tokenizer->generateUrl ($url);
		$urlChanged  = $url.$separator.'new_param=new123';
		$this->assertTrue (!$this->checker->checkToken($urlChanged));
	}
	
	
}