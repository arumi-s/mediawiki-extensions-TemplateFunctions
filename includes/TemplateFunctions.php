<?php

namespace MediaWiki\Extension\TemplateFunctions;

use Parser;
use Title;
use PPFrame;
use PPTemplateFrame_Hash;
use PPNode;

/**
 * Extra Magic Words handlers
 */
class TemplateFunctions
{
	/**
	 * Checks if any or all arguments in the set are provided when invoking a template and return the argument name,
	 * or calculate how many arguments in the set are provided and return the total.
	 * 
	 * @param Parser $parser
	 * @param PPFrame $frame
	 * @param PPNode[] $args
	 * @return string
	 */
	public static function param(Parser $parser, PPFrame $frame, $args)
	{
		if (!$frame->isTemplate()) {
			return '';
		}

		/** @var PPTemplateFrame_Hash */
		$templateFrame = $frame;

		$mode = isset($args[0]) ? trim($templateFrame->expand($args[0])) : '';

		if (count($args) <= 1 && $mode === '') {
			if (isset($templateFrame->mExtFunctionsLastMatch)) {
				return $templateFrame->mExtFunctionsLastMatch;
			}
			return '';
		}

		if ($mode === '$') {
			array_shift($args);
			$count = 0;
			foreach ($args as $arg) {
				$name = trim($templateFrame->expand($arg));
				if ($name !== '' && (isset($templateFrame->namedArgs[$name]) || isset($templateFrame->numberedArgs[$name]))) {
					++$count;
				}
			}
			return strval($count);
		}

		$matched = $mode !== '&';
		if (!$matched) {
			array_shift($args);
		}
		foreach ($args as $arg) {
			$name = trim($templateFrame->expand($arg));
			if ($name !== '' && $matched === (isset($templateFrame->namedArgs[$name]) || isset($templateFrame->numberedArgs[$name]))) {
				return $templateFrame->mExtFunctionsLastMatch = $matched ? $name : '';
			}
		}
		return $templateFrame->mExtFunctionsLastMatch = $matched ? '' : $name;
	}

	/**
	 * Gets all the available arguments for a specific template.
	 * 
	 * @param Parser $parser
	 * @param string|null $template
	 * @param string $separator
	 * @return string
	 */
	public static function getparam(Parser $parser, $template = null, $separator = '')
	{
		if (self::getTemplateText($parser, $template, $title, $text) === false) {
			return '';
		}

		$args = preg_match_all('/(?:-{)?{{{\\s*([^|{}]+)/u', $text, $args) ? array_unique($args[1]) : [];

		return implode(str_replace('\n', "\n", $parser->getStripState()->unstripNoWiki($separator)), $args);
	}

	/**
	 * Gets the unparsed, raw value of a specific argument within a template.
	 * 
	 * @param Parser $parser
	 * @param PPFrame $frame
	 * @param PPNode[] $args
	 * @return string
	 */
	public static function rawparam(Parser $parser, PPFrame $frame, array $args)
	{
		if (!$frame->isTemplate()) {
			return '';
		}

		/** @var PPTemplateFrame_Hash */
		$templateFrame = $frame;

		$name = isset($args[0]) ? trim($frame->expand($args[0])) : '';
		if ($name === '') {
			return '';
		}

		if (isset($templateFrame->namedArgs[$name])) {
			$text = $templateFrame->namedArgs[$name];
		} else if (isset($templateFrame->numberedArgs[$name])) {
			$text = $templateFrame->numberedArgs[$name];
		} else {
			return '';
		}
		return $templateFrame->expand($text, PPFrame::NO_TEMPLATES | PPFrame::NO_ARGS);
	}

	/**
	 * Parse given wiki text.
	 * 
	 * @param Parser $parser
	 * @param string $text
	 * @return string[]
	 */
	public static function exeparam(Parser $parser, $text)
	{
		return [
			$text,
			'noparse' => false,
		];
	}

	/**
	 * Adds a template link to the current page
	 * 
	 * @param Parser $parser
	 * @param string|null $text
	 * @return string
	 */
	public static function link(Parser $parser, $text = null)
	{
		$title = Title::newFromText($text);
		if ($title === null) {
			return '';
		}

		$parser->getOutput()->addTemplate($title, $parser->getTitle()->getArticleID(), $parser->getRevisionId());
		return '';
	}

	/**
	 * Fetches content of target page if valid and found, otherwise
	 * produces wikitext of a link to the target page.
	 * 
	 * Referenced
	 * @link https://github.com/wikimedia/mediawiki-extensions-LabeledSectionTransclusion/blob/master/includes/LabeledSectionTransclusion.php
	 * 
	 * @param Parser $parser
	 * @param string $page title text of target page
	 * @param Title|null &$title normalized title object
	 * @param string|null &$text wikitext output
	 * @return bool true if returning text, false if target not found
	 * 
	 */
	private static function getTemplateText(Parser $parser, string $page, &$title, &$text)
	{
		$title = Title::newFromText($page);

		if ($title === null) {
			$text = '';
			return true;
		} else {
			list($text, $title) = $parser->fetchTemplateAndTitle($title);
		}

		// if article doesn't exist, return empty string.
		if ($text === false) {
			$text = '';
			return false;
		} else {
			return true;
		}
	}
}
