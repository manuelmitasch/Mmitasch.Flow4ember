<?php

namespace Mmitasch\Flow4ember\Domain\Model;

/* *
 * This script belongs to the TYPO3 Flow package "Mmitasch.Flow4ember".   *
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

class TypeConverter {

	/**
	 * @param string $flowType
	 * @param string $emberType
	 * @param function from
	 * @param function to
	 */
	public function __construct($flowType, $emberType, $from, $to) {
		$this->flowType = $flowType;
		$this->emberType = $emberType;
		$this->from = $from;
		$this->to = $to;
	}

	/**
	 * @var string
	 */
	protected $flowType;

	/**
	 * @var string
	 */
	protected $emberType;

	/**
	 * Function to convert value from flowType to emberType
	 * @var function
	 */
	protected $from;

	/**
	 * Function to convert value from emberType to flowType
	 * @var function
	 */
	protected $to;

	/**
	 * @return string
	 */
	public function getFlowType() {
		return $this->flowType;
	}

	/**
	 * @param string $flowType
	 * @return void
	 */
	public function setFlowType($flowType) {
		$this->flowType = $flowType;
	}

	/**
	 * @return string
	 */
	public function getEmberType() {
		return $this->emberType;
	}

	/**
	 * @param string $emberType
	 * @return void
	 */
	public function setEmberType($emberType) {
		$this->emberType = $emberType;
	}

	/**
	 * @return function
	 */
	public function getFrom() {
		return $this->from;
	}

	/**
	 * @param function $from
	 * @return void
	 */
	public function setFrom($from) {
		$this->from = $from;
	}

	/**
	 * @return function
	 */
	public function getTo() {
		return $this->to;
	}

	/**
	 * @param function $to
	 * @return void
	 */
	public function setTo($to) {
		$this->to = $to;
	}

}

?>