var oldRadio = "All";
var oldAssetStatusFilter = "all";

$(document).ready(function() {
//jQuery(document).ready(function() {
    loadAssetsRadioButtons();
    loadAssetTable();
    loadLogTable();
    oldRadio = getCheckedRadio();
    oldAssetStatusFilter = getAssetStatusFilter();
    refreshData();
});

function refreshData() {
    var pollTime = 0.2;  //Seconds
    var checkedRadio = getCheckedRadio();
    if (oldRadio != checkedRadio) {
        oldRadio = checkedRadio;
        //console.log("checked radio = " + checkedRadio);
        loadLogTable();
    }
    var assetStatusFilter = getAssetStatusFilter();
    if (oldAssetStatusFilter != assetStatusFilter) {
        oldAssetStatusFilter = assetStatusFilter;
        loadAssetTable();
    }
    setTimeout(refreshData, pollTime*1000);
}

function sendRequest(param, element) {
    console.log("param= " + param);
    var request = new XMLHttpRequest();
    request.withCredentials = true;
    request.responseType = 'text';
    request.open("GET", "mewp.php?" + param, true);
    request.send();
    request.onreadystatechange =
        function() {
            var resp = this.response;
            document.getElementById(element).innerHTML = resp;
            if (element == "responseDiv") {
                location.reload();
            }
        };
    request.onerror =
        function() {
            //alert("Request failed");
            console.log("Request Failed");
            document.getElementById("responseDiv").innerHTML = "Request Failed";

        };
}

function getCheckedRadio() {
    var asset = $("input[name=assetsRadio]:checked").val();//.val()
    //console.log("location = " + location);
    return asset;
}
function getAssetStatusFilter() {
    var assetStatusFilter = $("input[name=statusRadio]:checked").val();
    return assetStatusFilter;
}

function loadAssetTable() {
    var assetStatusFilter = getAssetStatusFilter();
    sendRequest("param=loadAssetTable&assetStatusFilter=" + assetStatusFilter, "assetTable");
}

function loadAssetsRadioButtons() {
    sendRequest("param=loadAssetsRadioButtons&asset=all", "assetsRadioButtons");
}

function loadLogTable() {
    var asset = $("input[name=assetsRadio]:checked").val();
    if (asset == null) {
        asset = "all";
    }
    sendRequest("param=loadLogTable&asset=" + asset, "logTable");
}

function setAssetOut() {
    var asset =  $("#asset").val();
    var name = $("#name").val();
    var clockno = $("#clockno").val();
    var jobno = $("#jobno").val();
    console.log("jobno =" + jobno);
    var request = "param=setAssetOut&asset=" + asset + "&name=" + name + "&clockno=" + clockno + "&jobno=" + jobno;
    console.log(request);
    sendRequest(request, "responseDiv");
    //location.reload();
}

function readFields() {
    var asset =  $("#asset").val();
    console.log("asset =" + asset);
    var name = $("#name").val();
    console.log("name =" + name);
    var clockno = $("#clockno").val();
    console.log("clockno =" + clockno);
    var jobno = $("#jobno").val();
    console.log("jobno =" + jobno);
    var request = "param=setAssetOut&asset=" + asset + "&name=" + name + "&clockno=" + clockno + "&jobno=" + jobno;
    console.log(request);
}

function setAssetIn() {
    var asset =  $("#asset").val();
    var comment = $("#commentInput").val();
    //console.log("selected asset =" + asset);
    sendRequest("param=setAssetIn&asset=" + asset + "&comment=" + comment, "responseDiv");

    //location.reload();
}
