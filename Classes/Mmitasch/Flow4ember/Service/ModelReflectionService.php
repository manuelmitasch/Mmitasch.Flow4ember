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
			$this->metaModels[$modelname] = new \Mmitasch\Flow4ember\Domain\Model\Metamodel($modelname);	
		}
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
		return $this->metaModels[$flowModelName];
	}
	
	/**
	 * Get Metamodel by resource name
	 * 
	 * @param string $resourceName
	 * @return \Mmitasch\Flow4ember\Domain\Model\Metamodel
	 */
	public function findByResourceName($resourceName) {
		foreach ($this->metaModels as $flowname => $metaModel) {
			if ($metaModel->getResourceName() === $resourceName) return $metaModel;
		}
		return NULL;
	}
	
	
	// todo remove
	public function dumpModels() {
		\TYPO3\Flow\var_dump($this->metaModels);
	}

}

?>
