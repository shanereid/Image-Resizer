<?php defined('SYSPATH') or die('No direct script access.');

abstract class Sir_Controller extends Controller_Template {
	public $pageTitle;
	public $javascripts;
	public $stylesheets;
	public $meta;
	
    public function before()
    {
        parent::before();
 
        // Make $page_title available to all views
        View::bind_global('pageTitle', $this->pageTitle);
 
        // Load $head into the template as a view, bind some variables to it can be customised.
        $this->template->head = View::factory('generic/head');
        $this->template->head->bind('javascripts', $this->javascripts);
        $this->template->head->bind('stylesheets', $this->stylesheets);
        $this->template->head->bind('meta', $this->meta);
        
        // Load $foot into the template as a view.
        $this->template->foot = View::factory('generic/foot');
    }
}