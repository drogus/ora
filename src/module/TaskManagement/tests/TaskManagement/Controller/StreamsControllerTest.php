<?php
namespace TaskManagement\Controller;

use Test\Bootstrap;
use Zend\Mvc\Router\Http\TreeRouteStack as HttpRouter;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\RouteMatch;
use PHPUnit_Framework_TestCase;
use Rhumsaa\Uuid\Uuid;
use Ora\TaskManagement\Task;
use Ora\StreamManagement\Stream;
use Ora\User\User;
use Ora\Organization\Organization;

class StreamsControllerTest extends \PHPUnit_Framework_TestCase {
	
	/**
	 * 
	 * @var StreamsController
	 */
    protected $controller;
    protected $request;
    protected $response;
    protected $routeMatch;
    protected $event;
    
    protected $task;
    protected $member1;
    protected $member2;

    protected function setUp()
    {
        $streamServiceStub = $this->getMockBuilder('Ora\StreamManagement\StreamService')
        	->getMock();
        
        $organizationServiceStub = $this->getMockBuilder('Ora\Organization\OrganizationService')
        	->getMock();
        
        $serviceManager = Bootstrap::getServiceManager();
        $this->controller = new StreamsController($streamServiceStub, $organizationServiceStub);
        $this->request    = new Request();
        $this->routeMatch = new RouteMatch(array('controller' => 'streams'));
        $this->event      = new MvcEvent();
        $config = $serviceManager->get('Config');
        $routerConfig = isset($config['router']) ? $config['router'] : array();
        $router = HttpRouter::factory($routerConfig);

        $this->event->setRouter($router);
        $this->event->setRouteMatch($this->routeMatch);
        $this->controller->setEvent($this->event);
        $this->controller->setServiceLocator($serviceManager);
        
    	$transaction = $this->getMockBuilder('ZendExtension\Mvc\Controller\Plugin\EventStoreTransactionPlugin')
    		->disableOriginalConstructor()
    		->setMethods(['begin', 'commit', 'rollback'])
    		->getMock();
        $this->controller->getPluginManager()->setService('transaction', $transaction);

        $user = User::create();
        $this->setupLoggedUser($user);
    }
    
    public function testCreateStream() {
        $organization = Organization::create('Cum sociis natoque penatibus et', $this->getLoggedUser());
        $stream = Stream::create($organization, 'Vestibulum sed magna vitae velit', $this->getLoggedUser());
        
        $this->controller->getOrganizationService()
        	->expects($this->once())
        	->method('getOrganization')
        	->with($organization->getId()->toString())
        	->willReturn($organization);
        
        $this->controller->getStreamService()
        	->expects($this->once())
        	->method('createStream')
        	->willReturn($stream);
        
        $this->request->setMethod('post');
    	$params = $this->request->getPost();
    	$params->set('organizationId', $organization->getId()->toString());
    	$params->set('subject', 'Vestibulum sed magna vitae velit');
    	
    	$result   = $this->controller->dispatch($this->request);
    	$response = $this->controller->getResponse();
    	 
    	$this->assertEquals(201, $response->getStatusCode());
    	$this->assertNotEmpty($response->getHeaders()->get('Location'));
    }
    
    public function testCreateStreamInNotExistingOrganization() {
        $this->controller->getOrganizationService()
        	->expects($this->once())
        	->method('getOrganization')
        	->with('00000000-0000-0000-2000-000000000000')
        	->willReturn(null);
        
        $this->request->setMethod('post');
    	$params = $this->request->getPost();
    	$params->set('organizationId', '00000000-0000-0000-2000-000000000000');
    	$params->set('subject', 'Vestibulum sed magna vitae velit');
    	
    	$result   = $this->controller->dispatch($this->request);
    	$response = $this->controller->getResponse();
    	 
    	$this->assertEquals(404, $response->getStatusCode());
    }
    
    public function testCreateStreamWithoutOrganization() {
    	$this->request->setMethod('post');
    	$params = $this->request->getPost();
    	$params->set('subject', 'Vestibulum sed magna vitae velit');
    	
    	$result   = $this->controller->dispatch($this->request);
    	$response = $this->controller->getResponse();
    	 
    	$this->assertEquals(400, $response->getStatusCode());
    }
    
    public function testCreateStreamAsAnonymous() {
    	$this->setupAnonymous();
    	
    	$this->request->setMethod('post');
    	$params = $this->request->getPost();
    	
    	$result   = $this->controller->dispatch($this->request);
    	$response = $this->controller->getResponse();
    	 
    	$this->assertEquals(401, $response->getStatusCode());
    }
    
    protected function setupAnonymous() {
    	$identity = $this->getMockBuilder('Zend\Mvc\Controller\Plugin\Identity')
    		->disableOriginalConstructor()
    		->getMock();
    	$identity->method('__invoke')->willReturn(null);
    	
    	$this->controller->getPluginManager()->setService('identity', $identity);
    }
    
    protected function setupLoggedUser(User $user) {
    	$identity = $this->getMockBuilder('Zend\Mvc\Controller\Plugin\Identity')
    		->disableOriginalConstructor()
    		->getMock();
    	$identity->method('__invoke')->willReturn(['user' => $user]);
    	
    	$this->controller->getPluginManager()->setService('identity', $identity);
    }
    
    protected function getLoggedUser() {
    	return $this->controller->identity()['user'];
    }
}