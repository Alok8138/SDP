
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

$emp = new Employee("Alok", 30000);
echo $emp->getSalary();
?>
