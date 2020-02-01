<?php

namespace GollumSF\UrlTokenizerBundle\Checker;

use GollumSF\UrlTokenizerBundle\Tokenizer\Tokenizer;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * CheckerInterface
 *
 * @author Damien Duboeuf <smeagolworms4@gmail.com>
 */
interface CheckerInterface {
	
	/**
	 * Test if url tokens are valids
	 *
	 * @param string $url
	 * @param boolean $fullmatch (optional)
	 * @param string $key (optional)
	 * @return boolean
	 */
	public function checkToken($url, $fullmatch = NULL, $key = NULL);
	
}