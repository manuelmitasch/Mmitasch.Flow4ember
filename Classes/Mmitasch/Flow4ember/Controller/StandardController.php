<?php
namespace Mmitasch\Flow4Ember\Controller;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Mmitasch.Flow4Ember".   *
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

class StandardController extends \TYPO3\Flow\Mvc\Controller\ActionController {

	/**
	 * @Flow\Inject
	 * @var \Mmitasch\Flow4ember\Service\ModelReflectionService
	 */
	protected $modelReflectionService;
	
	
	/**
	 * @return void
	 */
	public function indexAction() {
		$this->view->assign('foos', array(
			'bar', 'baz'
		));
		
		
		if ($this->modelReflectionService instanceof \TYPO3\Flow\Object\DependencyInjection\DependencyProxy) {
                        $this->modelReflectionService->_activateDependency();
        }
		
	}

}

?>