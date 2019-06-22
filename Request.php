<?php

/**
 * 数据操作类
 */


require('MMysql.php');

class Request
{
    //允许的请求方式
    private static $method_type = array('get', 'post', 'put', 'patch', 'delete');
    //测试数据
    private static $test_class = array(
        1 => array('name' => '托福班', 'count' => 18),
        2 => array('name' => '雅思班', 'count' => 20),
    );

    private static $configArr = array(
        'host'=>'localhost',
        'port'=>'3306',
        'user'=>'root',
        'passwd'=>'123',
        'dbname'=>'myblog'
        );
//获取数据
    public static function getRequest()
    {
        //请求方式
        $method = strtolower($_SERVER['REQUEST_METHOD']);
        if (in_array($method, self::$method_type)) {
            //调用请求方式对应的方法
            $data_name = $method . 'Data';
            return self::$data_name($_REQUEST);
        }
        return false;
    }

    //GET 获取信息
    private static function getData($request_data)
    {
        $mysql = new MMysql(self::$configArr);
        $type = $request_data['type'];
        if ($type == 0) {
            $data = $mysql->field(array('comment','userid','id'))
                ->select('comment');
            return $data;
        } elseif ($request_data['userid']) {
            $userid = $request_data['userid'];
            $data = $mysql->field(array('comment','id'))
                ->where('userid=' . $userid)
                ->select('comment');
            return $data;
        }
        else {
            $res = array('state'=>'err');
            return $res;
        }
    }

    //POST
    private static function postData($request_data)
    {
        $mysql = new MMysql(self::$configArr);
        $type = $request_data['type'];
        if($type == 0){
            if (!empty($request_data['name'])) {
                $data['name'] = $request_data['name'];
                $data['pwd'] = $request_data['pwd'];
                $mysql->insert('user',$data);
                $res  = array('state'=>'success');
                return $res;//返回新生成的资源对象
            } else {
                return false;
            }
        }
        elseif ($type == 1){
            $data['comment'] = $request_data['comment'];
            $data['userid'] = $request_data['userid'];
            $data['date'] = date('Y-m-d H:i:s');
             $mysql->insert('comment',$data);
             return $data;
        }
        else {
            $data['name'] = $request_data['name'];
            $data['pwd'] = $request_data['pwd'];
            $res = $mysql->field(array('pwd'))
                ->where('name=\'' . $data['name'] . '\'')
                ->select('user');
            if($res[0]['pwd']==$data['pwd']){
                return array('state'=>'success');
            }
            else{
                return array('state'=>'err');
            }
        }

    }


    //PATCH
    private static function patchData($request_data)
    {
        $mysql = new MMysql(self::$configArr);
        $userid = $request_data['userid'];
        $pwd = $request_data['pwd'];
        $mysql->where(array('id'=>$userid))->update('user',array('pwd'=>$pwd));
        return array('state'=>'success');
    }

    //DELETE
    private static function deleteData($request_data)
    {
        $mysql = new MMysql(self::$configArr);
        $commentid = (int)$request_data['commentid'];
        $mysql->where(array('id'=>$commentid))->delete('comment');
        return array('state'=>'success');
    }
}