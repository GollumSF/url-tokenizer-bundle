<?php
namespace Test\GollumSF\UrlTokenizerBundle\Unit\EventSubscriber;

use GollumSF\UrlTokenizerBundle\Annotation\ValidToken;
use GollumSF\UrlTokenizerBundle\Checker\CheckerInterface;
use GollumSF\UrlTokenizerBundle\EventSubscriber\ValidTokenSubscriber;
use GollumSF\UrlTokenizerBundle\Exception\ExpiredTokentHttpException;
use GollumSF\UrlTokenizerBundle\Exception\InvalidTokentHttpException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\KernelInterface;

class ValidTokenSubscriberTest extends TestCase {
	
	public function testGetSubscribedEvents() {
		$this->assertEquals(ValidTokenSubscriber::getSubscribedEvents(), [
			KernelEvents::CONTROLLER => [
				['onKernelController', -1],
			],
		]);
	}
	
	public function provideOnKernelController() {
		return [
			[ new ValidToken([]), null, null, null, false ],
			[ new ValidToken([ 'fullUrl' => true ]), true, null, null, false ],
			[ new ValidToken([ 'fullUrl' => false ]), false, null, null, false ],
			[ new ValidToken([ 'key' => 'CUSTOM_KEY' ]), null, 'CUSTOM_KEY', null, false ],
			[ new ValidToken([ 'lifeTime' => 4242 ]), null, null, 4242, true ],
		];
	}

	/**
	 * @dataProvider provideOnKernelController
	 */
	public function testOnKernelController($annotation, $isFullUrl, $key, $lifeTime, $callLifeTime) {
		$kernel     = $this->getMockForAbstractClass(KernelInterface::class);
		$request    = $this->getMockBuilder(Request::class)->disableOriginalConstructor()->getMock();
		$checker    = $this->getMockForAbstractClass(CheckerInterface::class);
		$attributes = $this->getMockBuilder(ParameterBag::class)->disableOriginalConstructor()->getMock();

		$request->attributes = $attributes;

		$event = new ControllerEvent($kernel, function(){}, $request, HttpKernelInterface::MASTER_REQUEST);

		$attributes
			->expects($this->once())
			->method('get')
			->with('_'.ValidToken::ALIAS_NAME)
			->willReturn($annotation)
		;

		$checker
			->expects($this->at(0))
			->method('checkTokenMasterRequest')
			->with($isFullUrl, $key)
			->willReturn(true)
		;

		if ($callLifeTime) {
			$checker
				->expects($this->at(1))
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

	/**
	 * @dataProvider provideOnKernelController
	 */
	public function testOnKernelControllerKoToken($annotation, $isFullUrl, $key) {
		$kernel     = $this->getMockForAbstractClass(KernelInterface::class);
		$request    = $this->getMockBuilder(Request::class)->disableOriginalConstructor()->getMock();
		$checker    = $this->getMockForAbstractClass(CheckerInterface::class);
		$attributes = $this->getMockBuilder(ParameterBag::class)->disableOriginalConstructor()->getMock();

		$request->attributes = $attributes;

		$event = new ControllerEvent($kernel, function(){}, $request, HttpKernelInterface::MASTER_REQUEST);

		$attributes
			->expects($this->once())
			->method('get')
			->with('_'.ValidToken::ALIAS_NAME)
			->willReturn($annotation)
		;

		$checker
			->expects($this->at(0))
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
		$kernel     = $this->getMockForAbstractClass(KernelInterface::class);
		$request    = $this->getMockBuilder(Request::class)->disableOriginalConstructor()->getMock();
		$checker    = $this->getMockForAbstractClass(CheckerInterface::class);
		$attributes = $this->getMockBuilder(ParameterBag::class)->disableOriginalConstructor()->getMock();

		$request->attributes = $attributes;

		$event = new ControllerEvent($kernel, function(){}, $request, HttpKernelInterface::MASTER_REQUEST);

		$attributes
			->expects($this->once())
			->method('get')
			->with('_'.ValidToken::ALIAS_NAME)
			->willReturn(new ValidToken([ 'lifeTime' => 4242 ]))
		;

		$checker
			->expects($this->at(0))
			->method('checkTokenMasterRequest')
			->with(null, null)
			->willReturn(true)
		;
		$checker
			->expects($this->at(1))
			->method('checkTokenTimeMasterRequest')
			->with(4242)
			->willReturn(false)
		;

		$validTokenSubscriber = new ValidTokenSubscriber($checker);

		$this->expectException(ExpiredTokentHttpException::class);

		$validTokenSubscriber->onKernelController($event);
	}
	
}