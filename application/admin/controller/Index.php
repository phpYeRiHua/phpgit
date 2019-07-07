<?php
namespace app\admin\Controller;
use think\Request;
use think\Controller;

class Index extends Common
{
    public function index()
    {
        return $this->fetch();
    }
    public function top()
    {
        return $this->fetch();
    }
    public function menu()
    {
        // halt($this->_user);
        $this->assign('menus',$this->_user['menus']);
        return $this->fetch();
    }
    public function main()
    {
        return $this->fetch();
    }




}
