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
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Reflection\ReflectionService
	 */
	protected $reflectionService;
	
	/**
	 * @Flow\Inject
	 * @var \Mmitasch\Flow4ember\Service\ConverterService
	 */
	protected $converterService;
	
	/**
	 * 
	 * @param string $flowModelName
	 * @param \TYPO3\Flow\Reflection\ReflectionService $reflectionService
	 * @param \TYPO3\Flow\Object\ObjectManagerInterface $objectManager
	 */
	public function __construct($flowModelName, \TYPO3\Flow\Reflection\ReflectionService $reflectionService, \Mmitasch\Flow4ember\Service\ConverterService $converterService, \TYPO3\Flow\Object\ObjectManagerInterface $objectManager) {
		$this->reflectionService = $reflectionService;
		$this->converterService = $converterService;
		
		$this->flowName = $flowModelName;
		$this->modelName = NamingUtility::extractMetamodelname($flowModelName);
		$this->resourceNameSingular = strtolower($this->modelName);

		// set resource name
		// TODO check Ember.yaml for custom resource name
		$resourceAnnotation = $reflectionService->getClassAnnotation($flowModelName, '\Mmitasch\Flow4ember\Annotations\Resource');
		$this->resourceName = $resourceAnnotation->getName();
		$this->resourceName = ($this->resourceName === NULL) ? strtolower($this->modelName) . "s" : $this->resourceName;
		
		// set repository if exists
		$repositoryName = str_replace(array('\\Model\\'), array('\\Repository\\'), $this->flowName) . 'Repository';
		if ($objectManager->isRegistered($repositoryName)) {
			$this->repository = $objectManager->get($repositoryName);
		}
		
		// init properties + associations
		$this->initPropertiesAndAssociations();
	}

	/**
	 * meta model name; derived from $flowModelName
	 * @var string
	 */
	public $modelName;
	
	/**
	 * fully qualified class name of flow domain model
	 * @var string
	 */
	protected $flowName;
	
	/**
	 * ember model name.
	 * either derived from modelName or configured through annotation or Ember.yaml.
	 * @var string
	 */
	protected $emberName;

	/**
	 * meta resource name
	 * @var string
	 */
	protected $resourceName;
	
	/**
	 * resource name singular (lower cased model name)
	 * @var string
	 */
	protected $resourceNameSingular;

	/**
	 * repository of flow domain model
	 * @var TYPO3\Flow\Persistence\Repository
	 */
	protected $repository;
	
	/**
	 * @var array<\Mmitasch\Flow4ember\Domain\Model\Property>
	 */
	protected $properties;

	/**
	 * @var array<\Mmitasch\Flow4ember\Domain\Model\Association>
	 */
	protected $associations;
	
	
	/**
	 * @return string
	 */
	public function getFlowModelName() {
		return $this->flowName;
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
	 * @return string
	 */
	public function getResourceNameSingular() {
		return $this->resourceNameSingular;
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
	
	/**
	 * @return array<\Mmitasch\Flow4ember\Domain\Model\Property>
	 */
	public function getProperties() {
		return $this->properties;
	}

	/**
	 * @return array<\Mmitasch\Flow4ember\Domain\Model\Association>
	 */
	public function getAssociations() {
		return $this->associations;
	}

	
	/**
	 * Init the model properties and associations
	 */
	public function initPropertiesAndAssociations() {
		if ($this->reflectionService instanceof \TYPO3\Flow\Object\DependencyInjection\DependencyProxy) {
                        $this->reflectionService->_activateDependency();
        }
		
		foreach ($this->reflectionService->getClassPropertyNames($this->flowName) as $propertyName) {
			$varType = $this->reflectionService->getPropertyTagValues($this->flowName, $propertyName, 'var');
			$flowType = \TYPO3\Flow\Utility\TypeHandling::parseType($varType[0]);
			$flowModelName = \TYPO3\Flow\Utility\TypeHandling::isCollectionType($flowType['type']) ? $flowType['elementType'] : $flowType['type'];

			// TODO: lookup ember type config from Ember.yaml
			$emberType = $this->reflectionService->getPropertyAnnotation($this->flowName, $propertyName, '\Mmitasch\Flow4ember\Annotations\Type');

			// TODO: handle abstract classes and interfaces
			
			if ($this->isAssociation($propertyName)) {
				
					// get annotations
				$annotations = $this->reflectionService->getPropertyAnnotations($this->flowName, $propertyName);
				
					// get flow type of association
				if (array_key_exists('Doctrine\ORM\Mapping\OneToOne', $annotations)) {
					$flowType = 'OneToOne';
				} elseif (array_key_exists('Doctrine\ORM\Mapping\OneToMany',$annotations))  {
					$flowType = 'OneToMany';
				} elseif (array_key_exists('Doctrine\ORM\Mapping\ManyToMany', $annotations)) {
					$flowType = 'ManyToMany';
				} elseif (array_key_exists('Doctrine\ORM\Mapping\ManyToOne', $annotations)) {
					$flowType = 'ManyToOne';
				} else {
					//TODO: check how to make proper exception code
					throw new \RuntimeException('Could not identify association type.', 1361478316); 
				}
				
					// get ember type of association
				if ($flowType === 'OneToOne' || $flowType === 'OneToMany') {
					$emberType = 'belongsTo';
				} else {
					$emberType = 'hasMany';
				}
				
				// TODO: check Ember.yaml if association should be sideloaded
				$sideload = (array_key_exists('Mmitasch\Flow4ember\Annotations\Sideload', $annotations));
				
				// TODO: check Ember.yaml if association models should be embedded
				$embedded = NULL;
				if (array_key_exists('Mmitasch\Flow4ember\Annotations\Embedded', $annotations)) {
					$embedded = $annotations['Mmitasch\Flow4ember\Annotations\Embedded'][0]->getType(); 
				} 
				
					// add association
				$this->associations[$propertyName] = new Association($propertyName, $propertyName, $flowModelName, $flowType, $emberType, $sideload, $embedded);
				
			} else {
				$converter = $this->converterService->getTypeConverter($flowType['type'], $emberType);
				$this->properties[$propertyName] = new Property($propertyName, $converter);
			}
		}
	}
	
	
	/**
	 * Returns TRUE if the property is an association on the object
	 *
	 * @param string $propertyName
	 * @return boolean
	 */
	protected function isAssociation($propertyName) {
		foreach ($this->reflectionService->getPropertyAnnotations($this->flowName, $propertyName) as $annotationName => $annotation) {
			if (strpos($annotationName, 'OneToOne') !== FALSE
					|| strpos($annotationName, 'OneToMany') !== FALSE
					|| strpos($annotationName, 'ManyToOne') !== FALSE
					|| strpos($annotationName, 'ManyToMany') !== FALSE) {
				return TRUE;
			}
		}

		return FALSE;
	}

}

?>