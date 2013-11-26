<?php
namespace Mmitasch\Flow4ember\Annotations;

/*                                                                        *
 * This script belongs to the TYPO3 Flow framework.                       *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

/**
 * Marks an object as an ember resource.
 * Thus a rest api should be created for this class.
 *
 * @Annotation
 * @Target("CLASS")
 */
final class Resource {
	/**
	 * Ember model name
	 * @var string
	 */
	public $modelName;
	
	/**
	 * Get ember model name 
	 * @return string
	 */
	public function getModelName() {
		return ucfirst($this->modelName);
	}
}

?>