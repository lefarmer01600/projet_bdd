function openModal() {
    document.getElementById("userModal").style.display = "block";
}

function closeModal() {
    document.getElementById("userModal").style.display = "none";
}

window.onclick = function(event) {
    if (event.target == document.getElementById("userModal")) {
        closeModal();
    }
}