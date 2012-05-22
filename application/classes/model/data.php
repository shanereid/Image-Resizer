<?php defined('SYSPATH') or die('No direct script access.');

class Model_Data extends Model {

	public function __construct() {
	    // Initialise Databse & Session
		$this->db = Database::instance();
	}
	
	public function getPresets($sizes = false)
	{
	    $query = DB::select()->from('preset')->where('created','<',date('Y-m-d H:i:s'));
	    
	    if($sizes) {
	        foreach ($sizes as $size) {
	            $query->or_where('id','=',$size);
	        }
	    }
	    
	    $query->order_by('image_w','ASC')->order_by('image_h','ASC');
	    
	    $presets = $query->execute()->as_array();
	    
	    return $presets;
	}
}
