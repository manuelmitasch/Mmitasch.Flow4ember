<?php

namespace Mmitasch\Flow4ember\Domain\Model;

/* *
 * This script belongs to the TYPO3 Flow package "Mmitasch.Flow4ember".   *
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow,
	Mmitasch\Flow4ember\Utility\NamingUtility as NamingUtility;

/**
 * Contains all model relevant information
 */
class Metamodel {

	/**
	 * @param type $flowmodelName
	 * @param \TYPO3\Flow\Reflection\ReflectionService $reflectionService
	 */
	public function __construct($flowmodelName, \TYPO3\Flow\Reflection\ReflectionService $reflectionService) {
		$this->flowmodelName = $flowmodelName;
		$this->modelName = NamingUtility::extractMetamodelname($flowmodelName);

		// TODO check Ember.yaml for custom resource name
		$resourceAnnotation = $reflectionService->getClassAnnotation($flowmodelName, '\Mmitasch\Flow4ember\Annotations\Resource');
		$this->resourceName = $resourceAnnotation->getName();
		$this->resourceName = ($this->resourceName === NULL) ? strtolower($this->modelName) . "s" : $this->resourceName;
	}

	/**
	 * fully qualified class name of flow domain model
	 * @var string
	 */
	protected $flowmodelName;

	/**
	 * meta model name; derived from $flowmodelname
	 * @var string
	 */
	protected $modelName;

	/**
	 * meta resource name
	 * @var string
	 */
	protected $resourceName;

	
	
	/**
	 * @return string
	 */
	public function getFlowmodelName() {
		return $this->flowmodelName;
	}

	/**
	 * @return string
	 */
	public function getModelName() {
		return $this->modelName;
	}

	/**
	 * @return string
	 */
	public function getResourceName() {
		return $this->resourceName;
	}

	/**
	 * @param string $resourceName
	 * @return void
	 */
	public function setResourceName($resourceName) {
		$this->resourceName = $resourceName;
	}

}

?>