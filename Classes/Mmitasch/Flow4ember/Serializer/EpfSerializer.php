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
class EpfSerializer implements SerializerInterface {

	
	/**
	 * @var \TYPO3\Flow\Log\SystemLoggerInterface
	 */
	protected $systemLogger;
	
	/**
	 * @param \TYPO3\Flow\Log\SystemLoggerInterface $systemLogger
	 * @return void
	 */
	public function injectSystemLogger(\TYPO3\Flow\Log\SystemLoggerInterface $systemLogger) {
		$this->systemLogger = $systemLogger;
	}
	
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
	 * The Metamodels possibly needed for serialization
	 * 
	 * @var array<\Mmitasch\Flow4ember\Domain\Model\Metamodel>
	 */
	protected $metaModels;
	
	/**
	 * Constructor
	 * 
	 * @param array<\Mmitasch\Flow4ember\Domain\Model\Metamodel> $metaModels
	 */
	function __construct($metaModels) {
		$this->metaModels = $metaModels;
		
//		\TYPO3\Flow\var_dump($metaModels);
	}


	/**
	 * Used for serializing inputted objects
	 * 
	 * @param mixed $objects Passed objects (can be array of objects or single object)
	 * @param boolean $isCollection
	 * @param string $clientId
	 * @return type
	 */
	public function serialize ($objects, $isCollection, $clientId) {
		$result = array();
		if (empty($objects)) {
			return json_encode((object) $result);
		}
		$flowModelName = is_array($objects) ? get_class($objects[0]) : get_class($objects);
		$metaModel = $this->findByFlowModelName($flowModelName);
		
		if ($isCollection) {
			$resourceName = $metaModel->getResourceName();
			$result[$resourceName] = $this->serializeCollection($objects, $metaModel);
		} else {
			$resourceNameSingular = $metaModel->getResourceNameSingular();
			$result[$resourceNameSingular] = $this->serializeObject($objects, $metaModel);
			
			if (isset($clientId)) {
				$result[$resourceNameSingular]['client_id'] = $clientId;
			}
		}
		
		if (!empty($this->sideloadObjects)) {
			foreach ($this->sideloadObjects as $flowModelName => $objects) {
				$associationMetaModel = $this->findByFlowModelName($flowModelName);
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
		
		return json_encode((object) $result);
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
				$propertyPayloadName = $this->getPayloadName($property->getName());
				$result[$propertyPayloadName] = $value; 
			}
		}
		
			// add associations
		foreach ((array) $metaModel->getAssociations() as $association) {
			$getterName = 'get' . ucfirst($association->getFlowName());
			$associatedObjects = $object->$getterName();
			
				// only include in result if contains an object
			if (isset($associatedObjects) && !empty($associatedObjects)) {
					// define name of association property
				$name = $this->getPayloadName($association->getEmberName(), $association->getEmberType()); 
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
		$associationMetaModel = $this->findByFlowModelName($associationFlowModelName);
		
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

	
	
	/**
	 * Deserialize string to array with properties that will be used by the datamapper for the creation of models
	 * 
	 * @param type $data
	 * @param \Mmitasch\Flow4ember\Domain\Model\Metamodel $metaModel
	 * @return array Property array for datamapper
	 */
	public function deserialize ($data, $metaModel) {
		$result = array();
			$this->systemLogger->log("Data: " . print_r($data, TRUE), LOG_INFO); // TODO remove

		if (array_key_exists('id', $data)) {
			$result['__identity'] = $data['id'];
		}
		
		// Add properties
		foreach ((array) $metaModel->getProperties() as $property) {
			$propertyPayloadName = $this->getPayloadName($property->getName());

			if (array_key_exists($propertyPayloadName, $data)) {
					// TODO: use typconverter function
				$result[$property->getName()] = $data[$propertyPayloadName];
			}
		}
		
		// Add associations
		foreach ((array) $metaModel->getAssociations() as $association) {
			$associationPayloadName = $this->getPayloadName($association->getEmberName(), $association->getEmberType());
			$this->systemLogger->log("Association: " . $association->getEmberName() . "; PayloadName: " . $associationPayloadName . "; Type: " . $association->getEmberType(), LOG_INFO);

			
			$this->systemLogger->log("Payload name: " . $associationPayloadName, LOG_INFO); // TODO remove

			if(array_key_exists($associationPayloadName, $data)) {
				$result[$association->getFlowName()] = $data[$associationPayloadName];
			}
		}

		// TODO remove Logging
		ob_start();
		var_dump($result);
		$x = ob_get_clean();
		$this->systemLogger->log("Result: " .$x, LOG_INFO);

		
		return $result;
	}
	
	
	/**
	 * Get payloadname for properties and associations based on ember name.
	 *
	 * Example 1 (Property):
	 *   Given: "homeAddress"
	 *   Returned: "home_address"
	 * 
	 * Example 2 (Association, belongsTo): 
	 *   Given: "homeAddress", "belongsTo"
	 *	 Returned: "home_address_id"
	 * 
	 * Example 2 (Association, hasMany):
	 *   Given: "phoneNumbers", "hasMany"
	 *   Returned: "phone_number_ids"
	 * 
	 * @param string $name
	 * @param string $type
	 * @return string
	 */
	protected function getPayloadName ($name, $type='') {
		if ($type === 'belongsTo') {
			return NamingUtility::decamelize($name) . '_id';
		} elseif ($type === 'hasMany') {
			return NamingUtility::singularize(NamingUtility::decamelize($name)) . '_ids';
		}
		return NamingUtility::decamelize($name);
	}
	
	
	/**
	 * Get Metamodel by Flow model name
	 * 
	 * @param string $flowModelName
	 * @return \Mmitasch\Flow4ember\Domain\Model\Metamodel
	 */
	protected function findByFlowModelName($flowModelName) {
		if (!isset($this->metaModels[$flowModelName])) {
			throw new \RuntimeException('Could not find Metamodel for class: ' . $flowModelName . '.', 1375148357); 
		}
		return $this->metaModels[$flowModelName];
	}
	
}

?>