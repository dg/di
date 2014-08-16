<?php

/**
 * Test: Nette\DI\Compiler: service templates provider.
 */

use Nette\DI,
	Nette\DI\Statement,
	Tester\Assert;


require __DIR__ . '/../bootstrap.php';


interface IListener
{}


class Service extends Nette\Object implements IListener
{
}

class ListenersExtension extends DI\CompilerExtension implements DI\Extensions\ITemplateProvider
{

	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();
		$builder->addDefinition($this->prefix('foo'))
			->setClass('Service');
	}



	public function getDiTemplates()
	{
		return [
			'IListener' => [
				'tags' => ['listener']
			]
		];
	}

}


$compiler = new DI\Compiler;
$compiler->addExtension('templates', new Nette\DI\Extensions\TemplatesExtension);
$compiler->addExtension('listeners', new ListenersExtension());
$container = createContainer($compiler);

$builder = $compiler->getContainerBuilder();

Assert::same(
	array('listener' => TRUE, 'inject' => FALSE),
	$builder->getDefinition('listeners.foo')->tags
);
