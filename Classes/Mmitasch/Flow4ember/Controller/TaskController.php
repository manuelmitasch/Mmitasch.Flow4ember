<?php
namespace Mmitasch\Flow4ember\Controller;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Mmitasch.Flow4ember".   *
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Mvc\Controller\ActionController;
use Mmitasch\Flow4ember\Domain\Model\Task;

class TaskController extends ActionController {

	/**
	 * @Flow\Inject
	 * @var \Mmitasch\Flow4ember\Domain\Repository\TaskRepository
	 */
	protected $taskRepository;

	/**
	 * @return void
	 */
	public function indexAction() {
		$this->view->assign('tasks', $this->taskRepository->findAll());
	}

	/**
	 * @param \Mmitasch\Flow4ember\Domain\Model\Task $task
	 * @return void
	 */
	public function showAction(Task $task) {
		$this->view->assign('task', $task);
	}

	/**
	 * @return void
	 */
	public function newAction() {
	}

	/**
	 * @param \Mmitasch\Flow4ember\Domain\Model\Task $newTask
	 * @return void
	 */
	public function createAction(Task $newTask) {
		$this->taskRepository->add($newTask);
		$this->addFlashMessage('Created a new task.');
		$this->redirect('index');
	}

	/**
	 * @param \Mmitasch\Flow4ember\Domain\Model\Task $task
	 * @return void
	 */
	public function editAction(Task $task) {
		$this->view->assign('task', $task);
	}

	/**
	 * @param \Mmitasch\Flow4ember\Domain\Model\Task $task
	 * @return void
	 */
	public function updateAction(Task $task) {
		$this->taskRepository->update($task);
		$this->addFlashMessage('Updated the task.');
		$this->redirect('index');
	}

	/**
	 * @param \Mmitasch\Flow4ember\Domain\Model\Task $task
	 * @return void
	 */
	public function deleteAction(Task $task) {
		$this->taskRepository->remove($task);
		$this->addFlashMessage('Deleted a task.');
		$this->redirect('index');
	}

}

?>