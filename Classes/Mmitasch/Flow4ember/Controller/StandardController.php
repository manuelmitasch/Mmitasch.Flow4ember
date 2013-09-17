<?php
namespace Mmitasch\Flow4ember\Controller;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Mmitasch.Taskplaner".   *
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

class StandardController extends \TYPO3\Flow\Mvc\Controller\ActionController {

	/**
	 * @Flow\Inject
	 * @var \Mmitasch\Flow4ember\Service\ModelReflectionServiceInterface
	 */
	protected $modelReflectionService;
	
	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Mvc\Routing\RouterInterface
	 */
	protected $router;
	
	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Configuration\ConfigurationManager
	 */
	protected $configurationManager;
	
	/**
	 * @var string
	 */
	protected $packageKey;
	
	function __construct() {
		$this->packageKey = \Mmitasch\Flow4ember\Utility\NamingUtility::extractPackageKey(get_class($this));
	}
	
	/**
	 * @return void
	 */
	public function indexAction() {
		$this->view->assign('foos', array(
			'bar', 'baz'
		));
	}
	
	/**
	 * Display the configured REST resources and Flow routes
	 * 
	 * @return void
	 */
	public function apiAction() {
		$this->view->assign('resources', $this->modelReflectionService->getResources());
		$this->view->assign('routes', $this->getRoutes());
	}
	
		


	/**
	 * List the known routes
	 * This command displays a list of all currently registered routes.
	 *
	 * @return void
	 */
	protected function getRoutes() {
		$this->initializeRouter();
		$routes = array();

		foreach ($this->router->getRoutes() as $index => $route) {
			$routes[] = array(
				"uriPattern" => $route->getUriPattern(), 
				"routeName" => $route->getName()
			);
		}
		
		return $routes;
	}
	
	/**
	 * Initialize the injected router-object
	 *
	 * @return void
	 */
	protected function initializeRouter() {
		$routesConfiguration = $this->configurationManager->getConfiguration(\TYPO3\Flow\Configuration\ConfigurationManager::CONFIGURATION_TYPE_ROUTES);
		$this->router->setRoutesConfiguration($routesConfiguration);
	}

}

?>