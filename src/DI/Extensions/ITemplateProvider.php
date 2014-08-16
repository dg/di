<?php

/**
 * This file is part of the Nette Framework (http://nette.org)
 * Copyright (c) 2004 David Grudl (http://davidgrudl.com)
 */

namespace Nette\DI\Extensions;

use Kdyby;
use Nette;



/**
 * You can implement this interface on your own extension,
 * so that you can easily configure TemplatesExtension from your own extension.
 *
 * @author Filip Procházka <filip@prochazka.su>
 */
interface ITemplateProvider
{

	/**
	 * The returned array has following structure
	 *
	 * <code>
	 * return [
	 * 		'Nette\Application\UI\Control' => [
	 * 			setup => [],
	 * 			tags => [],
	 * 			inject => TRUE
	 * 		]
	 * ];
	 * </code>
	 *
	 * It's an associative array of class names
	 * and each one can contain the same structure, you would write to config.
	 *
	 * @return array
	 */
	function getDiTemplates();

}
