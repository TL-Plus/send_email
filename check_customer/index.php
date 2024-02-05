<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Check Customer DigiNext</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>

<body class="container mt-5">

    <h1 class="text-center">Check Customer DigiNext</h1>

    <form method="POST" action="" class="mt-4">
        <div class="mb-3">
            <label for="number_sequence" class="form-label">Enter a number sequence (separated by spaces):</label>
            <input type="text" name="number_sequence" id="number_sequence" class="form-control">
        </div>
        <button type="submit" name="convert" class="btn btn-primary">Convert Number Sequence</button>
        <button type="submit" name="export_excel" class="btn btn-success">Export to Excel and Send Telegram</button>
    </form>

    <?php include 'includes/body_index.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous">
    </script>

    <script>
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
    </script>

</body>

</html>