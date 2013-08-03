<?php
namespace Mmitasch\Flow4ember\Domain\Model;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Mmitasch.Flow4ember".   *
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow,
	Mmitasch\Flow4ember\Utility\NamingUtility;

class Association {

	function __construct($flowName, $emberName, $flowModelName, $flowType, $emberType, $sideload, $embedded, $isCollection) {
		$this->flowName = $flowName;
		$this->emberName = $emberName;
		$this->flowModelName = $flowModelName;
		$this->flowType = $flowType;
		$this->emberType = $emberType;
		$this->sideload = $sideload;
		$this->embedded = $embedded;
		$this->isCollection = $isCollection;
	}

		
	/**
	 * @var string
	 */
	protected $flowName;

	/**
	 * @var string
	 */
	protected $emberName;

	/**
	 * Association to which flow model
	 * @var string
	 */
	protected $flowModelName;
	
	/**
	 * Type of association in flow
	 * @var string
	 */
	protected $flowType;

	/**
	 * Type of association in ember
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
	 * @var boolean
	 */
	protected $isCollection;


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
	public function getEmberPayloadName() {
		return NamingUtility::decamelize($this->emberName);
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
	
	public function getFlowModelName() {
		return $this->flowModelName;
	}

	public function setFlowModelName($flowModelName) {
		$this->flowModelName = $flowModelName;
	}

	public function getFlowType() {
		return $this->flowType;
	}

	public function setFlowType($flowType) {
		$this->flowType = $flowType;
	}

	public function getEmberType() {
		return $this->emberType;
	}

	public function setEmberType($emberType) {
		$this->emberType = $emberType;
	}

	public function getIsCollection() {
		return $this->isCollection;
	}

	public function setIsCollection($isCollection) {
		$this->isCollection = $isCollection;
	}


}
?>