<?php
session_save_path("/Applications/XAMPP/xamppfiles/var/sessions");
session_start();
include 'config.php';

function prepareAndExecute($conn, $sql, $params)
{
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die('mysqli error: ' . htmlspecialchars($conn->error));
    }
    $stmt->bind_param(str_repeat('s', count($params)), ...$params);
    $stmt->execute();
    return $stmt;
}

// === USER LOGIN ===
if (isset($_POST['user_login_submit'])) {
    $email = $_POST['Email'];
    $password = $_POST['Password'];
    $sql = "SELECT * FROM signup WHERE Email = ? AND Password = BINARY ?";
    $stmt = prepareAndExecute($conn, $sql, [$email, $password]);
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['usermail'] = $email;
        header("Location: home.php");
        exit();
    } else {
        $loginError = "Something went wrong";
    }
}

// === STAFF LOGIN ===
if (isset($_POST['Emp_login_submit'])) {
    $email = $_POST['Emp_Email'];
    $password = $_POST['Emp_Password'];
    $sql = "SELECT * FROM emp_login WHERE Emp_Email = ? AND Emp_Password = BINARY ?";
    $stmt = prepareAndExecute($conn, $sql, [$email, $password]);
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['usermail'] = $email;
        header("Location: admin/admin.php");
        exit();
    } else {
        $loginError = "Something went wrong";
    }
}

// === USER SIGNUP ===
if (isset($_POST['user_signup_submit'])) {
    $username = $_POST['Username'];
    $email = $_POST['Email'];
    $password = $_POST['Password'];
    $cpassword = $_POST['CPassword'];

    if ($username == "" || $email == "" || $password == "") {
        $signupError = "Fill the proper details";
    } else {
        if ($password == $cpassword) {
            $sql_check = "SELECT * FROM signup WHERE Email = ?";
            $stmt_check = prepareAndExecute($conn, $sql_check, [$email]);
            $result = $stmt_check->get_result();

            if ($result->num_rows > 0) {
                $signupError = "Email already exists";
            } else {
                $sql_insert = "INSERT INTO signup (Username, Email, Password) VALUES (?, ?, ?)";
                $stmt_insert = prepareAndExecute($conn, $sql_insert, [$username, $email, $password]);

                if ($stmt_insert->affected_rows > 0) {
                    $_SESSION['usermail'] = $email;
                    header("Location: home.php");
                    exit();
                } else {
                    $signupError = "Something went wrong";
                }
            }
        } else {
            $signupError = "Password does not match";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    
    <link rel="stylesheet" href="./css/login.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    
    <!-- Sweet Alert -->
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

    <!-- AOS Animation -->
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
    
    <!-- Loading Bar -->
    <script src="https://cdn.jsdelivr.net/npm/pace-js@latest/pace.min.js"></script>
    <link rel="stylesheet" href="./css/flash.css" />
    
    <title>Hotel Blue Bird</title>
</head>
<body>
     <!-- Carousel Section -->
     <section id="carouselExampleControls" class="carousel slide carousel_section" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img class="carousel-image" src="./image/hotel1.jpg" alt="Hotel 1">
            </div>
            <div class="carousel-item">
                <img class="carousel-image" src="./image/hotel2.jpg" alt="Hotel 2">
            </div>
            <div class="carousel-item">
                <img class="carousel-image" src="./image/hotel3.jpg" alt="Hotel 3">
            </div>
            <div class="carousel-item">
                <img class="carousel-image" src="./image/hotel4.jpg" alt="Hotel 4">
            </div>
        </div>
    </section>

    <!-- Auth Section -->
    <section id="auth_section">
        <div class="logo">
            <img class="bluebirdlogo" src="./image/bluebirdlogo.png" alt="Bluebird Logo">
            <p>BLUEBIRD</p>
        </div>

        <div class="auth_container">
            <!-- Login Panel -->
            <div id="Log_in">
                <h2>Log In</h2>
                <div class="role_btn">
                    <div class="btns active" onclick="toggleRole('user')">User</div>
                    <div class="btns" onclick="toggleRole('staff')">Staff</div>
                </div>

                <!-- User Login Form -->
                <form class="user_login authsection active" id="userlogin" action="" method="POST">
                    <div class="form-floating">
                        <input type="text" class="form-control" name="Username" placeholder=" ">
                        <label for="Username">Username</label>
                    </div>
                    <div class="form-floating">
                        <input type="email" class="form-control" name="Email" placeholder=" ">
                        <label for="Email">Email</label>
                    </div>
                    <div class="form-floating">
                        <input type="password" class="form-control" name="Password" placeholder=" ">
                        <label for="Password">Password</label>
                    </div>
                    <button type="submit" name="user_login_submit" class="auth_btn">Log in</button>
                    <div class="footer_line">
                        <h6>Don't have an account? <span class="page_move_btn" onclick="signuppage()">Sign up</span></h6>
                    </div>
                </form>

                <!-- Staff Login Form -->
                <form class="employee_login authsection" id="employeelogin" action="" method="POST">
                    <div class="form-floating">
                        <input type="email" class="form-control" name="Emp_Email" placeholder=" ">
                        <label for="floatingInput">Email</label>
                    </div>
                    <div class="form-floating">
                        <input type="password" class="form-control" name="Emp_Password" placeholder=" ">
                        <label for="floatingPassword">Password</label>
                    </div>
                    <button type="submit" name="Emp_login_submit" class="auth_btn">Log in</button>
                </form>
            </div>

            <!-- Signup Panel -->
            <div id="sign_up">
                <h2>Sign Up</h2>
                <form class="user_signup" id="usersignup" action="" method="POST">
                    <div class="form-floating">
                        <input type="text" class="form-control" name="Username" placeholder=" ">
                        <label for="Username">Username</label>
                    </div>
                    <div class="form-floating">
                        <input type="email" class="form-control" name="Email" placeholder=" ">
                        <label for="Email">Email</label>
                    </div>
                    <div class="form-floating">
                        <input type="password" class="form-control" name="Password" placeholder=" ">
                        <label for="Password">Password</label>
                    </div>
                    <div class="form-floating">
                        <input type="password" class="form-control" name="CPassword" placeholder=" ">
                        <label for="CPassword">Confirm Password</label>
                    </div>
                    <button type="submit" name="user_signup_submit" class="auth_btn">Sign up</button>
                    <div class="footer_line">
                        <h6>Already have an account? <span class="page_move_btn" onclick="loginpage()">Log in</span></h6>
                    </div>
                </form>
            </div>
        </div>
    </section>
<script src="./javascript/index.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script>
        AOS.init();
    </script>
    <?php if (isset($loginError)) {
    echo "<script>swal({ title: '$loginError', icon: 'error' });</script>";
} ?>

<?php if (isset($signupError)) {
    echo "<script>swal({ title: '$signupError', icon: 'error' });</script>";
} ?>
</body>
</html>
