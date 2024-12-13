const users = [
    { id: 1, name: "Caleri o melhor", email: "Caleri@example.com", photo: "baixados.jpeg" },
    { id: 2, name: "Romero bosta", email: "Romero@example.com", photo: "romero.jpeg" },
    { id: 3, name: "Bolsonabo", email: "Bolsonabo@example.com", photo: "bolsonaro careca fds.jpeg" },
    { id: 4, name: "Lulastico", email: "Lulastico@example.com", photo: "lula.jpeg" },
];

function renderUsers() {
    const userCardContainer = document.getElementById("userCardContainer");
    userCardContainer.innerHTML = ""; // Limpa os cards existentes

    users.forEach(user => {
        const card = document.createElement("div");
        card.className = "user-card";
        card.innerHTML = `
            <img src="${user.photo}" alt="${user.name}" class="user-photo">
            <div class="user-info">
                <h3>${user.name}</h3>
                <p>${user.email}</p>
            </div>
            <div class="user-card-actions">
                <button onclick="editUser(${user.id})">Editar</button>
                <button onclick="deleteUser(${user.id})">Excluir</button>
            </div>
        `;
        userCardContainer.appendChild(card);
    });
}

function addUser() {
    const newUser = { id: users.length + 1, name: "Novo Usuário", email: "novo@example.com", photo: "default.jpg" };
    users.push(newUser);
    renderUsers();
}

function editUser(id) {
    alert(`Editando usuário com ID: ${id}`);
}

function deleteUser(id) {
    const index = users.findIndex(user => user.id === id);
    if (index > -1) {
        users.splice(index, 1);
        renderUsers();
    }
}

document.getElementById("addUserBtn").addEventListener("click", addUser);
renderUsers();
