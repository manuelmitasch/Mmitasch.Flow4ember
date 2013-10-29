<?php

namespace Mmitasch\Flow4ember\Service;

/**
 * This script belongs to the TYPO3 Flow package "Mmitasch.Flow4ember".   *
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow,
	Mmitasch\Flow4ember\Domain\Model\TypeConverter as TypeConverter;


/**
 * 
 * @Flow\Scope("singleton")
 */
class ConverterService {
	
	/**
	 * @var array<\Mmitasch\Flow4ember\Domain\Model\TypeConverter>
	 */
	protected $converters;
	
	/**
	 * @var array<\Mmitasch\Flow4ember\Domain\Model\TypeConverter>
	 */
	protected $standardConverters;
	
	/**
	 * Initialize the converter service lazily with standard converters.
	 * This method must be run only after all dependencies have been injected.
	 *
	 * @return void
	 */
	public function initializeObject() {
		// Adds standard type convertes for the following transforms: 
		//  - string => string
		//  - integer => number
		//  - float => number
		//  - \DateTime => date
		//  -  boolean => boolean
		
		$this->standardConverters['string'] = new TypeConverter('string', 'string', 
				function ($value) { return $value; }, 
				function($value) { return $value;  /* leave type conversion to flow type converter */ });
		$this->standardConverters['integer'] = new TypeConverter('integer', 'number', 
				function ($value) { return $value; }, 
				function($value) { return $value; /* leave type conversion to flow type converter */ });
		$this->standardConverters['float'] = new TypeConverter('float', 'number', 
				function ($value) { return $value; }, 
				function($value) { return $value; /* leave type conversion to flow type converter */ });
		$this->standardConverters['boolean'] = new TypeConverter('boolean', 'boolean', 
				function ($value) { return ($value) ? "true" : "false"; }, 
				function($value) { return $value; /* leave type conversion to flow type converter */ });
		$this->standardConverters['date'] = new TypeConverter('DateTime', 'date', 
				function ($value) { return $value->format(\DateTime::ISO8601); /* ISO8601 is the preferred format for serializing date in json*/}, 
				function($value) { 
					return array('date' => $value, 'dateFormat' => "D, d M Y H:i:s T"); // setup for flow type converter
				});
	}
	
	/**
	 * Returns a suited TypeConverter for given flowType and emberType
	 * If no emberType is passed (or no converter was found), it tries to find a standard converter.
	 * 
	 * @param type $flowType type in flow domain model
	 * @param type $emberType type in ember domain model
	 * @return \Mmitasch\Flow4ember\Domain\Model\TypeConverter
	 * @throws \RuntimeException
	 */
	public function getTypeConverter($flowType, $emberType) {
		
			// search custom converters
		foreach ((array)$this->converters as $key => $converter) {
			if ($converter->getFlowType() === $flowType && $converter->getEmberType() === $emberType) return $converter;
		}
		
			// search standard converters
		foreach ((array)$this->standardConverters as $key => $converter) {
			if ($converter->getFlowType() === $flowType && $converter->getEmberType() === $emberType) return $converter;
		}
		
			// search standard converters only flowType matches
		foreach ((array)$this->standardConverters as $key => $converter) {
			if ($converter->getFlowType() === $flowType) return $converter;
		}

		// todo lookup exception code semantics
		throw new \RuntimeException('No TypeConverter found (flowType to emberType)', 1361478315); 
		return NULL;
	}

	
	/**
	 * Add a new converter.
	 * 
	 * @param \Mmitasch\Flow4ember\Domain\Model\TypeConverter $converter
	 */
	public function addConverter($converter) {
		$this->converters[] = $converter;
	}

		
	// todo remove
	public function dumpConverters() {
		\TYPO3\Flow\var_dump($this->converters);
		\TYPO3\Flow\var_dump($this->standardConverters);
	}

}

?>
