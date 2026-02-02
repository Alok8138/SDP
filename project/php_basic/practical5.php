<?php
class Employee
{
    public $name;
    private $salary;

    public function __construct($name, $salary)
    {
        $this->name = $name;
        $this->salary = $salary;
    }

    public function getSalary()
    {
        return $this->salary;
    }

    public function setSalary($amount)
    {
        if ($amount > 0) {
            $this->salary = $amount;
        }
    }
}

class Manager extends Employee
{
    public $department;

    public function __construct($name, $salary, $department)
    {
        parent::__construct($name, $salary);
        $this->department = $department;
    }

    public function getDetails()
    {
        return $this->name . " manages " . $this->department;
    }
}



$emp = new Employee("Alok", 30000);
echo $emp->getSalary();
echo "<br>";

$mgr = new Manager("Ravi", 50000, "Sales");
echo $mgr->getDetails();
echo "<br>";
echo $mgr->getSalary();
?>


<?php




