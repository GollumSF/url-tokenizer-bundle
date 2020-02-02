<?php
namespace GollumSF\UrlTokenizerBundle\EventSubscriber;

use GollumSF\UrlTokenizerBundle\Annotation\ValidToken;
use GollumSF\UrlTokenizerBundle\Checker\CheckerInterface;
use GollumSF\UrlTokenizerBundle\Traits\AnnotationControllerReader;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class ValidTokenSubscriber implements EventSubscriberInterface {
	
	use AnnotationControllerReader;
	
	/** @var CheckerInterface */
	private $checker;
	
	public function __construct(CheckerInterface $checker) {
		$this->checker = $checker;
	}

	public static function getSubscribedEvents() {
		return [
			KernelEvents::CONTROLLER => [
				['onKernelController', 255],
			],
		];
	}
	
	public function onKernelController(ControllerEvent $event) {
		
		$request = $event->getRequest();
		
		/** @var ValidToken $validToken */
		$validToken = $this->getAnnotation($request, ValidToken::class);
		
		if ($validToken) {
			if (!$this->checker->checkMasterRequest($validToken->fullMatch, $validToken->fullMatch)) {
				throw new BadRequestHttpException('Url token invalid');
			}
		}
		
	}
	
}