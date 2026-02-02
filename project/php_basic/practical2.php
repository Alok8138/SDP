<?php 

//PHP Basic Control Structures task-2

echo "<h1>PHP Basic Control Structures task-2</h1>";


//switch case
$day = "sunday";

switch (strtolower($day)){
    case "monday":
        echo "Today is monday";
        echo "<br>";
        break;
    case "tuesday":
        echo "Today is tuesday";
        echo "<br>";
        break;
    case "wednesday":
        echo "Today is wednesday";
        echo "<br>";
        break;
    case "thursday":
        echo "Today is thursday";
        echo "<br>";
        break;
    case "friday":
        echo "Today is friday";
        echo "<br>";
        break;
    case "saturday":
        echo "Today is saturday";
        echo "<br>";   
        break;
    case "sunday":
        echo "Today is sunday";
        echo "<br>";
        break;
    default:
        echo "Invalid day";
}


//if else

$marks = 78;

if ($marks >= 90) {
    echo "Grade A";
    echo "<br>";
} elseif ($marks >= 75) {
    echo "Grade B";
    echo "<br>";
} elseif ($marks >= 50) {
    echo "Grade C";
    echo "<br>";
} else {
    echo "Fail";
    echo "<br>";
}

$age  = 20;

if($age >= 18){
    echo "You are eligible to vote.";
    echo "<br>";
} else {
    echo "You are not eligible to vote.";
    echo "<br>";
}


//loops
$items = ["Apple", "Banana", "Orange"];
echo "<h2>For Loop</h2>";
for($i = 0; $i < count($items); $i++){
    echo $items[$i]. "<br>";
}

echo "using for each loop<br>";

foreach($items as $itm){
    echo $itm. "<br>";
}


echo "<h2>Foreach Loop</h2>";
$student = ["name" => "Alok", "age" => 22, "course" => "Engineering"];


foreach($student as $key => $value){
    echo "$key  :$value <br>";
}

echo "<h2>While Loop</h2>";

//while loop
$age  = 1;
while($age < 25){
    echo "Your age is: $age <br>";
    $age++;
}   

?>

