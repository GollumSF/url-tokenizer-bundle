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
	 */
	public function generateToken(string $url, bool $fullmatch = false, string $key = NULL): string;
	
	/**
	 * Generate an URL with its token from an URL without one
	 */
	public function generateUrl(string $url, bool $fullmatch = false, ?string $key = NULL): string;
	
	/**
	 * Remove Tokens from URL
	 *
	 * @param string $url
	 * @return mixed string|NULL
	 */
	public function removeToken(string $url): string;
	
	/**
	 * Retrieve token from an url
	 */
	public function getToken(string $url): ?string;
}