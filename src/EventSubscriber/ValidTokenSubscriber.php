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

	public static function getSubscribedEvents(): array {
		return [
			KernelEvents::CONTROLLER => [
				['onKernelController', -1],
			],
		];
	}

	public function onKernelController(ControllerEvent $event): void {

		$validToken = $this->getValidTokenAttribute($event);

		if ($validToken) {
			if (!$this->checker->checkTokenMasterRequest($validToken->isFullUrl(), $validToken->getKey())) {
				throw new InvalidTokentHttpException('Token url invalid');
			}
			$lifeTime = $validToken->getLifeTime();
			if ($lifeTime !== null && !$this->checker->checkTokenTimeMasterRequest($lifeTime)) {
				throw new ExpiredTokentHttpException('Token url expired');
			}
		}

	}

	private function getValidTokenAttribute(ControllerEvent $event): ?ValidToken {
		$controller = $event->getController();

		if (is_array($controller)) {
			$className = get_class($controller[0]);
			$methodName = $controller[1];
		} elseif (is_object($controller)) {
			$className = get_class($controller);
			$methodName = '__invoke';
		} else {
			return null;
		}

		$reflectionMethod = new \ReflectionMethod($className, $methodName);
		$attributes = $reflectionMethod->getAttributes(ValidToken::class);
		if (count($attributes) > 0) {
			return $attributes[0]->newInstance();
		}

		$reflectionClass = new \ReflectionClass($className);
		$attributes = $reflectionClass->getAttributes(ValidToken::class);
		if (count($attributes) > 0) {
			return $attributes[0]->newInstance();
		}

		return null;
	}

}
