<?php

namespace TaskManagement\Controller;

use ZendExtension\Mvc\Controller\AbstractHATEOASRestfulController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use Ora\StreamManagement\StreamService;
use Ora\Organization\OrganizationService;

class StreamsController extends AbstractHATEOASRestfulController
{
    protected static $collectionOptions = array ('GET','POST');
    protected static $resourceOptions = array ('DELETE','GET');
    /**
     * 
     * @var StreamService
     */
	protected $streamService;
	/**
	 * 
	 * @var OrganizationService
	 */
	protected $organizationService;
	
	public function __construct(StreamService $streamService, OrganizationService $organizationService) {
		$this->streamService = $streamService;
		$this->organizationService = $organizationService;
	}
	
    public function get($id)
    {
        // HTTP STATUS CODE 405: Method not allowed
        $this->response->setStatusCode(405);
         
        return $this->response;
    }
    
    public function getList()
    {
        // HTTP STATUS CODE 405: Method not allowed
        $this->response->setStatusCode(405);
         
        return $this->response;
    }
    
    public function create($data)
    {
    	$identity = $this->identity();
    	if(is_null($identity)) {
    		$this->response->setStatusCode(401);
    		return $this->response;
       	}
       	$identity = $identity['user'];
    	
    	if(!isset($data['organizationId'])) {
    		$this->response->setStatusCode(400);
    		return $this->response;
    	}
    	$organization = $this->organizationService->getOrganization($data['organizationId']);
    	if(is_null($organization)) {
    		$this->response->setStatusCode(404);
    		return $this->response;
       	}
       	$subject = isset($data['subject']) ? $data['subject'] : null;
	    $stream = $this->streamService->createStream($organization, $subject, $identity);
	    $url = $this->url()->fromRoute('streams', array('id' => $stream->getId()->toString()));
	    $this->response->getHeaders()->addHeaderLine('Location', $url);
	    $this->response->setStatusCode(201);
	    
    	return $this->response;
    }
    
    public function update($id, $data)
    {
        // HTTP STATUS CODE 405: Method not allowed
        $this->response->setStatusCode(405);
         
        return $this->response;
    }
    
    public function replaceList($data)
    {
        // HTTP STATUS CODE 405: Method not allowed
        $this->response->setStatusCode(405);
         
        return $this->response;
    }
    
    public function deleteList()
    {
        // HTTP STATUS CODE 405: Method not allowed
        $this->response->setStatusCode(405);
         
        return $this->response;
    }
    
    public function delete($id)
    {
        // HTTP STATUS CODE 405: Method not allowed
        $this->response->setStatusCode(405);
         
        return $this->response;
    }
    
    public function getStreamService() 
    {
        return $this->streamService;
    }
    
    public function getOrganizationService()
    {
    	return $this->organizationService;
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