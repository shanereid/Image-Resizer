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
	    
	    if(!isset($params['sizes']))
	        $sizes = false;
	    else
	        $sizes = explode(',',$params['sizes']);
	    
	    $presets = $this->dataModel->getPresets($sizes);
	    
	    $batchId = md5($params['image'].'|'.microtime());
	    
	    mkdir('assets/images/uploads/batches/'.$batchId.'/', 0777, true);
	    
	    foreach ($presets as $preset) {
	        $this->imageModel->createThumbnail('assets/images/uploads/original/'.$params['image'], 'assets/images/uploads/batches/'.$batchId.'/'.$preset['image_prefix'].'_'.$params['name'], $preset['image_w'], $preset['image_h']);
	    }
	    
	    $this->sendResponse(array('batchId'=>$batchId));
	}
	
	public function action_testEndpoint()
	{
	    // General convention is to send an error using $this->sendResponse(false, '[Error Description]') if the API fails to do what it should,
	    // And send a success response using $this->sendResponse() if the API Succeeds.
	    // If you need to send data back, use $this->sendResponse(array('data'=>'Whatever you need to send back'))
	    
	    $logicResult = false;
	    
	    if($logicResult)
	        $this->sendResponse();
	    else
	        $this->sendResponse(false, 'Logic Result was false, so returning an error');
	}
	
	public function action_successUploadComplete() {
	    $params = $_GET;
	    
	    $this->imageModel = new Model_Image;
	    
	    if(!isset($params['image'])) {
	        $this->sendResponse(false, 'Image is required for this API Call.');
	        return;
	    }
	    
	    $profileImage = $this->imageModel->createThumbnail('assets/images/uploads/original/'.$params['image'], 'assets/images/uploads/successImages/'.$params['image'], 190, 220);
	    
	    if(!$profileImage) {
	        $this->sendResponse(false, 'Please upload an image with a minimum width & height of 480 x 463.');
	    } else {
	        $this->sendResponse(array('filename'=>$params['image']));
	    }
	}
	
	public function action_inspireUploadComplete() {
	    $params = $_GET;
	    
	    $this->imageModel = new Model_Image;
	    
	    if(!isset($params['image'])) {
	        $this->sendResponse(false, 'Image is required for this API Call.');
	        return;
	    }
	    
	    $moodboardImage = $this->imageModel->createThumbnail('assets/images/uploads/original/'.$params['image'], 'assets/images/uploads/inspireImages/'.$params['image'], 127, 180);
	    
	    $product = array(
	        'image_url' => 'assets/images/uploads/inspireImages/'.$params['image'],
	        'type' => 3
	    );
	    
	    $product['id'] = $this->dataModel->insertProduct($product);
	    
	    if(!$moodboardImage) {
	        $this->sendResponse(false, 'Please upload an image with a minimum width & height of 127 x 180.');
	    } else {
	        $this->sendResponse(array('product' => $product));
	    }
	}
	
	public function action_copyFacebookImageToInspire()
	{
	    $params = $_GET;
	    
	    $this->imageModel = new Model_Image;
	    
	    $url = $params['url'];
	    $urlParts = explode('.',$url);
	    $ext = $urlParts[count($urlParts) - 1];
	    
	    $filename = md5($url.'|'.microtime()).'.'.$ext;
	    
	    $img = 'assets/images/uploads/original/'.$filename;
	    file_put_contents($img, file_get_contents($url));
	    
	    $moodboardImage = $this->imageModel->createThumbnail('assets/images/uploads/original/'.$filename, 'assets/images/uploads/inspireImages/'.$filename, 127, 180);
	    
	    $product = array(
	        'image_url' => 'assets/images/uploads/inspireImages/'.$filename,
	        'type' => 3
	    );
	    
	    $product['id'] = $this->dataModel->insertProduct($product);
	    
	    if(!$moodboardImage) {
	        $this->sendResponse(false, 'Please upload an image with a minimum width & height of 127 x 180.');
	    } else {
	        $this->sendResponse(array('product' => $product));
	    }
	}
	
	public function action_copyFacebookImage()
	{
	    $params = $_GET;
	    
	    $this->imageModel = new Model_Image;
	    
	    $url = $params['url'];
	    $urlParts = explode('.',$url);
	    $ext = $urlParts[count($urlParts) - 1];
	    
	    $filename = md5($url.'|'.microtime()).'.'.$ext;
	    
	    $img = 'assets/images/uploads/original/'.$filename;
	    file_put_contents($img, file_get_contents($url));
	    
	    $image = $this->imageModel->createThumbnail('assets/images/uploads/original/'.$filename, 'assets/images/uploads/successImages/'.$filename, 190, 220);
	    
	    if($image) {
		    $this->sendResponse(array('filename'=>$filename));
	    }
    }
}