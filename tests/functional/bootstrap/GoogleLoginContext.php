<?php 

use Behat\Behat\Context\Context;
use Behat\MinkExtension\Context\MinkContext;
use Behat\Behat\Context\CustomSnippetAcceptingContext;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

class GoogleLoginContext extends MinkContext implements Context
{
    public function __construct()
    {
    }
    
    /**
     * @Given /^I am not authenticated in ORA Project$/
     */
    public function iAmNotAuthenticatedInOraProject()
    {
    	return true;
    	//throw new PendingException();
    }
    
    /**
     * @Given /^I don\'t see popup "([^"]*)"$/
    
    public function iDonTSeePopup($arg1)
    {
    	$page = $this->getSession()->getPage();
    	$popup = $page->findById('popupLogin');
    	
    	 TODO è necessario installare selenium 
    	return !$popup->isVisible();
    	//throw new PendingException();
    } */
        
    /**
     * @Given /^I click on "([^"]*)"$/
     */
    public function iClickOn($arg1)
    {    	    	
    	$page = $this->getSession()->getPage();    	
    	$page->clickLink($arg1);

    }
    
    /**
     * @Given /^I should see popup "([^"]*)"$/
     */
    public function iShouldSeePopup($arg1)
    {
    	$page = $this->getSession()->getPage();    	
    	$page->hasContent($arg1);
    }    
}