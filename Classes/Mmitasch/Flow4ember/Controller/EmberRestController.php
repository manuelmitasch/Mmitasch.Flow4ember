<?php 
namespace Mmitasch\Flow4ember\Controller;

/**
 * This script belongs to the TYPO3 Flow package "Mmitasch.Flow4ember".   *
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

/**
 * An action controller for RESTful web services
 */
class EmberRestController extends \TYPO3\Flow\Mvc\Controller\ActionController {

	/**
	 * The current request
	 * @var \TYPO3\Flow\Mvc\ActionRequest
	 */
	protected $request;

	/**
	 * The response which will be returned by this action controller
	 * @var \TYPO3\Flow\Http\Response
	 */
	protected $response;
	
	/**
	 * @var string
	 */
	protected $defaultViewObjectName = 'Mmitasch\Flow4ember\View\EmberView';

	/**
	 * @var \Mmitasch\Flow4ember\Domain\Model\Metamodel 
	 */
	protected $metaModel;

	/**
	 * @Flow\Inject
	 * @var \Mmitasch\Flow4ember\Service\ModelReflectionService
	 */
	protected $modelReflectionService;
	
	
	/**
	 * Determines the action method and assures that the method exists.
	 *
	 * @return string The action method name
	 * @throws \TYPO3\Flow\Mvc\Exception\NoSuchActionException if the action specified in the request object does not exist (and if there's no default action either).
	 */
	protected function resolveActionMethodName() {
		
			// get corresponding metamodel
		$arguments = $this->request->getArguments();
		$this->metaModel = $this->modelReflectionService->findByResourceName($arguments['resourceName']);
		
		if ($this->metaModel->getRepository() === NULL) {
			$this->throwStatus(500, NULL, 'No repository found for model with resource name: ' . $this->metaModel->getResourceName() . '.');
		}
		
			// choose action
		if ($this->request->getControllerActionName() === 'index') {
			$actionName = 'index';
			switch ($this->request->getHttpRequest()->getMethod()) {
				case 'HEAD':
				case 'GET' :
					$actionName = ($this->request->hasArgument('resourceId')) ? 'show' : 'list';
				break;
				case 'POST' :
					$actionName = 'create';
				break;
				case 'PUT' :
					if (!$this->request->hasArgument('resourceName')) {
						$this->throwStatus(400, NULL, 'No resource specified');
					}
					$actionName = 'update';
				break;
				case 'DELETE' :
					if (!$this->request->hasArgument('resourceName')) {
						$this->throwStatus(400, NULL, 'No resource specified');
					}
					$actionName = 'delete';
				break;
			}
			$this->request->setControllerActionName($actionName);
		}
		return parent::resolveActionMethodName();
	}

	/**
	 * Allow creation of resources in createAction()
	 *
	 * @return void
	 */
	public function initializeCreateAction() {
		$propertyMappingConfiguration = $this->arguments['resourceName']->getPropertyMappingConfiguration();
		$propertyMappingConfiguration->setTypeConverterOption('TYPO3\Flow\Property\TypeConverter\PersistentObjectConverter', \TYPO3\Flow\Property\TypeConverter\PersistentObjectConverter::CONFIGURATION_CREATION_ALLOWED, TRUE);
		$propertyMappingConfiguration->allowAllProperties();
	}

	/**
	 * Allow modification of resources in updateAction()
	 *
	 * @return void
	 */
	public function initializeUpdateAction() {
		$propertyMappingConfiguration = $this->arguments['resourceName']->getPropertyMappingConfiguration();
		$propertyMappingConfiguration->setTypeConverterOption('TYPO3\Flow\Property\TypeConverter\PersistentObjectConverter', \TYPO3\Flow\Property\TypeConverter\PersistentObjectConverter::CONFIGURATION_MODIFICATION_ALLOWED, TRUE);
		$propertyMappingConfiguration->allowAllProperties();
	}

	/**
	 * Redirects the web request to another uri.
	 *
	 * NOTE: This method only supports web requests and will throw an exception
	 * if used with other request types.
	 *
	 * @param mixed $uri Either a string representation of a URI or a \TYPO3\Flow\Http\Uri object
	 * @param integer $delay (optional) The delay in seconds. Default is no delay.
	 * @param integer $statusCode (optional) The HTTP status code for the redirect. Default is "303 See Other"
	 * @return void
	 * @throws \TYPO3\Flow\Mvc\Exception\StopActionException
	 * @api
	 */
	protected function redirectToUri($uri, $delay = 0, $statusCode = 303) {
			// the parent method throws the exception, but we need to act afterwards
			// thus the code in catch - it's the expected state
		try {
			parent::redirectToUri($uri, $delay, $statusCode);
		} catch (\TYPO3\Flow\Mvc\Exception\StopActionException $exception) {
			if ($this->request->getFormat() === 'json') {
				$this->response->setContent('');
			}
			throw $exception;
		}
	}
	
	
	
	/**
	 * List all resources/models
	 *
	 * @return void
	 */
	public function listAction() {
		$resourceRecords = $this->metaModel->getRepository()->findAll()->toArray();
		$this->view->assign('content', $resourceRecords);
		$this->view->assign('metaModel', $this->metaModel);
		$this->view->assign('isCollection', TRUE);
	}

	/**
	 * Shows a resource/model
	 *
	 * @param string $resourceId The model to show
	 * @return void
	 */
	public function showAction($resourceId) {
		$resource = array($this->metaModel->getRepository()->findByIdentifier($resourceId));
		$this->view->assign('content', $resource);
		$this->view->assign('metaModel', $this->metaModel);
	}

	/**
	 * Create a model
	 *
	 * @param string $model A new model to add
	 * @return void
	 */
	public function createAction($resourceId) {
		$this->metaModel->getRepository()->add($resourceId);
		$this->persistenceManager->persistAll();
		$this->response->setStatus(201);
		$this->view->assign('content', $resourceId);
		$this->view->assign('metaModel', $this->metaModel);
	}

	/**
	 * Update the given model
	 *
	 * @param string $resourceId The task to update
	 * @return void
	 */
	public function updateAction($resourceId) {
		$this->metaModel->getRepository()->update($resourceId);
		$this->response->setStatus(204);
	}

	/**
	 * Removes the given model
	 *
	 * @param string $resourceId The model to delete
	 * @return string
	 */
	public function deleteAction($resourceId) {
		$this->metaModel->getRepository()->remove($resourceId);
		$this->response->setStatus(204);
		return '';
	}
	
	
}
?>