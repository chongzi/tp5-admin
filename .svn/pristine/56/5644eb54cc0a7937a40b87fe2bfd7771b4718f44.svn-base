<?php

namespace app\admin\logic;

use app\admin\model\Menu;
use think\Db;

class MenuServer
{
    public function find($id)
    {
        return Menu::get($id);
    }

    public function findByUrl($url)
    {
        return Menu::where(['url' => $url])->order('pid desc')->find();
    }

    public function add($data)
    {
        return Menu::create($data);
    }

    public function delete($id)
    {
        return Menu::destroy($id);
    }

    public function update($id, $data)
    {
        $menu = new Menu();
        return false !== $menu->save($data, ['id' => $id]);
    }

    public function nav()
    {
        $map = [
            'pid' => 0,
            'hide' => 0,
        ];

        return $this->search($map, 'sort asc');
    }

    public function search($map = [], $sort = 'id desc', $limit = 10)
    {
        $query = Menu::where($map)->order($sort);
        if ($limit) {
            return $query->paginate($limit);
        } else {
            return $query->select();
        }
    }

    public function chain($id)
    {
        $menu = $this->find($id);

        if (!$menu) {
            return [];
        } elseif ($menu['pid'] == 0) {
            return [$menu];
        } else {
            $menus = $this->chain($menu['pid']);

            array_push($menus, $menu);
            return $menus;
        }
    }

    public function tree()
    {
        $menus = Menu::select();

        $list = [];
        foreach ($menus as $menu) {
            $list[$menu->id] = $menu;
        };

        $tree = [];
        foreach ($list as $menu) {
            if ($menu->pid == 0) {
                $tree[$menu->id] = &$list[$menu->id];
            } else if (isset($list[$menu->pid])) {
                $parent = $list[$menu->pid];
                $parent->appendChild($menu);
            }
        }

        return $tree;
    }

    public function clear()
    {
        $result = Db::table('__MENU__ a')
            ->field('a.id')
            ->join('__MENU__ b', 'a.pid=b.id', 'LEFT')
            ->where('a.pid > 0 and b.id is null')
            ->select();

        $ids = [];
        foreach ($result as $item) {
            array_push($ids, $item['id']);
        }

        return false !== Menu::destroy($ids);
    }
}
