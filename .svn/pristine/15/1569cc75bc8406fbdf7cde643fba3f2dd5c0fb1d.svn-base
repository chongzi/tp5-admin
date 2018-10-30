<?php

namespace app\admin\controller;

use think\Session;

class Menu extends Base
{
    public function index()
    {
        $pid = $this->request->param('pid', 0);
        $map = [
            'pid' => $pid,
        ];

        $this->data['map'] = $map;
        $this->data['chain'] = $this->menuServer->chain($pid);
        $this->data['menus'] = $this->menuServer->search($map, 'sort asc', 0);
        Session::set('back_forward', $this->request->url());
        return view('index', $this->data);
    }

    public function create()
    {
        $pid = $this->request->param('pid', 0);

        $this->data['info'] = ['pid' => $pid];
        $this->data['chain'] = $this->menuServer->chain($pid);
        $this->data['tree'] = $this->menuServer->tree();
        return view('create', $this->data);
    }

    public function save()
    {
        $data = $this->request->post();

        $validate = $this->validate($data, [
            'title' => 'require|max:25',
            'url' => 'require|max:25',
        ]);

        if ($validate === true) {
            if ($this->menuServer->add($data)) {
                $this->success('添加成功', Session::get('back_forward'));
            } else {
                $this->error('添加失败');
            }
        } else {
            $this->error('标题与连接必填');
        }
    }

    public function read($id)
    {
        $admin = $this->menuServer->find($id);
        return view('edit', ['admin' => $admin]);
    }

    public function edit($id)
    {
        $menu = $this->menuServer->find($id);

        $this->data['info'] = $menu;
        $this->data['chain'] = $this->menuServer->chain($menu->pid);
        $this->data['tree'] = $this->menuServer->tree();
        return view('edit', $this->data);
    }

    public function update($id)
    {
        $data = $this->request->put();

        $validate = $this->validate($data, [
            'title' => 'require|max:25',
            'url' => 'require|max:25',
        ]);

        if ($validate === true) {
            if ($this->menuServer->update($id, $data)) {
                $this->success('更新成功');
            }
        }
        $this->error('更新失败');
    }

    public function delete($id)
    {
        if ($this->menuServer->delete($id)) {
            $this->success('删除成功');
        } else {
            $this->success('删除失败');
        }
    }

    public function forbid($id)
    {
        $data['hide'] = 1;

        if ($this->menuServer->update($id, $data)) {
            $this->success('操作成功');
        } else {
            $this->error('操作失败');
        }
    }

    public function allow($id)
    {
        $data['hide'] = 0;

        if ($this->menuServer->update($id, $data)) {
            $this->success('操作成功');
        } else {
            $this->error('操作失败');
        }
    }

    public function clear()
    {
        if ($this->menuServer->clear()) {
            $this->success('清理成功');
        } else {
            $this->error('清理出错');
        }
    }

    public function sort()
    {
        $post = $this->request->post();

        if (isset($post['sort'])) {
            $sort = $post['sort'];

            foreach ($sort as $id => $val) {
                if (!$this->menuServer->update($id, ['sort' => $val])) {
                    $this->error('保存出错，请重试');
                }
            }
            $this->success('操作成功');
        } else {
            $this->error('数据不存在');
        }
    }
}
