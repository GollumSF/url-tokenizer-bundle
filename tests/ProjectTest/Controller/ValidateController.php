<?php
namespace Test\GollumSF\UrlTokenizerBundle\ProjectTest\Controller;

use GollumSF\UrlTokenizerBundle\Annotation\ValidToken;
use GollumSF\UrlTokenizerBundle\Tokenizer\TokenizerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ValidateController extends AbstractController {

	/**
	 * @Route("/validate", name="validate")
	 * @ValidToken()
	 */
	public function validate(TokenizerInterface $tokenizer) {
		return new Response('good');
	}

	/**
	 * @Route("/validate-fullurl", name="validate_fullurl")
	 * @ValidToken(fullUrl=true)
	 */
	public function validateFullurl(TokenizerInterface $tokenizer) {
		return new Response('good');
	}

	/**
	 * @Route("/validate-no-fullurl", name="validate_no_fullurl")
	 * @ValidToken(fullUrl=false)
	 */
	public function validateNoFullurl(TokenizerInterface $tokenizer) {
		return new Response('good');
	}

	/**
	 * @Route("/validate-key", name="validate_key")
	 * @ValidToken(key="CUSTOM_KEY")
	 */
	public function validateKey(TokenizerInterface $tokenizer) {
		return new Response('good');
	}

	/**
	 * @Route("/validate-lifetime", name="validate_lifetime")
	 * @ValidToken(lifeTime=60)
	 */
	public function validateLifetime(TokenizerInterface $tokenizer) {
		return new Response('good');
	}

	/**
	 * @Route("/validate-lifetime-ko", name="validate_lifetime_ko")
	 * @ValidToken(lifeTime=0)
	 */
	public function validateLifetimeKo(TokenizerInterface $tokenizer) {
		return new Response('good');
	}
	
}