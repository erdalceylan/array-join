# ![logo](assets/elastic-x-pack.png) Array Join for PHP 
[![GitHub package version](https://img.shields.io/packagist/v/erdalceylan/array-join.svg?style=flat-square)]()
[![Total Downloads](https://img.shields.io/packagist/dt/erdalceylan/array-join.svg?style=flat-square)]()
[![Packagist](https://img.shields.io/packagist/l/erdalceylan/array-join.svg?style=flat-square)]()
[![Travis](https://img.shields.io/badge/require-PHP%207-brightgreen.svg?style=flat-square)]()

## Installation

### Using Composer

```sh
composer require erdalceylan/array-join
```
##### OR
composer.json
```javascript
{
    "require": {
      "erdalceylan/array-join": "dev-master"
    }
}
```

### USAGE

#### data
```php
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
```
##### example 1
```php
 $instance = \ArrayJoin\Builder::newInstance()
     ->select("a.nick")
     ->from($users, "a")
     ->setFetchType(\ArrayJoin\Builder::FETCH_TYPE_ARRAY);
     
 $instance->execute();
 
 //output
 array (
   array ('nick' => 'erdal'),
   array ('nick' => 'furkan'),
   array ('nick' => 'huseyin'),
   array ('nick' => 'hümeyra'), 
   array ('nick' => 'tuba',)
);
```

##### example 2
```php
 $instance = \ArrayJoin\Builder::newInstance()
     ->select("a.id", "a.nick", "b.item")
     ->from($users, "a")
     ->innerJoin($items, "b", new \ArrayJoin\On("a.id = b.user_id"))
     ->setFetchType(\ArrayJoin\Builder::FETCH_TYPE_ARRAY)
     ->offset(1)
     ->limit(2);
     
 $instance->execute();
 //output
 array (
   array ('id' => 1,'nick' => 'erdal','item' => 'çorap',),
   array ('id' => 1,'nick' => 'erdal','item' => 'çorap',)
 );
```

##### example 3
```php
$instance = \ArrayJoin\Builder::newInstance()
    ->select("a.id", "a.nick", "b.item", "d.food")
    ->from($users, "a")
    ->innerJoin($items, "b", new \ArrayJoin\On("a.id = b.user_id"))
    ->leftJoin($texts, "c", new \ArrayJoin\On("a.id = c.user_id"))
    ->rightJoin($foods, "d", new \ArrayJoin\On("b.user_id = d.user_id"))
     ->where("a.id", "a.text", function ($fieldFirs, $fieldSecond){
         return $fieldFirs < 10;
     })
     ->groupBy("a.id", "d.food")
     ->limit(2)
     ->offset(1)
     ->setFetchType(\ArrayJoin\Builder::FETCH_TYPE_OBJECT);
     
 $instance->execute();
 
 array (
   stdClass::__set_state(array(
      'id' => 1,
      'nick' => 'erdal',
      'item' => 'çorap',
      'food' => 'iskender',
   )),
   stdClass::__set_state(array(
      'id' => 1,
      'nick' => 'erdal',
      'item' => 'çorap',
      'food' => 'iskender',
   )),
 );
```

