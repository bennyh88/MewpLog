<?php

    $asset = "all";
    $name = "";
    $clockno = "";
    $param = "param";
    $description = "";
    $assetStatusFilter = "";
    $commentStatus = "";
    $comment = "";
    $jobno = "";

    $mysqli = new mysqli("localhost","user","pass","mewplogdb");
    if ($mysqli -> connect_errno) {
        echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
        exit();
    }

    loadParams();

    switch ($param) {
        case "loadOutModalDropDown":
            //echo("loadOutModalDropDown");
            loadOutModalDropDown();
            break;
        case "loadInModalDropDown":
            //echo("loadInModalDropDown");
            loadInModalDropDown();
            break;
        case "loadAssetTable":
            //echo("submitLogEntry");
            loadAssetTable();
            break;
        case "loadAssetsRadioButtons":
            //echo("loadLocationsRadioButtons");
            loadAssetsRadioButtons();
            break;
        case "loadLogTable":
            //echo("loadLogTable");
            loadLogTable();
            break;
        case "setAssetOut":
            //echo("setAssetOut");
            setAssetOut();
            break;
        case "setAssetIn":
            //echo("setAssetIn");
            setAssetIn();
            break;
        case "loadCommentTable":
            //echo("loadCommentTable");
            loadCommentTable();
            break;
        case "getAssetDescription":
            //echo("getAssetDescription");
            getAssetDescription();
            break;
        default:
           echo "not a valid param";
           rejectUser();
    }

    closeDBConnection();
    exit();

    function loadParams() {
        global $param;
        global $asset;
        global $name;
        global $clockno;
        global $assetStatusFilter;
        global $commentStatus;
        global $comment;
        global $jobno;



        //get the data from the request and make it safe to use before storing it as a variable
        if ($_GET["param"]) {
            $param = filter_input(INPUT_GET, 'param', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            //echo "param";
        }
        if ($_GET["asset"]) {
            $asset = filter_input(INPUT_GET, 'asset', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            //echo "asset = ". $asset;
        }
        if ($_GET["name"]) {
            $name = filter_input(INPUT_GET, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            //echo "name";
        }
        if ($_GET["clockno"]) {
            $clockno = filter_input(INPUT_GET, 'clockno', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            //echo "clockno";
        }
        if ($_GET["jobno"]) {
            $jobno = filter_input(INPUT_GET, 'jobno', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            //echo "jobno";
        }
        if ($_GET["assetStatusFilter"]) {
            $assetStatusFilter = filter_input(INPUT_GET, 'assetStatusFilter', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            //echo "clockno";
        }
        if ($_GET["commentStatus"]) {
            $commentStatus= filter_input(INPUT_GET, 'commentStatus', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            //echo "clockno";
        }
        if ($_GET["comment"]) {
            $comment= filter_input(INPUT_GET, 'comment', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            //echo "clockno";
        }

        if (validateAsset() != true) { //TODO can i get rid of this??
            rejectUser();
        }

    }

    function validateAsset() { //TODO and this too??
        //if the user is not on the list then return false
        global $asset;
        global $mysqli;
        global $description;

        if ($asset == "all") {
            //echo "asset == all";
            return true;
        } else {
            //echo "location =" . $location;
            $query = "SELECT * FROM assets WHERE asset = ?";
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param("s", $asset);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                //echo "true location valid = ". $location;
                while($row = $result->fetch_assoc()) {
                    $description = $row["description"];
                }
                return true;
            } else {
                //echo "false";
                return false;
            }
        }
    }

    function rejectUser() {
        closeDBConnection();
        global $asset;
        echo "asset = ". $asset. " NOT A VALID ASSET";
        exit();
    }

    function closeDBConnection() {
        global $mysqli;
        $mysqli->close();
        //echo "MySQL connection closed successfully";
    }

    function loadLogTable() {
        global $asset;
        global $mysqli;

        if ($asset == "all") {
            $query = "SELECT asset, description, clockno, name, jobno, timeout, timein FROM mewplog ORDER BY timeout DESC Limit 25";
        } else {
            $query = "SELECT asset, description, clockno, name, jobno, timeout, timein FROM mewplog WHERE asset = ? ORDER BY timeout DESC Limit 25";
        }

        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("s", $asset);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo "<table><thead><tr><th>Asset</th><th>Name</th><th>Clock Number</th><th>Job</th><th>Time Out</th><th>Time In</th></tr></thead><tbody>";
            //echo "<tbody>";
            while($row = $result->fetch_assoc()) {
                echo "<tr><td>". $row["asset"]. " - ". $row["description"]. "</td><td>". $row["name"]. "</td><td>". $row["clockno"]. "</td><td>". $row["jobno"]. "</td><td>". $row["timeout"]. "</td><td>". $row["timein"]. "</td></tr>";
            }
            echo "</tbody>";
        } else {
            echo "No entries for ". $asset;
        }
        //echo "loadPageContent";
    }


    function loadAssetsRadioButtons() {
        global $mysqli;

        $query = "SELECT * FROM assets";
        $stmt = $mysqli->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            echo "<input type='radio' id='r0' name='assetsRadio' value='all' checked='checked'>All</input><br>";
            $i = 0;
            while ($row = $result->fetch_assoc()) {
              $i ++;
              echo "<input type='radio' id='r". $i. "' name='assetsRadio' value='". $row["asset"]. "'>". $row["asset"]. " - ". $row["description"]. "</input><br>";
            }
        }
    }

    function loadAssetTable() {
        global $mysqli;
        global $assetStatusFilter;

        $query = "";

        switch ($assetStatusFilter) {
            case "all":
                $query = "SELECT * FROM assets";
                break;
            case "out":
                $query = "SELECT * FROM assets WHERE status = 1";
                break;
            case "in":
                $query = "SELECT * FROM assets WHERE status = 0";
                break;
            default:
               echo "not a valid status";
               rejectUser();
        }

        $stmt = $mysqli->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            echo "
            <table>
                <thead>
                    <th>Asset</th><th>Status</th><th>Name</th><th>Clock Number</th><th>Job</th><th>Time Out</th><th>Comments</th>
                </thead>
            <tbody>";
            while ($row = $result->fetch_assoc()) {
                //<th>Asset - Description</th><th>Status</th><th>Name</th><th>Clock Number</th><th>Job</th><th>Time Out</th><th>button</th>
                $colour = checkLineColour($row["status"]);
                echo "<tr id=". $row["asset"]. " style = 'color:". $colour. "' ><td>". $row["asset"]. " - ". $row["description"]. "</td>";
                if ($row["status"] == 1) {
                    echo   "<td>In Use</td>
                            <td>". $row["name"]."</td>
                            <td>". $row["clockno"]. "</td>
                            <td>". $row["jobno"]. "</td>
                            <td>". $row["timeout"]. "</td>
                            <td><a href='comments.html?asset=". $row["asset"]."'>Comments</a></td>
                            </tr>";
                } else {
                    echo   "<td>Available</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td><a href='comments.html?asset=". $row["asset"]."'>Comments</a></td>
                            </tr>";
                }
            }
            echo
                "</tbody>
            </table>";
        }
    }

    function checkLineColour($status) {
        //status will be true when asset is out in use, but only for a particular log entry. should be reset to 0 when asset is returned
        if ($status == true) {
            return "red";
        } else {
            return "green";
        }
    }

    function setAssetIn() {
        global $mysqli;
        global $asset;
        global $comment;

        // mewplog copy assetno, description, name, clockno, timein, timeout from assets table
        $query = "SELECT * FROM assets WHERE asset = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("s", $asset);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            //echo "rows =". $result->num_rows;
            while($row = $result->fetch_assoc()) {
                $description = $row["description"];
                $name = $row["name"];
                $clockno = $row["clockno"];
                $timeout = $row["timeout"];
                $jobno = $row["jobno"];
                //echo "desc". $description;
                //echo "clockno =". $clockno;
            }

            if ($comment != null) {
                $status = "Waiting Action";
                $query = "INSERT INTO comments (asset, status, defect) VALUES (?,?,?)";
                //$query = "INSERT INTO comments (asset, status, defect) values ('M7449', 'Waiting Action', 'test comment, here is extra padding for length')";
                $stmt = $mysqli->prepare($query);
                $stmt->bind_param("sss", $asset, $status, $comment);
                $stmt->execute();
            }

            $query = "INSERT INTO mewplog (asset, description, name, clockno, jobno, timeout) VALUES (?,?,?,?,?,?)";
            //$query = "INSERT INTO mewplog (asset, description, name, clockno, timeout) VALUES ('M7449','test','name','8922','00000000')";

            $stmt = $mysqli->prepare($query);
            $stmt->bind_param("ssssss", $asset, $description, $name, $clockno, $jobno, $timeout);
            $stmt->execute();
        }


        // assets table change status to 0, clear timeout, clear name, clear clockno
        $status = 0;
        $name = "";
        $clockno = "";
        $timeout = "";

        $query = "UPDATE assets SET status = 0 WHERE asset = ?";
        //$query = "UPDATE assets SET status = ?, clockno = ?, name = ?, timeout = ? WHERE asset = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("s", $asset);
        //$stmt->bind_param("issss", $status, $clockno, $name, $timeout, $asset);
        $stmt->execute();
        echo "Asset set in successfully";

    }

    function setAssetOut() {
        //assets table change status to 1, store a time in timeout, store name, store clockno
        global $mysqli;
        global $asset;
        global $name;
        global $clockno;
        global $jobno;
        echo "jobno = " + $jobno;

        $status = 1;
        $query = "UPDATE assets SET status = 1, clockno = ?, name = ?, jobno = ? WHERE asset = ?";
        //$query = "UPDATE assets SET name="althamas" WHERE name="ram"
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("ssss", $clockno, $name, $jobno, $asset);
        $stmt->execute();

        echo "Asset set out successfully";
    }

    function loadOutModalDropDown() {
        global $mysqli;

        $query = "SELECT asset, description FROM assets WHERE status = 0";
        $stmt = $mysqli->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            echo   "<label for='asset'>Asset:</label>
                    <select name='asset' id='asset'>";
            while ($row = $result->fetch_assoc()) {
              echo "<option value='". $row["asset"]."'>". $row["asset"]. " - ". $row["description"]. "</option>";
            }
            echo "<select>";
        } else {
            echo "All Assets are in use!";
        }
    }

    function loadInModalDropDown() {
        global $mysqli;

        $query = "SELECT asset, description FROM assets WHERE status = 1";
        $stmt = $mysqli->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            echo   "<label for='asset'>Asset:</label>
                    <select name='asset' id='asset'>";
            while ($row = $result->fetch_assoc()) {
              echo "<option value='". $row["asset"]."'>". $row["asset"]. " - ". $row["description"]. "</option>";
            }
            echo "<select>";
        } else {
            echo "All Assets are available!";
        }
    }

    function loadCommentTable() {
        global $mysqli;
        global $commentStatus;

        $query = "";

        switch ($commentStatus) {
            case "all":
                $query = "SELECT * FROM comments";
                break;
            case "wact": //Waiting Action
                $query = "SELECT * FROM comments WHERE status = 'Waiting Action'";
                break;
            case "wor": //Work Order Raised
                $query = "SELECT * FROM comments WHERE status = 'Work Order Raised'";
                break;
            case "comp": //Complete
                $query = "SELECT * FROM comments WHERE status = 'Complete'";
                break;
            default:
               echo "not a valid status";
               rejectUser();
        }

        $stmt = $mysqli->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            echo "
            <table>
                <thead>
                    <th>Status</th><th>Status Date</th><th>Comment or Defect</th>
                </thead>
            <tbody>";
            while ($row = $result->fetch_assoc()) {
                $colour = checkCommentColour($row["status"]);
                echo "
                    <tr style = 'color:". $colour. "'>
                    <td><input type='button' id='". $row["id"]. "' value='". $row["status"]."' onclick='toggleCommentStatus(this)'></td>
                    <td>". $row["statusdate"]."</td>
                    <td>". $row["defect"]."</td>";
            }
            echo
                "</tbody>
            </table>";
        }
    }

    function checkCommentColour($status) {
        switch ($status) {
            case "Waiting Action":
                return "red";
            case "Work Order Raised":
                return "orange";
            case "Complete":
                return "green";
            default:
               return "black";
        }
    }

    function getAssetDescription() {
        global $description;
        global $asset;
        //echo "asset - description";
        echo $asset. " - ". $description;
    }
?>
