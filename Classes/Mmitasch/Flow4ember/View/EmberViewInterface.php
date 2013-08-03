<?php
namespace Mmitasch\Flow4ember\View;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Mmitasch.Flow4ember".   *
 *                                                                        *
 *                                                                        */

/**
 * Interface for Serializers in Flow4ember package
 */
interface EmberViewInterface {
	
	/**
	 * @param \Mmitasch\Flow4ember\Serializer\SerializerInterface $serializer
	 */
	public function setSerializer(\Mmitasch\Flow4ember\Serializer\SerializerInterface $serializer);

}

?>
