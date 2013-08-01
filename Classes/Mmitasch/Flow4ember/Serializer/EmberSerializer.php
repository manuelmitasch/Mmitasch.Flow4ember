<?php
namespace Mmitasch\Flow4ember\Serializer;

/**
 * This script belongs to the TYPO3 Flow package "Mmitasch.Flow4ember".   *
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow,
	Mmitasch\Flow4ember\Domain\Model\Metamodel,
	Mmitasch\Flow4ember\Domain\Model\Association,
	Mmitasch\Flow4ember\Utility\NamingUtility;

/**
 * Serializer that conforms to Ember Data RESTAdapter conventions
 */
class EmberSerializer implements SerializerInterface {

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
	
	/**
	 * Used for serializing inputted objects
	 * 
	 * @param mixed $objects Passed objects (can be array of objects or single object)
	 * @param boolean $isCollection
	 * @return type
	 */
	public function serialize ($objects, $isCollection) {
		$result = array();
		$flowModelName = is_array($objects) ? get_class($objects[0]) : get_class($objects);
		$metaModel = $this->modelReflectionService->findByFlowModelName($flowModelName);
		
		if ($isCollection) {
			$resourceName = $metaModel->getResourceName();
			$result[$resourceName] = $this->serializeCollection($objects, $metaModel);
		} else {
			$resourceNameSingular = $metaModel->getResourceNameSingular();
			$result[$resourceNameSingular] = $this->serializeObject($objects, $metaModel);
		}
		
		if (!empty($this->sideloadObjects)) {
			foreach ($this->sideloadObjects as $flowModelName => $objects) {
				$associationMetaModel = $this->modelReflectionService->findByFlowModelName($flowModelName);
				$associationResourceName = $associationMetaModel->getResourceName();
				
				if (is_array($objects)) {
					foreach ($objects as $object) {
						$result[$associationResourceName][] = $this->serializeObject($object, $associationMetaModel);
					}
				} else {
					$result[$associationResourceName][] = $this->serializeObject($objects, $associationMetaModel);
				}
				
				
			}
		}
		
		return json_encode((object)$result);
	}
	
	
	/**
	 * Used to serialize a collection of models
	 * 
	 * @param array $objects
	 * @param \Mmitasch\Flow4ember\Domain\Model\Metamodel $metaModel
	 * @return type
	 */
	protected function serializeCollection(array $objects, Metamodel $metaModel) {
		$result = array();
		
		foreach ($objects as $object) {
			$result[] = $this->serializeObject($object, $metaModel);
		}
		
		return $result;
	}
	
	/**
	 * Used to serialize a single model with it's properties and associations.
	 * 
	 * @param type $object
	 * @param \Mmitasch\Flow4ember\Domain\Model\Metamodel $metaModel
	 * @return type
	 */
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
				$propertyName = NamingUtility::decamelize($property->getName());
				$result[$propertyName] = $value; 
			}
		}
		
			// add associations
		foreach ((array) $metaModel->getAssociations() as $association) {
			$getterName = 'get' . ucfirst($association->getEmberName());
			$associatedObjects = $object->$getterName();
			
				// only include in result if contains an object
			if (isset($associatedObjects) && !empty($associatedObjects)) {
					// define name of association property
				if ($association->getIsCollection()) {
					if ($association->getEmbedded() === "always" || $association->getEmbedded() === "load") {
						$name = NamingUtility::decamelize($association->getEmberName()); // case: hasMany + embed association
					} elseif ($association->getSideload()) {
						$modelName = $this->modelReflectionService->findByFlowModelName($association->getFlowModelName())->getModelName();
						$name = NamingUtility::decamelize($modelName) . '_ids'; // case: hasMany + sideload association
						// TODO: check why ember uses this crazy naming convention, seems wrong (how to properly get from eg. tasks to task_ids)
					} else {
						$modelName = $this->modelReflectionService->findByFlowModelName($association->getFlowModelName())->getModelName();
						$name = NamingUtility::decamelize($modelName) . '_ids'; // case: hasMany (array of ids)
						// TODO: check why ember uses this crazy naming convention, seems wrong (how to properly get from eg. tasks to task_ids)
					}
				} else {
					$name = NamingUtility::decamelize($association->getEmberName()) . '_id'; // case: belongsTo
				}
				
				$result[$name] = $this->serializeAssociation($associatedObjects, $association); 
			}
		}
		
		return $result;
	}
	
	/**
	 * Used to serialize an association.
	 * 
	 * @param type $objects
	 * @param \Mmitasch\Flow4ember\Domain\Model\Association $association
	 * @return type
	 */
	protected function serializeAssociation($objects, Association $association) {
		$result = array();
		$associationFlowModelName = $association->getFlowModelName();
		$associationMetaModel = $this->modelReflectionService->findByFlowModelName($associationFlowModelName);
		
		if ($association->getIsCollection()) {
			 if ($association->getEmbedded() === "always" || $association->getEmbedded() === "load") {
				// case: hasMany + embed association
				foreach ($objects as $object) {
					$result[] = $this->serializeObject($object, $associationMetaModel);
				}
			} elseif ($association->getSideload()) {
				// case: hasMany + sideload association
				foreach ($objects as $object) {
					$id = $this->persistenceManager->getIdentifierByObject($object);
					$this->sideloadObjects[$associationFlowModelName][$id] = $object;
					$result[] = $this->persistenceManager->getIdentifierByObject($object);
				}
			} else {
				// case: hasMany (array of ids)
				foreach ($objects as $object) {
					$result[] = $this->persistenceManager->getIdentifierByObject($object); // add id
				}
			}
		} else {
			// case: belongsTo
			$result = $this->persistenceManager->getIdentifierByObject($objects);
			
			if ($association->getSideload()) {
				$this->sideloadObjects[$associationFlowModelName][] = $objects;
			}
		}
		return $result;
	}

	

}

?>