<?php
// echo "<pre>";
// class A
// {
//     protected $n = 10;
//     public function i($n)
//     {
//         $this->n = $n;
//     }

//     public function g()
//     {
//         return $this->n;
//     }
// }
// class B
// {
//     public $a = null;
//     public function a()
//     {
//         if ($this->a == null) {
//             $this->a = new A;
//         }
//         print_r($this->a);
//         return $this->a;
//     }
// }

// $b = new B;
// $b->a()->i(20);
// echo $b->a()->g();



echo "<pre>";

class Profile
{
    protected $bio = "No bio";

    public function setBio($bio)
    {
        $this->bio = $bio;
    }

    public function getBio()
    {
        return $this->bio;
    }
}

class User
{
    protected $profile = null;

    public function profile()
    {
        if ($this->profile == null) {
            $this->profile = new Profile;
        }else{

            print_r($this->profile);
            // print_r($this);
        }

        
        return $this->profile;
    }

}

$user = new User;

$user->profile()->setBio("I am learning Machine Learning");
echo $user->profile()->getBio();

