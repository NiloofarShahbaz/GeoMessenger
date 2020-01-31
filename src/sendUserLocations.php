<?php

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    if (isset($_SESSION["loggedIn"]) && $_SESSION["loggedIn"] == true) {
        require_once "dbConnect.php";

        if (isset($_GET['value']) && $_GET['value'] == 1) {

            // send all shared locations of logged in user
            $query = 'SELECT * FROM location WHERE user = ' . $_SESSION['id'];
            if ($result = mysqli_query($con, $query)) {
                if (mysqli_num_rows($result) > 0) {
                    echo '<table>';
                    echo '<tr>';
                    echo '<th style="width: 25%">Location name</th>';
                    echo '<th>status</th>';
                    echo '<th style="width: 25%;">date</th>';
                    echo '</tr>';
                    while ($row = mysqli_fetch_array($result)) {
                        echo "<tr>";
                        echo "<td>Location " . $row['id'] . "</td>";
                        if ($row['status'])
                            echo "<td>" . $row['status'] . "</td>";
                        else
                            echo "<td>_</td>";
                        $dt = date("D, M d, Y", strtotime($row['dateTime']));
                        echo "<td>" . $dt . "</td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                    mysqli_free_result($result);
                } else
                    echo '<p class="text-center" style="font-family: Roboto-Regular;">You have no shared locations yet!</p>';
            } else {
                echo "Oops. Something went wrong! Please try again later";
            }
            return;
        }


        $data = array();
        $x = 0;
        $data['success'] = false;
        $data['message'] = "Oops. Something went wrong. Please Try reloading the page.";
        $data['result'] = array();

        if (isset($_GET['value']) && $_GET['value'] == 5) {
            // send all locations
            $query = 'SELECT l.id AS location_id, l.user AS user_id, username, email, last_online, u.status AS user_status, l.status AS location_status, location_lat, location_lng, l.dateTime AS location_dateTime 
                      FROM 
                        (SELECT user, MAX(dateTime) AS dateTime 
                         FROM location GROUP BY user) AS latest_location, location l, users u 
                      WHERE l.user = latest_location.user AND l.dateTime = latest_location.dateTime AND u.id = l.user';
            if ($result = mysqli_query($con, $query)) {
                $data['success'] = true;
                $data['message'] = "Showing every one's latest shared location.";
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_array($result)) {
                        $data['result'][$x] = $row;
                        $x++;
                    }
                }
            }
            mysqli_free_result($result);
        }
        elseif (isset($_GET['value']) && $_GET['value'] == 4) {
            // send others locations
            $user_id = $_SESSION['id'];
            $query = 'SELECT l.id AS location_id, l.user AS user_id, username, email, last_online, u.status AS user_status, l.status AS location_status, location_lat, location_lng, l.dateTime AS location_dateTime 
                        FROM (
                            SELECT user, MAX(dateTime) AS dateTime  
                            FROM location  
                            WHERE user NOT in (
                                SELECT user2 AS friends FROM relationships WHERE user1=1 AND status=1 
                                UNION 
                                SELECT user1 AS friends FROM relationships WHERE user2=1 AND status=1
                            ) AND user != '.$user_id.'
                            GROUP BY user
                        ) AS latest_location, location l, users u 
                        WHERE l.user = latest_location.user AND l.dateTime = latest_location.dateTime AND u.id = l.user';

            if ($result = mysqli_query($con, $query)) {
                $data['success'] = true;
                $data['message'] = "Showing other's latest shared location.";
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_array($result)) {
                        $data['result'][$x] = $row;
                        $x++;
                    }
                }
                mysqli_free_result($result);
            }
        }
        elseif (isset($_GET['value']) && $_GET['value'] == 3) {
            // send friends locations
            $query = 'SELECT l.id AS location_id, l.user AS user_id, username, email, last_online, u.status AS user_status, l.status AS location_status, location_lat, location_lng, l.dateTime AS location_dateTime 
                        FROM (
                            SELECT user, MAX(dateTime) AS dateTime  
                            FROM location  
                            WHERE user in (
                                SELECT user2 AS friends FROM relationships WHERE user1=1 AND status=1 
                                UNION 
                                SELECT user1 AS friends FROM relationships WHERE user2=1 AND status=1
                            )
                            GROUP BY user
                        ) AS latest_location, location l, users u 
                        WHERE l.user = latest_location.user AND l.dateTime = latest_location.dateTime AND u.id = l.user';

            if ($result = mysqli_query($con, $query)) {
                $data['success'] = true;
                $data['message'] = "Showing your friends latest shared location.";
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_array($result)) {
                        $data['result'][$x] = $row;
                        $x++;
                    }
                }
                mysqli_free_result($result);
            }
        }
        elseif (isset($_GET['value']) && $_GET['value'] == 2){
            //send this user location
            $query = 'SELECT l.user AS user_id, l.id AS location_id, l.status AS location_status, location_lat, location_lng, l.dateTime AS location_dateTime 
                        FROM 
                        (Select MAX(dateTime) AS dateTime FROM location where user=1) AS latest_location, location l 
                        WHERE l.dateTime = latest_location.dateTime';

            if ($result = mysqli_query($con, $query)) {
                $data['success'] = true;
                $data['message'] = "Showing your latest shared location.";
                if (mysqli_num_rows($result) == 1) {
                    $row = mysqli_fetch_array($result);
                    $data['result'][0] = $row;
                }
                mysqli_free_result($result);
            }
        }

        echo json_encode($data);
        mysqli_close($con);

    } else {
        echo "Oops. Something went wrong! Please try again later";
    }
}