<?php
session_start();

if(!isset($_SESSION["loggedIn"]) || $_SESSION["loggedIn"] !== true){
    header("location: Auth/login.php");
    exit;
}

?>
<!Doctype html>
<html lang="en">
<head>
    <title>Home</title>
    <link rel="stylesheet" type="text/css" href="../static/bower_components/leaflet/leaflet.css" media="all">
    <link rel="stylesheet" type="text/css" href="../static/bower_components/leaflet-locatecontrol/dist/L.Control.Locate.min.css">
    <link rel="stylesheet" href="../static/bower_components/fontawesome-free-5.12.0-web/css/all.min.css" type="text/css" media="all">
    <link rel="stylesheet" type="text/css" href="../static/css/base.css" media="all">

    <script src="../static/bower_components/jquery/dist/jquery.min.js"></script>
    <script src="../static/bower_components/leaflet/leaflet.js"></script>
    <script src="../static/bower_components/leaflet-locatecontrol/dist/L.Control.Locate.min.js"></script>
    <script src="../static/js/base.js"></script>
    <script src="../static/js/dropdown.js"></script>
    <script src="../static/js/ajax.js"></script>

</head>
<body>
    <div class="row">
    </div>
    <div class="row"">
        <div class="col-3">
            <div class="card" style="display: block;overflow: scroll;height: 100%;padding-left: 0;padding-right: 0;">
                <div class="card-header text-center">MySharedLocations</div>
                <div id="myLocations" class="card-body" style="padding-right: 0;padding-left: 0;"></div>
            </div>
        </div>
        <div class="col-6">
            <div class="dropdown mb-3">
                <button class="dropdown-btn" onclick="dropdownClickEvent(); changeDropdownIcon();" type="button">
                    Share your location
                    <i class="fas fa-map-marker" id="dropdown-icon"></i>
                </button>
                <div class="dropdown-content" id="dropdown">
                    <p class="mb-1">1. Change your location in map if it's not accurate. otherwise let it be!</p>
                    <div id="dropdownMapId" class="mb-2"></div>
                    <form method="post" id="mapForm" novalidate>
                        <input type="hidden" name="latitude" id="lat">
                        <input type="hidden" name="longitude" id="lng">
                        <p style="margin-bottom: 0!important;">2. Write your status.</p>
                        <textarea class="form-input" name="status" rows="4" cols="50"></textarea>
                        <button type="submit" class="mt-3 btn-block">
                            share
                            <i class="fas fa-share" id="form-button-icon"></i>
                        </button>
                    </form>
                    <p class="mt-2 mb-0" id="form-result">
                        <i class="fas" id="form-result-icon"></i>
                        <span id="form-result-status"></span>
                    </p>
                </div>
            </div>
            <div class="mb-3">
                <label>select
                    <select name="checklist">
                        <option value="2">Me</option>
                        <option value="3">Friends</option>
                        <option value="4">Others</option>
                        <option value="5">All</option>
                    </select>
                </label>
                <span>
                    <i id="mapFilterStatusIcon" class="fas ml-1"></i>
                    <span id="mapFilterStatus"></span>
                </span>
            </div>
            <div id="mapId"></div>
        </div>
        <div class="col-3">
            <div class="card" style="display: block;overflow: scroll;height: 100%;">
                <div class="card-header text-center">Friends Status</div>
                <div class="card-body">

                </div>
            </div>
        </div>
    </div>
    <script src="../static/js/map.js"></script>
    <script>
        function get_session_id() {
            return "<?php echo $_SESSION['id']; ?>";
        }
    </script>

</body>
</html>
