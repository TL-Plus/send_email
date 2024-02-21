document.addEventListener("DOMContentLoaded", function() {

    // Get references to the textarea, "Copy All" button, and the copy message
    var textarea = document.getElementById("output");
    var copyButton = document.getElementById("copyButton");
    var copyMessage = document.getElementById("copyMessage");

    if (copyButton) {
        // Event handling when the "Copy All" button is clicked
        copyButton.addEventListener("click", function() {
            textarea.select();
            document.execCommand("copy");
            copyMessage.style.display = "block";
        });
    }
})
