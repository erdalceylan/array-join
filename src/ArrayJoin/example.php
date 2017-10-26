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
     (object)["id"=>2, "nick"=>"furkan" ],
    ["id"=>3, "nick"=>"huseyin"],
    ["id"=>4, "nick"=>"hümeyra" ],
    ["id"=>5, "nick"=>"tuba" ],
];

 $items = [
     ["user_id"=>1, "item"=>"kaban", "mmx" => "mmx1"],
    ["user_id"=>1, "item"=>"çorap", "mmx" => "mmx2"],
    ["user_id"=>1, "item"=>"çorap", "mmx" => "mmx3"],
     (object)["user_id"=>1, "item"=>"çorap", "mmx" => "mmx4"],
    ["user_id"=>1, "item"=>"çorap", "mmx" => "mmx5"],
    ["user_id"=>1, "item"=>"çorap", "mmx" => "mmx6"],
    ["user_id"=>2, "item"=>"araba", "mmx" => "mmx7"],
     (object)["user_id"=>9, "item"=>"ev", "mmx" => "mmx8"],
    ["user_id"=>10, "item"=>"yat", "mmx" => "mmx9"],
];

$foods = [
    ["user_id"=>1, "food"=>"iskender"],
    ["user_id"=>2, "food"=>"adana"],
];

$texts = [
    ["user_id"=>1, "text"=>"merhaba"],
    ["user_id"=>15, "text"=>" hi"],
];



$instance = \ArrayJoin\Builder::newInstance()
    ->select("a.id", "a.nick", "b.item", "d.food")
    ->from($users, "a")
    ->innerJoin($items, "b", new \ArrayJoin\On("a.id = b.user_id"))
    ->leftJoin($texts, "c", new \ArrayJoin\On("a.id = c.user_id"))
    ->rightJoin($foods, "d", new \ArrayJoin\On("b.user_id = d.user_id"))
     ->where("a.id", "a.text", function ($fieldFirs, $fieldSecond){
         return $fieldFirs < 10;
     })
     ->limit(2)
     ->offset(1)
     ->setFetchType(\ArrayJoin\Builder::FETCH_TYPE_OBJECT);

 var_export($instance->execute());exit;