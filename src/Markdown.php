<?php

namespace markup;

require_once dirname(__DIR__) . '/lib/markdown_extra.php';
require_once dirname(__DIR__) . '/lib/smartypants.php';
require_once dirname(__DIR__) . '/lib/markdown_extra_extended.php';

use MarkdownExtra_Parser;
use SmartyPants_Parser;
use MarkdownExtraExtended_Parser;

class Markup {

    protected static $_pass = array(
		'normalize',
		'easy',
		'gfm',
		// 'markdownExtra',
		'markdownExtraExtended',
		'smartyPants'
	);

    protected static $_loaded = array();

	public static function config(array $config = array()) {
		return static::$_pass = $config;
	}

	public static function parse($content, array $pass = array()) {
		if (!$pass) {
			$pass = static::$_pass;
		}
		foreach ($pass as $parser) {
			if (is_callable($parser)) {
				$content = call_user_func($parser, $content);
			} else {
				$content = static::{$parser}($content);
			}
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
	public static function markdownExtra($content) {
		if (!isset(static::$_loaded[__FUNCTION__])) {
			static::$_loaded[__FUNCTION__] = new MarkdownExtra_Parser();
		}
		return static::$_loaded[__FUNCTION__]->transform($content);
}

	// markdown/html -> html
	public static function smartyPants($content) {
		if (!isset(static::$_loaded[__FUNCTION__])) {
			static::$_loaded[__FUNCTION__] = new SmartyPants_Parser();
		}
		return static::$_loaded[__FUNCTION__]->transform($content);
	}

	// markdown/html -> html
	public static function markdownExtraExtended($content) {
		if (!isset(static::$_loaded[__FUNCTION__])) {
			static::$_loaded[__FUNCTION__] = new MarkdownExtraExtended_Parser();
		}
		return static::$_loaded[__FUNCTION__]->transform($content);
	}
}

?>