<?php
namespace Mmitasch\Flow4ember\Service;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Mmitasch.Flow4ember".   *
 * Highly inspired by "TYPO3.Kickstart" package.                          *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow,
	TYPO3\Flow\Package\Package;

/**
 * Service for the Kickstart generator
 */
class GeneratorService {

	/**
	 * @var \TYPO3\Flow\Object\ObjectManagerInterface
	 * @Flow\Inject
	 */
	protected $objectManager;

	/**
	 * @var \TYPO3\Flow\Package\PackageManagerInterface
	 * @Flow\Inject
	 */
	protected $packageManager;

	/**
	 * @var \TYPO3\Fluid\Core\Parser\TemplateParser
	 * @Flow\Inject
	 */
	protected $templateParser;

	/**
	 * @var \TYPO3\Kickstart\Utility\Inflector
	 * @Flow\Inject
	 */
	protected $inflector;

	/**
	 * @var \TYPO3\Flow\Reflection\ReflectionService
	 * @Flow\Inject
	 */
	protected $reflectionService;

	/**
	 * @var array
	 */
	protected $generatedFiles = array();

	
	/**
	 * Generates an Ember.yaml configuration file for given package key
	 *
	 * @param string $packageKey The package key
	 * @param boolean $overwrite Overwrite any existing files?
	 * @return array An array of generated filenames
	 */
	public function generateConfig($packageKey, $overwrite = FALSE) {
		$templatePathAndFilename = 'resource://Mmitasch.Flow4ember/Private/Generator/Configuration/Ember.yaml.tmpl';
		$package = $this->packageManager->getPackage($packageKey);
		
		$contextVariables = array();
		$contextVariables['packageKey'] = $packageKey;
		$contextVariables['restController'] = $package->getNamespace() . '\Controller\EpfRestController';
		$contextVariables['modelReflectionService'] = $package->getNamespace() . '\Service\ModelReflectionService';
		$fileContent = $this->renderTemplate($templatePathAndFilename, $contextVariables);

		$configurationFilename = 'Ember.yaml';
		$configurationPath = $package->getConfigurationPath();
		$targetPathAndFilename = $configurationPath . $configurationFilename;

		$this->generateFile($targetPathAndFilename, $fileContent, $overwrite);

		return $this->generatedFiles;
	}
	
	/**
	 * Generates the RestController for the given package key
	 * 
	 * @param string $packageKey The package key
	 * @param boolean $overwrite Overwrite any existing files?
	 * @return array An array of generated filenames
	 */
	public function generateRestController($packageKey, $overwrite = FALSE) {
		$package = $this->packageManager->getPackage($packageKey);
		
		$controllerFilename = 'EpfRestController.php.generated'; // TODO remove generated string
		$controllerPath = $package->getClassesNamespaceEntryPath() . 'Controller/';
		$targetPathAndFilename = $controllerPath . $controllerFilename;
		
		$fileContent = file_get_contents($controllerPath . 'EpfRestController.php');
		$fileContent = str_replace('Mmitasch\\Flow4ember\\Controller', $package->getNamespace() . '\Controller', $fileContent);
		$fileContent = str_replace('Mmitasch\Flow4ember\Service\ModelReflectionService', $package->getNamespace() . '\Service\ModelReflectionService', $fileContent);

		$this->generateFile($targetPathAndFilename, $fileContent, $overwrite);

		return $this->generatedFiles;
	}
	
	/**
	 * Generates the ModelReflectionService for the given package key
	 * 
	 * @param string $packageKey The package key
	 * @param boolean $overwrite Overwrite any existing files?
	 * @return array An array of generated filenames
	 */
	public function generateModelReflectionservice($packageKey, $overwrite = FALSE) {
		$package = $this->packageManager->getPackage($packageKey);
		
		$serviceFilename = 'ModelReflectionService.php.generated'; // TODO remove generated string
		$servicePath = $package->getClassesNamespaceEntryPath() . 'Service/';
		$targetPathAndFilename = $servicePath . $serviceFilename;
		
		$fileContent = file_get_contents($servicePath . 'ModelReflectionService.php');
		$fileContent = str_replace('namespace Mmitasch\\Flow4ember\\Service', 'namespace ' . $package->getNamespace() . '\Service', $fileContent);

		$this->generateFile($targetPathAndFilename, $fileContent, $overwrite);

		return $this->generatedFiles;
	}

	/**
	 * Generate a file with the given content and add it to the
	 * generated files
	 *
	 * @param string $targetPathAndFilename
	 * @param string $fileContent
	 * @param boolean $force
	 * @return void
	 */
	protected function generateFile($targetPathAndFilename, $fileContent, $force = FALSE) {
		if (!is_dir(dirname($targetPathAndFilename))) {
			\TYPO3\Flow\Utility\Files::createDirectoryRecursively(dirname($targetPathAndFilename));
		}

		if (substr($targetPathAndFilename, 0, 11) === 'resource://') {
			list($packageKey, $resourcePath) = explode('/', substr($targetPathAndFilename, 11), 2);
			$relativeTargetPathAndFilename = $packageKey . '/Resources/' . $resourcePath;
		} elseif (strpos($targetPathAndFilename, 'Tests') !== FALSE) {
			$relativeTargetPathAndFilename = substr($targetPathAndFilename, strrpos(substr($targetPathAndFilename, 0, strpos($targetPathAndFilename, 'Tests/') - 1), '/') + 1);
		} else {
			$relativeTargetPathAndFilename = substr($targetPathAndFilename, strrpos(substr($targetPathAndFilename, 0, strpos($targetPathAndFilename, 'Classes/') - 1), '/') + 1);
		}

		if (!file_exists($targetPathAndFilename) || $force === TRUE) {
			file_put_contents($targetPathAndFilename, $fileContent);
			$this->generatedFiles[] = 'Created .../' . $relativeTargetPathAndFilename;
		} else {
			$this->generatedFiles[] = 'Omitted .../' . $relativeTargetPathAndFilename;
		}
	}

	/**
	 * Render the given template file with the given variables
	 *
	 * @param string $templatePathAndFilename
	 * @param array $contextVariables
	 * @return string
	 * @throws \TYPO3\Fluid\Core\Exception
	 */
	protected function renderTemplate($templatePathAndFilename, array $contextVariables) {
		$templateSource = \TYPO3\Flow\Utility\Files::getFileContents($templatePathAndFilename, FILE_TEXT);
		if ($templateSource === FALSE) {
			throw new \TYPO3\Fluid\Core\Exception('The template file "' . $templatePathAndFilename . '" could not be loaded.', 1225709595);
		}
		$parsedTemplate = $this->templateParser->parse($templateSource);

		$renderingContext = $this->buildRenderingContext($contextVariables);

		return $parsedTemplate->render($renderingContext);
	}

	/**
	 * Build the rendering context
	 *
	 * @param array $contextVariables
	 * @return \TYPO3\Fluid\Core\Rendering\RenderingContext
	 */
	protected function buildRenderingContext(array $contextVariables) {
		$renderingContext = new \TYPO3\Fluid\Core\Rendering\RenderingContext();

		$renderingContext->injectTemplateVariableContainer(new \TYPO3\Fluid\Core\ViewHelper\TemplateVariableContainer($contextVariables));
		$renderingContext->injectViewHelperVariableContainer(new \TYPO3\Fluid\Core\ViewHelper\ViewHelperVariableContainer());

		return $renderingContext;
	}
}
?>
