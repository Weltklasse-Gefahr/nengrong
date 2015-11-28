<?php
namespace Home\Controller;

use Think\Controller;

class UserController extends Controller {
	public function myInformation()
    {
        $this->display();
    }

    public function securityCenter()
    {
        $this->display();
    }
}