<?php

namespace app\admin\controller;

use app\admin\logic\Authorization;

class AuthManager extends Base
{
    public function index()
    {
        return view('index', $this->data);
    }

    public function action($id)
    {
        Authorization::updateRules();

        $group = $this->groupServer->find($id);

        $this->data['mainRules'] = Authorization::rules(2);
        $this->data['childRules'] = Authorization::rules(1);
        $this->data['thisGroup'] = $group;
        $this->data['groupRules'] = explode(',', $group['rules']);
        $this->data['groups'] = $this->groupServer->search(['status' => 1]);
        $this->data['tree'] = $this->menuServer->tree();
        return view('action', $this->data);
    }

    public function auth($id)
    {
        $post = $this->request->post();
        if(isset($post['rules'])){
            $this->authGroup($id, $post['rules']);
        }elseif(isset($post['memberIds'])){
            $this->authUser($id, $post['memberIds']);
        }
    }
    
    public function user($id)
    {
        $this->data['managers'] = $this->adminServer->search(['status' => 1]);
        $this->data['memberIds'] = Authorization::memberIds($id);
        $this->data['thisGroup'] = $this->groupServer->find($id);;
        $this->data['groups'] = $this->groupServer->search(['status' => 1]);
        return view('user', $this->data);
    }
    
    protected function authGroup($id, $rules)
    {
        $rules = implode(',', $rules);
        if($this->groupServer->update($id, ['rules' => $rules])){
            $this->success('授权成功');
        }else{
            $this->error('授权失败');
        }
    }
    
    protected function authUser($id, $memberIds)
    {
        if( Authorization::authUser($id, $memberIds)){
            $this->success('授权成功');
        }else{
            $this->error('授权失败');
        }
    }
}
