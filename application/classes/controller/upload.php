<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Upload extends Controller {
	
	public $template = 'standard';
    
    private function sendResponse($response = false, $error = false)
    {
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
    
    public function action_do() {
        $allowedExtensions = array('jpeg','jpg','png','gif','pjpeg');
        
        $sizeLimit = 40 * 1024 * 1024;
        
        $uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
        $result = $uploader->handleUpload('assets/images/uploads/original/');
        $this->response->body(htmlspecialchars(json_encode($result), ENT_NOQUOTES));
    }
    
    public function action_index() {
        echo is_dir('uploads/')? 'true' : 'false';
    }
}

/**
 * Handle file uploads via XMLHttpRequest
 */
class qqUploadedFileXhr {
    /**
     * Save the file to the specified path
     * @return boolean TRUE on success
     */
    function save($path) {    
        $input = fopen("php://input", "r");
        $temp = tmpfile();
        $realSize = stream_copy_to_stream($input, $temp);
        fclose($input);
        
        if ($realSize != $this->getSize()){            
            return false;
        }
        
        $target = fopen($path, "w");        
        fseek($temp, 0, SEEK_SET);
        stream_copy_to_stream($temp, $target);
        fclose($target);
        
        return true;
    }
    function getName() {
        return $_GET['qqfile'];
    }
    function getSize() {
        if (isset($_SERVER["CONTENT_LENGTH"])){
            return (int)$_SERVER["CONTENT_LENGTH"];            
        } else {
            throw new Exception('Getting content length is not supported.');
        }      
    }   
}

/**
 * Handle file uploads via regular form post (uses the $_FILES array)
 */
class qqUploadedFileForm {  
    /**
     * Save the file to the specified path
     * @return boolean TRUE on success
     */
    function save($path) {
        if(!move_uploaded_file($_FILES['qqfile']['tmp_name'], $path)){
            return false;
        }
        return true;
    }
    function getName() {
        return $_FILES['qqfile']['name'];
    }
    function getSize() {
        return $_FILES['qqfile']['size'];
    }
}

class qqFileUploader {
    private $allowedExtensions = array();
    private $sizeLimit = 10485760;
    private $file;

    function __construct(array $allowedExtensions = array(), $sizeLimit = 10485760){        
        $allowedExtensions = array_map("strtolower", $allowedExtensions);
            
        $this->allowedExtensions = $allowedExtensions;        
        $this->sizeLimit = $sizeLimit;
        
        $this->checkServerSettings();       

        if (isset($_GET['qqfile'])) {
            $this->file = new qqUploadedFileXhr();
        } elseif (isset($_FILES['qqfile'])) {
            $this->file = new qqUploadedFileForm();
        } else {
            $this->file = false; 
        }
    }
    
    private function checkServerSettings(){        
        $postSize = $this->toBytes(ini_get('post_max_size'));
        $uploadSize = $this->toBytes(ini_get('upload_max_filesize'));        
        
        if ($postSize < $this->sizeLimit || $uploadSize < $this->sizeLimit){
            $size = max(1, $this->sizeLimit / 1024 / 1024) . 'M';             
            die("{'error':'increase post_max_size and upload_max_filesize to $size'}");    
        }        
    }
    
    private function toBytes($str){
        $val = trim($str);
        $last = strtolower($str[strlen($str)-1]);
        switch($last) {
            case 'g': $val *= 1024;
            case 'm': $val *= 1024;
            case 'k': $val *= 1024;        
        }
        return $val;
    }
    
    /**
     * Returns array('success'=>true) or array('error'=>'error message')
     */
    function handleUpload($uploadDirectory, $replaceOldFile = FALSE){
        if (!is_writable($uploadDirectory)){
            return array('error' => "Server error. Upload directory isn't writable.");
        }
        
        if (!$this->file){
            return array('error' => 'No files were uploaded.');
        }
        
        $size = $this->file->getSize();
        
        if ($size == 0) {
            return array('error' => 'The file you uploaded is empty. Please provide a valid file.');
        }
        
        if ($size > $this->sizeLimit) {
            return array('error' => 'SIZE');
        }
        
        $pathinfo = pathinfo($this->file->getName());
        $filename = $pathinfo['filename'];
        $filename = md5($filename.'|'.microtime());
        $ext = $pathinfo['extension'];

        if($this->allowedExtensions && !in_array(strtolower($ext), $this->allowedExtensions)){
            $these = implode(', ', $this->allowedExtensions);
            return array('error' => 'Invalid image received. The image that you upload must be one of the following types: "'.$these.'"');
        }
        
        if(!$replaceOldFile){
            /// don't overwrite previous files that were uploaded
            while (file_exists($uploadDirectory . $filename . '.' . $ext)) {
                $filename .= rand(10, 99);
            }
        }
        $params = array();
        if ($this->file->save($uploadDirectory . $filename . '.' . $ext)){
            $params['filename'] = $filename.'.'.$ext;
            $params['orig_filename'] = $pathinfo['filename'].'.'.$pathinfo['extension'];
            $params['success'] = true;
            
            if(in_array('jpeg', $this->allowedExtensions)) {
                $fileInfo = getimagesize($uploadDirectory . $filename . '.' . $ext);
                if(empty($fileInfo))
                    return array('error' => 'Invalid image received.');
            }
        } else {
            return $params['error'] = "We're sorry, it seems our image uploading service is experiencing difficulties. Please try again later.";
        }
        return $params;
    }
}