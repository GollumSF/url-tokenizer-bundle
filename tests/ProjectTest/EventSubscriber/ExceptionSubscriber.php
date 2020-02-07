<?php
namespace Test\GollumSF\UrlTokenizerBundle\ProjectTest\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class ExceptionSubscriber implements EventSubscriberInterface {

	public static function getSubscribedEvents() {
		return [
			KernelEvents::EXCEPTION => [
				['onKernelException', 256],
			],
		];
	}

	public function onKernelException(ExceptionEvent $event) {
		$e = $event->getThrowable();
		$event->setResponse(new Response(\json_encode([
			'message' => $e->getMessage(),
			'code' => $e->getCode(),
			'file' => $e->getFile(),
			'line' => $e->getLine(),
			'stack' => $e->getTraceAsString(),
			'class' => get_class($e)
		]), ($e instanceof HttpException) ? $e->getStatusCode() : 500));
	}
}
