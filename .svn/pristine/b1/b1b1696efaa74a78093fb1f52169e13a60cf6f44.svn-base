<?php

namespace app\admin\controller;

use think\Session;

class Group extends Base
{

    public function index()
    {
        $this->data['groups'] = $this->groupServer->search([], 'id asc');
        Session::set('back_forward', $this->request->url());
        return view('index', $this->data);
    }

    public function create()
    {
        return view('create', $this->data);
    }

    public function save()
    {
        $data = $this->request->post();

        $data['module'] = 'admin';
        $data['type'] = '1';

        $validate = $this->validate($data, [
            'title' => 'require|max:25',
        ]);

        if ($validate === true) {
            if ($this->groupServer->add($data)) {
                $this->success('添加成功', Session::get('back_forward'));
            } else {
                $this->error('添加失败');
            }
        } else {
            $this->error('用户组名称必填');
        }
    }

    public function read($id)
    {
        $this->data['group'] = $this->groupServer->find($id);
        return view('edit', $this->data);
    }

    public function edit($id)
    {
        return $this->read($id);
    }

    public function update($id)
    {
        $data = $this->request->put();

        $validate = $this->validate($data, [
            'title' => 'max:25',
        ]);

        if ($validate === true) {
            if ($this->groupServer->update($id, $data)) {
                $this->success('更新成功', Session::get('back_forward'));
            } else {
                $this->error('更新失败');
            }
        } else {
            $this->error('昵称或密码格式不正确');
        }
    }

    public function delete($id)
    {

        if ($this->groupServer->delete($id)) {
            $this->success('操作成功');
        } else {
            $this->error('操作失败');
        }
    }

    public function forbid($id)
    {
        $data['status'] = 0;

        if ($this->groupServer->update($id, $data)) {
            $this->success('操作成功');
        } else {
            $this->error('操作失败');
        }
    }

    public function allow($id)
    {
        $data['status'] = 1;

        if ($this->groupServer->update($id, $data)) {
            $this->success('操作成功');
        } else {
            $this->error('操作失败');
        }
    }
}
