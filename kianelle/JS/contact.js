document.querySelector(".contact-form").addEventListener("submit", async (e) => {
    e.preventDefault();

    const data = {
        nombre: document.querySelector("#nombre").value,
        email: document.querySelector("#email").value,
        mensaje: document.querySelector("#mensaje").value
    };

    const response = await fetch("/kianelle/backend/contacto.php", {
        method: "POST",
        headers: {"Content-Type": "application/json"},
        body: JSON.stringify(data)
    });

    const result = await response.json();
    alert(result.message);
});
