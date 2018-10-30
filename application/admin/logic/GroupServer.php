<?php

namespace app\admin\logic;

use app\admin\model\AuthGroup;

class GroupServer
{

    public function find($id)
    {
        return AuthGroup::get($id);
    }

    public function search($map = [], $sort = 'id desc', $limit = 10)
    {
        $query = AuthGroup::where($map)->order($sort);
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
        return AuthGroup::create($data);
    }

    public function update($id, $data)
    {
        $group = new AuthGroup();
        return false !== $group->save($data, ['id' => $id]);
    }

    public function delete($id)
    {
        return AuthGroup::destroy($id);
    }
}
