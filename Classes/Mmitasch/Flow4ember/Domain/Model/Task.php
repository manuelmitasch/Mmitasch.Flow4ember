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
 * @Ember\Resource
 */
class Task {

	/**
	 * @var string
	 */
	protected $name;
	
	/**
	 * @var \Mmitasch\Flow4ember\Domain\Model\Tasklist
	 * @ORM\ManyToOne(inversedBy="tasks")
	 */
	protected $list;
	
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
	
	/**
	 * @var \Mmitasch\Flow4ember\Domain\Model\Tasklist
	 * @return type
	 */
	public function getList() {
		return $this->list;
	}

	/**
	 * @param \Mmitasch\Flow4ember\Domain\Model\Tasklist $list
	 * @return void
	 */
	public function setList(\Mmitasch\Flow4ember\Domain\Model\Tasklist $list) {
		$this->list = $list;
	}


	
}
?>