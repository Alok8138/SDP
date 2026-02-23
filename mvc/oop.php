<?php

/*  What is static in PHP
        - The property or method belongs to the class itself, they does not belongs to an object.
        - static method or var can be access without creating an obj. they accessed using ::(scope resolution operator)
        - Syntax : static VAR_NAME 
 */


class Student
{

    public static $name = "Alok";
  


    public static function hii($name)
    {


        echo "hii, $name <br>";

        // echo self::$name;
        echo "<br>";
    }
}



/* RESTRICATION:there is no static class by keyword i.e static class A
                    - class which contain all the static method is called ststic class  
                    - we cant use this keyword with static variable or method 
                    - if you want to acess the non static property inside the static method then first create an object of that class 
                      and using that obj you can access it.
*/


// op: hii,Alok

/*this is good practice to access the static method and variables*/

// Student::hii(Student::$name);

//op: Alok
// $obj = new Student();
// echo $obj::$name;

/*op:warning:: Undefined property: Student::$name in 
        static var cant be access using arrow operator
        for static var it does not check class 

*/

// $obj = new Student();
// echo $obj->name;


/* 
    op:alok
        first it search in object instance area in headp if it does not found hii() method then 
        it check if there exist any static method in class entry(static memory area) with same name if does then it execute that method
*/

/*  dont access static method using obj it is bad practice  */

$obj = new Student();
echo $obj->hii($obj::$name);

// Component	        Where is it stored?	            Number of copies
// Static Variable	    Class Entry (Global Scope)	    Only 1
// Regular Variable	    Object Instance (Heap)	        1 per object created
// Static Method	    Class Entry (Function Table)    Only 1
// Regular Method	    Class Entry (Function Table)    Only 1



/*
    count = 0 assign only once, second time when we call the same fn this line is ignored by compiler and it has program life time.
 */
function counter()
{
    static $count = 0;
    $count++;
    echo $count . "<br>";
}

counter();
counter();
counter();




/* Self Keyword 
    -self refers to the current class where the code is written.
    -It is mainly used to access:
        Static properties
        Static methods 
     Inside the same class.
*/


class Student1
{

    public static $name = "Alok";


    public static function hii()
    {
        // $this->name;
        // echo "hii, $this->name";
        $name = self::$name;
        echo "hii, $name";
        echo "<br>";
    }
}


Student1::hii();

/* RESTRICTION: self is refer the static property of class inside the class scope (if extended scope still accessible) only.
*/



class A
{
    static $var1 = 10;
    static $var2 = 20;
    protected static function add(){

        return self::$var1 + self::$var2;

    }

}

class B extends A
{
     
    public function callAdd()
    {
        $aobj = new A();
        echo "in B class: ";
        echo "<br>";

        echo $aobj::$var1;
        echo "<br>";
        echo self::add();
        
    }
}


$b = new B();

$b->callAdd();
