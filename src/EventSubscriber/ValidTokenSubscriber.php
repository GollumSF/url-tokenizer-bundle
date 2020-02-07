<?php
namespace GollumSF\UrlTokenizerBundle\EventSubscriber;

use GollumSF\UrlTokenizerBundle\Annotation\ValidToken;
use GollumSF\UrlTokenizerBundle\Checker\CheckerInterface;
use GollumSF\UrlTokenizerBundle\Exception\ExpiredTokentHttpException;
use GollumSF\UrlTokenizerBundle\Exception\InvalidTokentHttpException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ValidTokenSubscriber implements EventSubscriberInterface {
	
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
		$validToken = $request->attributes->get('_'.ValidToken::ALIAS_NAME);
		
		if ($validToken) {
			if (!$this->checker->checkTokenMasterRequest($validToken->isFullUrl(), $validToken->getKey())) {
				throw new InvalidTokentHttpException('Token url invalid');
			}
			$lifeTime = $validToken->getLifeTime();
			if ($lifeTime && !$this->checker->checkTokenTimeMasterRequest($lifeTime)) {
				throw new ExpiredTokentHttpException('Token url expired');
			}
		}
		
	}
	
}