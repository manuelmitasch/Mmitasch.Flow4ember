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
	 * @return type
	 */
	public function serialize ($objects, $isCollection);

}

?>
