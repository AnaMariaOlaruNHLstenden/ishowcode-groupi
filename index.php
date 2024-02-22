<?php
$msgs = [];
session_start();
if($_SERVER["REQUEST_METHOD"] == "POST"){
    if(!$user = filter_input(INPUT_POST,"un", FILTER_SANITIZE_SPECIAL_CHARS))
        $msgs[] = "Invalid username or no username given";

    if(!$pass = filter_input(INPUT_POST,"pw"))
        $msgs[] = "No password given";

    if(count($msgs) == 0)
    {
        require_once("pdo.php");
        if($dbHandler){
            try{
                $stmt = $dbHandler->prepare("SELECT *
                                             FROM `users`
                                             WHERE `Name` = :username"
                                        );
                $stmt->bindParam("username", $user, PDO::PARAM_STR);
                $stmt->execute();         
            }catch(Exception $ex) {
                printError($ex);
            }
        }
        if($stmt && $stmt->rowCount() == 1){
            $results = $stmt->fetchall(PDO::FETCH_ASSOC);
            if(password_verify($pass, $results[0]["Password"])){
                $_SESSION["username"] = $results[0]["Name"];
                header("Location: user.php");
                exit();
            }

        }else{
            $msgs[] = "Invalid name or password";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login please</title>
    </head>

    <body>
        <h1>Login</h1>
        <?php
            if(count($msgs) > 0){
                foreach ($msgs as $msg) {
                    echo $msg;
                }
            }
        ?>
        <form action="<?=htmlentities($_SERVER['PHP_SELF'])?>" method="post">
            <table>
                <tr>
                    <td>
                        username:
                    </td>
                    <td>
                        <input type="text" name="un" />
                    </td>
                </tr>
                <tr>
                    <td>password:</td>
                    <td>
                        <input type="password" name="pw" />
                    </td>
                </tr>
            </table>
        <input type="submit" name="submit" />
        </form>
        <a href="register.php">register</a>
    </body>
</html>
