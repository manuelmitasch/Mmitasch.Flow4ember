<?php
namespace Mmitasch\Flow4ember\Serializer;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Mmitasch.Flow4ember".   *
 *                                                                        *
 *                                                                        */

/**
 * Interface for Serializers in Flow4ember package
 */
interface SerializerInterface {
	
	/**
	 * Used for serializing inputted objects
	 * 
	 * @param mixed $objects
	 * @param boolean $isCollection
	 * @param string $clientId
	 * @return type
	 */
	public function serialize ($objects, $isCollection, $clientId);
	
	/**
	 * Deserialize string to array with properties that will be used by the datamapper for the creation of models
	 * 
	 * @param type $data
	 * @param \Mmitasch\Flow4ember\Domain\Model\Metamodel $metaModel
	 * @return array Property array for datamapper
	 */
	public function deserialize ($data, $metaModel);
	
}

?>
