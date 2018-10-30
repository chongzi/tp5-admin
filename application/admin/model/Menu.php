<?php

namespace app\admin\model;

use think\Model;

class Menu extends Model
{
    use TextParserTrait;
    protected $current = false;

    protected $childrenContainer = [];
    protected $sorted = false;

    public function appendChild(Menu $menu)
    {
        $this->childrenContainer[$menu->id] = $menu;
    }

    public function children()
    {
        if (!$this->sorted) {
            ksort($this->childrenContainer);
        }

        return $this->childrenContainer;
    }

    public function childrenOfGroup($group = null)
    {
        $map = [
            'pid' => $this->id,
        ];

        if ($group) {
            $map['group'] = $group;
        }

        $children = $this->where($map)->order('sort asc')->select();

        $groups = [];
        foreach ($children as $child) {
            if($child['group']){
                $group = $child['group'];
                if (!isset($groups[$group])) {
                    $groups[$group] = [];
                }

                $groups[$group][] = $child;
            }
        }

        return $groups;
    }

    public function setCurrent($value = true)
    {
        $this->current = $value;
    }

    public function current()
    {
        return $this->current;
    }
}
