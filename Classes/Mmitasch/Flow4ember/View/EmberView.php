<?php
namespace Mmitasch\Flow4ember\View;

/**
 * This script belongs to the TYPO3 Flow package "Mmitasch.Flow4ember".   *
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow,
	Mmitasch\Flow4ember\Serializer\EmberSerializer;

/**
 */
class EmberView extends \TYPO3\Flow\Mvc\View\AbstractView implements EmberViewInterface {

	/**
	 * @var \Mmitasch\Flow4ember\Serializer\SerializerInterface
	 */
	protected $serializer;
	
	/**
	 * Transforms the value view variable to a serializable
	 * object representation and JSON encodes the result.
	 *
	 * @return string The JSON encoded variables
	 */
	public function render() {
		$this->controllerContext->getResponse()->setHeader('Content-Type', 'application/json'); // TODO: uncomment
		$isCollection = (array_key_exists('isCollection', $this->variables) && $this->variables['isCollection'] === TRUE);
		$clientId = (array_key_exists('clientId', $this->variables)) ? $this->variables['clientId'] : NULL;
		
		return $this->serializer->serialize($this->variables['content'], $isCollection, $clientId);
	}
	
	/**
	 * @param \Mmitasch\Flow4ember\Serializer\SerializerInterface $serializer
	 */
	public function setSerializer(\Mmitasch\Flow4ember\Serializer\SerializerInterface $serializer) {
		$this->serializer = $serializer;
	}




}

?>