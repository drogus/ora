<?php
namespace TaskManagement\Controller;

use ZendExtension\Mvc\Controller\AbstractHATEOASRestfulController;
use Zend\Validator\ValidatorChain;
use Zend\Validator\NotEmpty;
use Zend\I18n\Validator\Float;
use Zend\Validator\Between;
use ZendExtension\Mvc\View\ErrorJsonModel;
use Ora\InvalidArgumentException;
use Ora\TaskManagement\TaskService;
use Ora\DomainEntityUnavailableException;
use Ora\IllegalStateException;

class SharesController extends AbstractHATEOASRestfulController {
	
	protected static $collectionOptions = array();
	protected static $resourceOptions = array('POST');
	
	/**
	 *
	 * @var TaskService
	 */
	protected $taskService;
	
	public function __construct(TaskService $taskService) {
		$this->taskService = $taskService;
	}
	
	public function invoke($id, $data)
	{
		$validator = new ValidatorChain();
		$validator->attach(new NotEmpty(), true)
				  ->attach(new Float(), true)
				  ->attach(new Between(array('min' => 0, 'max' => 100), true));
		
		$error = new ErrorJsonModel();
		
		$total = 0;
		foreach ($data as $key => $value) {
			if($validator->isValid($value)) {
				$total += $value;
			} else {
				$error->addSecondaryErrors($key, $validator->getMessages());
			}
		}
		if($error->hasErrors()) {
			$error->setCode(ErrorJsonModel::$ERROR_INPUT_VALIDATION);
			$this->response->setStatusCode(400);
			return $error;
		}
		
		$task = $this->taskService->getTask($id);
		if (is_null($task)) {
			$this->response->setStatusCode(404);
			return $this->response;
		}
		
		$identity = $this->loggedIdentity();
		if(is_null($identity)) {
			$this->response->setStatusCode(401);
			return $error;
		}
		$this->transaction()->begin();
		try {
			$task->assignShares($data, $identity);
			$this->transaction()->commit();
			$this->response->setStatusCode(201);
			return $this->response;
		} catch (InvalidArgumentException $e) {
			$this->transaction()->rollback();
			$error->setCode(ErrorJsonModel::$ERROR_INPUT_VALIDATION);
			$error->setDescription($e->getMessage());
			$this->response->setStatusCode(400);
		} catch (DomainEntityUnavailableException $e) {
			$this->transaction()->rollback();
			$this->response->setStatusCode(403);
		} catch (IllegalStateException $e) {
			$this->transaction()->rollback();
			$this->response->setStatusCode(412);
		}
		return $error;
	}
	
	public function getTaskService() {
		return $this->taskService;
	}

	protected function getCollectionOptions()
	{
		return self::$collectionOptions;
	}
	
	protected function getResourceOptions()
	{
		return self::$resourceOptions;
	}
	
}