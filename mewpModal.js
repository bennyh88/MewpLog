
//out modal
function assetOutModal() {
    var outModal = document.getElementById("outModal");
    console.log("outmodal = " + outModal);
    outModal.style.display = "block";
    sendRequest("param=loadOutModalDropDown&asset=all", "outModalAssetDropDown");
}
function closeOutModal() {
    var outModal = document.getElementById("outModal");
    outModal.style.display = "none";
}


//in modal
function assetInModal() {
    var inModal = document.getElementById("inModal");
    inModal.style.display = "block";
    sendRequest("param=loadInModalDropDown&asset=all", "inModalAssetDropDown");
}
function closeInModal() {
    var inModal = document.getElementById("inModal");
    inModal.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
    console.log("event = " + event.target);
    if (event.target == inModal) {
        inModal.style.display = "none";
    }
    if (event.target == outModal) {
        outModal.style.display = "none";
    }
}
