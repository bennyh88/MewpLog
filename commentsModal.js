
function editModal(editbuttonid) {
    editButtonID = editbuttonid;
    var editModal = document.getElementById("editModal");
    editModal.style.display = "block";
    //sendRequest("param=loadInModalDropDown&asset=all", "inModalAssetDropDown");
}
function closeEditModal() {
    var editModal = document.getElementById("editModal");
    editModal.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
//TODO doesnt work?
window.onclick = function(event) {
    //console.log("event = " + event.target);
    if (event.target == editModal) {
        editModal.style.display = "none";
    }
}
