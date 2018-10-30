<?php

namespace app\admin\logic;

use app\admin\model\Admin;
use think\Session;

/**
 * 认证
 */
class Authentication
{
    public static function login($user_name, $password)
    {
        $admin = Admin::get(['name' => $user_name]);
        if ($admin && $admin->status) {
            if ($admin->checkPwd($password)) {
                self::setLoginUser($admin);
                return $admin;
            } else {
                return 0;
            }
        }

        return -1;
    }

    public static function logout()
    {
        return Session::clear('admin');
    }

    public static function checkLogin()
    {
        if ($id = Session::get('id', 'admin')) {
            $admin = Admin::get($id);
            return $admin;
        }
        return false;
    }

    protected static function setLoginUser(Admin $admin)
    {
        Session::set('id', $admin->id, 'admin');
        Session::set('name', $admin->name, 'admin');
        Session::set('nick_name', $admin->nick_name, 'admin');
    }

}
