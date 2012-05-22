<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Landing extends Sir_Controller {
	
	public $template = 'landing';
    
    public function __construct(Request $request, Response $response)
    {
    	parent::__construct($request, $response);
    	
    	$this->dataModel = new Model_Data;
    	
    	date_default_timezone_set('Europe/London');
    }
    
    public function before()
    {
        header('P3P:CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');
        
        $this->javascripts = array('https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js', 'assets/js/fileuploader.js');
        $this->stylesheets = array();
        
        parent::before();
    }
    
	public function action_index()
	{
	    $this->template->head->pageTitle = 'Image Resizer.';
	    
	    $this->template->presets = $this->dataModel->getPresets();
	}
}
