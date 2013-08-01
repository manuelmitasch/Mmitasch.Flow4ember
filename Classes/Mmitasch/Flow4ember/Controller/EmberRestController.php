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
class EmberRestController extends \TYPO3\Flow\Mvc\Controller\RestController {

	/**
	 * Resource name
	 * If set, will be used to retrieve according Metamodel.
	 * If not set will be retrieved from arguments (route part).
	 * 
	 * @var string 
	 */
	protected $resourceName;
	
	/**
	 * Flow model name of resource
	 * If set, will be used to retrieve according Metamodel
	 * If not set (eg. by inheriting class), will not be used.
	 * 
	 * @var string
	 */
	protected $flowModelName;
	
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
	 * Determines the action method and assures that the method exists.
	 *
	 * @return string The action method name
	 * @throws \TYPO3\Flow\Mvc\Exception\NoSuchActionException if the action specified in the request object does not exist (and if there's no default action either).
	 */
	protected function resolveActionMethodName() {
		
			// choose action (if not other specified through config)
		if ($this->request->getControllerActionName() === 'index') {
			if (!$this->request->hasArgument('resourceName')) {
						$this->throwStatus(400, NULL, 'No resource specified');
			} 
			
			$actionName = 'index';
			switch ($this->request->getHttpRequest()->getMethod()) {
				case 'HEAD':
				case 'GET' :
					$actionName = ($this->request->hasArgument('resource')) ? 'show' : 'list';
				break;
				case 'POST' :
					$actionName = 'create';
				break;
				case 'PUT' :
					$actionName = 'update';
				break;
				case 'DELETE' :
					$actionName = 'delete';
				break;
				case 'OPTION' :
					$actionName = 'option';
				break;
			}
			$this->request->setControllerActionName($actionName);
		}
		return parent::resolveActionMethodName();
	}
	
	/**
	 * Initializes the controller before invoking an action method.
	 *
	 * @return void
	 */
	protected function initializeAction() {
		if ($this->resourceName === NULL) {
			if ($this->flowModelName !== NULL) {
				// resourceName is not set but flowModelName is set (eg. by inheriting class)
				$this->metaModel = $this->modelReflectionService->findByFlowModelName($this->flowModelName);
			} else {
				// resourceName is not set (eg. by inheriting class) => find from arguments (route part)
				if ($this->request->hasArgument("resourceName")) {
					$this->resourceName = $this->request->getArgument('resourceName');
					$this->metaModel = $this->modelReflectionService->findByResourceName($this->resourceName);
				} else {
					$this->throwStatus(500, NULL, 'No resource name found.');
				}
			} 
		}
		else {
			// resourceName is set
			$this->metaModel = $this->modelReflectionService->findByResourceName($this->resourceName);
		}
		
			// check if repository exists
		if ($this->metaModel->getRepository() === NULL) {
			$this->throwStatus(500, NULL, 'No repository found for model with resource name: ' . $this->metaModel->getResourceName() . '.');
		}
		
		if ($this->request->hasArgument('resourceId')) {
				// set model id in request argument
			$this->request->setArgument('model', array('__identity' => $this->request->getArgument('resourceId')));
				// set data type for property mapper
			$this->arguments->addNewArgument('model', $this->metaModel->getFlowModelName(), TRUE); 
		}
		
	}
	

	/**
	 * Allow creation of resources in createAction()
	 *
	 * @return void
	 */
	public function initializeCreateAction() {
		$propertyMappingConfiguration = $this->arguments['model']->getPropertyMappingConfiguration();
		$propertyMappingConfiguration->setTypeConverterOption('TYPO3\Flow\Property\TypeConverter\PersistentObjectConverter', \TYPO3\Flow\Property\TypeConverter\PersistentObjectConverter::CONFIGURATION_CREATION_ALLOWED, TRUE);
		$propertyMappingConfiguration->allowAllProperties();
	}

	/**
	 * Allow modification of resources in updateAction()
	 *
	 * @return void
	 */
	public function initializeUpdateAction() {
		$propertyMappingConfiguration = $this->arguments['model']->getPropertyMappingConfiguration();
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
		$models = $this->metaModel->getRepository()->findAll()->toArray();
		$this->view->assign('content', $models);
		$this->view->assign('isCollection', TRUE);
	}

	/**
	 * Shows a resource/model
	 *
	 * @param object $model the model
	 * @return void
	 */
	public function showAction($model) {
		$this->view->assign('content', $model);
	}

	/**
	 * Create a model
	 *
	 * @param string $model The new model to add
	 * @return void
	 */
	public function createAction($model) {
		$newModel = $this->metaModel->getRepository()->add($model);
		$this->persistenceManager->persistAll();
		$this->response->setStatus(201);
		$this->view->assign('content', $newModel);
	}

	/**
	 * Update the given model
	 *
	 * @param string $model The task to update
	 * @return void
	 */
	public function updateAction($model) {
		$this->metaModel->getRepository()->update($model);
		$this->response->setStatus(204);
	}

	/**
	 * Removes the given model
	 *
	 * @param string $model The model to delete
	 * @return string
	 */
	public function deleteAction($model) {
		$this->metaModel->getRepository()->remove($model);
		$this->response->setStatus(204);
		return '';
	}
	
	/**
	 * Detect the supported request methods for a single order and set the "Allow" header accordingly (This is invoked on OPTION requests)
	 *
	 * @return string An empty string in order to prevent the view from rendering the action
	 */
	public function optionAction() {
		$allowedMethods = array('GET');
		$uuid = $this->request->getArgument('resourceId');
		$model = $this->metaModel->getRepository()->findByIdentifier($uuid);
		if ($model === NULL) {
			$this->throwStatus(404, NULL, 'The model "' . $uuid . '" does not exist');
		}
		$this->response->setHeader('Allow', implode(', ', $allowedMethods));
		$this->response->setStatus(204);
		return '';
	}
	
}
?>