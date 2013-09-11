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
 * 
 * @Flow\Entity
 * @Ember\Resource
 */
class Tasklist {

	 public function __construct() {
        $this->tasks = new \Doctrine\Common\Collections\ArrayCollection();
    }
	
	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var \Doctrine\Common\Collections\Collection<\Mmitasch\Flow4ember\Domain\Model\Task>
	 * @ORM\OneToMany(mappedBy="list", cascade={"all"})
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
	 * @param \Doctrine\Common\Collections\Collection<\Mmitasch\Flow4ember\Domain\Model\Task> $tasks
	 */
	public function setTasks($tasks) {
		$this->tasks = $tasks;
	}

	/**
	 * @param \Mmitasch\Flow4ember\Domain\Model\Task $task
	 * @return void
	 */
	public function addTask($task) {
		$task->setList($this);
		$this->tasks->add($task);
	}

}
?>