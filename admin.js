

$(document).ready(function() {
    //console.log("docReady");
    loadMEWPTable();
});

function sendRequest(param, element) {

    var request = new XMLHttpRequest();
    request.withCredentials = true;
    request.responseType = 'text';
    request.open("GET", "admin.php?" + param, true);
    request.send();
    request.onreadystatechange =
        function() {
            var resp = this.response;
            document.getElementById(element).innerHTML = resp;
            if (element == "messageDiv") {
                loadMEWPTable();
            }
        };
    request.onerror =
        function() {
            //alert("Request failed");
            console.log("Request Failed");
            document.getElementById("messageDiv").innerHTML = "Request Failed";

        };
}

function loadMEWPTable() {
    var param = "param=loadMEWPTable";
    sendRequest(param, "MEWPTable");
}

function createAsset() {
    var asset = document.getElementById("asset").value;
    console.log("asset = " + asset);
    var description = document.getElementById("description").value;
    console.log("asset = " + asset);
    var pwd = document.getElementById("adminpwd1").value;
    var param = "param=createAsset&asset=" + asset + "&description=" + description + "&pwd=" + pwd;
    sendRequest(param, "messageDiv");
}

function deleteAsset() {
    var asset = document.getElementById("assetDel").value;
    var pwd = document.getElementById("adminpwd2").value;
    var param = "param=deleteAsset&asset=" + asset + "&pwd=" + pwd;
    sendRequest(param, "messageDiv");
}
