<?php
namespace Home\Controller;
use Think\Controller;
class OurModeController extends Controller {
    public function index(){
        if($_COOKIE["lang"]=="en")
        {
            $this->display(index_en);
        }
        else
        {
            $this->display();
        }
    }
    public function ourProject(){
        if($_COOKIE["lang"]=="en")
        {
            $this->display(ourProject_en);
        }
        else
        {
            $this->display();
        }
    }
    public function finance(){
        if($_COOKIE["lang"]=="en")
        {
            $this->display(finance_en);
        }
        else
        {
            $this->display();
        }
    }
    public function manage(){
        if($_COOKIE["lang"]=="en")
        {
            $this->display(manage_en);
        }
        else
        {
            $this->display();
        }
    }
}

