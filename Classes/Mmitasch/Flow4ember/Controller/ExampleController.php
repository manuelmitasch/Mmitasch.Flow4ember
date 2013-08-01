<?php
namespace Mmitasch\Flow4ember\Controller;

class ExampleController extends EmberRestController {
	
//	protected $resourceName = "tasks";
	protected $flowModelName = "Mmitasch\Flow4ember\Domain\Model\Task";
	
	
	/**
	 * List all resources/models
	 *
	 * @return void
	 */
	public function orderedAction() {
		$resourceRecords = $this->metaModel->getRepository()->findTasksOrdered()->toArray();
		$this->view->assign('content', $resourceRecords);
		$this->view->assign('metaModel', $this->metaModel);
		$this->view->assign('isCollection', TRUE);
	}
}

?>
