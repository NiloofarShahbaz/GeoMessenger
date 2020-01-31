<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    $data = array();

    if (isset($_SESSION["loggedIn"]) && $_SESSION["loggedIn"] == true) {
        require_once "dbConnect.php";
        $user_id = $_SESSION['id'];
        $query = 'SELECT user2 AS friends FROM relationships WHERE user1='.$user_id.' AND status=1 UNION SELECT user1 AS friends FROM relationships WHERE user2='.$user_id.' AND status=1';
        if($result=mysqli_query($con, $query)){
            $data['success'] = true;
            $data['result'] = array();
            $x = 0;
            if(mysqli_num_rows($result)>0){
                while ($row = mysqli_fetch_row($result)){
                    $data['result'][$x] = $row[0];
                    $x++;
                }
            }

            mysqli_free_result($result);
        } else {
            $data['success'] = false;
            $data['message'] = 'Oops! Something went wrong';
        }
        mysqli_close($con);
    } else {
        $data['success'] = false;
        $data['message'] = 'Oops! Something went wrong';
    }
    echo json_encode($data);
}