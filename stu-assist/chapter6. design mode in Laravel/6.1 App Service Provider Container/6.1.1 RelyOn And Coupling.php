<?php
/*
 * 6.1.1 依赖与耦合
 * 如下代码要回答一个问题:什么是依赖?
 * */

// 旅游工具接口
interface Visit
{
    public function go();
}

// 旅行的不同交通工具实现:腿
class Leg implements Visit
{
    public function go()
    {
        echo '走路去西藏';
    }
}

// 旅行的不同交通工具实现:汽车
class Car implements Visit
{
    public function go()
    {
        echo '开车去西藏';
    }
}

// 旅行的不同交通工具实现:火车
class Train implements Visit
{
    public function go()
    {
        echo '坐火车去西藏';
    }
}

// 旅游者类 这个类在实现功能时 需要依赖旅游工具接口的实现
class Traveller
{
    protected $trafficTool;

    public function __construct()
    {
        /**
         * 依赖是从这里产生的
         * 旅游者类的构造函数中使用了旅游工具接口的实现
        */
        $this->trafficTool = new Leg();
    }

    /**
     * 基于旅游工具接口的实现 来实现本类的功能 就产生了依赖
    */
    public function visitTibet()
    {
        $this->trafficTool->go();
    }
}

$tra = new Traveller();
$tra->visitTibet();