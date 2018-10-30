<?php

namespace app\admin\logic;

use app\admin\model\Admin;

class AdminServer
{
    protected $allowField = ['nick_name', 'avatar', 'pwd', 'status', 'last_login_time', 'login_time'];

    public function find($id)
    {
        return Admin::get($id);
    }

    public function search($map = [], $sort = 'id desc', $limit = 10)
    {
        $query = Admin::where($map)->order($sort);
        if ($limit) {
            return $query->paginate($limit);
        } else {
            return $query->select();
        }
    }

    public function add($data)
    {
        if (isset($data['nick_name'])) {
            $data['nick_name'] = $data['name'];
        }
        return Admin::create($data);
    }

    public function update($id, $data)
    {
        $admin = new Admin();
        return false !== $admin->allowField($this->allowField)->save($data, ['id' => $id]);
    }

    public function delete($id)
    {
        return Admin::destroy($id);
    }
}
