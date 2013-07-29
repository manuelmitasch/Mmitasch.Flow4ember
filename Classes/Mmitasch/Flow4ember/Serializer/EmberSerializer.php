<?php
namespace Mmitasch\Flow4ember\Serializer;

use TYPO3\Flow\Annotations as Flow,
	Radmiraal\Emberjs\Utility\EmberDataUtility,
	TYPO3\Flow\Reflection\ObjectAccess,
	TYPO3\Flow\Reflection\ReflectionService,
	TYPO3\Flow\Utility\Arrays,
	TYPO3\Flow\Utility\TypeHandling,
	TYPO3\Flow\Object\Configuration\Configuration,
	Mmitasch\Flow4ember\Domain\Model\Metamodel;

class EmberSerializer {

	/**
	 * @Flow\Inject
	 * @var \Mmitasch\Flow4ember\Service\ModelReflectionService
	 */
	protected $modelReflectionService;
	
	/**
	 * @var \TYPO3\Flow\Persistence\PersistenceManagerInterface
	 * @Flow\Inject
	 */
	protected $persistenceManager;
	
	/**
	 * @var array
	 */
	protected $sideloadObjects;
	
	
	public function serialize(array $objects, Metamodel $metaModel, $isCollection) {
		$result = array();
		
//		$task = $objects[0];
//		$getter = 'getName';
//		\TYPO3\Flow\var_dump($task->$getter());
//		die();
		
		if ($isCollection) {
			$resourceName = $metaModel->getResourceName();
			$result[$resourceName] = $this->serializeCollection($objects, $metaModel);
		} else {
			$resourceNameSingular = $metaModel->getResourceNameSingular();
			$result[$resourceNameSingular] = $this->serializeObject($objects[0], $metaModel);
		}
		
		return json_encode((object)$result);
	}
	
	
	protected function serializeCollection(array $objects, Metamodel $metaModel) {
		$result = array();
		
		foreach ($objects as $object) {
			$result[] = $this->serializeObject($object, $metaModel);
		}
		
		return $result;
	}
	
	protected function serializeObject($object, Metamodel $metaModel) {
		$result = array();
		
			// add id 
		$result['id'] = $this->persistenceManager->getIdentifierByObject($object);
				
			// add properties with values
		foreach ((array) $metaModel->getProperties() as $property) {
			$getterName = 'get' . ucfirst($property->getName());
			// TODO: use TypeConverters
			$value = $object->$getterName();
			
				// only include in result if has value
			if (isset($value)) {
				$result[$property->getName()] = $value; 
			}
		}
		
			// add associations
		foreach ((array) $metaModel->getAssociations() as $association) {
			$getterName = 'get' . ucfirst($association->getEmberName());
			$associatedObjects = $object->$getterName();
			
				// only include in result if contains an object
			if (isset($associatedObjects)) {
				$result[$association->getName()] = $this->serializeAssociation($associatedObjects, $association); 
			}
		}
		
		return $result;
		
	}
	
	
	protected function serializeAssociation(array $objects, Association $association) {
		$result = array();
		
		$associationFlowModelName = $association->getFlowModelName();
		$associationMetaModel = $this->modelReflectionService->findByFlowModelName($associationFlowModelName);
		
		if ($association->getSideload()) {
			foreach ($objects as $object) {
				$id = $this->persistenceManager->getIdentifierByObject($object);
				$this->sideloadObjects['$associationFlowModelName'][$id] = $object;
			}
		} elseif ($association->getEmbedded === "always" || $association->getEmbedded === "load") {
			foreach ($objects as $object) {
				$result[] = $this->serializeObject($object, $associationMetaModel);
			}
		} else {
			foreach ($objects as $object) {
					// add id
				$result[] = $this->persistenceManager->getIdentifierByObject($object);
			}
		}
		
		return $result;
	}

	

}

?>