<?php
namespace Home\Controller;

use Think\Controller;

class InnerStaffController extends Controller {
    
    public function getProjectProviderInfo(){
    	$this->display("Admin:providerInfo");
    }
}