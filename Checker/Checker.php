<?php

namespace GollumSF\UrlTokenizerBundle\Checker;

use GollumSF\UrlTokenizerBundle\Tokenizer\Tokenizer;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Checker
 *
 * @author Damien Duboeuf <smeagolworms4@gmail.com>
 */
class Checker {
	
	/**
	 * @var Tokenizer
	 */
	private $tokenizer;
	
	/**
	 * Checker constructor.
	 * @param Tokenizer $tokenizer
	 */
	public function __construct(Tokenizer $tokenizer) {
		$this->tokenizer = $tokenizer;
	}
	
	
	/**
	 * Test if url tokens are valids
	 *
	 * @param string $url
	 * @return boolean
	 */
	public function checkToken($url) {
		
		$urlWithoutToken = $this->tokenizer->removeToken($url);
		$token           = $this->tokenizer->getToken($url);
		return $this->tokenizer->generateToken($urlWithoutToken) == $token;
	}
	
}