<?php

$user_settings = [
    "theme" => "dark",
    "notifications" => true,
    "login_count" => 5
];

$serialized_data = serialize($user_settings);
echo "<pre>";
echo $serialized_data;
echo "</pre>";
echo "<pre>";
var_dump($user_settings);

$original_array = unserialize($serialized_data);
echo "<pre>";
echo $original_array['theme']; // Outputs: dark




$user = [
    "name" => "Alex",
    "age" => 25,
    "skills" => ["PHP", "JSON", "MySQL"]
];

$json_string = json_encode($user);
echo "<pre>";
echo $json_string;




$json = '{"name":"Alex","age":25}';

// 1. Convert to an Object (Default)
$obj = json_decode($json);
echo "<pre>";
print_r($obj);
echo "<pre>";
echo $obj->name; // Use arrow notation

// 2. Convert to an Array (Recommended for beginners)
$arr = json_decode($json, true);
echo "<pre>";
echo $arr['name']; // Use square brackets