(function () {
    var isLoggedIn = sessionStorage.getItem('username') !== null;

    if (isLoggedIn) {
        showReport();
    } else {
        showLogin();
    }
})();

function login() {
    var username = $('#username').val();
    var password = $('#password').val();

    $.ajax({
        url: '/send_email/report_ctc/auth.php',
        method: 'POST',
        contentType: 'application/x-www-form-urlencoded',
        data: {
            username: username,
            password: password
        },
        success: function (data) {
            if ($.trim(data) === 'success') {
                sessionStorage.setItem('username', username);
                showReport();
            } else {
                $('#error-message').text('Invalid username or password.');
            }
        },
        error: function (error) {
            console.error('Error during authentication:', error);
        }
    });
}

function showLogin() {
    $('#form-login').show();
    $('#form-report').hide();
}

function showReport() {
    $('#form-login').hide();
    $('#form-report').show();
}
