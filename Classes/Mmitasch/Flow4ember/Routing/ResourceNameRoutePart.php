<?php
namespace Mmitasch\Flow4ember\Routing;

use TYPO3\Flow\Annotations as Flow;

/**
 */
class ResourceNameRoutePart extends \TYPO3\Flow\Mvc\Routing\DynamicRoutePart {

	/**
	 * @Flow\Inject
	 * @var \Mmitasch\Flow4ember\Service\ModelReflectionService
	 */
	protected $modelReflectionService;
	

	/**
	 * @param string $value
	 * @return boolean
	 */
	protected function matchValue($value) {
		if ($value === NULL || $value === '' || !$this->modelReflectionService->hasResourceName($value)) return FALSE;
		else {
			$this->value = $value;
			$this->setName('resourceName');
			return TRUE;
		}
	}

}

?>