function generatePDF() {
    var formData = $('#generatePdfForm').serialize();
    var userName = sessionStorage.getItem('userName');
    var ccuTotals = sessionStorage.getItem('ccuTotals');
    var ccuValuesString = sessionStorage.getItem('ccuValues');
    var ccuValues = JSON.parse(ccuValuesString);

    formData += '&userName=' + userName + '&ccuTotals=' + ccuTotals;

    Object.keys(ccuValues).forEach(function (id) {
        formData += '&ccu[' + id + ']=' + ccuValues[id];
    });

    showNotification('Đang tạo và gửi tệp PDF. Vui lòng đợi...');

    generatePdfAndHandleModal(formData);
}

async function generatePdfAndHandleModal(formData) {
    try {
        // Make the AJAX request
        const response = await $.ajax({
            type: 'POST',
            url: '/send_email/report_ctc/generate_pdf.php',
            data: formData,
        });

        showNotification('Tệp PDF được tạo và gửi thành công!');
        
        clearSessionStorage();

        const delayDuration = 5000;
        await new Promise(resolve => setTimeout(resolve, delayDuration));

        window.location.href = '/send_email/report_ctc/';
    } catch (error) {
        showNotification('Đã xảy ra lỗi khi tạo và gửi tệp PDF.');
    }
}

function showNotification(message) {
    $('#notificationModalBody').text(message);
}

function checkData() {
    var userName = $('#userName').val().trim();
    var ccuTotals = $('#ccuTotals').val().trim();
    
    if (userName === '' && ccuTotals === '') {
        showNotification('Vui lòng điền thông tin vào bảng dữ liệu.');
        return;
    }
    
    if (ccuTotals === '') {
        showNotification('Vui lòng điền Tổng CCU.');
        return;
    }
    
    if (userName === '') {
        showNotification('Vui lòng điền tên Nhân Viên.');
        return;
    }
    
    var ccuValues = {};
    $('input[name^="ccu["]').each(function () {
        var id = this.name.match(/\[(\d+)\]/)[1];
        ccuValues[id] = this.value;
    });
    
    sessionStorage.setItem('userName', userName);
    sessionStorage.setItem('ccuTotals', ccuTotals);
    sessionStorage.setItem('ccuValues', JSON.stringify(ccuValues));
    
    showNotification('Vui lòng đợi...');

    $('#checkData').submit();
}

function clearSessionStorage() {
    sessionStorage.removeItem('userName');
    sessionStorage.removeItem('ccuTotals');
    sessionStorage.removeItem('ccuValues');
    sessionStorage.removeItem('username');
}

setInterval(function() {
    var currentTime = new Date();
    var day = currentTime.getDate();
    var month = currentTime.getMonth() + 1;
    var year = currentTime.getFullYear();
    var hours = currentTime.getHours();
    var minutes = currentTime.getMinutes();
    var seconds = currentTime.getSeconds();

    // Pad single digits with leading zero
    hours = (hours < 10) ? '0' + hours : hours;
    minutes = (minutes < 10) ? '0' + minutes : minutes;
    day = (day < 10) ? '0' + day : day;
    month = (month < 10) ? '0' + month : month;

    var formattedTime = day + "-" + month + "-" + year + " " + hours + ":" + minutes + ":" + seconds;

    // Update the display element
    $('#currentTime').text('Thời gian kiểm tra: ' + formattedTime);
}, 1000);