/************************************************************
 * Day 6 - JavaScript Basics
 * Topics Covered:
 * 1. JavaScript Introduction
 * 2. Script Placement (Explanation)
 * 3. Variables
 * 4. Data Types
 * 5. Console Logging
 * 6. Basic JavaScript Programs
 ************************************************************/

/*
============================================================
1. JavaScript Introduction
============================================================
JavaScript is a programming language used to make web pages
interactive. It can:
- Perform calculations
- Handle user actions
- Manipulate HTML & CSS
- Communicate with servers
.
*/

// JavaScript is executed line by line
console.log("JavaScript is running successfully!");


/*
============================================================
2. Script Placement (IMPORTANT NOTE)
============================================================
JavaScript can be added to HTML in 3 ways:
1. Inline JS        (not recommended)
2. Internal JS      (<script> inside HTML)
3. External JS      (this file - BEST PRACTICE)

Best practice:
<script src="day6.js"></script>
Place it at the end of <body> OR use `defer`
*/


/*
============================================================
3. Variables in JavaScript
============================================================
Variables store data values.
*/

// var (OLD - avoid using)
var oldWay = "This is var";
console.log(oldWay);

// let (modern, value can change)
let age = 21;
age = 22;
console.log("Age:", age);

// const (modern, value cannot change)
const country = "India";
console.log("Country:", country);


/*
============================================================
4. Data Types in JavaScript
============================================================
JavaScript is dynamically typed.
*/

// Number
let price = 99.99;
console.log("Price:", price, "| Type:", typeof price);

// String
let name = "Alok";
console.log("Name:", name, "| Type:", typeof name);

// Boolean
let isStudent = true;
console.log("Is Student:", isStudent, "| Type:", typeof isStudent);

// Undefined
let notAssigned;
console.log("Undefined value:", notAssigned, "| Type:", typeof notAssigned);

// Null (intentional absence of value)
let emptyValue = null;
console.log("Null value:", emptyValue, "| Type:", typeof emptyValue);

// Object
let student = {
  name: "Alok",
  age: 21,
  course: "Engineering"
};
console.log("Student Object:", student);

// Array
let skills = ["HTML", "CSS", "JavaScript"];
console.log("Skills Array:", skills);

// Function
function greet() {
  return "Hello, welcome to JavaScript!";
}
console.log(greet());


/*
============================================================
5. Console Logging
============================================================
Console is used for debugging and testing.
*/

console.log("This is a normal log");
console.warn("This is a warning message");
console.error("This is an error message");

console.table(student);


/*
============================================================
6. Basic JavaScript Programs
============================================================
*/

// Program 1: Print a message
console.log("Program 1: Hello World");

// Program 2: Add two numbers
let a = 10;
let b = 20;
let sum = a + b;
console.log("Program 2: Sum =", sum);

// Program 3: Check even or odd
let number = 7;
if (number % 2 === 0) {
  console.log("Program 3:", number, "is Even");
} else {
  console.log("Program 3:", number, "is Odd");
}

// Program 4: Simple calculator
let x = 15;
let y = 5;

console.log("Program 4: Addition =", x + y);
console.log("Program 4: Subtraction =", x - y);
console.log("Program 4: Multiplication =", x * y);
console.log("Program 4: Division =", x / y);

// Program 5: Greeting user
let userName = "Alok";
console.log("Program 5: Hello " + userName);

