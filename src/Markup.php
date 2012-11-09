<?php

namespace markup;

require_once __DIR__ . '/markdown.php';
require_once __DIR__ . '/smartypants.php';

use MarkdownExtra_Parser;
use SmartyPants_Parser;

class Markup {

    public static $settings = array(
		'pass' => array('normalize', 'easy', 'gfm', 'markdown', 'smartyPants')
	);

    protected static $_loaded = array();

	public static function parse($content, $pass = array()) {
		if (!$pass) {
			$pass = static::$settings['pass'];
		}
		foreach ($pass as $parser) {
			$content = static::{$parser}($content);
		}
		return $content;
    }

	public static function normalize($content) {
		return str_replace("\r\n", "\n", $content);
	}

	// No pre and code blocks through indentation.
	public static function easy($content) {
		$content = explode("\n", $content);

		foreach ($content as &$line) {
			$line = ltrim($line, " \t");
		}
		return implode("\n", $content);
	}

	// markdown -> markdown
	// @link http://github.github.com/github-flavored-markdown/
	// @link http://github.com/mojombo/github-flavored-markdown
	public static function gfm($content) {
		return preg_replace('/(?<!\n)\n(?![\n\*\#\-])/', "  \n", $content);
	}

	// markdown -> html
	public static function markdown($content) {
		if (!isset(static::$_loaded['markdown'])) {
			static::$_loaded['markdown'] = new MarkdownExtra_Parser();
		}
		return static::$_loaded['markdown']->transform($content);
	}

	// markdown/html -> html
	public static function smartyPants($content) {
		if (!isset(static::$_loaded['smartyPants'])) {
			static::$_loaded['smartyPants'] = new SmartyPants_Parser();
		}
		return static::$_loaded['smartyPants']->transform($content);
	}
}

?>