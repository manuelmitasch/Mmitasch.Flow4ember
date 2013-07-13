<?php
namespace Mmitasch\Flow4ember\Domain\Model;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Mmitasch.Flow4ember".   *
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

class Association {

	/**
	 * @var string
	 */
	protected $flowName;

	/**
	 * @var string
	 */
	protected $emberName;

	/**
	 * @var string
	 */
	protected $flowType;

	/**
	 * @var string
	 */
	protected $emberType;

	/**
	 * @var boolean
	 */
	protected $sideload;

	/**
	 * @var string
	 */
	protected $embedded;


	/**
	 * @return string
	 */
	public function getFlowName() {
		return $this->flowName;
	}

	/**
	 * @param string $flowName
	 * @return void
	 */
	public function setFlowName($flowName) {
		$this->flowName = $flowName;
	}

	/**
	 * @return string
	 */
	public function getEmberName() {
		return $this->emberName;
	}

	/**
	 * @param string $emberName
	 * @return void
	 */
	public function setEmberName($emberName) {
		$this->emberName = $emberName;
	}

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
	 * @return boolean
	 */
	public function getSideload() {
		return $this->sideload;
	}

	/**
	 * @param boolean $sideload
	 * @return void
	 */
	public function setSideload($sideload) {
		$this->sideload = $sideload;
	}

	/**
	 * @return string
	 */
	public function getEmbedded() {
		return $this->embedded;
	}

	/**
	 * @param string $embedded
	 * @return void
	 */
	public function setEmbedded($embedded) {
		$this->embedded = $embedded;
	}

}
?>