function togglePasswordVisibility() {
    const passwordField = document.getElementById('password');
    const icon = document.getElementById('togglePasswordIcon');
    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        icon.src = '/images/eyebrow.png'; 
    } else {
        passwordField.type = 'password';
        icon.src = '/images/openeye.png'; 
    }
}