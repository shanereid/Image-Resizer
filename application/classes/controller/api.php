<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Api extends Controller {
	
	public function __construct(Request $request, Response $response)
	{
		parent::__construct($request, $response);
		
		// Instantiate the Data Model
		$this->dataModel = new Model_Data;
		
		date_default_timezone_set('Europe/London');
	}
	
    public function before()
    {
        $this->config = Kohana::$config->load('general');
    }
	
	private function sendResponse($response = false, $error = false)
	{
	    // Sends a JSON Response back to the server, both params are optional.
	    // First param allows you to add more keys to the returning array
	    // Second param sends an error.
		if(!$response)
			$response = array();
		$response['status'] = !$error? 'success' : 'failed';
		if($error)
			$response['error'] = $error;
		$responseStr = json_encode($response);
		if(isset($_GET['cb'])) {
			$responseStr = $_GET['cb'].'('.$responseStr.');';
		}
		
		$this->response->headers('Content-Type','text/javascript');
		
		$this->response->body($responseStr);
	}
	
	public function action_doResize()
	{
	    ini_set('memory_limit', '512M');
	    set_time_limit(0);
	    
	    $params = $_GET;
	    
	    $this->imageModel = new Model_Image;
	    
	    if(!isset($params['image'])) {
	        $this->sendResponse(false, 'Image is required for this API Call.');
	        return;
	    }
	    
	    if(!isset($params['name'])) {
	        $this->sendResponse(false, 'Name is required for this API Call.');
	        return;
	    }
	    
	    if(!isset($params['batchId']))
	        $batchId = md5($params['image'].'|'.microtime());
	    else
	        $batchId = $params['batchId'];
	    
	    if(!isset($params['sizes']))
	        $sizes = false;
	    else
	        $sizes = explode(',',$params['sizes']);
	    
	    $presets = $this->dataModel->getPresets($sizes);
	    
	    if(!is_dir('assets/images/uploads/batches/'.$batchId.'/'))
    	    mkdir('assets/images/uploads/batches/'.$batchId.'/', 0777, true);
	    
	    foreach ($presets as $preset) {
	        $this->imageModel->createThumbnail('assets/images/uploads/original/'.$params['image'], 'assets/images/uploads/batches/'.$batchId.'/'.$preset['image_prefix'].'_'.$params['name'], $preset['image_w'], $preset['image_h']);
	    }
	    
	    $this->imageModel->zip('assets/images/uploads/batches/'.$batchId.'/', 'assets/images/uploads/batches/'.$batchId.'.zip');
	    
	    $this->sendResponse(array('batchId'=>$batchId, 'zip'=>'assets/images/uploads/batches/'.$batchId.'.zip'));
	}
}