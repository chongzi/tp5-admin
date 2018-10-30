<?php

namespace app\admin\model;

// 模型字段文字解析
// 由于trait的特性。属性无法被重写，因此这里的属性只能是有公共文本值的属性。
trait TextParserTrait
{
    protected $status_opt = [
        -1 => '删除',
        0 => '禁用',
        1 => '正常',
    ];

    public function statusText()
    {
        if (isset($this->data['status'])) {
            return $this->status_opt[$this->data['status']];
        }
        return '';
    }

    protected $hide_opt = [
        0 => '显示',
        1 => '隐藏',
    ];

    public function hideText()
    {
        if (isset($this->data['hide'])) {
            return $this->hide_opt[$this->data['hide']];
        }
        return '';
    }

    protected $is_dev_opt = [
        0 => '否',
        1 => '是',
    ];
    
    public function isDevText()
    {
        if (isset($this->data['is_dev'])) {
            return $this->is_dev_opt[$this->data['is_dev']];
        }
        return '';
    }
}
