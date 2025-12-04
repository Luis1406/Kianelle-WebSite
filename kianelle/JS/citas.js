const form = document.querySelector(".appointment-form");
const appointmentIdInput = document.querySelector("#appointment-id");
const submitButton = document.querySelector("#submit-button");

const serviceSelect = document.querySelector("#service-select");
const dateSelect = document.querySelector("#date-select");
const timeSelect = document.querySelector("#time-select");
const firstNameInput = document.querySelector("#first-name");
const lastNameInput = document.querySelector("#last-name");
const phoneInput = document.querySelector("#phone");
const emailInput = document.querySelector("#email");
const commentsInput = document.querySelector("#comments");

const tbody = document.querySelector("#appointments-tbody");
const noAppointmentsMessage = document.querySelector("#no-appointments-message");

let citas = [];

// ---------- UTILIDADES ----------

function resetForm() {
    form.reset();
    appointmentIdInput.value = "";
    submitButton.textContent = "Confirmar mi Cita";
}

function scrollToForm() {
    const section = document.querySelector("#appointment-form-section");
    if (section) {
        window.scrollTo({
            top: section.offsetTop - 80,
            behavior: "smooth"
        });
    }
}

// ---------- RENDERIZAR TABLA ----------

function renderCitas() {
    tbody.innerHTML = "";

    if (!citas || citas.length === 0) {
        noAppointmentsMessage.style.display = "block";
        return;
    }

    noAppointmentsMessage.style.display = "none";

    citas.forEach((cita) => {
        const tr = document.createElement("tr");

        tr.innerHTML = `
            <td>${cita.servicio}</td>
            <td>${cita.fecha}</td>
            <td>${cita.hora}</td>
            <td>${cita.nombre} ${cita.apellido}</td>
            <td>${cita.telefono}</td>
            <td>
                <div class="action-buttons">
                    <button class="action-btn edit" data-id="${cita.id}">
                        <i class="fa-solid fa-pen"></i> Editar
                    </button>
                    <button class="action-btn cancel" data-id="${cita.id}">
                        <i class="fa-solid fa-xmark"></i> Cancelar
                    </button>
                </div>
            </td>
        `;

        const editBtn = tr.querySelector(".action-btn.edit");
        const cancelBtn = tr.querySelector(".action-btn.cancel");

        editBtn.addEventListener("click", () => startEdit(cita.id));
        cancelBtn.addEventListener("click", () => deleteCita(cita.id));

        tbody.appendChild(tr);
    });
}

// ---------- OPERACIONES CON EL BACKEND ----------

async function loadCitas() {
    try {
        const response = await fetch("/kianelle/backend/listar_citas.php");
        const result = await response.json();

        if (result.success) {
            citas = result.citas;
            renderCitas();
        } else {
            alert(result.message || "Error al cargar las citas");
        }
    } catch (error) {
        console.error(error);
        alert("Ocurrió un error al intentar cargar las citas.");
    }
}

async function createOrUpdateCita(e) {
    e.preventDefault();

    const isEditing = appointmentIdInput.value !== "";

    const data = {
        servicio: serviceSelect.value,
        fecha: dateSelect.value,
        hora: timeSelect.value,
        nombre: firstNameInput.value,
        apellido: lastNameInput.value,
        telefono: phoneInput.value,
        email: emailInput.value,
        comentarios: commentsInput.value
    };

    if (isEditing) {
        data.id = appointmentIdInput.value;
    }

    const url = isEditing
        ? "/kianelle/backend/actualizar_cita.php"
        : "/kianelle/backend/crear_cita.php";

    try {
        const response = await fetch(url, {
            method: "POST",
            headers: {"Content-Type": "application/json"},
            body: JSON.stringify(data)
        });

        const result = await response.json();
        alert(result.message);

        if (result.success) {
            resetForm();
            await loadCitas();
        }
    } catch (error) {
        console.error(error);
        alert("Ocurrió un error al procesar la cita.");
    }
}

function startEdit(id) {
    const cita = citas.find(c => String(c.id) === String(id));
    if (!cita) return;

    appointmentIdInput.value = cita.id;

    serviceSelect.value = cita.servicio;
    dateSelect.value = cita.fecha;
    timeSelect.value = cita.hora;
    firstNameInput.value = cita.nombre;
    lastNameInput.value = cita.apellido;
    phoneInput.value = cita.telefono;
    emailInput.value = cita.email;
    commentsInput.value = cita.comentarios || "";

    submitButton.textContent = "Actualizar Cita";
    scrollToForm();
}

async function deleteCita(id) {
    const confirmar = confirm("¿Seguro que deseas cancelar esta cita?");
    if (!confirmar) return;

    try {
        const response = await fetch("/kianelle/backend/eliminar_cita.php", {
            method: "POST",
            headers: {"Content-Type": "application/json"},
            body: JSON.stringify({ id })
        });

        const result = await response.json();
        alert(result.message);

        if (result.success) {
            // Si estabas editando esta misma cita, resetea el formulario
            if (appointmentIdInput.value === String(id)) {
                resetForm();
            }
            await loadCitas();
        }
    } catch (error) {
        console.error(error);
        alert("Ocurrió un error al cancelar la cita.");
    }
}

// ---------- INICIALIZAR ----------

form.addEventListener("submit", createOrUpdateCita);
loadCitas();
