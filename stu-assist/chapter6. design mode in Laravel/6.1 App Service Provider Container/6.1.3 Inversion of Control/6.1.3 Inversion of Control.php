<?php
/*
 * 此处 我们先实现一个单纯的IoC容器类 然后再用例子演示
 * */

// 设计容器类 容器类装实例或提供实例的回调函数
// 这句话的意思是 容器类最终为业务类提供实例 或者 提供一个闭包函数 该函数可以创建实例
class Container
{
    // 用于承载能够提供实例的回调函数 真正的容器还会装实例等其他内容
    // 从而实现单例等高级功能
    protected $bindings = [];

    // 绑定接口和生成相应实例的回调函数
    public function bind($abstract, $concrete = null, $share = false)
    {
        // 如果提供的参数不是一个闭包函数 则产生默认的闭包函数
        if(!$concrete instanceof Closure)
        {
            $concrete = $this->getClosure($abstract, $concrete);
        }
        $this->bindings[$abstract] = compact('concrete', 'share');
    }

    // 默认创建实例的闭包函数
    public function getClosure($abstract, $concrete)
    {
        // 创建实例的闭包函数 $c一般为IoC容器对象 即本类的对象 在调用闭包生成实例时提供
        // 即:build函数中的$concrete($this)
        return function ($c) use ($abstract, $concrete)
        {
            $method = ($abstract == $concrete) ? 'build' : 'make';
            return $c->$method($concrete);
        };
    }

    // 想要实例化对象 首先要解决这个类自身的依赖关系
    public function make($abstract)
    {
        $concrete = $this->getConcrete($abstract);
        if($this->isBuildable($concrete, $abstract))
        {
            $object = $this->build($concrete);
        }
        else
        {
            $object = $this->make($concrete);
        }
        return $object;
    }

    // 确认是否可实例化
    protected function isBuildable($concrete, $abstract)
    {
        return $concrete === $abstract || $concrete instanceof Closure;
    }

    // 获取绑定的闭包函数
    protected function getConcrete($abstract)
    {
        if(!isset($this->bindings[$abstract]))
        {
            return $abstract;
        }
        return $this->bindings[$abstract]['concrete'];
    }

    // 实例化对象
    public function build($concrete)
    {
        if($concrete instanceof Closure)
        {
            return $concrete($this);
        }

        try
        {
            $reflector = new ReflectionClass($concrete);
            if(!$reflector->isInstantiable())
            {
                echo $message = "Target [$concrete] is not instantiable";
            }
            $constructor = $reflector->getConstructor();
            if(is_null($constructor))
            {
                return new $concrete;
            }
            $dependencies = $constructor->getParameters();
            $instances = $this->getDependencies($dependencies);
            return $reflector->newInstanceArgs($instances);
        }
        catch (ReflectionException $e)
        {
            echo $message = "Target [$concrete] is not instantiable";
            return $message;
        }
    }

    // 解决通过反射机制实例化对象时的依赖
    protected function getDependencies($parameters)
    {
        $dependencies = [];
        foreach ($parameters as $parameter)
        {
            $dependency = $parameter->getClass();
            if(is_null($dependency))
            {
                $dependencies[] = NULL;
            }
            else
            {
                $dependencies[] = $this->resolveClass($parameter);
            }
        }
        return (array)$dependencies;
    }

    protected function resolveClass(ReflectionParameter $parameter)
    {
        return $this->make($parameter->getClass()->name);
    }
}

// 使用示例

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

    public function __construct(Visit $trafficTool)
    {
        /**
         * 依赖是从这里产生的
         * 旅游者类的构造函数中使用了旅游工具接口的实现
         */
        $this->trafficTool = $trafficTool;
    }

    /**
     * 基于旅游工具接口的实现 来实现本类的功能 就产生了依赖
     */
    public function visitTibet()
    {
        $this->trafficTool->go();
    }
}

$app = new Container();
// 完成容器填充
$app->bind("Visit", "Train");
$app->bind("traveller", "Traveller");
// 通过容器实现依赖注入 完成类的实例化
$tra = $app->make('traveller');
$tra->visitTibet();