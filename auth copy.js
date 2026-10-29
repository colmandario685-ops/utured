function showForm(formType) {
    const formLogin = document.getElementById("formLogin");
    const formRegister = document.getElementById("formRegister");
    const tabLogin = document.getElementById("tabLogin");
    const tabRegister = document.getElementById("tabRegister");

    if (formType === 'login') {
        formLogin.classList.remove("hidden");
        formRegister.classList.add("hidden");
        tabLogin.classList.add("active");
        tabRegister.classList.remove("active");
    } else {
        formLogin.classList.add("hidden");
        formRegister.classList.remove("hidden");
        tabRegister.classList.add("active");
        tabLogin.classList.remove("active");
    }
}

document.addEventListener("DOMContentLoaded", () => {
    const modal = document.getElementById("recoveryModal");
    const btnOpen = document.getElementById("btnOpenRecovery");
    const btnClose = document.getElementById("btnCloseRecovery");

    if (btnOpen) {
        btnOpen.addEventListener("click", (e) => {
            e.preventDefault();
            modal.classList.remove("hidden");
        });
    }

    if (btnClose) {
        btnClose.addEventListener("click", () => {
            modal.classList.add("hidden");
        });
    }
});