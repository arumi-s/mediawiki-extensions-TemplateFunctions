<?php

namespace MediaWiki\Extension\TemplateFunctions;

use Parser;

class Hooks implements
	\MediaWiki\Hook\ParserFirstCallInitHook
{
	public function __construct()
	{
	}

	/**
	 * Registers our parser functions with a fresh parser.
	 *
	 * @see https://www.mediawiki.org/wiki/Manual:Hooks/ParserFirstCallInit
	 *
	 * @param Parser $parser
	 */
	public function onParserFirstCallInit($parser)
	{
		$parser->setFunctionHook('param', [TemplateFunctions::class, 'param'], Parser::SFH_OBJECT_ARGS);
		$parser->setFunctionHook('getparam', [TemplateFunctions::class, 'getparam']);
		$parser->setFunctionHook('rawparam', [TemplateFunctions::class, 'rawparam'], Parser::SFH_OBJECT_ARGS);
		$parser->setFunctionHook('exeparam', [TemplateFunctions::class, 'exeparam']);
		$parser->setFunctionHook('link', [TemplateFunctions::class, 'link']);
	}

}
