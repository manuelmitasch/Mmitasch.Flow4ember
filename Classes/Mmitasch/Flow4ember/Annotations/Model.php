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
 * Marks domain model as an ember model.
 * NO rest api will be created for this class.
 * Thus, it can only be used in embedded associations in Ember.
 *
 * @Annotation
 * @Target("CLASS")
 */
final class Model {
	
	/**
	 * Ember model name
	 * @var string
	 */
	public $name;
	
	/**
	 * Get ember model name 
	 * @return string
	 */
	public function getName() {
		return ucfirst($this->name);
	}
	
}

?>