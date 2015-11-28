namespace Home\Model;
use Think\Model;
class DocModel extends Model{
    //或者使用字符串定义
    protected $connection = 'mysql://root:""@localhost:3306/nengrongweb#utf8';
    public function findall()
    {
       $sql = 'SELECT `title` FROM doc';
       return $this->query( $sql );
    }
}
