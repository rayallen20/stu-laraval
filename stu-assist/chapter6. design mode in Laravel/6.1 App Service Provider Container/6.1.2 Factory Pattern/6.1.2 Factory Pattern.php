<?php
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

// 交通工具工厂
class TrafficToolFactory
{
    public function createTrafficTool($name)
    {
        switch ($name)
        {
            case 'Leg':
                return new Leg();
                break;
            case 'Car':
                return new Car();
                break;
            case 'Train':
                return new Train();
                break;
            default:
                exit('set trafficTool error!!');
                break;
        }
    }
}

// 旅游者类 通过交通工具工厂 把创建交通工具这一职责(本不该属于旅游者类的职责)
// 分配给交通工具工厂来实现
class Traveller
{
    protected $trafficTool;
    public function __construct($trafficTool)
    {
        // 其实也产生了依赖 但是 这个依赖是旅游者类和交通工具工厂类的依赖
        $factory = new TrafficToolFactory();
        $this->trafficTool = $factory->createTrafficTool($trafficTool);
    }

    public function visitTibet()
    {
        $this->trafficTool->go();
    }
}

$tra = new Traveller('Train');
$tra->visitTibet();