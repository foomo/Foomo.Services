<?php

namespace Foomo\Services\ProxyGenerator\ActionScript;

class Utils {

	/**
	 * render an indented comment
	 *
	 * @param string $comment multi line comment text
	 * @param integer $indent number of tabs to indent
	 * @return string
	 */
	public static function renderComment($comment, $indent)
	{
		$lines = explode(PHP_EOL, $comment);
		$ret = str_repeat(chr(9), $indent) . '/**' . PHP_EOL;
		foreach($lines as $line) {
			$ret .= str_repeat(chr(9), $indent) .' * ' . $line . PHP_EOL;
		}
		$ret .= str_repeat(chr(9), $indent) . ' */' . PHP_EOL;
		return $ret;
	}
}