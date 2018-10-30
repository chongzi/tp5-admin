<?php

namespace app\admin\controller;

use think\Session;

class Config extends Base
{
    public function index()
    {
        $this->data['configs'] = $this->configServer->search();
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
            'value' => 'require|max:25',
        ]);

        if ($validate === true) {
            if ($this->configServer->add($data)) {
                $this->success('添加成功', Session::get('back_forward'));
            } else {
                $this->error('添加失败');
            }
        } else {
            $this->error('标示与值必填');
        }
    }

    public function read($id)
    {
        $admin = $this->configServer->find($id);
        return view('edit', ['admin' => $admin]);
    }

    public function edit($id)
    {
        $this->data['config'] = $this->configServer->find($id);
        return view('edit', $this->data);
    }

    public function update($id)
    {
        $data = $this->request->put();

        $validate = $this->validate($data, [
            'name' => 'require|max:25',
        ]);

        if ($validate === true) {
            if ($this->configServer->update($id, $data)) {
                $this->success('更新成功');
            }
        }
        $this->error('更新失败');
    }

    public function delete($id)
    {
        if ($this->configServer->delete($id)) {
            $this->success('删除成功');
        } else {
            $this->success('删除失败');
        }
    }
}
