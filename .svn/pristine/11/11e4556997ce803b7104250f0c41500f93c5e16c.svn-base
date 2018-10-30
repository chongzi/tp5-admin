<?php

namespace app\admin\logic;

use app\admin\model\Config;
use think\Db;
use think\Config as ThinkConfig;

class ConfigServer
{
    public function find($id)
    {
        return Config::get($id);
    }

    public function findByUrl($url)
    {
        return Config::where(['url' => $url])->order('pid desc')->find();
    }

    public function add($data)
    {
        return Config::create($data);
    }

    public function delete($id)
    {
        return Config::destroy($id);
    }

    public function update($id, $data)
    {
        $menu = new Config();
        return false !== $menu->save($data, ['id' => $id]);
    }

    public function load()
    {
        $list = $this->lists();
        ThinkConfig::set($list);
    }

    public function search($map = [], $sort = 'id desc', $limit = 10)
    {
        $query = Config::where($map)->order($sort);
        if ($limit) {
            return $query->paginate($limit);
        } else {
            return $query->select();
        }
    }

    public function lists(){
        $data   = Config::where('status', '1')->select();
        $config = array();
        if($data && is_array($data)){
            foreach ($data as $value) {
                $config[$value['name']] = $this->parse($value['type'], $value['value']);
            }
        }
        return $config;
    }

    private function parse($type, $value){
        switch ($type) {
            case 3: //解析数组
                $array = preg_split('/[,;\r\n]+/', trim($value, ",;\r\n"));
                if(strpos($value,':')){
                    $value  = array();
                    foreach ($array as $val) {
                        list($k, $v) = explode(':', $val);
                        $value[$k]   = $v;
                    }
                }else{
                    $value =    $array;
                }
                break;
        }
        return $value;
    }
}
