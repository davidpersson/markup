<?php
/**
 * markup
 *
 * Copyright (c) 2012 David Persson
 *
 * Distributed under the terms of the MIT License.
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright  2011-2013 David Persson <nperson@gmx.de>
 * @license    http://opensource.org/licenses/mit-license The MIT License
 * @link       http://github.com/davidpersson/markdown
 */

namespace markup;

require_once dirname(__DIR__) . '/lib/markdown_extra.php';
require_once dirname(__DIR__) . '/lib/markdown_extra_extended.php';
require_once dirname(__DIR__) . '/lib/smartypants.php';

class Markdown {

	/**
	 * Parser steps to pass content through.
	 *
	 * @var array
	 */
	protected static $_pass = [
		'normalize',
		'easy',
		'gfm',
		'markdownExtraExtended',
		'smartyPants'
	];

	/**
	 * Holds instances of already loaded parsers.
	 *
	 * @var array
	 */
	protected static $_loaded = [];

	/**
	 * Allows to configure the steps content is run through.
	 *
	 * @param array $config An array of steps.
	 * @return void
	 */
	public static function config(array $config = []) {
		return static::$_pass = $config;
	}

	/**
	 * Parse markdown to HTML.
	 *
	 * @param string $content The markdown content to parse.
	 * @param array $config An array of steps.
	 * @return string HTML
	 */
	public static function parse($content, array $pass = []) {
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

	/* Pre-Parsers */

	/**
	 * Normalizes line endings to \n.
	 *
	 * @param string $content The markdown content to parse.
	 * @return string The markdown content with normalized line endings.
	 */
	public static function normalize($content) {
		return str_replace("\r\n", "\n", $content);
	}

	/**
	 * Simplifies certain markdown features. Currently strips away assumed
	 * unintended indentation. By applying this step you loose any pre/code
	 * block formatting.
	 *
	 * @param string $content The markdown content to parse.
	 * @return string Markdown content.
	 */
	public static function easy($content) {
		$content = explode("\n", $content);

		foreach ($content as &$line) {
			$line = ltrim($line, " \t");
		}
		return implode("\n", $content);
	}

	/**
	 * Applies a subset of GitHub flavored markdown.
	 *
	 * @link http://github.github.com/github-flavored-markdown/
	 * @param string $content The markdown content to parse.
	 * @return string Markdown content.
	 */
	public static function gfm($content) {
		return preg_replace('/(?<!\n)\n(?![\n\*\#\-])/', "  \n", $content);
	}

	/* Parsers */

	/**
	 * Parses markdown using the markdownExtra parser
	 * and transforms it into HTML.
	 *
	 * @param string $content The markdown content to parse.
	 * @return string The content tranformed into HTML.
	 */
	public static function markdownExtra($content) {
		if (!isset(static::$_loaded[__FUNCTION__])) {
			static::$_loaded[__FUNCTION__] = new \MarkdownExtra_Parser();
		}
		return static::$_loaded[__FUNCTION__]->transform($content);
	}

	/**
	 * Parses markdown using the markdownExtraExtended
	 * parser and transforms it into HTML.
	 *
	 * @param string $content The markdown content to parse.
	 * @return string The content tranformed into HTML.
	 */
	public static function markdownExtraExtended($content) {
		if (!isset(static::$_loaded[__FUNCTION__])) {
			static::$_loaded[__FUNCTION__] = new \MarkdownExtraExtended_Parser();
		}
		return static::$_loaded[__FUNCTION__]->transform($content);
	}

	/* Post-Parser */

	/**
	 * Parses markdown (or post-process HTML) using the smartyPants parser
	 * and transforms it into HTML.
	 *
	 * @param string $content The markdown or HTML content to parse.
	 * @return string The content tranformed into HTML.
	 */
	public static function smartyPants($content) {
		if (!isset(static::$_loaded[__FUNCTION__])) {
			static::$_loaded[__FUNCTION__] = new \SmartyPants_Parser();
		}
		return static::$_loaded[__FUNCTION__]->transform($content);
	}
}

?>