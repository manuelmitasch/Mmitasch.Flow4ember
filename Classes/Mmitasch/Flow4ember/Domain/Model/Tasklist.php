<?php
namespace Mmitasch\Flow4ember\Domain\Model;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Mmitasch.Flow4ember".   *
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM,
	Mmitasch\Flow4ember\Annotations as Ember;

/**
 * Exists for testing purposes only.
 * @Flow\Entity
 * @Ember\Resource(name="lists")
 */
class Tasklist {

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var \Doctrine\Common\Collections\Collection<\Mmitasch\Flow4ember\Domain\Model\Task>
	 * @ORM\OneToMany(mappedBy="list")
	 * @Ember\Sideload
	 */
	protected $tasks;


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
	 * @return \Doctrine\Common\Collections\Collection<\Mmitasch\Flow4ember\Domain\Model\Task>
	 */
	public function getTasks() {
		return $this->tasks;
	}

	/**
	 * @param \Mmitasch\Flow4ember\Domain\Model\Task $task
	 * @return void
	 */
	public function addTask($task) {
		$this->tasks->add($task);
	}

}
?>