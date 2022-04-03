<?php

namespace GollumSF\UrlTokenizerBundle\Checker;

use GollumSF\UrlTokenizerBundle\Tokenizer\TokenizerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Checker
 *
 * @author Damien Duboeuf <smeagolworms4@gmail.com>
 */
class Checker implements CheckerInterface {

	/** @var TokenizerInterface */
	private $tokenizer;

	/** @var RequestStack */
	private $requestStack;

	/**
	 * Checker constructor.
	 */
	public function __construct(
		TokenizerInterface $tokenizer,
		RequestStack $requestStack
	) {
		$this->tokenizer = $tokenizer;
		$this->requestStack = $requestStack;
	}

	protected function getMasterRequest(): Request {
		return version_compare(Kernel::VERSION, '6.0.0', '<') ? $this->requestStack->getMasterRequest() : $this->requestStack->getMainRequest();
	}

	/**
	 * Test if url in master Request token is
	 */
	public function checkTokenMasterRequest(bool $fullUrl = null, ?string $key = NULL): bool {
		return $this->checkToken($this->getMasterRequest()->getUri(), $fullUrl, $key);
	}

	/**
	 * Test if url in master Request token time is
	 */
	public function checkTokenTimeMasterRequest(int $lifeTime): bool {
		return $this->checkTokenTime($this->getMasterRequest()->getUri(), $lifeTime);
	}

	/**
	 * Test if url in master Request token time is
	 */
	public function checkTokenAndTokenTimeMasterRequest(int $lifeTime, bool $fullUrl = null, ?string $key = NULL): bool {
		return $this->checkTokenAndTokenTime($this->getMasterRequest()->getUri(), $lifeTime, $fullUrl, $key);
	}

	/**
	 * Test if url token is valid
	 */
	public function checkToken(string $url, bool $fullUrl = null, ?string $key = NULL): bool {
		$urlWithoutToken = $this->tokenizer->removeToken($url);
		$token           = $this->tokenizer->getToken($url);
		return $this->tokenizer->generateToken($urlWithoutToken, $fullUrl, $key) === $token;
	}

	public function checkTokenTime(string $url, int $lifeTime): bool {
		$time = $this->tokenizer->getTokenTime($url);
		return time() < $time + $lifeTime;
	}

	public function checkTokenAndTokenTime(string $url, int $lifeTime, bool $fullUrl = null, ?string $key = NULL): bool {
		return $this->checkToken($url, $fullUrl, $key) && $this->checkTokenTime($url, $lifeTime);
	}

}
