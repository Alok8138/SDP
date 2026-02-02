<?php

setcookie("user_preference","dark_mode",time()+3600,"/");

if(isset($_COOKIE["user_preference"])){
    echo "User preference is: " . $_COOKIE["user_preference"];
} else {
    echo "No user preference set.";
}

echo "<br>";

?>



<?php
session_start();
$_SESSION['username'] = "Alok";
echo "Session set";
echo "<br>";
?>


<?php
// session_start();
if(isset($_SESSION['username'])){
    echo "Username from session is: " . $_SESSION['username'];
} else {
    echo "No username set in session.";
}

echo "<br>";
// session_destroy();
?>
