<?php 
namespace Mmitasch\Flow4ember\Controller;

/**
 * This script belongs to the TYPO3 Flow package "Mmitasch.Flow4ember".   *
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow,
 \Mmitasch\Flow4ember\Serializer\EpfSerializer;

/**
 * An action controller for RESTful web services EPF style
 */
class EpfRestController extends \TYPO3\Flow\Mvc\Controller\RestController {

	/**
	 * @var \TYPO3\Flow\Log\SystemLoggerInterface
	 */
	protected $systemLogger;
	
	/**
	 * @param \TYPO3\Flow\Log\SystemLoggerInterface $systemLogger
	 * @return void
	 */
	public function injectSystemLogger(\TYPO3\Flow\Log\SystemLoggerInterface $systemLogger) {
		$this->systemLogger = $systemLogger;
	}
	
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
	protected $defaultViewObjectName = 'Mmitasch\Flow4ember\View\EpfView';

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
	 * @var array
	 */
	protected $supportedMediaTypes = array('application/json');
	
	/**
	 * @var Mmitasch\Flow4ember\Serializer\SerializerInterface
	 */
	protected $serializer;
	
	function __construct() {
		$this->serializer = new EpfSerializer();
		parent::__construct();
	}
	
	
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
					$actionName = ($this->request->hasArgument('resourceId')) ? 'show' : 'list';
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
	 * Initializes the view before invoking an action method.
	 *
	 * @param \TYPO3\Flow\Mvc\View\ViewInterface $view The view to be initialized
	 * @return void
	 */
	protected function initializeView(\TYPO3\Flow\Mvc\View\ViewInterface $view) {
		if (method_exists($view, 'setSerializer')) {
			$view->setSerializer($this->serializer);	
		}
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
		
		$arguments = $this->request->getArguments();
		
		if (array_key_exists('resourceId', $arguments) && $this->actionMethodName !== 'deleteAction') {
			$resourceName = $this->metaModel->getResourceNameSingular();

			if (array_key_exists('id', $arguments[$resourceName]) && $this->request->getArgument('resourceId') !== $arguments[$resourceName]['id']) {
				$this->throwStatus(400, NULL, 'ResourceId in URL (' . $this->request->getArgument('resourceId') . ') is not the same as given in json hash (' . $arguments[$resourceName]['id'] . ').');
			}
			
				// set model id in request argument
			$arguments['model'] = array('__identity' => $this->request->getArgument('resourceId'));
				// set data type for model argument
			$this->arguments->addNewArgument('model', $this->metaModel->getFlowModelName(), TRUE); 
		}
		
		unset($arguments['resourceName']);
		$this->request->setArguments($arguments);
	}
	
	
	/**
	 * Allow creation of resources in createAction()
	 *
	 * @return void
	 */
	public function initializeCreateAction() {
			// set data type of model argument
		$this->arguments->addNewArgument('model', $this->metaModel->getFlowModelName(), TRUE);
		
		$propertyMappingConfiguration = $this->arguments['model']->getPropertyMappingConfiguration();
		$propertyMappingConfiguration->setTypeConverterOption(
				'TYPO3\Flow\Property\TypeConverter\PersistentObjectConverter',
				\TYPO3\Flow\Property\TypeConverter\PersistentObjectConverter::CONFIGURATION_CREATION_ALLOWED, TRUE);
		$propertyMappingConfiguration->allowAllProperties();
		
		foreach ((array) $this->metaModel->getAssociations() as $association) {
			$propertyMappingConfiguration->forProperty($association->getFlowName())->setTypeConverterOption(
					'TYPO3\Flow\Property\TypeConverter\PersistentObjectConverter',
					\TYPO3\Flow\Property\TypeConverter\PersistentObjectConverter::CONFIGURATION_CREATION_ALLOWED, TRUE);
			if ($association->getEmberType() !== 'belongsTo') {
				$propertyMappingConfiguration->forProperty($association->getFlowName() . '.*')->setTypeConverterOption(
					'TYPO3\Flow\Property\TypeConverter\PersistentObjectConverter',
					\TYPO3\Flow\Property\TypeConverter\PersistentObjectConverter::CONFIGURATION_CREATION_ALLOWED, TRUE);			
			}
		}
		
		$arguments = $this->request->getArguments();
		$resourceName = $this->metaModel->getResourceNameSingular();
		
		if (array_key_exists($resourceName, $arguments)) {
			$arguments['model'] = $this->serializer->deserialize($arguments[$resourceName], $this->metaModel);
			
			if (array_key_exists('client_id', $arguments[$resourceName])) {
				$arguments['clientId'] = $arguments[$resourceName]['client_id'];
			}
			
			$this->request->setArguments($arguments);
		} else {
			$this->throwStatus(400, NULL, 'No resource found with correct resource hash. Expected: {\'' . $resourceName . '\': {}}');
		}
	}

	/**
	 * Allow modification of resources in updateAction()
	 *
	 * @return void
	 */
	public function initializeUpdateAction() {
			// set data type of model argument
		$this->arguments->addNewArgument('model', $this->metaModel->getFlowModelName(), TRUE);
		
		$propertyMappingConfiguration = $this->arguments['model']->getPropertyMappingConfiguration();
		$propertyMappingConfiguration->setTypeConverterOption(
				'TYPO3\Flow\Property\TypeConverter\PersistentObjectConverter', 
				\TYPO3\Flow\Property\TypeConverter\PersistentObjectConverter::CONFIGURATION_MODIFICATION_ALLOWED, TRUE);
		$propertyMappingConfiguration->allowAllProperties();
		
		foreach ((array) $this->metaModel->getAssociations() as $association) {
			$propertyMappingConfiguration->forProperty($association->getFlowName())->setTypeConverterOption(
					'TYPO3\Flow\Property\TypeConverter\PersistentObjectConverter',
					\TYPO3\Flow\Property\TypeConverter\PersistentObjectConverter::CONFIGURATION_MODIFICATION_ALLOWED, TRUE);	
			if ($association->getEmberType() !== 'belongsTo') {
				$propertyMappingConfiguration->forProperty($association->getFlowName() . '.*')->setTypeConverterOption(
					'TYPO3\Flow\Property\TypeConverter\PersistentObjectConverter',
					\TYPO3\Flow\Property\TypeConverter\PersistentObjectConverter::CONFIGURATION_MODIFICATION_ALLOWED, TRUE);			
			}
		}
		
		$arguments = $this->request->getArguments();
		$resourceName = $this->metaModel->getResourceNameSingular();
		
		if (array_key_exists($resourceName, $arguments)) {
			if (array_key_exists('model', $arguments)) {
				$arguments['model'] = array_merge((array)$arguments['model'], (array)$this->serializer->deserialize($arguments[$resourceName], $this->metaModel));
			} else {
				$arguments['model'] = $this->serializer->deserialize($arguments[$resourceName], $this->metaModel);
			}			
			
			$this->request->setArguments($arguments);
		} else {
			$this->throwStatus(400, NULL, 'No resource found with correct resource hash. Expected: {\'' . $resourceName . '\': {}}');
		}
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
		$arguments = $this->request->getArguments();
		$models = array();
		
		// enable bulk loading by ids
		if (array_key_exists('ids', $arguments)) {
			foreach ($arguments['ids'] as $id) {
				$object = $this->metaModel->getRepository()->findByIdentifier($id);
				if (!empty($object)) {
					$models[] = $object;
				}
			}
		} else {
			$models = $this->metaModel->getRepository()->findAll()->toArray();
		}
		
		$x=\TYPO3\Flow\var_dump($models, '', TRUE, TRUE);
		$this->systemLogger->log("Models in List Action: " . $x, LOG_INFO); // TODO remove
		
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
	 * @param object $model The new model to add
	 * @param string $clientId The clientId passed from ember persistance layer
	 * @return void
	 */
	public function createAction($model, $clientId = NULL) {
		$this->metaModel->getRepository()->add($model);
		
//		$assignee = $model->getAssignee();
//		$assignee->setTask($model);
//		$x=\TYPO3\Flow\var_dump($assignee, '', FALSE, TRUE);
//		$this->systemLogger->log("Assignee in Create Action: " . $x, LOG_INFO); // TODO remove
//
//		
//		$flowModelName = get_class($assignee);
//		echo $flowModelName;
//		$metaModel = $this->modelReflectionService->findByFlowModelName($flowModelName);
//		$metaModel->getRepository()->update($assignee);
		
		$this->persistenceManager->persistAll(); 
		
		$this->response->setStatus(201);
		$this->view->assign('content', $model);
		$this->view->assign('clientId', $clientId);
	}

	/**
	 * Update the given model
	 *
	 * @param object $model The task to update
	 * @return void
	 */
	public function updateAction($model) {
		$this->metaModel->getRepository()->update($model);
		$this->persistenceManager->persistAll(); 
		
		$this->response->setStatus(200);
		$this->view->assign('content', $model);
	}

	/**
	 * Removes the given model
	 *
	 * @param string $resourceId The resourceId 
	 * @return string
	 */
	public function deleteAction($resourceId) {
		$repository = $this->metaModel->getRepository();
		$model = $repository->findByIdentifier($resourceId);

		if ($model === NULL) {
			$this->response->setStatus(404);
		} else {
			$repository->remove($model);
			$this->response->setStatus(204);
		}
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