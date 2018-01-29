<?php
class A
{

}

class B
{

}

function isBuildable($a, $b)
{
    return $a === $b || $a instanceof A;
}

$a = new A();
$b = new B();

$result = isBuildable($a, $b);
var_dump($result);
die;
