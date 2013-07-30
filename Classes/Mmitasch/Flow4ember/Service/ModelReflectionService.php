<?php

namespace Mmitasch\Flow4ember\Service;

/**
 * This script belongs to the TYPO3 Flow package "Mmitasch.Flow4ember".   *
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow,
		\Mmitasch\Flow4ember\Domain\Model\Metamodel;

/**
 * 
 * @Flow\Scope("singleton")
 */
class ModelReflectionService {
	
	/**
	 * @var array
	 */
	protected $metaModels;
	
	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Object\ObjectManagerInterface
	 */
	protected $objectManager;
	
	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Reflection\ReflectionService
	 */
	protected $reflectionService;
	
	
	/**
	 * Initialize the reflection service lazily
	 * This method must be run only after all dependencies have been injected.
	 *
	 * @return void
	 */
	public function initializeObject() {
		$models = $this->reflectionService->getClassNamesByAnnotation('\Mmitasch\Flow4ember\Annotations\Resource');
		
		foreach ($models as $modelname) {
			$this->metaModels[$modelname] = new Metamodel($modelname);	
		}
		
//		$this->dumpModels(); // TODO: remove
	}
	
	
	/**
	 * @return array<\Mmitasch\Flow4ember\Domain\Model\Metamodel>
	 */
	public function getMetaModels() {
		return $this->metaModels;
	}
	
	
	/**
	 * Get Metamodel by Flow model name
	 * 
	 * @param string $flowModelName
	 * @return \Mmitasch\Flow4ember\Domain\Model\Metamodel
	 */
	public function findByFlowModelName($flowModelName) {
		if (array_key_exists($flowModelName, $this->metaModels)) {
			return $this->metaModels[$flowModelName];
		} else {
			throw new \RuntimeException('Could not find Metamodel for class: ' . $flowModelName . '.', 1361478318); 
		}
	}
	
	/**
	 * Get Metamodel by resource name
	 * 
	 * @param string $resourceName
	 * @return \Mmitasch\Flow4ember\Domain\Model\Metamodel
	 */
	public function findByResourceName($resourceName) {
		foreach ($this->metaModels as $flowName => $metaModel) {
			if ($metaModel->getResourceName() === $resourceName) return $metaModel;
		}
		
		throw new \RuntimeException('Could not find Metamodel for resourceName: ' . $resourceName . '.', 1361478317); 
	}
	
	/**
	 * Is the resource with given name registered?
	 * 
	 * @param string $resourceName
	 * @return boolean
	 */
	public function hasResourceName($resourceName) {
		return ($this->findByResourceName($resourceName) !== NULL);
	}
	
	
	// todo remove
	public function dumpModels() {
		\TYPO3\Flow\var_dump($this->metaModels);
	}

}

?>
