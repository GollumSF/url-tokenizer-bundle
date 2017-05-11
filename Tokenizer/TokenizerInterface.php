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
	 * @param string $key (optional)
	 * @return mixed string
	 */
	public function generateToken($url, $key = NULL);
	
	/**
	 * Generate an URL with its token from an URL without one
	 *
	 * @param string $url
	 * @return string
	 */
	public function generateUrl($url);
	
	/**
	 * Remove Tokens from URL
	 *
	 * @param string $url
	 * @return mixed string|NULL
	 */
	public function removeToken($url);
	
	/**
	 * Retrieve token from an url
	 * @param string $url
	 *
	 * @return mixed string|NULL
	 */
	public function getToken($url);
}