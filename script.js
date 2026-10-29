document.addEventListener("DOMContentLoaded", () => {
    const recoveryForm = document.getElementById("recoveryForm");
    const recoveryMessage = document.getElementById("recoveryMessage");
    const closeModal = document.getElementById("closeModal");
    const modal = document.getElementById("recoveryModal");

    // Manejo del envío del formulario de recuperación
    if (recoveryForm) {
        recoveryForm.addEventListener("submit", (e) => {
            e.preventDefault();
            const email = document.getElementById("recoveryEmail").value;

            if (email) {
                // Simulación de envío AJAX
                recoveryForm.classList.add("hidden");
                recoveryMessage.classList.remove("hidden");
            }
        });
    }

    // Cerrar el modal
    if (closeModal) {
        closeModal.addEventListener("click", () => {
            modal.style.display = "none";
        });
    }
});