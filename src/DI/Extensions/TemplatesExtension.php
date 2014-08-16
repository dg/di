<?php

/**
 * This file is part of the Nette Framework (http://nette.org)
 * Copyright (c) 2004 David Grudl (http://davidgrudl.com)
 */

namespace Nette\DI\Extensions;

use Nette,
	Nette\DI\Statement;


/**
 * Templates for services.
 *
 * @author     David Grudl
 */
class TemplatesExtension extends Nette\DI\CompilerExtension
{
	public $defaults = array(
		'setup' => array(),
		'tags' => array(),
		'inject' => FALSE,
	);

	/** @var Nette\DI\ServiceDefinition[] */
	private $templates = array();


	public function loadConfiguration()
	{
		foreach ($this->getConfig() as $class => $info) {
			$this->validate($info, $this->defaults, $this->prefix($class));
			$this->templates[strtolower($class)] = $info + $this->defaults;
		}
	}


	public function beforeCompile()
	{
		// load additional configuration here, because in loadConfiguration(),
		// there could have been added more extensions that wouldn't have been processed otherwise
		foreach ($this->compiler->getExtensions('Nette\DI\Extensions\ITemplateProvider') as $provider) {
			/** @var ITemplateProvider $provider */
			foreach ($provider->getDiTemplates() as $class => $info) {
				$this->validate($info, $this->defaults, $this->prefix($class));
				$this->templates[strtolower($class)] = $info + $this->defaults;
			}
		}

		foreach ($this->getContainerBuilder()->getDefinitions() as $name => $def) {
			if (!$def->class) {
				continue;
			}

			$setups = array();
			foreach (class_parents($def->class) + class_implements($def->class) + array($def->class) as $class) {
				if (!isset($this->templates[$class = strtolower($class)])) {
					continue;
				}
				$template = $this->templates[$class];

				foreach ($template['setup'] as $setup) {
					$setups[] = $setup instanceof Statement ? $setup : new Statement($setup);
				}

				if (isset($template['inject'])) {
					$template['tags']['inject'] = $template['inject'];
				}

				foreach ($template['tags'] as $tag => $attrs) {
					if (is_int($tag) && is_string($attrs)) {
						list($tag, $attrs) = array($attrs, TRUE);
					}
					if (!isset($def->tags[$tag])) {
						$def->addTag($tag, $attrs);
					}
				}
			}
			$def->setup = array_merge($setups, $def->setup);
		}
	}


	private function validate(array $config, array $expected, $name)
	{
		if ($extra = array_diff_key($config, $expected)) {
			$extra = implode(", $name.", array_keys($extra));
			throw new Nette\InvalidStateException("Unknown option $name.$extra.");
		}
	}

}
