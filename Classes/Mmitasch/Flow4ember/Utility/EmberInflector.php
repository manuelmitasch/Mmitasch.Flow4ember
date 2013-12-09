<?php
namespace Mmitasch\Flow4ember\Utility;

// 
// Inflect class original based on ShoInflector from Sho Kuwamoto released under the MIT license.
// Adapted to mirror ember-inflector (https://github.com/stefanpenner/ember-inflector)
// http://kuwamoto.org/2007/12/17/improved-pluralizing-in-php-actionscript-and-ror/
//
// Thanks to http://www.eval.ca/articles/php-pluralize (MIT license)
//           http://dev.rubyonrails.org/browser/trunk/activesupport/lib/active_support/inflections.rb (MIT license)
//           http://www.fortunecity.com/bally/durrus/153/gramch13.html
//           http://www2.gsu.edu/~wwwesl/egw/crump.htm


class EmberInflector
{
    static $plural = array(
        '/(quiz)$/i'               => "$1zes",
		'/^(oxen)$/i'              => "$1",
		'/^(ox)$/i'                => "$1en",
		'/([m|l])ice$/i'           => "$1ice",
		'/([m|l])ouse$/i'          => "$1ice",
		'/(matr|vert|ind)ix|ex$/i' => "$1ices",
		'/(x|ch|ss|sh)$/i'         => "$1es",
		'/([^aeiouy]|qu)y$/i'      => "$1ies",
		'/(hive)$/i'               => "$1s",
		'/(?:([^f])fe|([lr])f)$/i' => "$1$2ves",
		'/sis$/i'                  => "ses",
		'/([ti])a$/i'              => "$1a",
		'/([ti])um$/i'             => "$1a",
		'/(buffal|tomat)o$/i'	   => "$1oes",
        '/(bu)s$/i'                => "$1ses",
		'/(alias|status)$/i'       => "$1es",
		'/(octop|vir)i$/i'	       => "$1i",
		'/(octop|vir)us$/i'        => "$1i",
		'/^(ax|test)is$/i'         => "$1es",
        '/s$/i'                    => "s",
        '/$/'                      => "s"
    );

    static $singular = array(
		'/(database)s$/i'           => "$1",
        '/(quiz)zes$/i'             => "$1",
        '/(matr)ices$/i'            => "$1ix",
		'/(vert|ind)ices$/i'        => "$1ex",
        '/^(ox)en$/i'               => "$1",
        '/(alias|status)(es)$/i'    => "$1",
		'/(octop|vir)(us|i)$/i'     => "$1us",
		'/^(a)x[ie]s/i'             => "$1xis",
		'/(cris|test)(is|es)$/i'    => "$1is",
        '/(shoe)s$/i'               => "$1",
		'/(o)es$/i'                 => "$1",
		'/(bus)es$/i'               => "$1",
        '/([m|l])ice$/i'            => "$1ouse",
        '/(x|ch|ss|sh)es$/i'        => "$1",
		'/(m)ovies$/i'              => "$1ovie",
		'/(s)eries$/i'              => "$1eries",
		'/([^aeiouy]|qu)ies$/i'     => "$1y",
		'/([lr])ves$/i'             => "$1f",
		'/(tive)s$/i'               => "$1",
		'/(hive)s$/i'               => "$1",
        '/([^f])ves$/i'             => "$1fe",
		'/(^analy)(sis|ses)$/i'     => "$1sis",
		'/((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he)(sis|ses)$/i'  => "$1sis",
		'/([ti])a$/i'               => "$1um",
		'/(n)ews$/i'                => "$1ews",
		'/(ss)$/i'					=> "$1",
        '/s$/i'                     => ""
    );

    static $irregular = array(
		'zombie' => 'zombies',
		'cow'    => 'kine',
		'move'   => 'moves',
        'sex'    => 'sexes',
        'child'  => 'children',
        'man'    => 'men',
        'person' => 'people'
    );

    static $uncountable = array(
        'police',
		'jeans',
		'sheep',
		'fish',
        'series',
        'species',
        'money',
        'rice',
        'information',
        'equipment'
    );

    public static function pluralize( $string )
    {
        // save some time in the case that singular and plural are the same
        if ( in_array( strtolower( $string ), self::$uncountable ) )
            return $string;

        // check for irregular singular forms
        foreach ( self::$irregular as $pattern => $result )
        {
			if ( $pattern === $string ) 
				return $result;
        }

        // check for matches using regular expressions
        foreach ( self::$plural as $pattern => $result )
        {
            if ( preg_match( $pattern, $string ) )
                return preg_replace( $pattern, $result, $string );
        }

        return $string;
    }

    public static function singularize( $string )
    {
        // save some time in the case that singular and plural are the same
        if ( in_array( strtolower( $string ), self::$uncountable ) )
            return $string;

        // check for irregular plural forms
        foreach ( self::$irregular as $result => $pattern )
        {
            $pattern = '/' . $pattern . '$/i';

			if ($pattern === $string)
				return $result;
        }

        // check for matches using regular expressions
        foreach ( self::$singular as $pattern => $result )
        {
            if ( preg_match( $pattern, $string ) )
                return preg_replace( $pattern, $result, $string );
        }

        return $string;
    }

    public static function pluralize_if($count, $string)
    {
        if ($count == 1)
            return "1 $string";
        else
            return $count . " " . self::pluralize($string);
    }
}
?>