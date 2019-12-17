<?php

/**
 * Test: Nette\DI\Compiler: generics
 */

declare(strict_types=1);

use Nette\DI;


require __DIR__ . '/../bootstrap.php';


class Generic
{
	public function __construct($arg = null)
	{
		$this->arg = $arg;
	}
}

class GenericFactory
{
	/** @return Generic<T> */
	public static function create(string $t): Generic
	{
		dump(__METHOD__, func_get_args());
		return new Generic(new $t);
	}
}

class Entity
{
}

class Service1
{
	/** @param Generic<Entity> $arg */
	public function __construct(Generic $arg)
	{
		$this->arg = $arg;
	}
}

class Service2
{
	/** @param Generic<stdClass> $arg */
	public function __construct(Generic $arg)
	{
		$this->arg = $arg;
	}
}

$compiler = new DI\Compiler;
$container = createContainer($compiler, '
services:
	- factory: Generic(Entity())
	  type: Generic<Entity>

	- factory: GenericFactory::create("")  # universal factory
	  type: Generic<>

	- Generic  # will be ignored

	- Service1  # Generic<Entity> is passed as argument
	- Service2  # GenericFactory::create("stdClass") is passed as argument
');


$obj = $container->getByType(Service1::class);
dump($obj);

$obj = $container->getByType(Service2::class);
dump($obj);
