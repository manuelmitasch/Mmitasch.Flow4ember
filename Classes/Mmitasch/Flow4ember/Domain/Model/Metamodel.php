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
	 * Configuration from Ember.yaml for this model
	 * 
	 * @var array
	 */
	protected $config;
	
	/**
	 * 
	 * @param string $flowModelName
	 * @param string $packageKey The packageKey for the package in which the Metamodel will be used.
	 * @param array $config Configuration from Ember.yaml
	 * @param \TYPO3\Flow\Reflection\ReflectionService $reflectionService
	 * @param \TYPO3\Flow\Object\ObjectManagerInterface $objectManager
	 */
	public function __construct($flowModelName, $packageKey, $config, \TYPO3\Flow\Reflection\ReflectionService $reflectionService, \Mmitasch\Flow4ember\Service\ConverterService $converterService, \TYPO3\Flow\Object\ObjectManagerInterface $objectManager) {
		$this->config = $this->extractConfig($config, $flowModelName);	
		$this->emberNamespace = $this->extractEmberNamespace($config);
		unset($config); // unset to avoid confusion between $config and $this->config
		$this->reflectionService = $reflectionService;
		$this->converterService = $converterService;
		
		$this->flowName = $flowModelName;
		$this->modelName = NamingUtility::extractMetamodelname($flowModelName);
		$this->packageKey = $packageKey;


		// get Annotations
		$resourceAnnotation = $reflectionService->getClassAnnotation($flowModelName, '\Mmitasch\Flow4ember\Annotations\Resource');
		$modelAnnotation = $reflectionService->getClassAnnotation($flowModelName, '\Mmitasch\Flow4ember\Annotations\Model');

		// set isResource
		if ($modelAnnotation !== NULL) {
			$this->isResource = false;
		}
		if ($resourceAnnotation !== NULL) {
			$this->isResource = true;
		}
		if (isset($this->config['resource'])) {
			if ($this->config['resource'] === 'no') {
				$this->isResource = false;
			} else {
				$this->isResource = true;
			}
		}
				
		// set resource name
		$this->resourceName = NamingUtility::pluralize($this->getModelNameLowercased());
		$this->customResourceName = false;

		// set repository if exists
		$repositoryName = str_replace(array('\\Model\\'), array('\\Repository\\'), $this->flowName) . 'Repository';
		if ($objectManager->isRegistered($repositoryName)) {
			$this->repository = $objectManager->get($repositoryName);
		}
		
		// init properties + associations
		$this->initPropertiesAndAssociations();
	}

	/**
	 * The packageKey for the package in which the Metamodel will be used.
	 * 
	 * @var string
	 */
	protected $packageKey;
	
	/**
	 * meta model name; derived from $flowModelName
	 * 
	 * @var string
	 */
	public $modelName;
	
	/**
	 * fully qualified class name of flow domain model
	 * @var string
	 */
	protected $flowName;
	
	/**
	 * the ember namespace for the model (eg. App of App.Model)
	 * @var string
	 */
	protected $emberNamespace;
	
	/**
	 * is model also a resource or just a model
	 * for resouces a REST endpoint is provided.
	 * models can only be used as embedded models in ember.
	 *  
	 * @var boolean
	 */
	protected $isResource;

	/**
	 * meta resource name
	 * @var string
	 */
	protected $resourceName;
	
	/**
	 * True if a custom resource name was assigned through Ember.yaml or Annotation
	 * @var boolean
	 */
	protected $customResourceName;
	
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
	public function getFlowName() {
		return $this->flowName;
	}

	/**
	 * @return string
	 */
	public function getModelName() {
		return $this->modelName;
	}
	
	public function getModelNameLowercased() {
		return strtolower($this->modelName);
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
		return $this->getModelNameLowercased();
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
	 * @return boolean
	 */
	public function getIsResource() {
		return $this->isResource;
	}	
	
	/**
	 * @return boolean
	 */
	public function isResource() {
		return $this->isResource;
	}	
	
	/**
	 * @return string
	 */
	public function getPackageKey() {
		return $this->packageKey;
	}

	/**
	 * @return string
	 */
	public function getEmberName() {
		return $this->emberNamespace . '.' . $this->modelName;
	}
		
	/**
	 * @return string
	 */
	public function getEmberNamePlural() {
		return $this->emberNamespace . '.' . ucfirst($this->resourceName);
	}
	
	/**
	 * @return boolean
	 */
	public function getCustomResourceName() {
		return $this->customResourceName;
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
				$inversedBy = NULL;
				$mappedBy = NULL;
				
					// get annotations
				$annotations = $this->reflectionService->getPropertyAnnotations($this->flowName, $propertyName);
				
					// get flow type of association
				if (isset($annotations['Doctrine\ORM\Mapping\OneToOne'])) {
					$flowType = 'OneToOne';
				} elseif (isset($annotations['Doctrine\ORM\Mapping\OneToMany']))  {
					$flowType = 'OneToMany';
					$mappedBy = reset($annotations['Doctrine\ORM\Mapping\OneToMany'])->mappedBy;
				} elseif (isset($annotations['Doctrine\ORM\Mapping\ManyToMany'])) {
					$flowType = 'ManyToMany';
				} elseif (isset($annotations['Doctrine\ORM\Mapping\ManyToOne'])) {
					$flowType = 'ManyToOne';
					$inversedBy = reset($annotations['Doctrine\ORM\Mapping\ManyToOne'])->inversedBy;
				} else {
					throw new \RuntimeException('Could not identify association type.', 1375148355); 
				}
				
					// get ember type of association
				if ($flowType === 'OneToMany' || $flowType === 'ManyToMany') {
					$emberType = 'hasMany';
					$isCollection = TRUE;
				} else {
					$emberType = 'belongsTo';
					$isCollection = FALSE;
				}
				
				// Sideload?
					// check annotation
				$sideload = (array_key_exists('Mmitasch\Flow4ember\Annotations\Sideload', $annotations)); 
					// check Ember.yaml config
				if (isset($this->config['associations'][$propertyName]['sideload'])
						&& $this->config['associations'][$propertyName]['sideload'] === 'yes') {
					$sideload = true;
				}
				
				// Embedded?
				$embedded = NULL;
					// check annotations
				if (array_key_exists('Mmitasch\Flow4ember\Annotations\Embedded', $annotations)) {
					$embedded = $annotations['Mmitasch\Flow4ember\Annotations\Embedded'][0]->getType(); 
				} 
					// check Ember.yaml config
				if (isset($this->config['associations'][$propertyName]['embeeded'])) {
					if ($this->config['associations'][$propertyName]['embedded'] === 'always') {
						$embedded = 'always';
					} elseif ($this->config['associations'][$propertyName]['embedded'] === 'load') {
						$embedded = 'load';
					}
				}  
				
					// add association
				$this->associations[$propertyName] = new Association($propertyName, $propertyName, $flowModelName, $flowType, $emberType, $sideload, $embedded, $isCollection, $inversedBy, $mappedBy);
				
			} else {
				// TODO check Ember.yaml for custom TypeConverter
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
	
	
	/**
	 * Extract model config from Ember.yaml config
	 * 
	 * @param array $config
	 * @param string $flowName
	 * @return array
	 */
	protected function extractConfig ($config, $flowName) {
		$extractedConfig = array();
		
		if (isset($config['models'][$flowName])) {
			$extractedConfig = $config['models'][$flowName];			
		}
		
		return $extractedConfig;
	}
	
	/**
	 * Extract the embername space from Ember.yaml config
	 * or set to standard "App"
	 * 
	 * @param array $config
	 * @return string
	 */
	protected function extractEmberNamespace ($config) {
		if ($config !== NULL && array_key_exists('emberNamespace', $config)) {
			return $config['emberNamespace'];
		}
		return 'App';
	}
	
}

?>