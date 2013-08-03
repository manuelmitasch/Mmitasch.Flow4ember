<?php
namespace Mmitasch\Flow4ember\Domain\Model;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Mmitasch.Flow4ember".   *
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow,
	Mmitasch\Flow4ember\Utility\NamingUtility;

class Property {

	/**
	 * @param string $name
	 * @param Mmitasch\Flow4ember\Domain\Model\TypeConverter $converter
	 */
	public function __construct($name, $converter) {
		$this->name = $name;
		$this->converter = $converter;
	}
	
	/**
	 * @var string
	 */
	protected $name;
	
	/**
	 * @var Mmitasch\Flow4ember\Domain\Model\TypeConverter
	 */
	protected $converter;


	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @param string $name
	 * @return void
	 */
	public function setName($name) {
		$this->name = $name;
	}

	/**
	 * @return Mmitasch\Flow4ember\Domain\Model\TypeConverter
	 */
	public function getConverter() {
		return $this->converter;
	}

	/**
	 * @param Mmitasch\Flow4ember\Domain\Model\TypeConverter $converter
	 * @return void
	 */
	public function setConverter($converter) {
		$this->converter = $converter;
	}
	
	/**
	 * @return string
	 */
	public function getPayloadName() {
		return NamingUtility::decamelize($this->name);
	}

}
?>