document.addEventListener('DOMContentLoaded', () => {
    // --- Elementos del DOM ---
    const loginView = document.getElementById('login-view');
    const registerView = document.getElementById('register-view');
    const dashboardContainer = document.getElementById('dashboard-container');

    const loginUsernameInput = document.getElementById('login-username');
    const loginPasswordInput = document.getElementById('login-password');
    const loginBtn = document.getElementById('login-btn');
    const loginError = document.getElementById('login-error');

    const registerUsernameInput = document.getElementById('register-username');
    const registerPasswordInput = document.getElementById('register-password');
    const registerBtn = document.getElementById('register-btn');
    const registerError = document.getElementById('register-error');

    const showRegisterLink = document.getElementById('show-register-link');
    const showLoginLink = document.getElementById('show-login-link');

    const welcomeUsernameSpan = document.getElementById('welcome-username');
    const lastAccessSpan = document.getElementById('last-access');
    const visitCountSpan = document.getElementById('visit-count');
    const logoutBtn = document.getElementById('logout-btn');

    const newTaskInput = document.getElementById('new-task-input');
    const addTaskBtn = document.getElementById('add-task-btn');
    const pendingTasksList = document.getElementById('pending-tasks-list');
    const completedTasksList = document.getElementById('completed-tasks-list');

    const USER_COOKIE_PREFIX = 'userData_';
    const LOGGED_IN_USER_COOKIE = 'loggedInUser';
    let currentUser = null; // Objeto del usuario actualmente logueado

    // --- Funciones de Cookies ---
    function setCookie(name, value, days) {
        let expires = "";
        if (days) {
            const date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = name + "=" + (value || "") + expires + "; path=/; SameSite=Lax";
    }

    function getCookie(name) {
        const nameEQ = name + "=";
        const ca = document.cookie.split(';');
        for (let i = 0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) === ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    }

    function eraseCookie(name) {
        document.cookie = name + '=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT; SameSite=Lax;';
    }

    // --- Lógica de Autenticación y Usuario ---
    function getUserData(username) {
        const cookieValue = getCookie(USER_COOKIE_PREFIX + username);
        return cookieValue ? JSON.parse(cookieValue) : null;
    }

    function saveUserData(username, data) {
        setCookie(USER_COOKIE_PREFIX + username, JSON.stringify(data), 30); // Guardar por 30 días
    }

    function handleRegister() {
        const username = registerUsernameInput.value.trim();
        const password = registerPasswordInput.value;
        registerError.textContent = '';

        if (!username || !password) {
            registerError.textContent = 'Usuario y contraseña son requeridos.';
            return;
        }
        if (username.includes(' ') || password.includes(' ')) {
            registerError.textContent = 'Usuario y contraseña no deben contener espacios.';
            return;
        }

        if (getUserData(username)) {
            registerError.textContent = 'Este nombre de usuario ya existe.';
            return;
        }

        const newUser = {
            // username: username, // El username es la clave de la cookie, no es necesario guardarlo dentro del objeto
            password: password, // ADVERTENCIA: Contraseña en texto plano. NO SEGURO.
            lastAccess: null,
            visitCount: 0,
            pendingTasks: [],
            completedTasks: []
        };
        saveUserData(username, newUser);
        alert('¡Registro exitoso! Ahora puedes iniciar sesión.');
        showLoginView();
        registerUsernameInput.value = '';
        registerPasswordInput.value = '';
    }

    function handleLogin() {
        const username = loginUsernameInput.value.trim();
        const password = loginPasswordInput.value;
        loginError.textContent = '';

        if (!username || !password) {
            loginError.textContent = 'Usuario y contraseña son requeridos.';
            return;
        }

        const userData = getUserData(username);

        if (!userData || userData.password !== password) {
            loginError.textContent = 'Nombre de usuario o contraseña incorrectos.';
            return;
        }

        // Login exitoso
        currentUser = { username, ...userData }; // Combinamos username con sus datos
        currentUser.visitCount++;
        currentUser.lastAccess = new Date().toLocaleString('es-ES');

        saveUserData(username, { // No guardamos el username dentro del objeto en la cookie
            password: currentUser.password,
            lastAccess: currentUser.lastAccess,
            visitCount: currentUser.visitCount,
            pendingTasks: currentUser.pendingTasks,
            completedTasks: currentUser.completedTasks
        });
        setCookie(LOGGED_IN_USER_COOKIE, username, 1); // Sesión por 1 día

        loginUsernameInput.value = '';
        loginPasswordInput.value = '';
        renderDashboard();
        showDashboardView();
    }

    function handleLogout() {
        eraseCookie(LOGGED_IN_USER_COOKIE);
        currentUser = null;
        showLoginView();
    }

    // --- Lógica del Dashboard y Tareas ---
    function renderDashboard() {
        if (!currentUser) return;
        welcomeUsernameSpan.textContent = currentUser.username;
        lastAccessSpan.textContent = currentUser.lastAccess || 'Nunca';
        visitCountSpan.textContent = currentUser.visitCount;
        renderTasks();
    }

    function renderTasks() {
        if (!currentUser) return;
        pendingTasksList.innerHTML = '';
        currentUser.pendingTasks.forEach((task, index) => {
            const li = document.createElement('li');
            li.innerHTML = `<span class="task-text">${escapeHtml(task)}</span>
                            <div>
                                <button class="complete" data-index="${index}">✔️</button>
                                <button class="delete-pending" data-index="${index}">🗑️</button>
                            </div>`;
            pendingTasksList.appendChild(li);
        });

        completedTasksList.innerHTML = '';
        currentUser.completedTasks.forEach((task, index) => {
            const li = document.createElement('li');
            li.classList.add('completed-task');
            li.innerHTML = `<span class="task-text">${escapeHtml(task)}</span>
                            <div>
                                <button class="delete-completed" data-index="${index}">🗑️</button>
                            </div>`;
            completedTasksList.appendChild(li);
        });

        // Añadir event listeners a los botones de las tareas
        addEventListenersToTaskButtons();
    }
    
    function escapeHtml(unsafe) {
        return unsafe
             .replace(/&/g, "&amp;")
             .replace(/</g, "&lt;")
             .replace(/>/g, "&gt;")
             .replace(/"/g, "&quot;")
             .replace(/'/g, "&#039;");
    }

    function addEventListenersToTaskButtons() {
        document.querySelectorAll('.complete').forEach(button => {
            button.onclick = (e) => completeTask(parseInt(e.target.dataset.index));
        });
        document.querySelectorAll('.delete-pending').forEach(button => {
            button.onclick = (e) => deletePendingTask(parseInt(e.target.dataset.index));
        });
        document.querySelectorAll('.delete-completed').forEach(button => {
            button.onclick = (e) => deleteCompletedTask(parseInt(e.target.dataset.index));
        });
    }


    function handleAddTask() {
        if (!currentUser) return;
        const taskText = newTaskInput.value.trim();
        if (taskText) {
            currentUser.pendingTasks.push(taskText);
            saveCurrentUserState();
            renderTasks();
            newTaskInput.value = '';
        }
    }

    function completeTask(index) {
        if (!currentUser || index < 0 || index >= currentUser.pendingTasks.length) return;
        const task = currentUser.pendingTasks.splice(index, 1)[0];
        currentUser.completedTasks.push(task);
        saveCurrentUserState();
        renderTasks();
    }

    function deletePendingTask(index) {
        if (!currentUser || index < 0 || index >= currentUser.pendingTasks.length) return;
        currentUser.pendingTasks.splice(index, 1);
        saveCurrentUserState();
        renderTasks();
    }

    function deleteCompletedTask(index) {
        if (!currentUser || index < 0 || index >= currentUser.completedTasks.length) return;
        currentUser.completedTasks.splice(index, 1);
        saveCurrentUserState();
        renderTasks();
    }

    function saveCurrentUserState() {
        if (!currentUser) return;
        // Guardamos el estado sin el 'username' key dentro del objeto, ya que el username es la clave de la cookie
        const { username, ...dataToSave } = currentUser;
        saveUserData(username, dataToSave);
    }

    // --- Navegación de Vistas ---
    function showLoginView() {
        loginView.style.display = 'block';
        registerView.style.display = 'none';
        dashboardContainer.style.display = 'none';
        loginError.textContent = '';
        registerError.textContent = '';
    }

    function showRegisterView() {
        loginView.style.display = 'none';
        registerView.style.display = 'block';
        dashboardContainer.style.display = 'none';
        loginError.textContent = '';
        registerError.textContent = '';
    }

    function showDashboardView() {
        loginView.style.display = 'none';
        registerView.style.display = 'none';
        dashboardContainer.style.display = 'block';
    }

    // --- Inicialización ---
    function init() {
        // Event listeners de autenticación
        loginBtn.addEventListener('click', handleLogin);
        registerBtn.addEventListener('click', handleRegister);
        logoutBtn.addEventListener('click', handleLogout);

        // Event listeners de navegación
        showRegisterLink.addEventListener('click', (e) => { e.preventDefault(); showRegisterView(); });
        showLoginLink.addEventListener('click', (e) => { e.preventDefault(); showLoginView(); });

        // Event listeners de tareas
        addTaskBtn.addEventListener('click', handleAddTask);
        newTaskInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') handleAddTask();
        });
        
        // Comprobar si hay un usuario logueado (sesión persistente)
        const loggedInUsername = getCookie(LOGGED_IN_USER_COOKIE);
        if (loggedInUsername) {
            const userData = getUserData(loggedInUsername);
            if (userData) {
                currentUser = { username: loggedInUsername, ...userData };
                // No es necesario actualizar lastAccess y visitCount aquí si no se considera una nueva "sesión"
                // Si cada carga de página es una "visita", entonces sí:
                // currentUser.visitCount++; 
                // currentUser.lastAccess = new Date().toLocaleString('es-ES');
                // saveCurrentUserState(); // Guardar el estado actualizado

                renderDashboard();
                showDashboardView();
            } else {
                // Cookie de sesión existe pero datos de usuario no, limpiar
                eraseCookie(LOGGED_IN_USER_COOKIE);
                showLoginView();
            }
        } else {
            showLoginView();
        }
    }

    init(); // Iniciar la aplicación
});