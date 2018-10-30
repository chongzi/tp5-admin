<?php

namespace app\admin\logic;

use app\admin\model\Admin;
use app\admin\model\AuthGroup;
use app\admin\model\AuthGroupAccess;
use app\admin\model\AuthRule;
use app\admin\model\Menu;

/**
 * 授权
 */
class Authorization
{
    protected static $allowRules;

    public static function updateRules()
    {
        $menus = Menu::select();
        $rules = AuthRule::where('module', 'admin')
            ->where('type', 'in', [1, 2])
            ->order('name')
            ->select();

        $data = [];
        foreach ($menus as $menu) {
            
            $temp['name'] = self::formatUrl($menu['url']);
            $temp['title'] = $menu['title'];
            $temp['module'] = 'admin';
            if ($menu['pid'] > 0) {
                $temp['type'] = 1;
            } else {
                $temp['type'] = 2;
            }
            $temp['status'] = 1;
            $data[strtolower($temp['name'] . $temp['module'] . $temp['type'])] = $temp; //去除重复项
        }

        $update = array(); //保存需要更新的节点
        $ids = array(); //保存需要删除的节点的id
        foreach ($rules as $index => $rule) {
            $key = strtolower($rule['name'] . $rule['module'] . $rule['type']);
            //如果数据库中的规则与配置的节点匹配,说明是需要更新的节点
            if (isset($data[$key])) {
                $data[$key]['id'] = $rule['id']; //为需要更新的节点补充id值
                $update[] = $data[$key];
                unset($data[$key]);
                unset($rules[$index]);
                unset($rule['condition']);
                $diff[$rule['id']] = $rule;
            } elseif ($rule['status'] == 1) {
                $ids[] = $rule['id'];
            }
        }

        if (count($update)) {
            $update = array_filter($update, function ($item) use ($diff) {
                return $item != $diff[$item['id']];
            });

            (new AuthRule)->saveAll($update);
        }
        if (count($ids)) {
            AuthRule::where('id', 'in', $ids)->update(['status' => -1]);
        }
        if (count($data)) {
            (new AuthRule)->saveAll(array_values($data));
        }
    }

    public static function rules($type = 1)
    {
        $rules = AuthRule::where('module', 'admin')
            ->where('status', 1)
            ->where('type', $type)
            ->column('name', 'id');
        return array_flip($rules);
    }

    public static function memberIds($groupId)
    {
        $ids = AuthGroupAccess::where('group_id', $groupId)->column('uid');

        if ($ids) {
            return $ids;
        } else {
            return [];
        }
    }

    public static function groupIds($uid)
    {
        $groupIds = AuthGroupAccess::where('uid', $uid)->column('group_id');

        if ($groupIds) {
            return $groupIds;
        } else {
            return [];
        }
    }

    public static function authUser($groupId, $uids)
    {
        $exists = self::memberIds($groupId);

        $add = [];
        $delete = [];
        if (empty($exists)) {
            $add = $uids;
        } elseif (empty($uids)) {
            $delete = $exists;
        } else {
            $remain = array_intersect($exists, $uids);

            $delete = array_diff($exists, $remain);
            $add = array_diff($uids, $remain);
        }

        if (!empty($delete)) {
            AuthGroupAccess::where('uid', 'in', $delete)->where('group_id', $groupId)->delete();
        }

        if (!empty($add)) {
            $all = [];
            foreach ($add as $id) {
                $all[] = [
                    'uid' => $id,
                    'group_id' => $groupId,
                ];
            }

            (new AuthGroupAccess)->saveAll($all);
        }
        return true;
    }

    public static function authRoles($uid, $groupIds)
    {

        $exists = self::groupIds($uid);

        $add = [];
        $delete = [];
        if (empty($exists)) {
            $add = $groupIds;
        } elseif (empty($groupIds)) {
            $delete = $exists;
        } else {
            $remain = array_intersect($exists, $groupIds);

            $delete = array_diff($exists, $remain);
            $add = array_diff($groupIds, $remain);
        }

        if (!empty($delete)) {
            AuthGroupAccess::where('group_id', 'in', $delete)->where('uid', $uid)->delete();
        }

        if (!empty($add)) {
            $all = [];
            foreach ($add as $groupId) {
                $all[] = [
                    'uid' => $uid,
                    'group_id' => $groupId,
                ];
            }

            (new AuthGroupAccess)->saveAll($all);
        }
        return true;
    }

    public static function check($uid, $action)
    {
        $rules = AuthRule::where(['name' => $action])->column('id');
        if (!$rules) {
            return false;
        }

        $allowRules = [];
        if(isset(self::$allowRules[$uid])){
            $allowRules = self::$allowRules[$uid];
        }else{

            $groupIds = self::groupIds($uid);
            $groups = AuthGroup::where('id', 'in', $groupIds)->select();
            if (empty($groups)) {
                return false;
            }
            
            foreach ($groups as $group) {
                $_rules = explode(',', $group['rules']);

                $allowRules = array_merge($allowRules, $_rules);
            }

            self::$allowRules[$uid] = $allowRules = array_unique($allowRules);

        }
        if (count($rules) == count(array_intersect($rules, $allowRules))) {
            return true;
        }
        return false;
    }
    
    public static function formatUrl($url)
    {
        $path = [
            'admin', 'Index', 'index'
        ];
        $info = explode('/', $url);
        
        if(count($info) == 1){
            $path[1] = preg_replace_callback('/_([a-zA-Z])/', function ($match) {return strtoupper($match[1]);}, $info[0]);
        }elseif(count($info) == 2){
            $path[1] = preg_replace_callback('/_([a-zA-Z])/', function ($match) {return strtoupper($match[1]);}, $info[0]);
            $path[2] = strtolower($info[1]);
        }else{
            $path[0] = strtolower($info[0]);
            $path[1] = preg_replace_callback('/_([a-zA-Z])/', function ($match) {return strtoupper($match[1]);}, $info[1]);
            $path[2] = strtolower($info[2]);
        }
        
        return implode('/', $info);
    }
}
