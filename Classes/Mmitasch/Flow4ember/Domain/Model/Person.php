<?php
namespace Mmitasch\Flow4ember\Domain\Model;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Mmitasch.Flow4ember".   *
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow,
	Doctrine\ORM\Mapping as ORM,
	Mmitasch\Flow4ember\Annotations as Ember;

/**
 * Exists for testing purposes only.
 * 
 * @Flow\Entity
 * @Ember\Resource(name="people")
 */
class Person {

	/**
	 * @var string
	 */
	protected $name;
	
	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @param string $name
	 * @return void
	 */
	public function setName($name) {
		$this->name = $name;
	}
	
}
?>