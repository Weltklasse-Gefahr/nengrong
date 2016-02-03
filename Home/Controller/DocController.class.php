<?php
namespace Home\Controller;
use Think\Controller;
class DocController extends Controller {
    public function index(){
        $this->display();
    }
    public function docList(){
        //$User = D('doc');
        //$User->select();
        $mysql_server_name="localhost"; //数据库服务器名称
        $mysql_username="root"; // 连接数据库用户名
        $mysql_password=""; // 连接数据库密码
        $mysql_database="nengrongweb"; // 数据库的名字
        $conn=mysql_connect($mysql_server_name, $mysql_username,$mysql_password);
        $strsql="SELECT * FROM news";
        $result=mysql_db_query($mysql_database, $strsql, $conn);
        $row=mysql_fetch_row($result);
        var_dump($row); 
        $this->display();
    }
}
