<?php
    // database connection
    require('dbconnect.php'); 
    
    session_start();
    $pageTitle = "Log in";
   
//Log in validation
    $msg = "";
    if (isset($_POST['doLogin'])) {
        $email    = $_POST['email'];
        $password = $_POST['password'];
       
    try {
      $query = "
        SELECT * FROM users 
        WHERE email = :email;
      ";
      $stmt = $dbconnect->prepare($query);
      $stmt->bindValue(':email', $email);
      $stmt->execute(); 
      $user = $stmt->fetch(); 
    } catch (\PDOException $e) {
      throw new \PDOException($e->getMessage(), (int) $e->getCode());
    } 
        if ($user && $password === $user['password']) {
            $_SESSION['username'] = $user['username'];
            header('Location: admin.php');
            exit;
        } else {
            $msg = 
            '<div class="alert alert-danger" role="alert">
            Invalid email or password
            </div>';
        }
    }

//sign up validation
$error ='';
$msgSignup = '';
    if (isset($_POST['signUp'])) {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $confirmPassword = $_POST['confirmPassword'];
     
    try {
	$stmt = $dbconnect->query("SELECT * FROM users");
	$users = $stmt->fetchAll(); 
   } catch (\PDOException $e) {
	throw new \PDOException($e->getMessage(), (int) $e->getCode());
  }
    //check database for existing email
    foreach ($users as $key => $user) { 
    if($email === $user['email']){
     $error .= '<div class="alert alert-danger" role="alert">
            Email already exists.
            </div>';   
    }
    }     
    if(empty($username)){
        $error .= '<li>User name must not be empty</li>';
    }
    if(empty($email)){
        $error .= '<li>Email must not be empty</li>';
    }else if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $error .= '<li>Incorrect email format</li>';
    }      
    if(empty($password)){
        $error .= '<li>Password must not be empty</li>';
    }
    if(!empty($password) && strlen($password) < 6){
        $error .= '<li>Password must have at least 6 character</li>';
    }
    if($confirmPassword !== $password){
        $error .= '<li>Confirm password must be same as password</li>';
    }
    if($error){
        $msgSignup = "<ul style='background-color:#f8d7da;'>{$error}</ul>";
    }else{
        //after validation data inserted into table
    try {
      $query = "
        INSERT INTO users (username, password, email)
        VALUES (:username, :password, :email);
      ";
      $stmt = $dbconnect->prepare($query);
      $stmt->bindValue(':username', $username);
      $stmt->bindValue(':password', $password);
      $stmt->bindValue(':email', $email);
      $result = $stmt->execute();    
    } catch (\PDOException $e) {
      throw new \PDOException($e->getMessage(), (int) $e->getCode());
    }
        
    if($result){
      $msgSignup = "<ul style='background-color:#d4edda;'>Sign up successfull. Now you can log in with email and password</ul>";  
    }
    
    }
}
?>
<?php include('head.php'); ?>

<div id="container">
    <div class="row">
        <div class="col-12" id="bkgdImg">
            <img src="img/bookbackground.jpg">
        </div>
        <div class="col-6">
            <form method="POST" action="#">
                <div class="lgnForm">
                    <legend>Log in</legend>
                    <hr>
                    <!--show error message for log in-->
                    <?=$msg?>
                    <p>
                        <label for="input1">Email:</label>
                        <input type="text" class="text" name="email">
                    </p>
                    <p>
                        <label for="input2">password:</label>
                        <input type="password" class="text" name="password">
                    </p>
                    <p>
                        <input type="submit" name="doLogin" value="Login">
                    </p>
                </div>
            </form>
            <hr>
        </div>
        <div class="col-6">
            <form method="POST" action="#">
                <div class="lgnForm">
                    <legend>Sign up</legend>
                    <hr>
                    <!--show error message for Sign Up-->
                    <?=$msgSignup?>
                    <p>
                        <label for="input1">User name:</label><br>
                        <input type="text" class="text" name="username">
                    </p>
                    <p>
                        <label for="input2">Email:</label><br>
                        <input type="text" class="text" name="email">
                    </p>
                    <p>
                        <label for="input3">Password:</label><br>
                        <input type="password" class="text" name="password">
                    </p>
                    <p>
                        <label for="input4">Confirm Password:</label><br>
                        <input type="password" class="text" name="confirmPassword">
                    </p>
                    <p>
                        <input type="submit" name="signUp" value="Sign Up">
                    </p>
                </div>
            </form>
            <hr>
        </div>

    </div>
</div>

<?php include('footer.php'); ?>
