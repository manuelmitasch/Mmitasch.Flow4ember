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
	public function __construct($flowModelName, \TYPO3\Flow\Reflection\ReflectionService $reflectionService, \TYPO3\Flow\Object\ObjectManagerInterface $objectManager) {
		$this->flowModelName = $flowModelName;
		$this->modelName = NamingUtility::extractMetamodelname($flowModelName);

		// set resource name
		// TODO check Ember.yaml for custom resource name
		$resourceAnnotation = $reflectionService->getClassAnnotation($flowModelName, '\Mmitasch\Flow4ember\Annotations\Resource');
		$this->resourceName = $resourceAnnotation->getName();
		$this->resourceName = ($this->resourceName === NULL) ? strtolower($this->modelName) . "s" : $this->resourceName;
		
		// set repository if exists
		$repositoryName = str_replace(array('\\Model\\'), array('\\Repository\\'), $this->flowModelName) . 'Repository';
		if ($objectManager->isRegistered($repositoryName)) {
			$this->repository = $objectManager->get($repositoryName);
		}
		
		// init properties + associations
//		$this->initPropertiesAndAssociations();
	}

	/**
	 * fully qualified class name of flow domain model
	 * @var string
	 */
	protected $flowModelName;

	/**
	 * meta model name; derived from $flowModelName
	 * @var string
	 */
	public $modelName;

	/**
	 * meta resource name
	 * @var string
	 */
	protected $resourceName;

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
		return $this->flowModelName;
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

	/**
	 * Init the model properties and associations
	 */
	public function initPropertiesAndAssociations() {

		foreach ($this->reflectionService->getClassPropertyNames($this->flowModelName) as $propertyName) {
			$propertyType = $this->reflectionService->getPropertyTagValues($this->flowModelName, $propertyName, 'var');
			$flowType = \TYPO3\Flow\Utility\TypeHandling::parseType($propertyType[0]);
			// todo lookup ember type config from ember.yaml
			$emberType = $this->reflectionService->getPropertyAnnotation($this->flowModelName, $propertyName, '\Mmitasch\Flow4ember\Annotations\Type');

//			if (class_exists($type['type']) && !isset($classNames[$type['type']])) {
//				$this->readClass($type['type'], $classNames);
//			}
//			if (class_exists($type['elementType']) && !isset($classNames[$type['elementType']])) {
//				if ($this->reflectionService->isClassAbstract($type['elementType'])) {
//					$implementations = $this->reflectionService->getAllSubClassNamesForClass($type['elementType']);
//					foreach ($implementations as $implementationClassName) {
//						if (isset($classNames[$implementationClassName])) {
//							continue;
//						}
//						$this->readClass($implementationClassName, $classNames);
//					}
//				} else {
//					$this->readClass($type['elementType'], $classNames);
//				}
//			}

			if ($this->isAssociation($propertyName)) {
				// TODO: Add new association object to array
			} else {
				$converter = $this->converterService->getTypeConverter($flowType, $emberType);
				$this->properties[$propertyName] = new Property($propertyName, $converter);
				
//				$modelDefinition[$propertyName] = array(
//					'type' => \TYPO3\Flow\Utility\TypeHandling::isCollectionType($type['type']) ? $type['elementType'] : $type['type']
//				);
				
			}
		}
	}
	
	
	/**
	 * Returns TRUE if the property is a association on the object
	 *
	 * @param string $propertyName
	 * @return boolean
	 */
	protected function isAssociation($propertyName) {
		foreach ($this->reflectionService->getPropertyAnnotations($this->flowModelName, $propertyName) as $annotationName => $annotation) {
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