<?php

    $asset = "all";
    $param = "param";
    $description = "";
    $commentStatus = "";
    $commentid = "";
    $details = "";

    $mysqli = new mysqli("localhost","user","pass","mewplogdb");
    if ($mysqli -> connect_errno) {
        echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
        exit();
    }

    loadParams();

    switch ($param) {
        case "loadCommentTable":
            //echo("loadCommentTable");
            loadCommentTable();
            break;
        case "getAssetDescription":
            //echo("getAssetDescription");
            getAssetDescription();
            break;
        case "changeDefectStatus":
            //echo("changeDefectStatus");
            changeDefectStatus();
            break;
        case "appendComment":
            //echo("appendComment");
            appendComment();
            break;
        default:
           echo "not a valid param";
           rejectUser();
    }

    closeDBConnection();

    function loadParams() {
        global $param;
        global $asset;
        global $commentStatus;
        global $commentid;
        global $details;

        //get the data from the request and make it safe to use before storing it as a variable
        if ($_GET["param"]) {
            $param = filter_input(INPUT_GET, 'param', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            //echo "param";
        }
        if ($_GET["asset"]) {
            $asset = filter_input(INPUT_GET, 'asset', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            //echo "asset = ". $asset;
        }
        if ($_GET["commentStatus"]) {
            $commentStatus= filter_input(INPUT_GET, 'commentStatus', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            //echo "clockno";
        }
        if ($_GET["commentid"]) {
            $commentid = filter_input(INPUT_GET, 'commentid', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            //echo "clockno";
        }
        if ($_GET["details"]) {
            $details = filter_input(INPUT_GET, 'details', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
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

    function loadCommentTable() {
        global $mysqli;
        global $commentStatus;
        global $asset;

        $query = "";

        switch ($commentStatus) {
            case "all":
                //$query = "SELECT * FROM comments WHERE asset = ? ORDER BY FIELD(status, "Waiting Action", "Work Order Raised", "Complete") DESC, statusdate DESC";
                $query = "SELECT * FROM comments WHERE asset = ? ORDER BY status DESC, statusdate DESC";
                break;
            case "wact": //Waiting Action
                $query = "SELECT * FROM comments WHERE status = 'Waiting Action' AND asset = ? ORDER BY statusdate DESC";
                break;
            case "wor": //Work Order Raised
                $query = "SELECT * FROM comments WHERE status = 'Work Order Raised' AND asset = ? ORDER BY statusdate DESC";
                break;
            case "comp": //Complete
                $query = "SELECT * FROM comments WHERE status = 'Complete' AND asset = ? ORDER BY statusdate DESC";
                break;
            default:
               echo "not a valid status";
               rejectUser();
        }

        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("s", $asset);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            echo "
            <table>
                <thead>
                    <th>Status</th><th>Status Date</th><th>Comment or Defect</th><th></th>
                </thead>
            <tbody>";
            while ($row = $result->fetch_assoc()) {
                $colour = checkCommentColour($row["status"]);
                echo    "<tr style = 'color:". $colour. "'>";

                if ($row["status"] == "Complete") {
                    echo "<td>". $row["status"]."</td>";
                } else {
                    echo "<td><input type='submit' id='". $row["id"]. "' value='". $row["status"]."' onclick='toggleCommentStatus(this)'></td>";
                }

                echo   "<td>". $row["statusdate"]."</td>
                        <td>". $row["defect"]."</td>
                        <td><input type='button' id='edit". $row["id"]. "' value='Add Details' onclick='editModal(this)'></td>";
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

    function changeDefectStatus() {
        global $mysqli;
        global $commentid;
        $commentStatus = "";
        //get commentid status
        $query = "SELECT status FROM comments WHERE id = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("s", $commentid);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            if ($result->num_rows > 1) {
                echo "error: multiple database entries with same id";
                closeDBConnection();
                exit();
            }
            while($row = $result->fetch_assoc()) {
                $commentStatus = $row["status"];
                switch ($commentStatus) {
                    case "Waiting Action":
                        $commentStatus = "Work Order Raised";
                        break;
                    case "Work Order Raised":
                        $commentStatus = "Complete";
                        break;
                    case "Complete":
                        echo "status cannot be changed once complete";
                        closeDBConnection();
                        exit();
                        break;
                    default:
                       echo "error: comment status unrecognised";
                       closeDBConnection();
                       exit();
                }

                $query = "UPDATE comments SET status = ? WHERE id = ?";
                $stmt = $mysqli->prepare($query);
                $stmt->bind_param("ss",$commentStatus, $commentid);
                $stmt->execute();
                echo "status updated";
            }
        } else {
            echo "error: row with id ". $commentid. " does not exist in the database";
            closeDBConnection();
            exit();
        }
    }


    function appendComment() {
        global $commentid;
        global $details;
        global $mysqli;
        //parse comment id to get the line id in database;
        $commentid = str_replace('edit', '', $commentid);
        //$details = " \n". $details;
        $details = "<br>". $details;

        $query = "UPDATE comments set defect = concat(defect, ?) WHERE id = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("ss", $details, $commentid);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            //loadCommentTable();
            echo "details added successfully!";
        }

    }
?>
