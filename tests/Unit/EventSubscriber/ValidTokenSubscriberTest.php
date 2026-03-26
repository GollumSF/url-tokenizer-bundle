<?php
namespace Test\GollumSF\UrlTokenizerBundle\Unit\EventSubscriber;

use GollumSF\UrlTokenizerBundle\Annotation\ValidToken;
use GollumSF\UrlTokenizerBundle\Checker\CheckerInterface;
use GollumSF\UrlTokenizerBundle\EventSubscriber\ValidTokenSubscriber;
use GollumSF\UrlTokenizerBundle\Exception\ExpiredTokentHttpException;
use GollumSF\UrlTokenizerBundle\Exception\InvalidTokentHttpException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\KernelInterface;

class ValidTokenSubscriberTestController {
	#[ValidToken()]
	public function withAttribute() {}

	#[ValidToken(fullUrl: true)]
	public function withFullUrl() {}

	#[ValidToken(fullUrl: false)]
	public function withNoFullUrl() {}

	#[ValidToken(key: 'CUSTOM_KEY')]
	public function withKey() {}

	#[ValidToken(lifeTime: 4242)]
	public function withLifeTime() {}

	public function withoutAttribute() {}
}

#[ValidToken(lifeTime: 1234)]
class ValidTokenSubscriberTestClassLevelController {
	public function someAction() {}
}

#[ValidToken(key: 'INVOKE_KEY')]
class ValidTokenSubscriberTestInvokableController {
	public function __invoke() {}
}

class ValidTokenSubscriberTest extends TestCase {

	public function testGetSubscribedEvents() {
		$this->assertEquals(ValidTokenSubscriber::getSubscribedEvents(), [
			KernelEvents::CONTROLLER => [
				['onKernelController', -1],
			],
		]);
	}

	public static function provideOnKernelController() {
		return [
			[ 'withAttribute', null, null, null, false ],
			[ 'withFullUrl', true, null, null, false ],
			[ 'withNoFullUrl', false, null, null, false ],
			[ 'withKey', null, 'CUSTOM_KEY', null, false ],
			[ 'withLifeTime', null, null, 4242, true ],
		];
	}

	#[DataProvider('provideOnKernelController')]
	public function testOnKernelController($method, $isFullUrl, $key, $lifeTime, $callLifeTime) {
		$kernel  = $this->createMock(KernelInterface::class);
		$request = $this->getMockBuilder(Request::class)->disableOriginalConstructor()->getMock();
		$checker = $this->createMock(CheckerInterface::class);

		$controller = new ValidTokenSubscriberTestController();
		$event = new ControllerEvent($kernel, [$controller, $method], $request, HttpKernelInterface::MAIN_REQUEST);

		$checker
			->expects($this->once())
			->method('checkTokenMasterRequest')
			->with($isFullUrl, $key)
			->willReturn(true)
		;

		if ($callLifeTime) {
			$checker
				->expects($this->once())
				->method('checkTokenTimeMasterRequest')
				->with($lifeTime)
				->willReturn(true)
			;
		} else {
			$checker
				->expects($this->never())
				->method('checkTokenTimeMasterRequest')
			;
		}

		$validTokenSubscriber = new ValidTokenSubscriber($checker);

		$validTokenSubscriber->onKernelController($event);
	}

	#[DataProvider('provideOnKernelController')]
	public function testOnKernelControllerKoToken($method, $isFullUrl, $key) {
		$kernel  = $this->createMock(KernelInterface::class);
		$request = $this->getMockBuilder(Request::class)->disableOriginalConstructor()->getMock();
		$checker = $this->createMock(CheckerInterface::class);

		$controller = new ValidTokenSubscriberTestController();
		$event = new ControllerEvent($kernel, [$controller, $method], $request, HttpKernelInterface::MAIN_REQUEST);

		$checker
			->expects($this->once())
			->method('checkTokenMasterRequest')
			->with($isFullUrl, $key)
			->willReturn(false)
		;

		$checker
			->expects($this->never())
			->method('checkTokenTimeMasterRequest')
		;

		$validTokenSubscriber = new ValidTokenSubscriber($checker);

		$this->expectException(InvalidTokentHttpException::class);

		$validTokenSubscriber->onKernelController($event);
	}

	public function testOnKernelControllerKoTokenTime() {
		$kernel  = $this->createMock(KernelInterface::class);
		$request = $this->getMockBuilder(Request::class)->disableOriginalConstructor()->getMock();
		$checker = $this->createMock(CheckerInterface::class);

		$controller = new ValidTokenSubscriberTestController();
		$event = new ControllerEvent($kernel, [$controller, 'withLifeTime'], $request, HttpKernelInterface::MAIN_REQUEST);

		$checker
			->expects($this->once())
			->method('checkTokenMasterRequest')
			->with(null, null)
			->willReturn(true)
		;
		$checker
			->expects($this->once())
			->method('checkTokenTimeMasterRequest')
			->with(4242)
			->willReturn(false)
		;

		$validTokenSubscriber = new ValidTokenSubscriber($checker);

		$this->expectException(ExpiredTokentHttpException::class);

		$validTokenSubscriber->onKernelController($event);
	}

	public function testOnKernelControllerClassLevelAttribute() {
		$kernel  = $this->createMock(KernelInterface::class);
		$request = $this->getMockBuilder(Request::class)->disableOriginalConstructor()->getMock();
		$checker = $this->createMock(CheckerInterface::class);

		$controller = new ValidTokenSubscriberTestClassLevelController();
		$event = new ControllerEvent($kernel, [$controller, 'someAction'], $request, HttpKernelInterface::MAIN_REQUEST);

		$checker
			->expects($this->once())
			->method('checkTokenMasterRequest')
			->with(null, null)
			->willReturn(true)
		;
		$checker
			->expects($this->once())
			->method('checkTokenTimeMasterRequest')
			->with(1234)
			->willReturn(true)
		;

		$validTokenSubscriber = new ValidTokenSubscriber($checker);
		$validTokenSubscriber->onKernelController($event);
	}

	public function testOnKernelControllerInvokable() {
		$kernel  = $this->createMock(KernelInterface::class);
		$request = $this->getMockBuilder(Request::class)->disableOriginalConstructor()->getMock();
		$checker = $this->createMock(CheckerInterface::class);

		$controller = new ValidTokenSubscriberTestInvokableController();
		$event = new ControllerEvent($kernel, $controller, $request, HttpKernelInterface::MAIN_REQUEST);

		$checker
			->expects($this->once())
			->method('checkTokenMasterRequest')
			->with(null, 'INVOKE_KEY')
			->willReturn(true)
		;

		$validTokenSubscriber = new ValidTokenSubscriber($checker);
		$validTokenSubscriber->onKernelController($event);
	}

	public function testOnKernelControllerStringCallable() {
		$kernel  = $this->createMock(KernelInterface::class);
		$request = $this->getMockBuilder(Request::class)->disableOriginalConstructor()->getMock();
		$checker = $this->createMock(CheckerInterface::class);

		$event = new ControllerEvent($kernel, 'strlen', $request, HttpKernelInterface::MAIN_REQUEST);

		$checker
			->expects($this->never())
			->method('checkTokenMasterRequest')
		;

		$validTokenSubscriber = new ValidTokenSubscriber($checker);
		$validTokenSubscriber->onKernelController($event);
	}

	public function testOnKernelControllerNoAttribute() {
		$kernel  = $this->createMock(KernelInterface::class);
		$request = $this->getMockBuilder(Request::class)->disableOriginalConstructor()->getMock();
		$checker = $this->createMock(CheckerInterface::class);

		$controller = new ValidTokenSubscriberTestController();
		$event = new ControllerEvent($kernel, [$controller, 'withoutAttribute'], $request, HttpKernelInterface::MAIN_REQUEST);

		$checker
			->expects($this->never())
			->method('checkTokenMasterRequest')
		;
		$checker
			->expects($this->never())
			->method('checkTokenTimeMasterRequest')
		;

		$validTokenSubscriber = new ValidTokenSubscriber($checker);
		$validTokenSubscriber->onKernelController($event);
	}

}
