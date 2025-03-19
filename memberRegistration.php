<?php
require("model/member.php");
// require("utilities/sanitize.php");
// TODO: not required as in member.php
// First check IF we are already logged in - get session data with the session_start
session_start();
// IF UserName exists then already logged in - so we are updating Member details

$userId = 0;
$userName   = "";
$password   = "";
$password2  = "";
$firstName  = "";
$lastName   = "";
$street     = "";
$town       = "";
$state      = "";
$postcode   = "";
$phone      = "";
$email      = "";
$action     = "";

// IF already Logged In, then UPDATE mode - display existing Member details and allow updates/delete
IF (ISSET($_SESSION["member"])) {
    $member = unserialize($_SESSION["member"]);  //get the stored Member object

    $mode = "UPD";
    // $userName = $_SESSION["userName"];
    // echo("member:".$userName);
    $message="Please Amend or Delete your Member details";
    // Otherwise IF not logged in - we come here in ADD mode.
} ELSE {
    $mode = "ADD";
    $message="Please Register for this wonderful website";
    $member = new Member();
}
// echo("Mode:".$mode);


$method  = $_SERVER["REQUEST_METHOD"];

//POST mean we have pressed a SUBMIT button.
IF ($method=="POST") {
    // ECHO("POST mode:".$mode);
    IF ($mode=="ADD") {
        $message = addNew($member);
    } ELSE {
        $message = update($member);
    }
} // Finish POST

// //GET mode - this is the first time through.
// // IF update mode - get the Member record based on the UserName

IF ($mode=="UPD") {

    $userId     = $member->getMemberId();
    $userName   = $member->getUserName();
    $password   = "";
    $password2  = "";
    $firstName  = $member->getFirstName();
    $lastName   = $member->getLastName();
    $street     = $member->getStreet();
    $town       = $member->getTown();
    $state      = $member->getState();
    $postcode   = $member->getPostcode();
    $phone      = $member->getPhone();
    $email      = $member->getEmail();
}


/**
 * Add New Member
 *
 * Passed in an empty Member object
 * Get fields from the screen
 * Populate the Member Object
 * Save Member to database
 * Force Member to Login again
 *
 * @param $member empty instanceof Member class (see Member.php)
 */
FUNCTION addNew($member) {

    // echo($userName);
    $password   = $_POST["password"];
    $password2  = $_POST["password2"];
    $message = "Adding new Member: (".$member->getMemberId().") ".$member->getUserName()."-".$member->getFullName();

    IF ($password != $password2) {
        $message = "Add Member: Passwords do not match. Try again!";
    } ELSE {
        $member->setUserName($_POST["userName"]);
        $member->setPassword($_POST["password"]);
        $member->setFirstName($_POST["firstName"]);
        $member->setLastName($_POST["lastName"]);
        $member->setStreet($_POST["street"]);
        $member->setTown($_POST["town"]);
        $member->setState($_POST["state"]);
        $member->setPostcode($_POST["postcode"]);
        $member->setPhone($_POST["phone"]);
        $member->setEmail($_POST["email"]);

        $action     = $_POST["btnAction"];  #which button was pressed?

        IF ($member->userExists()) {
        // echo("member: ".$userName." exists");
            $message = "Add Member: UserName (".$member->getUserName().") already exists! Please choose another";
        } ELSE {
        // Check that password is entered the same twice as a Verification

            # Now add the new Member
            // # Hash Password - see https://www.php.net/manual/en/faq.passwords.php
            // $password = password_hash($password, PASSWORD_DEFAULT);

            IF ($action == "add") {
                // $sql = "INSERT INTO Members (userName, password, firstName, lastName, street, town, state, postcode, phone, email) VALUES ('$userName', '$password', '$firstName', '$lastName', '$street', '$town', '$state', '$postcode','$phone','$email')";

                $retCode = $member->save();
                IF ($retCode==0) {
                    $message = "New record created successfully";
                    // Force the Member to login again after adding their Member details
                    header("Location: login.php");
                } ELSE {
                    $message = "Error: " . $sql . "<br>" . $conn->error;
                    //echo($message);
                }
            }
        }
    }
    return $message;
}

FUNCTION update($member) {

    $action     = $_POST["btnAction"];
    // Check that password is entered the same twice as a Verification
    $password   = $_POST["password"];
    $password2  = $_POST["password2"];
    $userId     = $_POST["userId"];
    // echo("update  memberId=".$userId."<br/>");

    // // Check IF Member Order History requested - IF so navigate to Member Orders
    // IF ($action == "ord") {
    //     header("Location: MemberOrders.php");
    // }

    # for update and delete - passwords match
    IF ($password != $password2) {
        $message = "Passwords do not match. Try again!";
    } ELSE {
        $userName   = $_POST["userName"];

        IF ($action == "upd") {
            $member->setUserName($_POST["userName"]);
            $member->setPassword($_POST["password"]);
            $member->setFirstName($_POST["firstName"]);
            $member->setLastName($_POST["lastName"]);
            $member->setStreet($_POST["street"]);
            $member->setTown($_POST["town"]);
            $member->setState($_POST["state"]);
            $member->setPostcode($_POST["postcode"]);
            $member->setPhone($_POST["phone"]);
            $member->setEmail($_POST["email"]);

            $retCode = $member->save();
            IF ($retCode==0) {
                $message = "Member record updated successfully";
                $footer = "Current Member: ".$member->getUsername()." (".$member->getFirstName()." ".$member->getLastName().") - (c) SPX Cinemas 2025";
                $_SESSION["footer"] = escapeGET($footer);
                //header("Location: MemberRegistration.php");
            } ELSE {
                $message = "Error: " . $sql . "<br>" . $conn->error;
                echo($message);
            }
        } ELSE IF ($action == "del") {
            // Check IF Member has any basket/orders and DELETE them first ...maybe CASCADE delete them FIRST
            $retCode = $member->delete();
            $_SESSION["member"]=null;
            $member = null;
            IF ($retCode==0) {
                $message = "Member record deleted successfully";
                header("Location: memberRegistration.php");
            } ELSE {
                $message = "Error: " . $sql . "<br>" . $conn->error;
                echo($message);
            }

        }

    }
    return $message;
}
?>

<!DOCTYPE html>
<html>
    <head>
        <?php require('head.php');?>
    </head>
    <body>
         <?php require('header.php'); ?>
         <?php require('nav.php'); ?>
         <maincontent>
            <h1>Member Registration</h1>
            <div class="container border border-dark">
                <form class="" name="register" action="" method="POST">
                    <div class="row mt-3">
                        <div class="col">
                        <label for "userName" class="form-label">userName: </label>
                        <input class="form-control" name="userName" type="text" size="10" maxlength="10" value="<?php echo($userName);?>"></input>
                        <input type="hidden" name="userId" type="text" value="<?php echo($userId);?>"></input>
                        </div>
                        <div class="col">
                        <label for "password" class="form-label">Password: </label>
                        <input class="form-control" name="password" type="password" size="12" maxlength="12" value="<?php echo($password); ?>" required></input>
                        </div>
                        <div class="col">
                        <label for "password2" class="form-label">Repeat Password: </label>
                        <input class="form-control" name="password2" type="password" size="12" maxlength="12" value="<?php echo($password2); ?>" required></input>
                        </div>
                    </div>
                    <div class="row  mt-5">
                        <div class="col">
                        <label for "firstName" class="form-label">First Name: </label>
                        <input class="form-control" name="firstName" type="text" size="35" maxlength="35" value="<?php echo($firstName); ?>" ></input>
                        </div>
                        <div class="col">
                        <label for "lastName" class="form-label">Last Name: </label>
                        <input class="form-control" name="lastName" type="text" size="35" maxlength="35" value="<?php echo($lastName); ?>"></input>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-4">
                            <label for "street" class="form-label">Street: </label>
                            <input class="form-control" name="street" type="text" size="50" maxlength="50" value="<?php echo($street); ?>"></input>
                        </div>
                        <div class="col-4">
                            <label for "town" class="form-label">Town: </label>
                            <input class="form-control" name="town" type="text" size="50" maxlength="50" value="<?php echo($town); ?>"></input>
                        </div>
                        <div class="col-1">
                            <label for "state" class="form-label">State: </label>
                            <input class="form-control" name="state" type="text" size="3" maxlength="3" value="<?php echo($state); ?>"></input>
                        </div>
                        <div class="col-1">
                            <label for "postcode" class="form-label">Postcode: </label>
                            <input class="form-control" name="postcode" type="text" size="4" maxlength="4" value="<?php echo($postcode); ?>"></input>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-2">
                            <label for "phone" class="form-label">Phone: </label>
                            <input class="form-control" name="phone" type="text" size="4" maxlength="12" value="<?php echo($phone); ?>"></input>
                        </div>
                        <div class="col-3">
                            <label for "email" class="form-label">Email: </label>
                            <input class="form-control" name="email" type="email" size="50" maxlength="50" value="<?php echo($email); ?>"></input>
                        </div>
                    </div>


                    <div class="mb-3 mt-3 row">
<?php               IF ($mode=="ADD") {   ?>
                        <div class="col-2">
                            <button type="submit" name="btnAction" value="add" class="btn btn-primary">Add New Member</button>
                        </div>
<?php               } ELSE {
?>
                        <div class="col-2">
                            <button type="submit" name="btnAction" value="upd" class="btn btn-primary">Update Member</button>
                        </div>
                        <div class="col-2">
                            <button type="submit" name="btnAction" value="del" class="btn btn-primary">Delete Member</button>
                        </div>
                        <!-- <div class="col-4">
                            <button type="submit" name="btnAction" value="ord" class="btn btn-success">Member Order History</button>
                        </div> -->
<?php               }   ?>
                    </div>
                    <div class="row mx-auto mb-3 mt-3 message alert-danger">
                        <?php echo($message); ?>
                    </div>
                </form>
            </div>

         </maincontent>
         <?php require('footer.php'); ?>
    </body>
</html>