<?php

namespace Mmitasch\Flow4ember\Service;

/**
 * This script belongs to the TYPO3 Flow package "Mmitasch.Flow4ember".   *
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow,
		Mmitasch\Flow4ember\Domain\Model\Metamodel;

/**
 * @Flow\Scope("singleton")
 */
class ModelReflectionService implements ModelReflectionServiceInterface {
	
	/**
	 * @var array
	 */
	protected $metaModels;
	
	/**
	 * config from Ember.yaml files
	 * 
	 * @var array
	 */
	protected $config;
	
	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Configuration\ConfigurationManager
	 */
	protected $configurationManager;
	
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
		$this->config = $this->configurationManager->getConfiguration('Ember');
		$models = $this->reflectionService->getClassNamesByAnnotation('\Mmitasch\Flow4ember\Annotations\Resource');

			// TODO add models that have Ember.Model Annotation
			// add each model that has Ember.Resource Annotation
		foreach ($models as $modelName) {
			$tokens = explode('\\', $modelName);
			$packageNamespace = trim($tokens[0]);
			$packageName = trim($tokens[1]);
			$packageKey = $packageNamespace . '.' . $packageName;
			
			$this->metaModels[$packageKey][$modelName] = new Metamodel($modelName);	
		}
		
		// TODO add Ember.yaml config
			// add/override each model that is configured in Ember.yaml
//		foreach ($this->config as $packageNamespace => $packagesConfigs) {
//			foreach ($packagesConfigs as $packageName => $packageConfig) {
//				foreach ($packageConfig as $modelName => $modelConfig) {
//					$packageKey = $packageNamespace . '.' . $packageName;
//					$this->metaModels[$packageKey][$modelName] = new Metamodel($modelName, $packageConfig);
//				}
//			}
//		}
		
//		$this->dumpModels(); // TODO: remove
	}
	
	
	/**
	 * Get all Metamodels for the given package
	 * 
	 * @param string $packageKey The package in which the models are used (eg. 'Mmitasch.Taskplaner')
	 * @return array<\Mmitasch\Flow4ember\Domain\Model\Metamodel>
	 */
	public function getMetaModels($packageKey) {
		if (!isset($this->metaModels[$packageKey])) {
			throw new \RuntimeException('Could NOT find any Metamodels for package "' . $packageKey . '". Make sure to either annotate your models with Ember.Resource or configure models in your Ember.yaml', 1375148357); 
		}
		return $this->metaModels[$packageKey];
	}
	
	/**
	 * Get Metamodel by Flow model name
	 * 
	 * @param string $flowModelName
	 * @param string $packageKey The package in which the models are used (eg. 'Mmitasch.Taskplaner')
	 * @return \Mmitasch\Flow4ember\Domain\Model\Metamodel
	 */
	public function findByFlowModelName($flowModelName, $packageKey) {
		if (!isset($this->metaModels[$packageKey][$flowModelName])) {
			throw new \RuntimeException('Could not find Metamodel for class: ' . $flowModelName . '.', 1375148357); 
		}
		return $this->metaModels[$packageKey][$flowModelName];
	}
	
	/**
	 * Get Metamodel by resource name
	 * 
	 * @param string $resourceName
 	 * @param string $packageKey The package in which the models are used (eg. 'Mmitasch.Taskplaner')
	 * @return \Mmitasch\Flow4ember\Domain\Model\Metamodel
	 */
	public function findByResourceName($resourceName, $packageKey) {
		if (!isset($this->metaModels[$packageKey])) {
			throw new \RuntimeException('Could NOT find any Metamodels for package "' . $packageKey . '". Make sure to either annotate your models with Ember.Resource or configure models in your Ember.yaml', 1375148357); 
		}
		
			// search metamodel with given resourceName
		foreach ($this->metaModels[$packageKey] as $flowName => $metaModel) {
			if ($metaModel->getResourceName() === $resourceName) return $metaModel;
		}
		
		throw new \RuntimeException('Could not find Metamodel for resourceName: ' . $resourceName . '.', 1375148356); 
	}
	
	/**
	 * Is the resource with given name registered?
	 * 
	 * @param string $resourceName
	 * @param string $packageKey The package in which the models are used (eg. 'Mmitasch.Taskplaner')
	 * @return boolean
	 */
	public function hasResourceName($resourceName, $packageKey) {
		return ($this->findByResourceName($resourceName, $packageKey) !== NULL);
	}
	
	
	// todo remove
	public function dumpModels() {
		\TYPO3\Flow\var_dump($this->metaModels);
	}

}

?>
