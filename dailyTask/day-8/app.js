let name = "Alok";
let age = 21;
let isIntern = true;

// Non-primitive data storage
// Arrays and Objects store multiple values
let skills = ["HTML", "CSS", "JavaScript"];
let profile = {
  name: "Alok",
  role: "Frontend Intern",
  experience: "Fresher"
};

/*==========================================================
  2. ARRAYS (WHAT & WHY)
==========================================================*/

// Array = ordered collection of data
let numbers = [10, 20, 30, 40];

// Accessing array elements
console.log(numbers[0]); // 10
console.log(numbers.length); // length of array

/*==========================================================
  3. IMPORTANT ARRAY METHODS (MUST KNOW)
==========================================================*/

// push() – add at end
numbers.push(50);

// pop() – remove from end
numbers.pop();

// unshift() – add at start
numbers.unshift(5);

// shift() – remove from start
numbers.shift();

// indexOf() – find index
console.log(numbers.indexOf(30));

// includes() – check existence
console.log(numbers.includes(20));

// slice() – copy portion (does NOT modify original)
let slicedArray = numbers.slice(1, 3);

// splice() – add/remove (MODIFIES original)
numbers.splice(1, 1, 25,26,27); // at index 1, remove 1 item, add 25,26,27

// concat() – merge arrays
let moreNumbers = [60, 70];
let mergedArray = numbers.concat(moreNumbers);

// join() – convert array to string
let joined = numbers.join(", ");

// reverse() – reverse array
numbers.reverse();

// sort() – sort array
numbers.sort((a, b) => a - b);

/*==========================================================
  4. ARRAY ITERATION METHODS (VERY IMPORTANT)
==========================================================*/

// for loop
for (let i = 0; i < numbers.length; i++) {
  console.log("for loop:", numbers[i]);
}

// for...of loop (BEST for arrays)
for (let num of numbers) {
  console.log("for...of:", num);
}

// forEach() – MOST USED
numbers.forEach(function (value, index) {
  console.log(`forEach index ${index} value ${value}`);
});

// map() – transform array (returns new array)
let squaredNumbers = numbers.map(num => num * num);

// filter() – filter based on condition
let evenNumbers = numbers.filter(num => num % 2 === 0);

// reduce() – reduce to single value
let sum = numbers.reduce((total, num) => total + num, 0);

/*==========================================================
  5. OBJECTS (WHAT & WHY)
==========================================================*/

// Object = key-value pairs
let student = {
  name: "Alok",
  age: 21,
  skills: ["JS", "React"],
  isActive: true
};
let b = student.keys();

// Accessing object values
console.log(student.name);
console.log(student["age"]);

// Adding new property
student.city = "Delhi";

// Updating property
student.age = 22;

// Deleting property
delete student.isActive;

/*==========================================================
  6. OBJECT METHODS & ITERATION
==========================================================*/

// Object.keys()
console.log(Object.keys(student));

// Object.values()
console.log(Object.values(student));

// Object.entries()
console.log(Object.entries(student));

// for...in loop (BEST for objects)
for (let key in student) {
  console.log(key, ":", student[key]);
}

/*==========================================================
  7. ARRAY OF OBJECTS (REAL-WORLD USE)
==========================================================*/

let users = [
  { id: 1, name: "Amit", role: "Admin" },
  { id: 2, name: "Ravi", role: "User" },
  { id: 3, name: "Neha", role: "Intern" }
];

// Display user names
users.forEach(user => {
  console.log(user.name);
});

// Filter interns
let interns = users.filter(user => user.role === "Intern");

/*==========================================================
  8. DATA DISPLAY METHODS
==========================================================*/

// Console display
console.log("Users:", users);

// DOM display (browser only)
document.body.innerHTML += "<h2>Internship Data</h2>";

users.forEach(user => {
  document.body.innerHTML += `<p>${user.name} - ${user.role}</p>`;
});

/*==========================================================
  9. LOCAL STORAGE (BASIC DATA STORAGE)
==========================================================*/

// Store data
localStorage.setItem("studentProfile", JSON.stringify(student));

// Get data
let storedData = JSON.parse(localStorage.getItem("studentProfile"));
console.log("Stored Data:", storedData);

// Remove data
// localStorage.removeItem("studentProfile");

/*==========================================================
  10. IIFE (IMMEDIATELY INVOKED FUNCTION EXPRESSION)
==========================================================*/

// Why IIFE?
// - Avoid global pollution
// - Execute code immediately

(function () {
  console.log("IIFE executed immediately");
  let secret = "Internship Ready";
})();

/*==========================================================
  11. COMBINED PRACTICAL EXAMPLE
==========================================================*/

(function () {
  let products = [
    { name: "Laptop", price: 50000 },
    { name: "Phone", price: 20000 },
    { name: "Tablet", price: 30000 }
  ];

  let expensiveProducts = products.filter(p => p.price > 25000);

  expensiveProducts.forEach(p => {
    console.log(`${p.name} costs ${p.price}`);
  });
})();

/************************************************************
 * END OF FILE
 * If you understand EVERYTHING in this file,
 * you are READY for INTERNSHIP JavaScript tasks.
 ************************************************************/
