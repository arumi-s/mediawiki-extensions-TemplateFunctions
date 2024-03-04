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
		$parser->setFunctionHook('addlink', [TemplateFunctions::class, 'addlink']);
		$parser->setFunctionHook('inoutro', [TemplateFunctions::class, 'inoutro'], Parser::SFH_OBJECT_ARGS);
		$parser->setFunctionHook('htmlencode', [TemplateFunctions::class, 'htmlencode']);
		$parser->setFunctionHook('htmldecode', [TemplateFunctions::class, 'htmldecode']);
		$parser->setFunctionHook('urlencodequery', [TemplateFunctions::class, 'urlencodequery']);
		$parser->setFunctionHook('convertspec', [TemplateFunctions::class, 'convertspec']);
	}

}
