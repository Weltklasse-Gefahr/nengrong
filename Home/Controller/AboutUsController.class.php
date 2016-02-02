<?php
namespace Home\Controller;
use Think\Controller;
class AboutUsController extends Controller {
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
    public function company(){
        if($_COOKIE["lang"]=="en")
        {
            $this->display(company_en);
        }
        else
        {
            $this->display();
        }
    }
    public function team(){
        if($_COOKIE["lang"]=="en")
        {
            $this->display(team_en);
        }
        else
        {
            $this->display();
        }
    }
    public function contact(){
        if($_COOKIE["lang"]=="en")
        {
            $this->display(contact_en);
        }
        else
        {
            $this->display();
        }
    }
    public function joinUs(){
        if($_COOKIE["lang"]=="en")
        {
            $this->display(joinUs_en);
        }
        else
        {
            $this->display();
        }
    }
}

