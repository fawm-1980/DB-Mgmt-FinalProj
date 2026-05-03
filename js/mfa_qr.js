document.addEventListener('DOMContentLoaded', function () {
    const qrElement = document.getElementById('qrcode');
    const otpauthUriElement = document.getElementById('otpauth-uri');

    if (!qrElement || !otpauthUriElement) {
        return;
    }

    new QRCode(qrElement, {
        text: otpauthUriElement.value,
        width: 200,
        height: 200
    });
});