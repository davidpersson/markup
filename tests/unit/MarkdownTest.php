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

namespace markup\tests\unit;

use markup\Markdown;

class MarkdownTest extends \PHPUnit_Framework_TestCase {

	public function testGfm() {
		// Not touch single underscores inside words
		$data = "foo_bar";
		$result = Markdown::gfm($data);
		$expected = "foo_bar";
		$this->assertEquals($expected, $result);

		// Not touch underscores in code blocks
		$data = "    foo_bar_baz";
		$result = Markdown::gfm($data);
		$expected = "    foo_bar_baz";
		$this->assertEquals($expected, $result);

		// Not treat pre blocks with pre-text differently
		/*
		$data = <<<MARKDOWN


<pre>
this is `a\\_test` and this\\_too
</pre>
MARKDOWN;
		$a = Markdown::gfm($data);
		$b = <<<FLAVORED
hmm<pre>
this is `a\\_test` and this\\_too
</pre>
FLAVORED;
		$this->assertEquals(substr($a, 2), substr($b, 3));
		*/

		// Escape two or more underscroes inside words
		/*
		$data = "foo_bar_baz";
		$result = Markdown::gfm($data);
		$expected = "foo\_bar\_baz";
		$this->assertEquals($expected, $result);
		*/

		// Turn newlines into br tags in simple cases
		$data = "foo\nbar";
		$result = Markdown::gfm($data);
		$expected = "foo  \nbar";
		$this->assertEquals($expected, $result);

		// Convert newlines in all groups
		$data = "apple\npear\norange\n\nruby\npython\nerlang";
		$result = Markdown::gfm($data);
		$expected = "apple  \npear  \norange\n\nruby  \npython  \nerlang";
		$expected = str_replace(' ', '_', $expected);
		$result = str_replace(' ', '_', $result);
		$this->assertEquals($expected, $result);

		// Convert newlines in even long groups
		$data = "apple\npear\norange\nbanana\n\nruby\npython\nerlang";
		$result = Markdown::gfm($data);
		$expected = "apple  \npear  \norange  \nbanana\n\nruby  \npython  \nerlang";
		$this->assertEquals($expected, $result);

		// Not convert newlines in lists
		$data = "# foo\n# bar";
		$result = Markdown::gfm($data);
		$expected = "# foo\n# bar";
		$this->assertEquals($expected, $result);

		$data = "* foo\n* bar";
		$result = Markdown::gfm($data);
		$expected = "* foo\n* bar";
		$this->assertEquals($expected, $result);

		// Do not cut off "n"s
		$data = "this is fun";
		$result = Markdown::gfm($data);
		$expected = "this is fun";
	}

	public function testParseWithDefaultPasses() {
		$data = <<<MARKDOWN
## This is a test

some lines that contain
a break or

two more lines which behave differently

* and
* a nice
* list
MARKDOWN;
		$result = Markdown::parse($data);
		$expected = <<<HTML
<h2>This is a test</h2>

<p>some lines that contain<br />
a break or</p>

<p>two more lines which behave differently</p>

<ul>
<li>and</li>
<li>a nice</li>
<li>list</li>
</ul>

HTML;
		$this->assertEquals($expected, $result);
	}

}

?>