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
	 * 
	 * @param string $flowmodelName
	 * @param \TYPO3\Flow\Reflection\ReflectionService $reflectionService
	 * @param \TYPO3\Flow\Object\ObjectManagerInterface $objectManager
	 */
	public function __construct($flowmodelName, \TYPO3\Flow\Reflection\ReflectionService $reflectionService, \TYPO3\Flow\Object\ObjectManagerInterface $objectManager) {
		$this->flowmodelName = $flowmodelName;
		$this->modelName = NamingUtility::extractMetamodelname($flowmodelName);

		// set resource name
		// TODO check Ember.yaml for custom resource name
		$resourceAnnotation = $reflectionService->getClassAnnotation($flowmodelName, '\Mmitasch\Flow4ember\Annotations\Resource');
		$this->resourceName = $resourceAnnotation->getName();
		$this->resourceName = ($this->resourceName === NULL) ? strtolower($this->modelName) . "s" : $this->resourceName;
		
		// set repository if exists
		$repositoryName = str_replace(array('\\Model\\'), array('\\Repository\\'), $this->flowmodelName) . 'Repository';
		if ($objectManager->isRegistered($repositoryName)) {
			$this->repository = $objectManager->get($repositoryName);
		}
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
	public $modelName;

	/**
	 * meta resource name
	 * @var string
	 */
	protected $resourceName;

	/**
	 * repository 
	 * @var TYPO3\Flow\Persistence\Repository
	 */
	protected $repository;
	
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
	
	/**
	 * @return TYPO3\Flow\Persistence\Repository
	 */
	public function getRepository() {
		return $this->repository;
	}


}

?>