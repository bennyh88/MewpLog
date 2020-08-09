<?php

    $param = "param";
    $asset = "asset";
    $description = "description";
    $pwd = "pwd";

    $mysqli = new mysqli("localhost","user","pass","mewplogdb");
    if ($mysqli -> connect_errno) {
        echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
        exit();
    }

    loadParams();

    switch ($param) {
        case "loadMEWPTable":
            //echo("loadLocationsTable");
            loadMEWPTable();
            break;
        case "createAsset":
            //echo("createLocation");
            createAsset();
            break;
        case "deleteAsset":
            //echo("deleteLocation");
            deleteAsset();
            break;
        default:
           echo "not a valid param! param = ". $param;
           rejectUser();
    }

    closeDBConnection();
    exit();

    function loadParams() {
        global $param;
        global $asset;
        global $description;
        global $pwd;

        //get the data from the request and make it safe to use before storing it as a variable
        if ($_GET["param"]) {
            $param = filter_input(INPUT_GET, 'param', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            //echo "param";
        }
        if ($_GET["asset"] ) {
            $asset = filter_input(INPUT_GET, 'asset', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            //echo "asset";
        }
        if ($_GET["description"]) {
            $description = filter_input(INPUT_GET, 'description', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            //echo "description";
        }
        if ($_GET["pwd"]) {
            $pwd = filter_input(INPUT_GET, 'pwd', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            //echo "pwd";
        }
    }


    function rejectUser() {
        closeDBConnection();
        exit();
    }

    function closeDBConnection() {
        global $mysqli;
        $mysqli->close();
        //echo "MySQL connection closed successfully";
    }

    function loadMEWPTable() {
        global $mysqli;

        $query = "SELECT asset, description FROM assets ORDER BY asset";

        $stmt = $mysqli->prepare($query);
        //$stmt->bind_param("s", $location);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            echo "<table><thead><th>Asset</th><th>Description</th></thead><tbody>";
            while($row = $result->fetch_assoc()) {
                echo "<tr><td>". $row["asset"]. "</td><td>". $row["description"]. "</td></tr>";
            }
            echo "</tbody></table>";
        }
        //echo "loadPageContent";
    }

    function createAsset() {
        global $mysqli;
        global $asset;
        global $description;
        global $pwd;

        if ($asset == "") {
            echo "Asset must not be blank";
            rejectUser();
        }
        if ($description == "") {
            echo "Description must not be blank";
            rejectUser();
        }
        if ($pwd != "pass") {
            echo "Password Incorrect!";
            rejectUser();
        }
        $status = 0;
        $name = "";
        $clockno = "";
        $timeout = "";

        $query = "REPLACE INTO assets (asset, description, status, name, clockno) VALUES (?,?,?,?,?)";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("ssiss", $asset, $description, $status, $name, $clockno);
        $stmt->execute();

        if ($mysqli->affected_rows >= 1) {
            echo "Asset; ". $asset. ", was created successfully!";
        } else {
            echo "Asset; ". $asset. ", was not created, Please refresh the page and try again.";
            rejectUser();
        }

        //echo "Asset; ". $asset. ", was created successfully!";

    }

    function deleteAsset() {
        global $mysqli;
        global $asset;
        global $pwd;

        if ($asset == "") {
            echo "ID must not be blank";
            rejectUser();
        }
        if ($pwd != "pass") {
            echo "Password Incorrect!";
            rejectUser();
        }

        $query = "DELETE FROM assets WHERE asset = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("s", $asset);
        $stmt->execute();

        if ($mysqli->affected_rows == true) {
            $query = "DELETE FROM mewplog WHERE asset = ?";
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param("s", $asset);
            $stmt->execute();
            echo "Asset; ". $asset. ", was deleted successfully!";
        } else {
            echo "Asset; ". $asset. ", was not deleted, Check Asset Number is correct or refresh the page.";
            rejectUser();
        }
    }
?>
