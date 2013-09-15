<?php

namespace Mmitasch\Flow4ember\Service;

/**
 * Description of Notification
 *
 * @author Manuel Mitasch <manuel at cms.mine.nu>
 */

class Notification {
	
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
	
	public function receiveDirectoriesHaveChanged($monitorIdentifier, array $changedDirectories) {
		$this->systemLogger->log("DIR CHANGED!1d!!"); // TODO remove
	}
	
	public function receiveFilesHaveChanged($monitorIdentifier, array $changedFiles) {
		$this->systemLogger->log("FILES CHANGED!1d!!"); // TODO remove
	}
	
	
}

?>
