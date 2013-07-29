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
 * Defines if an association should be embedded and it's embed type
 *
 * @Annotation
 * @Target("PROPERTY")
 */
final class Embedded {

	/**
	 * Ember type
	 * @var string
	 */
	public $type;
	
	
	/**
	 * Get ember type 
	 * @return string
	 */
	public function getType() {
		return $this->type;
	}


}

?>