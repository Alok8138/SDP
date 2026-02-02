<?php

echo "<h2>PHP Basic Data Types and Variables task-1</h2>";
$greeting = "Hello";

$year = 2026;

$price = 20.99;

$isActive  = true;

$items = ["Apple", "Banana", "Orange"];

$nothing = null;

echo $greeting.", The Year is ". $year. "<br>"; // Output: Hello, The Year is 2026

echo "Price: $".$price."<br>"; // Output: Price: $20.99

echo "is Active: ". $isActive . "<br>"; // Output: is Active: 1

print_r($items); // Output: Array ( [0] => Apple [1] => Banana [2] => Orange )

echo '<pre>';
var_dump($items);

print($price);


