<?php

namespace Mmitasch\Flow4ember\Service;

/* *
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
			$metamodel = new \Mmitasch\Flow4ember\Domain\Model\Metamodel($modelname);	
			$this->metaModels[] = $metamodel;
			var_dump($metamodel.getRepository()); // todo remove
		}
		
	}
	
	
	/**
	 * @return array<\Mmitasch\Flow4ember\Domain\Model\Metamodel>
	 */
	public function getMetaModels() {
		return $this->metaModels;
	}


}

?>
