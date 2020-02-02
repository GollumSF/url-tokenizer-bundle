<?php
namespace Test\GollumSF\UrlTokenizerBundle\Traits;

use Doctrine\Common\Annotations\Reader;
use GollumSF\ReflectionPropertyTest\ReflectionPropertyTrait;
use GollumSF\UrlTokenizerBundle\Reflection\ControllerAction;
use GollumSF\UrlTokenizerBundle\Reflection\ControllerActionExtractorInterface;
use GollumSF\UrlTokenizerBundle\Traits\AnnotationControllerReader;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class AnnotationControllerReaderClass {
	use AnnotationControllerReader;
}
class StubController {
	public function action() {
	}
}


class AnnotationControllerReaderTest extends TestCase {
	
	use ReflectionPropertyTrait;

	public function testGetAnnotation() {

		$reader = $this->getMockBuilder(Reader::class)
			->disableOriginalConstructor()
			->getMock()
		;
		$controllerActionExtractor = $this->getMockBuilder(ControllerActionExtractorInterface::class)
			->getMockForAbstractClass()
		;
		$controllerActionString = StubController::class.'::action';
		$actionController = new ControllerAction(
			StubController::class,
			'action'
		);
		$annotation = new \stdClass();

		$controllerActionExtractor
			->method('extractFromString')
			->with($controllerActionString)
			->willReturn($actionController)
		;

		$reader
			->method('getMethodAnnotation')
			->willReturnCallback(function(\ReflectionMethod $method, $annotationClass) use ($annotation) {
				$this->assertEquals($method->getName(), 'action');
				$this->assertEquals($annotationClass, 'AnnotationClass');
				return $annotation;
			})
		;

		$annotationControllerReader = new AnnotationControllerReaderClass();
		$annotationControllerReader->setReader($reader);
		$annotationControllerReader->setControllerActionExtractor($controllerActionExtractor);

		$request = new Request([], [], [ '_controller' => $controllerActionString ]);

		$this->assertEquals(
			$this->reflectionCallMethod($annotationControllerReader, 'getAnnotation', [ $request, 'AnnotationClass' ]),
			$annotation
		);
	}


	public function testGetAnnotationNull() {

		$controllerActionExtractor = $this->getMockBuilder(ControllerActionExtractorInterface::class)
			->getMockForAbstractClass()
		;
		$controllerActionString = StubController::class.'::action';

		$controllerActionExtractor
			->method('extractFromString')
			->with($controllerActionString)
			->willReturn(null)
		;

		$annotationControllerReader = new AnnotationControllerReaderClass();
		$annotationControllerReader->setControllerActionExtractor($controllerActionExtractor);

		$request = new Request([], [], [ '_controller' => $controllerActionString ]);

		$this->assertNull(
			$this->reflectionCallMethod($annotationControllerReader, 'getAnnotation', [ $request, 'AnnotationClass' ])
		);
	}
}