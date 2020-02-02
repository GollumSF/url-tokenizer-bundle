<?php

namespace GollumSF\UrlTokenizerBundle\Checker;

use GollumSF\UrlTokenizerBundle\Tokenizer\TokenizerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

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
		return $this->requestStack->getMasterRequest();
	}

	/**
	 * Test if url in master Request tokens are valids
	 */
	public function checkMasterRequest(bool $fullmatch = false, ?string $key = NULL): bool {
		return $this->checkToken($this->getMasterRequest()->getUri(), $fullmatch, $key);
	}

	/**
	 * Test if url tokens are valids
	 */
	public function checkToken(string $url, bool $fullmatch = false, ?string $key = NULL): bool {
		$urlWithoutToken = $this->tokenizer->removeToken($url);
		$token           = $this->tokenizer->getToken($url);
		return $this->tokenizer->generateToken($urlWithoutToken, $fullmatch, $key) == $token;
	}

}