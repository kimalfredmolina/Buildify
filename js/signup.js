function togglePasswordVisibility(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    if (input.type === "password") {
        input.type = "text";
        icon.src = "/images/eyebrow.png"; 
    } else {
        input.type = "password";
        icon.src = "/images/openeye.png";
    }
}
