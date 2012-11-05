<?php

namespace markup;

class Markup {

    public static $settings = array(
		'pass' => array('normalize', 'easy', 'gfm', 'markdown', 'smartyPants')
	);

    protected static $_loaded = array();

	public function parse($content, $pass = array()) {
		if (!$pass) {
			$pass = static::$settings['pass'];
		}
		foreach ($pass as $parser) {
			$content = static::{$parser}($content);
		}
		return $content;
    }

	public function normalize($content) {
		return str_replace("\r\n", "\n", $content);
	}

	// No pre and code blocks through indentation.
	public function easy($content) {
		$content = explode("\n", $content);

		foreach ($content as &$line) {
			$line = ltrim($line, " \t");
		}
		return implode("\n", $content);
	}

	// markdown -> markdown
	// @link http://github.github.com/github-flavored-markdown/
	// @link http://github.com/mojombo/github-flavored-markdown
	public function gfm($content) {
		return preg_replace('/(?<!\n)\n(?![\n\*\#\-])/', "  \n", $content);
	}

	// markdown -> html
	public function markdown($content) {
		if (!isset(static::$_loaded['markdown'])) {
			require_once 'markdown.php';
			statci::$_loaded['markdown'] = new MarkdownExtra_Parser();
		}
		return static::$_loaded['markdown']->transform($content);
	}

	// markdown/html -> html
	public function smartyPants($content) {
		if (!isset(static::$_loaded['smartyPants'])) {
			require_once 'smartypants.php';
			static::$_loaded['smartyPants'] = new SmartyPants_Parser();
		}
		return static::$_loaded['smartyPants']->transform($content);
	}
}

?>