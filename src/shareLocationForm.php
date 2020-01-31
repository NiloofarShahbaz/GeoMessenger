<?php
session_start();

 // if request is ajax

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {

    $latitude = $longitude = $status = "";
    $data = array();
    $data['success'] = false;
    $data['message'] = "Oops! something went wrong! Try reloading the page.";

    // if user is logged in
    if (isset($_SESSION["loggedIn"]) && $_SESSION["loggedIn"] == true) {
        if (!empty($_POST['latitude']) && !empty($_POST['longitude'])) {
            $latitude = $_POST['latitude'];
            $longitude = $_POST['longitude'];

            // we need float(10,6) to store in database
            $precision = 6;
            $latitude = substr(number_format($latitude, $precision + 1, '.', ''), 0, -1);
            $longitude = substr(number_format($longitude, $precision + 1, '.', ''), 0, -1);

            $status = trim($_POST['status']);

            require_once 'dbConnect.php';
            $query = 'INSERT INTO location (location_lat, location_lng, user, status) VALUES (?, ?, ?, ?)';
            if ($stmt = mysqli_prepare($con, $query)) {
                mysqli_stmt_bind_param($stmt, "ddis", $param_lat, $param_lng, $param_user, $param_status);
                $param_lat = $latitude;
                $param_lng = $longitude;
                $param_status = $status;
                $param_user = $_SESSION['id'];

                if(mysqli_stmt_execute($stmt)){
                    $data['success'] = true;
                    $data['message'] = 'Successfully shared!';
                }
            }
            mysqli_stmt_close($stmt);
            mysqli_close($con);
        }
    }
    echo json_encode($data);
}

