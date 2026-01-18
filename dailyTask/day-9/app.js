

const p = document.querySelectorAll("p");

for (let i = 0; i < p.length; i++){
    p[i].innerHTML = "changed";
}

console.log(p);

const li = document.querySelector("#myList");

const newLi = document.getElementById("myList");

console.log("query: ",li);
console.log(newLi);