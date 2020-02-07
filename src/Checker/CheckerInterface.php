<?php

namespace GollumSF\UrlTokenizerBundle\Checker;

/**
 * CheckerInterface
 *
 * @author Damien Duboeuf <smeagolworms4@gmail.com>
 */
interface CheckerInterface {

	/**
	 * Test if url in master Request token is
	 */
	public function checkTokenMasterRequest(bool $fullUrl = null, ?string $key = NULL): bool;

	/**
	 * Test if url in master Request token time is
	 */
	public function checkTokenTimeMasterRequest(int $lifeTime): bool;

	/**
	 * Test if url in master Request token time is
	 */
	public function checkTokenAndTokenTimeMasterRequest(int $lifeTime, bool $fullUrl = null, ?string $key = NULL): bool;
	
	/**
	 * Test if url token is valid
	 */
	public function checkToken(string $url, bool $fullUrl = null, ?string $key = NULL): bool ;

	/**
	 * Test if url token time is valid
	 */
	public function checkTokenTime(string $url, int $lifeTime): bool;

	/**
	 * Test if url token and token time is valid
	 */
	public function checkTokenAndTokenTime(string $url, int $lifeTime, bool $fullUrl = null, ?string $key = NULL): bool;
	
}