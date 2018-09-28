<?php
namespace GollumSF\UrlTokenizerBundle\Tokenizer;

/**
 * TokenizerInterface
 *
 * @author Damien Duboeuf <smeagolworms4@gmail.com>
 */
interface TokenizerInterface {
	
	/**
	 * Generate tokens from an URL
	 *
	 * @param string $url
	 * @param boolean $fullmatch (optional)
	 * @param string $key (optional)
	 * @return mixed string
	 */
	public function generateToken($url, $fullmatch = false, $key = NULL);
	
	/**
	 * Generate an URL with its token from an URL without one
	 *
	 * @param string $url
	 * @param boolean $fullmatch (optional)
	 * @return string
	 */
	public function generateUrl($url, $fullmatch = false, $key = NULL);
	
	/**
	 * Remove Tokens from URL
	 *
	 * @param string $url
	 * @return mixed string|NULL
	 */
	public function removeToken($url);
	l
	/**
	 * Retrieve token from an url
	 * @param string $url
	 *
	 * @return mixed string|NULL
	 */
	public function getToken($url);
}