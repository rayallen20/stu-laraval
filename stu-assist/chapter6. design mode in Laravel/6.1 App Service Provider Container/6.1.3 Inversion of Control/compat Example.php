<?php
$city = '北京';
$$city = 'city';
$state = '华北';
$event = '计算机协会';

$locationVars = [$$city, 'state'];

$result = compact("event", "这里没事", "北京",$locationVars);
//var_dump($result);
//die;

$bindings['Visit'] = [
    'concrete' => function ($c) use ($abstract, $concrete) // 其中 $abstract = "Visit", $concrete = "Train"
        {
            $method = ($abstract == $concrete) ? 'build' : 'make';
            return $c->$method($concrete);
        },
    'share' => false
];

$bindings['traveller'] = [
    'concrete' => function ($c) use ($abstract, $concrete) // 其中 $abstract = "traveller", $concrete = "Traveller"
    {
        $method = ($abstract == $concrete) ? 'build' : 'make';
        return $c->$method($concrete);
    },
    'share' => false
];

function ($c) use ($abstract, $concrete) // 其中 $abstract = "traveller", $concrete = "Traveller"
    {
        $method = ($abstract == $concrete) ? 'build' : 'make';
        return $c->$method($concrete);
    };
