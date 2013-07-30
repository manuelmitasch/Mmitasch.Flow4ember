<?php
namespace Mmitasch\Flow4ember\Controller;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Mmitasch.Flow4ember".   *
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Mvc\Controller\ActionController;
use Mmitasch\Flow4ember\Domain\Model\Tasklist;

class TasklistController extends ActionController {

	/**
	 * @Flow\Inject
	 * @var \Mmitasch\Flow4ember\Domain\Repository\TasklistRepository
	 */
	protected $tasklistRepository;
	
	/**
	 * @Flow\Inject
	 * @var \Mmitasch\Flow4ember\Domain\Repository\TaskRepository
	 */
	protected $taskRepository;

	/**
	 * @return void
	 */
	public function indexAction() {
		$this->view->assign('tasklists', $this->tasklistRepository->findAll());
	}

	/**
	 * @param \Mmitasch\Flow4ember\Domain\Model\Tasklist $tasklist
	 * @return void
	 */
	public function showAction(Tasklist $tasklist) {
		$this->view->assign('tasklist', $tasklist);
	}

	/**
	 * @return void
	 */
	public function newAction() {
	}

	/**
	 * @param \Mmitasch\Flow4ember\Domain\Model\Tasklist $newTasklist
	 * @return void
	 */
	public function createAction(Tasklist $newTasklist) {
		$this->tasklistRepository->add($newTasklist);
		$this->addFlashMessage('Created a new tasklist.');
		$this->redirect('index');
	}

	/**
	 * @param \Mmitasch\Flow4ember\Domain\Model\Tasklist $tasklist
	 * @return void
	 */
	public function editAction(Tasklist $tasklist) {
		$this->view->assign('tasklist', $tasklist);
		$this->view->assign('tasks', $this->taskRepository->findAll());
		
//		\TYPO3\Flow\var_dump( $this->taskRepository->findAll()->toArray());
//		die();
	}

	/**
	 * @param \Mmitasch\Flow4ember\Domain\Model\Tasklist $tasklist
	 * @return void
	 */
	public function updateAction(Tasklist $tasklist) {
		$this->tasklistRepository->update($tasklist);
		$this->addFlashMessage('Updated the tasklist.');
		$this->redirect('index');
	}

	/**
	 * @param \Mmitasch\Flow4ember\Domain\Model\Tasklist $tasklist
	 * @return void
	 */
	public function deleteAction(Tasklist $tasklist) {
		$this->tasklistRepository->remove($tasklist);
		$this->addFlashMessage('Deleted a tasklist.');
		$this->redirect('index');
	}

}

?>