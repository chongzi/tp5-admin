<?php

namespace app\admin\controller;

use app\admin\logic\Authorization;
use think\Session;

class Manager extends Base
{

    public function index()
    {
        $this->data['managers'] = $this->adminServer->search([], 'id asc');
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

        $validate = $this->validate($data, [
            'name' => 'require|max:25',
            'pwd' => 'require|length:6,25',
        ]);

        if ($validate === true) {
            if ($this->adminServer->add($data)) {
                $this->success('添加成功', Session::get('back_forward'));
            } else {
                $this->error('添加失败');
            }
        } else {
            $this->error('密码未填写，或用户名、密码格式不正确');
        }
    }

    public function read($id)
    {
        $this->data['manager'] = $this->adminServer->find($id);
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
            'nick_name' => 'max:25',
            'pwd' => 'length:6,25',
        ]);

        if ($validate === true) {
            if ($this->adminServer->update($id, $data)) {
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
        if ($this->adminServer->delete($id)) {
            $this->success('操作成功');
        } else {
            $this->error('操作失败');
        }
    }

    public function forbid($id)
    {
        $data['status'] = 0;

        if ($this->adminServer->update($id, $data)) {
            $this->success('操作成功');
        } else {
            $this->error('操作失败');
        }
    }

    public function allow($id)
    {
        $data['status'] = 1;

        if ($this->adminServer->update($id, $data)) {
            $this->success('操作成功');
        } else {
            $this->error('操作失败');
        }
    }

    public function group($id)
    {
        $this->data['manager'] = $this->adminServer->find($id);
        $this->data['groupIds'] = Authorization::groupIds($id);
        $this->data['groups'] = $this->groupServer->search(['status' => 1]);
        return view('group', $this->data);
    }

    public function auth($id)
    {
        $post = $this->request->post();
        
        if(!isset($post['groupIds'])){
            $groupIds = [];
        }else{
            $groupIds = $post['groupIds'];
        }
        
        if(Authorization::authRoles($id, $groupIds)){
            $this->success('授权成功');
        }else{
            $this->error('授权失败');
        }
    }
}
