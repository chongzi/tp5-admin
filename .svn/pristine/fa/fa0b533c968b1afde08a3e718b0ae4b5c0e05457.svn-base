<?php

namespace app\admin\controller;

use app\admin\logic\AdminServer;
use app\admin\logic\Authentication;
use app\admin\logic\Authorization;
use app\admin\logic\ConfigServer;
use app\admin\logic\GroupServer;
use app\admin\logic\MenuServer;
use app\admin\model\Menu;
use think\Controller;
use think\Request;

class Base extends Controller
{
    protected $data = [
        // 主导航
        'nav' => [],

        // 侧栏菜单
        'sideMenus' => [],

        // 定位
        'location' => [],

        // 是否是超级管理员
        'isRoot' => false,

        // 查询输入
        'search' => []
    ];

    protected $allowActions = [
        'admin/Index/profile',
        'admin/Index/update',
        'admin/Index/upload',
    ];

    protected $request;
    protected $action;

    protected $admin;

    protected $adminServer;
    protected $groupServer;
    protected $menuServer;
    protected $configServer;

    public function __construct()
    {
        $this->request = Request::instance();
        $this->adminServer = new AdminServer;
        $this->groupServer = new GroupServer;
        $this->menuServer = new menuServer;
        $this->configServer = new ConfigServer;

        $dispatch = $this->request->dispatch();
        $module = array_map(function ($item) {
            return preg_replace_callback('/_([a-zA-Z])/', function ($match) {return strtoupper($match[1]);}, $item);
        }, $dispatch['module']);
        $module[1] = ucfirst($module[1]);

        $this->action = implode('/', $module);

        $this->check();
        if ($this->request->isGet()) {
            $this->nav();
        }

        $this->configServer->load();

    }

    protected function check()
    {
        if ($admin = Authentication::checkLogin()) {
            $this->data['admin'] = $this->admin = $admin;

            if ($this->admin->id === 1) {
                $this->data['isRoot'] = true;
                return true;
            }

            if (in_array($this->action, $this->allowActions)) {
                return true;
            }

            if (!Authorization::check($this->admin->id, $this->action)) {

                $this->error('权限不足。', url('pub/index'));
            }
        } else {
            $this->redirect('pub/login');
        }
    }

    protected function nav()
    {
        $current = $this->menuServer->findByUrl($this->action);
        $nav = $this->menuServer->nav();
        $this->data['nav'] = $this->menuFilter($nav);
        if ($current) {
            $location = $this->menuServer->chain($current->id);
            $rootMenu = $location[0];

            $this->current($nav, $rootMenu);

            $sideMenus = $rootMenu->childrenOfGroup();
            if (isset($location[1])) {
                $this->current($sideMenus, $location[1]);
            }

            $this->data['sideMenus'] = $this->menuFilter($sideMenus);
            $this->data['location'] = $location;
        }
    }

    protected function menuFilter($menus)
    {
        if ($this->data['isRoot']) {
            return $menus;
        }

        if (method_exists($menus, 'getCollection')) {
            return $menus->filter(function ($menu) {
                if (!Authorization::check($this->admin->id, $menu->url)) {
                    return false;
                }
                return true;
            });
        } elseif (is_array($menus)) {
            $_menus = [];
            foreach ($menus as $key => $subMenus) {
                $subMenus = $this->menuFilter($subMenus);

                if (empty($subMenus)) {
                    continue;
                }
                $_menus[$key] = $subMenus;
            }
            return $_menus;
        } elseif ($menus instanceof Menu) {
            if (!Authorization::check($this->admin->id, $menus->url)) {
                return [];
            }
            return $menus;
        }
        return $menus;
    }

    protected function current(&$nav, $current)
    {
        $result = false;
        foreach ($nav as &$item) {
            if ($item instanceof Menu) {
                if ($item->id == $current->id) {
                    $item->setCurrent();
                    $result = true;
                }
            }

            if (is_array($item)) {
                $result = $this->current($item, $current);
            }

            $item['current'] = $result;
        }
        return $result;
    }

    protected function searchCondition()
    {
        $map = [];
        if (!empty($this->searchField)) {
            foreach ($this->searchField as $field) {
                $value = $this->request->get($field);
                // 传递给模板的条件
                $this->data['search'][$field] = $value;

                // sql 查询条件
                if (isset($value) && $value !== '') {
                    $map[$field] = $value;
                }
            }
        } else {
            $map = $this->request->get();
        }

        return $map;
    }

    protected function success($msg = '', $url = null, $data = '', $wait = 1, array $header = [])
    {   
        return parent::success($msg, $url, $data, $wait, $header);
    }
}
