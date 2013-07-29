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
class EmberView extends \TYPO3\Flow\Mvc\View\AbstractView {

	/**
	 * Transforms the value view variable to a serializable
	 * object representation and JSON encodes the result.
	 *
	 * @return string The JSON encoded variables
	 */
	public function render() {
//		$this->controllerContext->getResponse()->setHeader('Content-Type', 'application/json');

//	\TYPO3\Flow\var_dump($this->variables);
//		if (array_key_exists('isCollection', $this->variables)) {
//			
//		}
//		$isCollection = (array_key_exists('isCollection', $this->variables) && $this->variables['isCollection'] === TRUE);
		$isCollection = FALSE;
		$content = $this->variables['content'];

		$serializer = new EmberSerializer();
		return $serializer->serialize($content, $this->variables['metaModel'], $isCollection);
	}

}

?>