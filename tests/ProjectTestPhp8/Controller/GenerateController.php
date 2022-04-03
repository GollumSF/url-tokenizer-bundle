<?php
namespace Test\GollumSF\UrlTokenizerBundle\ProjectTestPhp8\Controller;

use GollumSF\UrlTokenizerBundle\Tokenizer\TokenizerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

class GenerateController extends AbstractController {

	#[Route("/generate")]
	public function generate(TokenizerInterface $tokenizer) {
		return new Response($tokenizer->generateUrl(
			$this->generateUrl('validate', [ 'param' => 'value' ], RouterInterface::ABSOLUTE_URL)
		));
	}
	
	#[Route("/generate-fullurl")]
	public function generateFullurl(TokenizerInterface $tokenizer) {
		return new Response($tokenizer->generateUrl(
			$this->generateUrl('validate_fullurl', [ 'param' => 'value' ], RouterInterface::ABSOLUTE_URL), true
		));
	}

	#[Route("/generate-no-fullurl")]
	public function getUrlNoFullurl(TokenizerInterface $tokenizer) {
		return new Response($tokenizer->generateUrl(
			$this->generateUrl('validate_no_fullurl', [ 'param' => 'value' ], RouterInterface::ABSOLUTE_URL), false
		));
	}

	#[Route("/generate-key")]
	public function getUrlKey(TokenizerInterface $tokenizer) {
		return new Response($tokenizer->generateUrl(
			$this->generateUrl('validate_key', [ 'param' => 'value' ], RouterInterface::ABSOLUTE_URL), null, 'CUSTOM_KEY'
		));
	}

	#[Route("/generate-lifetime")]
	public function getUrlLifetime(TokenizerInterface $tokenizer) {
		return new Response($tokenizer->generateUrl(
			$this->generateUrl('validate_lifetime', [ 'param' => 'value' ], RouterInterface::ABSOLUTE_URL)
		));
	}

	#[Route("/generate-lifetime-ko")]
	public function getUrlLifetimeKo(TokenizerInterface $tokenizer) {
		return new Response($tokenizer->generateUrl(
			$this->generateUrl('validate_lifetime_ko', [ 'param' => 'value' ], RouterInterface::ABSOLUTE_URL)
		));
	}
	
}
