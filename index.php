<div class="logo">
    <a href="login_signup.html" class="button">Login/Signup</a>
</div>
<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login_signup.html");
    exit();
}
?>
<div class="logo">
    <a href="login_signup.html" class="button">Login/Signup</a>
</div>
<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login_signup.html");
    exit();
}
?>

