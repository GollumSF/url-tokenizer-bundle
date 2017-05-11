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
	 * @return boolean
	 */
	public function checkToken($url);
	
}