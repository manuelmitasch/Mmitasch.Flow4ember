<?php

namespace Mmitasch\Flow4ember\Service;

/**
 * This script belongs to the TYPO3 Flow package "Mmitasch.Flow4ember".   *
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow,
		Mmitasch\Flow4ember\Domain\Model\Metamodel,
		Mmitasch\Flow4ember\Utility\NamingUtility;

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
			// get Ember.yaml configurations
		$this->config = $this->configurationManager->getConfiguration('Ember');
			 
			// get each model that has an Ember.Model Annotation
		$models = $this->reflectionService->getClassNamesByAnnotation('\Mmitasch\Flow4ember\Annotations\Model');
			// add each model that has an Ember.Model Annotation
		$this->addMetaModels($models);
		
			// get each model that has an Ember.Resource Annotation
		$resources = $this->reflectionService->getClassNamesByAnnotation('\Mmitasch\Flow4ember\Annotations\Resource');
			// add each model that has an Ember.Resource Annotation
		$this->addMetaModels($resources);
		
			// add/override each model that is configured in Ember.yaml
		foreach ($this->config as $packageNamespace => $packagesConfigs) {
			foreach ($packagesConfigs as $packageName => $packageConfig) {
				if(isset($packageConfig['models'])) {
					foreach ($packageConfig['models'] as $modelName => $modelConfig) {
						$packageKey = $packageNamespace . '.' . $packageName;
						$this->metaModels[$packageKey][$modelName] = new Metamodel($modelName, $packageKey, $packageConfig);
					}
				}
			}
		}
		
			// set emberModelType on association (after all models are loaded)
		foreach ($this->metaModels as $packageKey => $metaModels) {
			foreach ($metaModels as $metaModel) {
				if (is_array($metaModel->getAssociations())) {
					foreach ($metaModel->getAssociations()as $association) {
						$targetFlowModelName = $association->getFlowModelName();
						$targetMetaModel = $this->findByFlowModelName($targetFlowModelName, $packageKey);
						$association->setEmberModelName($targetMetaModel->getEmberName());
					}
				}
			}
		}
		
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
	
	/**
	 * Get all Metamodels that are a resource for the given package.
	 * 
	 * @param string $packageKey The package in which the models are used (eg. 'Mmitasch.Taskplaner')
	 * @return array<\Mmitasch\Flow4ember\Domain\Model\Metamodel>
	 */
	public function getResources($packageKey = NULL) {
		if (!isset($this->metaModels[$packageKey]) && $packageKey !== NULL) {
			throw new \RuntimeException('Could NOT find any Metamodels for package "' . $packageKey . '". Make sure to either annotate your models with Ember.Resource or configure models in your Ember.yaml', 1375148357); 
		}
		
		$resources = array();
		
		if ($packageKey === NULL) {
			foreach ($this->metaModels as $packageKey => $package) {
				foreach ($package as $metaModel) {
					if ($metaModel->isResource()) $resources[] = $metaModel;
				}
			}
		} else {
			foreach ($this->metaModels[$packageKey] as $metaModel) {
				if ($metaModel->isResource()) $resources[] = $metaModel;
			}
		}
		return $resources;
	}
	
	/**
	 * Creates new Metamodels for the given classnames
	 * and adds it to the metaModels array.
	 * 
	 * @param array $modelNames
	 */
	protected function addMetaModels($modelNames) {
		foreach ($modelNames as $modelName) {
			$packageKey = NamingUtility::extractPackageKey($modelName);
			$this->metaModels[$packageKey][$modelName] = new Metamodel($modelName, $packageKey);	
		}
	}
	
	
	// todo remove
	public function dumpModels() {
		\TYPO3\Flow\var_dump($this->metaModels);
	}

}

?>
