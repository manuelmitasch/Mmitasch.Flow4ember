<?php
namespace Mmitasch\Flow4ember\Routing;

use TYPO3\Flow\Annotations as Flow;

/**
 */
class ResourceNameRoutePart extends \TYPO3\Flow\Mvc\Routing\DynamicRoutePart {

	/**
	 * @Flow\Inject
	 * @var \Mmitasch\Flow4ember\Service\ModelReflectionServiceInterface
	 */
	protected $modelReflectionService;
	
	/**
	 * @var string
	 */
	protected $packageKey;
	
	/**
	 * Constructor
	 * Sets needed packageKey based on it's php namespace
	 */
	function __construct() {
		if (!isset($this->packageKey)) {
			$tokens = explode('\\', get_class($this));
			$packageNamespace = trim($tokens[0]);
			$packageName = trim($tokens[1]);
			$this->packageKey = $packageNamespace . '.' . $packageName;
		}
	}

	/**
	 * @param string $value
	 * @return boolean
	 */
	protected function matchValue($value) {
		if ($value === NULL || $value === '' || !$this->modelReflectionService->hasResourceName($value, $this->packageKey)) return FALSE;
		else {
			$this->value = $value;
			$this->setName('resourceName');
			return TRUE;
		}
	}

}

?>