<?php

    session_start();
    $error = "";
    if(array_key_exists("logout",$_GET)){
        unset($_SESSION);
        setcookie("id","",time() - 60*60);
        $_COOKIE["id"] = "";
    }else if((array_key_exists("id",$_SESSION) AND $_SESSION['id']) OR ($_COOKIE['id'] AND array_key_exists("id",$_COOKIE))){
        header("Location: loggedInPage.php");
    }

    if(array_key_exists("submit",$_POST)){
        
        include("connection.php");

        if(!$_POST['email']){
            $error.="Email address is required<br>";
        }
        if(!$_POST['password']){
            $error.="Password is required";
        }

        if($error != ""){
            $error = "<p> There are error(s) in your form submission:</p>".$error;
        }else{
            if($_POST["signUp"] == '1'){
                $query = "SELECT id FROM `users` WHERE email='".mysqli_real_escape_string($link, $_POST['email'])."'";
                $result = mysqli_query($link, $query);
                if(mysqli_num_rows($result) > 0){
                    $error = "That email address is already taken";
                }else{
                    $query = "INSERT INTO `users` (`email`,`password`) VALUES('".mysqli_real_escape_string($link, $_POST['email'])."','".mysqli_real_escape_string($link, $_POST['password'])."')";
                    if(!mysqli_query($link,$query)){
                        $error = "Try again later";
                    }else{
                        $query = "UPDATE `users` SET password ='".md5(md5(mysqli_insert_id($link)).$_POST['password'])."' WHERE id=".mysqli_insert_id($link)." LIMIT 1";
                        mysqli_query($link, $query);
                        $_SESSION['id'] = mysqli_insert_id($link);
                        if($_POST['stayLoggedIn'] == '1'){
                            setcookie("id",mysqli_insert_id($link),time() + 60*60*24*365);
                        }
                        header("Location: loggedInPage.php");
                    }
                }
            }
            else{
                $query = "SELECT * FROM `users` WHERE email='".mysqli_real_escape_string($link, $_POST['email'])."'";
                $result = mysqli_query($link,$query);
                $row = mysqli_fetch_array($result);
                if(isset($row)){
                    $hashedPassword = md5(md5($row['id']).$_POST['password']);
                    if($hashedPassword == $row['password']){
                        $_SESSION['id'] = $row['id'];
                        if($_POST['stayLoggedIn'] == '1'){
                            setcookie("id",$row['id'],time() + 60*60*24*365);
                        }
                        header("Location: loggedInPage.php");
                    }
                    else{
                        $error = "Either email or password you have entered is incorrect";
                    }
                }
                else{
                    $error = "Either email or password you have entered is incorrect";
                }
            }
        }
    }

?>

    <?php include("header.php"); ?>
    
        <div class="container" id="homePageContainer">
            <h1> S-E-C-R-E-T   D-I-A-R-Y  </h1>
            <p><strong> Store your thoughts permanently and securely </strong></p>
            <p><strong> Interested? Sign up now !!! </strong></p>
            <span><div id="error"><?php echo $error; ?></div></span>
        
            <form method="post" id="signUpForm">
                <div class="form-group">
                    <label for = "email"> Email </label>
                    <input name="email" class="form-control" type="email" placeholder="example@domain.com">
                </div>
                <div class="form-group">
                    <label for = "password"> Password </label>
                    <input name="password" class="form-control" type="password">
                </div>
                <div class="checkbox">
                    <label>
                        <input type = "checkbox" name = "stayLoggedIn" value=1>
                        Stay logged in
                    </label>
                    <input type="hidden" name="signUp" value="1">
                </div>
                <div class="form-group">
                    <input type="submit" class="btn btn-primary" name="submit" value="Sign Up">
                </div>
                <p><a href="#" role="button" class="toggleForms">Log in</a></p>
            </form>

            <form method="post" id="logInForm">
                <div class="form-group">
                    <label for = "email"> Email </label>
                    <input name="email" class="form-control" type="email" placeholder="example@domain.com">
                </div>
                <div class="form-group">
                    <label for = "password"> Password </label>
                    <input name="password" class="form-control" type="password">
                </div>
                <div class="checkbox">
                    <label>
                        <input type = "checkbox" name = "stayLoggedIn" value=1>
                        Stay logged in
                    </label>
                    <input type="hidden" name="signUp" value="0">
                </div>
                <div class="form-group">
                    <input type="submit" class="btn btn-primary" name="submit" value="Log In">
                </div>
                <p><a href="#" role="button" class="toggleForms">Sign up</a></p>
            </form>

        </div>

        <?php include("footer.php"); ?>
