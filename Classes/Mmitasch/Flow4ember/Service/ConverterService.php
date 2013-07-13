<?php

namespace Mmitasch\Flow4ember\Service;

/**
 * This script belongs to the TYPO3 Flow package "Mmitasch.Flow4ember".   *
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;


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
		// TODO: add proper standard converters
		// 
		// string => string
		// number => number
		// datetime => date
		// boolean => boolean
		
		$standardConverters['string'] = new TypeConverter('string', 'string', function ($value) { return value; }, function($value) { return value(); });
		$standardConverters['boolean'] = new TypeConverter('boolean', 'boolean', function ($value) { return ($value) ? "true" : "false"; }, function($value) { return new Boolean($value); });
		$standardConverters['date'] = new TypeConverter('\Datetime', 'date', function ($value) { return $value->format(\DateTime::ISO8601); }, function($value) { return new DateTime($value); });
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
		foreach ($this->converters as $key => $converter) {
			if ($converter->getFlowType() === $flowType && $converter->getEmberType() === $emberType) return $converter;
		}
		
		foreach ($this->standardConverters as $key => $converter) {
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
