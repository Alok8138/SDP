"use strict";

/*
====================================================
DAY 8 - JAVASCRIPT
TOPICS COVERED:
1. Functions
2. Parameters
3. Return values
4. Arrow functions
5. Loops:
   - for
   - while
   - do-while
   - for...of
   - for...in
6. Reusable functions
7. Loop + function combinations
8. IIFE (Immediately Invoked Function Expression)
====================================================
*/

/*--------------------------------------------------
1. BASIC FUNCTION (NO PARAMETER, NO RETURN)
--------------------------------------------------*/

function greet() {
  console.log("Hello, welcome to JavaScript!");
}

greet();

/*--------------------------------------------------
2. FUNCTION WITH PARAMETERS
--------------------------------------------------*/

function greetUser(name) {
  console.log("Hello " + name + "!");
}

greetUser("Alok");

/*--------------------------------------------------
3. FUNCTION WITH RETURN VALUE
--------------------------------------------------*/

function add(a, b) {
  return a + b;
}

console.log("Sum:", add(10, 20));

/*--------------------------------------------------
4. FUNCTION WITH MULTIPLE PARAMETERS
--------------------------------------------------*/

function calculateArea(length, width) {
  return length * width;
}

console.log("Area:", calculateArea(5, 4));

/*--------------------------------------------------
5. ARROW FUNCTIONS
--------------------------------------------------*/

const multiply = (a, b) => {
  return a * b;
};

console.log("Multiply:", multiply(3, 4));

// Arrow function with implicit return
const square = num => num * num;
console.log("Square:", square(6));

/*--------------------------------------------------
6. FOR LOOP
--------------------------------------------------*/

for (let i = 1; i <= 5; i++) {
  console.log("For Loop:", i);
}

/*--------------------------------------------------
7. WHILE LOOP
--------------------------------------------------*/

let count = 1;
while (count <= 3) {
  console.log("While Loop:", count);
  count++;
}

/*--------------------------------------------------
8. DO-WHILE LOOP
--------------------------------------------------*/

let num = 1;
do {
  console.log("Do-While:", num);
  num++;
} while (num <= 3);

/*--------------------------------------------------
9. FOR...OF LOOP (ARRAY)
--------------------------------------------------*/

const fruits = ["Apple", "Banana", "Mango"];

for (const fruit of fruits) {
  console.log("Fruit:", fruit);
}

/*--------------------------------------------------
10. FOR...IN LOOP (OBJECT)
--------------------------------------------------*/

const student = {
  name: "Alok",
  age: 21,
  course: "Engineering"
};

const b = student.keys;
console.log(b);


for (const key in student) {
  console.log(key + ":", student[key]);
}


/*--------------------------------------------------
12. REUSABLE FUNCTION USING LOOP
--------------------------------------------------*/

function printNumbers(n) {
  for (let i = 1; i <= n; i++) {
    console.log(i);
  }
}

printNumbers(5);

/*--------------------------------------------------
13. FUNCTION WITH LOOP & RETURN VALUE
--------------------------------------------------*/

function sumTillN(n) {
  let total = 0;

  for (let i = 1; i <= n; i++) {
    total += i;
  }

  return total;
}

console.log("Sum till 10:", sumTillN(10));

/*--------------------------------------------------
14. ARROW FUNCTION WITH LOOP
--------------------------------------------------*/

const countEvenNumbers = (n) => {
  let count = 0;

  for (let i = 1; i <= n; i++) {
    if (i % 2 === 0) {
      count++;
    }
  }

  return count;
};

console.log("Even numbers till 10:", countEvenNumbers(10));

/*--------------------------------------------------
15. FUNCTION CALLING ANOTHER FUNCTION
--------------------------------------------------*/

function doubleNumber(num) {
  return num * 2;
}

function processNumber(num) {
  console.log("Processed:", doubleNumber(num));
}

processNumber(8);

/*--------------------------------------------------
16. REAL-WORLD STYLE FUNCTION
--------------------------------------------------*/

function checkNumber(num) {
  if (num > 0) return "Positive";
  if (num < 0) return "Negative";
  return "Zero";
}

console.log(checkNumber(10));
console.log(checkNumber(-3));
console.log(checkNumber(0));

/*--------------------------------------------------
17. IIFE (Immediately Invoked Function Expression)
--------------------------------------------------*/

/*
IIFE runs immediately after definition.
Used to avoid polluting global scope.
*/

// Normal function IIFE
(function () {
  console.log("IIFE executed immediately!");
})();

// IIFE with parameters
(function (name) {
  console.log("Hello from IIFE,", name);
})("Alok");

// Arrow function IIFE
(() => {
  console.log("Arrow IIFE executed");
})();


