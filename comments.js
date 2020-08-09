var oldRadio = "all";
var editButtonID = "";

$(document).ready(function() {
    updateTitles();
    loadCommentTable();
    oldRadio = getCheckedStatusRadio();
    refreshData();
});

function refreshData() {
    var pollTime = 0.2;  //Seconds
    var checkedRadio = getCheckedStatusRadio();
    if (oldRadio != checkedRadio) {
        oldRadio = checkedRadio;
        //console.log("checked radio = " + checkedRadio);
        loadCommentTable();
    }
    setTimeout(refreshData, pollTime*1000);
}

function sendRequest(param, element) {
    var request = new XMLHttpRequest();
    request.withCredentials = true;
    request.responseType = 'text';
    request.open("GET", "comments.php?" + param, true);
    request.send();
    request.onreadystatechange =
        function() {
            var resp = this.response;
            document.getElementById(element).innerHTML = resp;
        };
    request.onerror =
        function() {
            //alert("Request failed");
            console.log("Request Failed");
            document.getElementById("responseDiv").innerHTML = "Request Failed";

        };
}

function getCheckedStatusRadio() {
    var commentStatus = $("input[name=statusRadio]:checked").val();
    //console.log("commentStatus = " + commentStatus;
    return commentStatus;
}

function loadCommentTable() {
    var commentStatus = getCheckedStatusRadio();
    if (commentStatus == null) {
        commentStatus = "all";
    }
    var asset = getAsset();
    sendRequest("param=loadCommentTable&commentStatus=" + commentStatus + "&asset=" + asset, "commentTable");
}

function updateTitles() {
    var asset = getAsset();
    $(document).attr("title", asset + " Comments & Defects");
    loadHeading1();
}

function getAsset() {
    let params = new URLSearchParams(location.search);
    var asset = params.get("asset");
    return asset;
}

function loadHeading1() {
    var asset = getAsset();
    sendRequest("param=getAssetDescription&asset=" + asset, "heading1");
}

function toggleCommentStatus(lineID) {
    var commentid = lineID.id;
    var asset = getAsset();
    console.log("lineID =" + commentid);
    sendRequest("param=changeDefectStatus&asset=" + asset + "&commentid=" + commentid, "responseDiv");
    location.reload();
}

function editComment() {
    var commentid = editButtonID.id;
    var details = $("#details").val();
    var asset = getAsset();
    //sendRequest("param=appendComment&asset=" + asset + "&commentid=" + commentid + "&details=" + details, "commentTable");
    sendRequest("param=appendComment&asset=" + asset + "&commentid=" + commentid + "&details=" + details, "responseDiv");
    location.reload();
}
