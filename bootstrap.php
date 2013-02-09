<?php
/**
 * markup
 *
 * Copyright (c) 2012-2013 David Persson
 *
 * Distributed under the terms of the MIT License.
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright  2011-2013 David Persson <nperson@gmx.de>
 * @license    http://opensource.org/licenses/mit-license The MIT License
 * @link       http://github.com/davidpersson/markdown
 */

spl_autoload_register(function($class) {
	$irregular = array(
		'MarkdownExtra_Parser' => 'lib/markdown_extra',
		'SmartyPants_Parser' => 'lib/smartypants.php',
		'MarkdownExtraExtended_Parser' => 'lib/markdown_extra_extended.php'
	);
	if (isset($irregular[$class])) {
		require __DIR__ . '/' . $irregular[$class];
		return;
	}

	if (strpos($class, 'markup') === false) {
		return;
	}
	$name = str_replace('markup\\', '', $class);
	$name = str_replace('\\', '/', $name);

	$file = __DIR__ . '/src/' . $name . '.php';

	if (file_exists($file)) {
		require_once $file;
	}
});

?>