<?php


declare(strict_types=1);
echo "<h1>PHP Basic Control Structures task-3</h1>";



//function
function calculateTotal($price, $quantity) {
    return $price * $quantity;
}

//calling function
$pricePerItem = 15.99;
$quantity = 3;
$total = calculateTotal($pricePerItem, $quantity);

echo "Price per item: $".$pricePerItem;
echo "<br>";
echo "Quantity:" . $quantity;
echo "<br>";
echo "Total Price: $".$total;


// function with strict types


function addNumbers(int $a, int $b): int {
    return $a + $b;
}
$sum = addNumbers(5, 10);
echo "<br>";
echo "Sum: " . $sum;
echo "<br>";





$x = 10;

function testScope()
{
    $x = 20;// it favors local variable over global variable
    global $x; // to access global variable inside function
    echo $x;
}

testScope();

echo "<br>";

$str = "Hello, World!   ";

$str = trim($str); // Remove whitespace from both ends
echo "Trimmed String: '" . $str . "'";
echo "<br>";

$upperStr = strtoupper($str); // Convert to uppercase
echo "Uppercase String: '" . $upperStr . "'";
echo "<br>";

$lowerStr = strtolower($str); // Convert to lowercase
echo "Lowercase String: '" . $lowerStr . "'";
echo "<br>";

$replacedStr = str_replace("World", "PHP", $str); // Replace substring
echo "Replaced String: '" . $replacedStr . "'";


//array functions
echo "<br>";
echo "<h2>Array Functions</h2>";

$fruits = ["Apple", "Banana", "Orange"];

array_push($fruits, "Mango"); // Add element to the end
print_r($fruits);
echo "<br>";

array_pop($fruits); // Remove last element
print_r($fruits);
echo "<br>";

$fruitCount = count($fruits); // Count elements
echo "Number of fruits: " . $fruitCount;
echo "<br>";

$searchKey = array_search("Banana", $fruits); // Search for element
echo "Index of Banana: " . $searchKey;
echo "<br>";

sort($fruits); // Sort array
print_r($fruits);
echo "<br>";
