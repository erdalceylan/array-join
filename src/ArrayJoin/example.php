<?php
/**
 * Created by IntelliJ IDEA.
 * User: erdal
 * Date: 19.10.2017
 * Time: 15:47
 */

require 'Field.php';
require 'On.php';
require 'JoinItem.php';
require 'Where.php';
require 'Builder.php';

 $users = [
    ["id"=>1, "nick"=>"erdal"],
    ["id"=>2, "nick"=>"furkan" ],
    ["id"=>3, "nick"=>"huseyin"],
    ["id"=>4, "nick"=>"hümeyra" ],
    ["id"=>5, "nick"=>"tuba" ],
];

 $items = [
     ["user_id"=>1, "item"=>"kaban", "mmx" => "mmx1"],
    ["user_id"=>1, "item"=>"çorap", "mmx" => "mmx2"],
    ["user_id"=>1, "item"=>"çorap", "mmx" => "mmx3"],
    ["user_id"=>1, "item"=>"çorap", "mmx" => "mmx4"],
    ["user_id"=>1, "item"=>"çorap", "mmx" => "mmx5"],
    ["user_id"=>1, "item"=>"çorap", "mmx" => "mmx6"],
    ["user_id"=>2, "item"=>"araba", "mmx" => "mmx7"],
    ["user_id"=>9, "item"=>"ev", "mmx" => "mmx8"],
    ["user_id"=>10, "item"=>"yat", "mmx" => "mmx9"],
];

$texts = [
    ["user_id"=>1, "text"=>"merhaba ben erdal"],
    ["user_id"=>15, "text"=>" hi my name is erdal"],
];



 $marged = \ArrayJoin\Builder::newInstance()
 ->select("a.id", "a.nick", "a.text","a.mmx")
 ->from($users, "a")
 ->innerJoin($items, "b", new \ArrayJoin\On("a.id = b.user_id"))
 ->rightJoin($texts, "c", new \ArrayJoin\On("b.user_id = c.user_id"))
 ->where("a.nick", "a.text", function ($fieldFirs, $fieldSecond){
     return $fieldFirs == "erdal";
 })
 ->limit(2)
 ->offset(1);

 var_dump($marged->execute());exit;