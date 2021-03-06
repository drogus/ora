<?php
namespace User\Controller;

use Test\Bootstrap;
use Zend\Mvc\Router\Http\TreeRouteStack as HttpRouter;
use Zend\Http\Request;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\RouteMatch;
use PHPUnit_Framework_TestCase;
use Ora\Organization\Organization;
use Ora\User\User;
use Ora\Accounting\OrganizationAccount;

class OrganizationsControllerTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * 
	 * @var OrganizationsController
	 */
    protected $controller;
    /**
     * 
     * @var Request
     */
    protected $request;
    /**
     * 
     * @var RouteMatch
     */
    protected $routeMatch;
    /**
     * 
     * @var MvcEvent
     */
    protected $event;
    
    protected function setUp()
    {
    	$orgService = $this->getMockBuilder('Ora\Organization\OrganizationService')
    		->getMock();
    	
        $serviceManager = Bootstrap::getServiceManager();
        $this->controller = new OrganizationsController($orgService);
        $this->request    = new Request();
        $this->routeMatch = new RouteMatch(array('controller' => 'organizations'));
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
    }
    
    public function testCreate() {
    	$user = User::create();
    	$this->setupLoggedUser($user);
    	
    	$this->controller->getOrganizationService()
    		->method('createOrganization')
    		->willReturn(Organization::create('Fusce nec ullamcorper', $user));
    	
    	$this->request->setMethod('post');
    	
    	$params = $this->request->getPost();
    	$params->set('name', 'Fusce nec ullamcorper');
    	
    	$result   = $this->controller->dispatch($this->request);
    	$response = $this->controller->getResponse();
    	
    	$this->assertEquals(201, $response->getStatusCode());
    	$this->assertNotEmpty($response->getHeaders()->get('Location'));
    }

    public function testCreateWithoutName() {
    	$user = User::create();
    	$this->setupLoggedUser($user);
    	
    	$this->controller->getOrganizationService()
    		->method('createOrganization')
    		->willReturn(Organization::create(null, $user));
    	
    	$this->request->setMethod('post');
    	    	
    	$result   = $this->controller->dispatch($this->request);
    	$response = $this->controller->getResponse();
    	
    	$this->assertEquals(201, $response->getStatusCode());
    	$this->assertNotEmpty($response->getHeaders()->get('Location'));
    }

    public function testCreateAsAnonymous() {
		$this->setupAnonymous();
        $this->request->setMethod('post');
    	
    	$params = $this->request->getPost();
    	$params->set('name', 'Fusce nec ullamcorper');
    	
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
}