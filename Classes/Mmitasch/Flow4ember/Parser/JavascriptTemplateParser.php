<?php
namespace Mmitasch\Flow4ember\Parser;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Mmitasch.Flow4ember".   *
 *                                                                        */

/**
 * Template parser building up an object syntax tree
 *
 */
class JavascriptTemplateParser extends \TYPO3\Fluid\Core\Parser\TemplateParser {

	

	/**
	 * Pattern which splits the shorthand syntax into different tokens. The
	 * "shorthand syntax" is everything like #{...}#
	 * 
	 * Uses # as it is the only symbol not used by Javascript
	 * https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Symbols
	 */
	public static $SPLIT_PATTERN_SHORTHANDSYNTAX = '/
		(
			\#{                                # Start of shorthand syntax
				(?:                          # Shorthand syntax is either composed of...
					[a-zA-Z0-9\->_:,.()]     # Various characters
					|"(?:\\\"|[^"])*"        # Double-quoted strings
					|\'(?:\\\\\'|[^\'])*\'   # Single-quoted strings
					|(?R)                    # Other shorthand syntaxes inside, albeit not in a quoted string
					|\s+                     # Spaces
				)+
			}\#                                # End of shorthand syntax
		)/x';
	
		/**
	 * Pattern which detects the object accessor syntax:
	 * {object.some.value}, additionally it detects ViewHelpers like
	 * {f:for(param1:bla)} and chaining like
	 * {object.some.value->f:bla.blubb()->f:bla.blubb2()}
	 *
	 * THIS IS ALMOST THE SAME AS IN $SCAN_PATTERN_SHORTHANDSYNTAX_ARRAYS
	 *
	 */
	public static $SCAN_PATTERN_SHORTHANDSYNTAX_OBJECTACCESSORS = '/
		^\#{                                                      # Start of shorthand syntax
			                                                # A shorthand syntax is either...
			(?P<Object>[a-zA-Z0-9\-_.]*)                                     # ... an object accessor
			\s*(?P<Delimiter>(?:->)?)\s*

			(?P<ViewHelper>                                 # ... a ViewHelper
				[a-zA-Z0-9]+                                # Namespace prefix of ViewHelper (as in $SCAN_PATTERN_TEMPLATE_VIEWHELPERTAG)
				:
				[a-zA-Z0-9\\.]+                             # Method Identifier (as in $SCAN_PATTERN_TEMPLATE_VIEWHELPERTAG)
				\(                                          # Opening parameter brackets of ViewHelper
					(?P<ViewHelperArguments>                # Start submatch for ViewHelper arguments. This is taken from $SCAN_PATTERN_SHORTHANDSYNTAX_ARRAYS
						(?:
							\s*[a-zA-Z0-9\-_]+                  # The keys of the array
							\s*:\s*                             # Key|Value delimiter :
							(?:                                 # Possible value options:
								"(?:\\\"|[^"])*"                # Double qouoted string
								|\'(?:\\\\\'|[^\'])*\'          # Single quoted string
								|[a-zA-Z0-9\-_.]+               # variable identifiers
								|{(?P>ViewHelperArguments)}     # Another sub-array
							)                                   # END possible value options
							\s*,?                               # There might be a , to seperate different parts of the array
						)*                                  # The above cycle is repeated for all array elements
					)                                       # End ViewHelper Arguments submatch
				\)                                          # Closing parameter brackets of ViewHelper
			)?
			(?P<AdditionalViewHelpers>                      # There can be more than one ViewHelper chained, by adding more -> and the ViewHelper (recursively)
				(?:
					\s*->\s*
					(?P>ViewHelper)
				)*
			)
		}\#$/x';
	
	
	/**
	 * Pattern which detects the array/object syntax like in JavaScript, so it
	 * detects strings like:
	 * {object: value, object2: {nested: array}, object3: "Some string"}
	 *
	 * THIS IS ALMOST THE SAME AS IN SCAN_PATTERN_SHORTHANDSYNTAX_OBJECTACCESSORS
	 *
	 */
	public static $SCAN_PATTERN_SHORTHANDSYNTAX_ARRAYS = '/^
		(?P<Recursion>                                  # Start the recursive part of the regular expression - describing the array syntax
			\#{                                           # Each array needs to start with {
				(?P<Array>                              # Start submatch
					(?:
						\s*[a-zA-Z0-9\-_]+              # The keys of the array
						\s*:\s*                         # Key|Value delimiter :
						(?:                             # Possible value options:
							"(?:\\\"|[^"])*"            # Double qouoted string
							|\'(?:\\\\\'|[^\'])*\'      # Single quoted string
							|[a-zA-Z0-9\-_.]+           # variable identifiers
							|(?P>Recursion)             # Another sub-array
						)                               # END possible value options
						\s*,?                           # There might be a , to seperate different parts of the array
					)*                                  # The above cycle is repeated for all array elements
				)                                       # End array submatch
			}\#                                           # Each array ends with }
		)$/x';
	
	
	/**
	 * Handler for everything which is not a ViewHelperNode.
	 *
	 * This includes Text, array syntax, and object accessor syntax.
	 *
	 * @param \TYPO3\Fluid\Core\Parser\ParsingState $state Current parsing state
	 * @param string $text Text to process
	 * @param integer $context one of the CONTEXT_* constants, defining whether we are inside or outside of ViewHelper arguments currently.
	 * @return void
	 */
	protected function textAndShorthandSyntaxHandler(\TYPO3\Fluid\Core\Parser\ParsingState $state, $text, $context) {
		$sections = preg_split($this->prepareTemplateRegularExpression(self::$SPLIT_PATTERN_SHORTHANDSYNTAX), $text, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
		foreach ($sections as $section) {
			$matchedVariables = array();
			if (preg_match(self::$SCAN_PATTERN_SHORTHANDSYNTAX_OBJECTACCESSORS, $section, $matchedVariables) > 0) {
				$this->objectAccessorHandler($state, $matchedVariables['Object'], $matchedVariables['Delimiter'], (isset($matchedVariables['ViewHelper'])?$matchedVariables['ViewHelper']:''), (isset($matchedVariables['AdditionalViewHelpers'])?$matchedVariables['AdditionalViewHelpers']:''));
			} elseif ($context === self::CONTEXT_INSIDE_VIEWHELPER_ARGUMENTS && preg_match(self::$SCAN_PATTERN_SHORTHANDSYNTAX_ARRAYS, $section, $matchedVariables) > 0) {
					// We only match arrays if we are INSIDE viewhelper arguments
				$this->arrayHandler($state, $matchedVariables['Array']);
			} else {
				$this->textHandler($state, $section);
			}
		}
	}

}
?>