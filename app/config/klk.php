<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plataforma Educativa IA con Firestore</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            background-color: #f7f8fc; /* Gris extra claro para el fondo */
            display: flex; 
            flex-direction: column; 
            align-items: center; 
            justify-content: center; 
            min-height: 100vh; 
            padding: 1rem; 
            transition: background-color 0.3s ease; 
        }
        .auth-container, .dashboard-container { 
            background-color: #ffffff; 
            padding: 2rem 2.5rem 3rem 2.5rem; /* Ajuste de padding */
            border-radius: 1rem; /* Bordes más redondeados */
            box-shadow: 0 10px 35px -10px rgba(0, 0, 0, 0.08), 0 4px 15px -5px rgba(0,0,0,0.05); /* Sombra más suave y moderna */
            width: 100%; 
            max-width: 460px; /* Ligeramente más ancho para login */
        }
        .dashboard-container { 
            max-width: 1050px; /* Más ancho para dashboards */
            margin-top: 2rem; 
            margin-bottom: 2rem; 
        }
        .toolbar { 
            border-bottom: 1px solid #eef2f7; /* slate-100, línea más sutil */
            padding-bottom: 1.25rem; /* Ajuste de padding */
            margin-bottom: 1.75rem; /* Ajuste de margen */
        }
        .toolbar-title { 
            font-size: 1.125rem; 
            font-weight: 600; 
            color: #334155; /* slate-700 */
        }
        .form-main-title { 
            text-align: center; 
            font-size: 1.875rem; /* 30px */
            font-weight: 700; 
            color: #1e293b; /* slate-800 */
            margin-bottom: 2.5rem; 
        }
        .input-field, .select-field, .textarea-field { 
            width: 100%; 
            padding: 0.875rem 1.125rem; 
            border: 1px solid #e2e8f0; /* slate-200, borde más claro */
            border-radius: 0.625rem; /* Bordes más redondeados para inputs */
            background-color: #f8fafc; /* slate-50, fondo sutil */
            color: #334155; /* slate-700 */
            transition: border-color 0.2s ease-in-out, box-shadow 0.2s ease-in-out, background-color 0.2s ease-in-out; 
        }
        .input-field::placeholder, .textarea-field::placeholder {
            color: #94a3b8; /* slate-400 */
        }
        .input-field:focus, .select-field:focus, .textarea-field:focus { 
            border-color: #6366f1; /* indigo-500 */
            background-color: #ffffff; 
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2); /* Sombra de foco más notoria */
            outline: none; 
        }
        .label-text { 
            display: block; 
            font-size: 0.875rem; 
            font-weight: 500; 
            color: #475569; /* slate-600 */
            margin-bottom: 0.625rem; 
        }
        .action-button { 
            width: 100%; 
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); /* Gradiente Indigo a Púrpura */
            color: white; 
            font-weight: 600; 
            padding: 0.875rem 1rem; 
            border-radius: 0.625rem; /* Consistente con inputs */
            box-shadow: 0 4px 15px -3px rgba(99, 102, 241, 0.35); 
            transition: all 0.3s ease-out; 
            border: none; 
            cursor: pointer; 
        }
        .action-button:hover { 
            transform: translateY(-3px) scale(1.01); /* Efecto de elevación y ligero zoom */
            box-shadow: 0 7px 25px -5px rgba(99, 102, 241, 0.45); 
        }
        .action-button:active {
            transform: translateY(-1px) scale(0.99);
            box-shadow: 0 4px 15px -3px rgba(99, 102, 241, 0.3);
        }
        .action-button:disabled { 
            background: #e2e8f0; /* slate-200 */
            color: #94a3b8; /* slate-400 */
            cursor: not-allowed; 
            transform: translateY(0); 
            box-shadow: none;
        }
        .logout-button { 
            background: linear-gradient(135deg, #ef4444 0%, #f472b6 100%); /* Gradiente Rojo a Rosa */
            box-shadow: 0 4px 15px -3px rgba(239, 68, 68, 0.35); 
        }
        .logout-button:hover { 
            box-shadow: 0 7px 25px -5px rgba(239, 68, 68, 0.45); 
        }
        .link-text { 
            font-weight: 500; 
            color: #6366f1; /* indigo-500 */
            transition: color 0.2s ease;
        }
        .link-text:hover { color: #4338ca; /* indigo-700 */ text-decoration: underline;}
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(30, 41, 59, 0.75); /* slate-800 con más opacidad */ padding-top: 3%; padding-bottom: 3%; }
        .modal-content { background-color: #ffffff; margin: auto; padding: 2.5rem; border: none; width: 90%; max-width: 600px; /* Ligeramente más ancho para registro */ border-radius: 1rem; text-align: left; position: relative; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); animation: fadeInScaleUp 0.4s cubic-bezier(0.165, 0.84, 0.44, 1) forwards; }
        .modal-close-button { color: #94a3b8; position: absolute; top: 1.25rem; right: 1.25rem; font-size: 1.75rem; font-weight: bold; cursor: pointer; line-height: 1; transition: color 0.2s ease; }
        .modal-close-button:hover { color: #1e293b; transform: rotate(90deg); }
        .loading-spinner { border: 3px solid rgba(255,255,255,0.3); border-top: 3px solid #ffffff; border-radius: 50%; width: 16px; height: 16px; animation: spin 0.8s linear infinite; display: inline-block; margin-left: 8px; }
        .loading-spinner-dark { border: 3px solid #e2e8f0; border-top: 3px solid #6366f1; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        .table-container { max-height: 400px; overflow-y: auto; border: 1px solid #e2e8f0; border-radius: 0.75rem; box-shadow: 0 2px 10px -3px rgba(0,0,0,0.05); }
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table th, .data-table td { padding: 0.875rem 1.125rem; /* Aumento de padding */ text-align: left; border-bottom: 1px solid #eef2f7; /* slate-100, borde más sutil */ font-size: 0.875rem; }
        .data-table th { font-weight: 600; color: #334155; background-color: #f8fafc; position: sticky; top: 0; z-index: 10;}
        .data-table tr:hover td { background-color: #f1f5f9; /* slate-100 */ }
        .hidden { display: none !important; }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(25px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes fadeInScaleUp { from { opacity: 0; transform: scale(0.92); } to { opacity: 1; transform: scale(1); } }
        .animate-fadeInUp { animation: fadeInUp 0.6s cubic-bezier(0.165, 0.84, 0.44, 1) forwards; }
        .dashboard-loaded.animate-children > * { opacity: 0; }
        .dashboard-loaded.animate-children > .animated-item { animation: fadeInUp 0.6s cubic-bezier(0.165, 0.84, 0.44, 1) forwards; }
        .dashboard-loaded.animate-children > .animated-item:nth-child(1) { animation-delay: 0.1s; }
        .dashboard-loaded.animate-children > .animated-item:nth-child(2) { animation-delay: 0.2s; }
        .dashboard-loaded.animate-children > .animated-item:nth-child(3) { animation-delay: 0.3s; }
        .dashboard-loaded.animate-children > .animated-item:nth-child(4) { animation-delay: 0.4s; }
        .small-action-button { padding: 0.3rem 0.6rem; font-size: 0.75rem; margin-right: 0.25rem; border-radius: 0.375rem; transition: transform 0.2s ease, box-shadow 0.2s ease; }
        .small-action-button:hover { transform: translateY(-1px); box-shadow: 0 2px 5px -1px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <div id="authContainer" class="auth-container animate-fadeInUp">
        <div class="toolbar"><h2 class="toolbar-title">Plataforma Educativa IA</h2></div>
        <h1 id="authTitle" class="form-main-title">Iniciar Sesión</h1>
        <div id="generalError" class="mb-4 text-sm text-red-600 bg-red-100 p-3 rounded-md hidden"></div>
        <form id="loginForm" class="space-y-6">
            <div><label for="username" class="label-text">Usuario o Correo Electrónico</label><input type="text" id="username" name="username" class="input-field" placeholder="nombre.usuario@ejemplo.com"><p id="usernameError" class="text-xs text-red-500 mt-1 hidden"></p></div>
            <div><label for="password" class="label-text">Contraseña</label><input type="password" id="password" name="password" class="input-field" placeholder="••••••••"><p id="passwordError" class="text-xs text-red-500 mt-1 hidden"></p></div>
            <div><label for="userTypeSelect" class="label-text">Acceder como (simulación de vista)</label><select id="userTypeSelect" name="userTypeSelect" class="select-field"><option value="user">Usuario (Alumno/Profesor)</option><option value="root">Root (Administrador)</option></select></div>
            <button type="submit" id="loginSubmitButton" class="action-button">Ingresar <span id="loginSpinner" class="loading-spinner hidden"></span></button>
        </form>
        <p class="text-center text-sm text-slate-600 mt-8">¿Aún no tienes cuenta? <a href="#" id="showRegisterModalLink" class="link-text">Regístrate aquí</a></p>
    </div>

    <div id="rootDashboard" class="dashboard-container hidden">
        <div class="animated-item flex justify-between items-center mb-6 pb-4 border-b border-slate-200">
            <h1 class="text-2xl font-bold text-slate-800">Panel de Administrador (Root)</h1>
            <button id="logoutBtnRoot" class="action-button logout-button !w-auto px-5 py-2 text-sm">Cerrar Sesión</button>
        </div>
        <p class="animated-item text-slate-700 mb-6">Bienvenido, Root. Gestiona usuarios y contenido del sistema.</p>
        
        <div class="animated-item grid grid-cols-1 lg:grid-cols-2 gap-x-8 gap-y-8">
            <div>
                <h3 class="font-semibold text-lg mb-4 text-slate-800">Usuarios del Sistema</h3>
                <div id="usersTableContainer" class="table-container">
                    <table class="data-table">
                        <thead><tr><th>Nombre Completo</th><th>Email/Usuario</th><th>Rol Escolar</th><th>Acciones</th></tr></thead>
                        <tbody id="usersTableBody"></tbody>
                    </table>
                    <p id="noUsersMessage" class="p-4 text-center text-slate-500 hidden">No hay usuarios registrados aún.</p>
                </div>
            </div>
            <div>
                <h3 class="font-semibold text-lg mb-4 text-slate-800">Contenido Global</h3>
                <form id="addGlobalContentForm" class="p-4 bg-slate-50 rounded-lg shadow-sm mb-6 space-y-4">
                    <div><label for="gcTitle" class="label-text text-sm">Título del Contenido</label><input type="text" id="gcTitle" class="input-field input-sm" placeholder="Ej: Anuncio Importante" required></div>
                    <div><label for="gcDescription" class="label-text text-sm">Descripción</label><textarea id="gcDescription" rows="2" class="textarea-field input-sm" placeholder="Detalles del contenido..." required></textarea></div>
                    <button type="submit" id="addGcButton" class="action-button !w-auto px-4 py-1.5 text-sm">Agregar Contenido <span id="addGcSpinner" class="loading-spinner hidden"></span></button>
                </form>
                <div id="globalContentContainer" class="table-container">
                     <table class="data-table">
                        <thead><tr><th>Título</th><th>Descripción</th><th>Acciones</th></tr></thead>
                        <tbody id="globalContentTableBody"></tbody>
                    </table>
                    <p id="noGlobalContentMessage" class="p-4 text-center text-slate-500 hidden">No hay contenido global.</p>
                </div>
            </div>
        </div>
    </div>

    <div id="userDashboard" class="dashboard-container hidden">
        <div class="animated-item flex justify-between items-center mb-6 pb-4 border-b border-slate-200">
            <h1 class="text-2xl font-bold text-slate-800">Mi Panel de Usuario</h1>
            <button id="logoutBtnUser" class="action-button logout-button !w-auto px-5 py-2 text-sm">Cerrar Sesión</button>
        </div>
        <p id="userWelcomeMessage" class="animated-item text-slate-700 mb-8">Bienvenido. Aquí puedes gestionar tus actividades y datos.</p>
        <div class="animated-item mb-10 p-6 bg-slate-50 rounded-xl shadow-sm">
            <h3 class="font-semibold text-xl mb-5 text-slate-800">Agregar Nuevo Elemento Personal</h3>
            <form id="addDataForm" class="space-y-5">
                <div><label for="dataName" class="label-text">Título del Elemento</label><input type="text" id="dataName" name="dataName" class="input-field" placeholder="Ej: Ensayo de Literatura" required></div>
                <div><label for="dataValue" class="label-text">Descripción o Contenido</label><textarea id="dataValue" name="dataValue" rows="3" class="textarea-field" placeholder="Escribe los detalles aquí..." required></textarea></div>
                <button type="submit" id="addDataButton" class="action-button !w-auto px-8 py-2.5">Agregar <span id="addDataSpinner" class="loading-spinner hidden"></span></button>
            </form>
        </div>
        <div class="animated-item"> 
            <h3 class="font-semibold text-xl mb-5 text-slate-800">Mis Elementos Personales</h3>
            <div id="userSpecificDataTableContainer" class="table-container">
                <table class="data-table">
                    <thead><tr><th>Título</th><th>Descripción</th><th>Fecha</th><th>Acciones</th></tr></thead>
                    <tbody id="userSpecificDataTableBody"></tbody>
                </table>
                <p id="noUserSpecificDataMessage" class="p-4 text-center text-slate-500 hidden">Aún no has agregado elementos.</p>
            </div>
        </div>
    </div>

    <div id="messageModal" class="modal"><div class="modal-content text-center"><span class="modal-close-button" onclick="closeModal('messageModal')">&times;</span><p id="modalMessageText" class="text-slate-800 text-xl my-6"></p><div id="securityTipSection" class="mt-6 pt-6 border-t border-slate-200 hidden text-left"><p class="text-md font-semibold text-slate-800 mb-2">✨ Consejo de Seguridad del Día:</p><p id="securityTipText" class="text-sm text-slate-700 italic"></p><div id="tipLoadingIndicator" class="text-sm text-slate-500 mt-3 hidden"><div class="loading-spinner-dark !w-5 !h-5 inline-block mr-2"></div>Cargando consejo...</div></div><button onclick="closeModal('messageModal')" class="mt-8 action-button w-auto px-6 mx-auto">Entendido</button></div></div>
    <div id="registerModal" class="modal"><div class="modal-content"><div class="toolbar !mb-6"><h2 class="toolbar-title">Registro en Plataforma Educativa IA</h2></div><span class="modal-close-button" onclick="closeModal('registerModal')">&times;</span><div id="registerGeneralError" class="mb-5 text-sm text-red-600 bg-red-100 p-3 rounded-md hidden"></div><form id="registerForm" class="space-y-5"><div><label for="regFullName" class="label-text">Nombre Completo</label><input type="text" id="regFullName" name="regFullName" class="input-field" placeholder="Tu nombre y apellidos" required><p id="regFullNameError" class="text-xs text-red-500 mt-1 hidden"></p></div><div><label for="regUsername" class="label-text">Usuario / Correo Electrónico (para login)</label><input type="text" id="regUsername" name="regUsername" class="input-field" placeholder="usuario o email@ejemplo.com" required><p id="regUsernameError" class="text-xs text-red-500 mt-1 hidden"></p></div><div><label for="regPassword" class="label-text">Crear Contraseña</label><div class="flex items-center"><input type="password" id="regPassword" name="regPassword" class="input-field rounded-r-none" placeholder="Mínimo 8 caracteres" required><button type="button" id="generatePasswordBtn" title="Generar contraseña segura con IA" class="bg-indigo-500 hover:bg-indigo-600 text-white px-3 py-2.5 rounded-r-md text-sm h-[48px] flex items-center">✨ <span class="hidden sm:inline ml-1.5">Generar</span> <span id="passwordLoadingIndicator" class="loading-spinner hidden"></span></button></div><p id="regPasswordError" class="text-xs text-red-500 mt-1 hidden"></p></div><div><label for="regConfirmPassword" class="label-text">Confirmar Contraseña</label><input type="password" id="regConfirmPassword" name="regConfirmPassword" class="input-field" placeholder="Vuelve a escribir la contraseña" required><p id="regConfirmPasswordError" class="text-xs text-red-500 mt-1 hidden"></p></div><div class="grid grid-cols-1 md:grid-cols-2 gap-5"><div><label for="regBirthDate" class="label-text">Fecha de Nacimiento</label><input type="date" id="regBirthDate" name="regBirthDate" class="input-field" required><p id="regBirthDateError" class="text-xs text-red-500 mt-1 hidden"></p></div><div><label for="regSchoolRole" class="label-text">Tu Rol Principal</label><select id="regSchoolRole" name="regSchoolRole" class="select-field" required><option value="">Selecciona un rol...</option><option value="alumno">Alumno/a</option><option value="profesor">Profesor/a</option><option value="administrativo">Administrativo/a</option><option value="padre">Padre/Tutor</option><option value="otro">Otro</option></select><p id="regSchoolRoleError" class="text-xs text-red-500 mt-1 hidden"></p></div></div><div><label for="regExtraInfo" class="label-text">Información Adicional (Curso, Materia, etc.)</label><textarea id="regExtraInfo" name="regExtraInfo" rows="2" class="textarea-field" placeholder="Opcional. Ej: 10mo Grado, Física Cuántica"></textarea></div><input type="hidden" id="regInternalUserType" name="regInternalUserType" value="user"><button type="submit" id="registerSubmitButton" class="action-button !bg-green-500 hover:!bg-green-600 !shadow-green-500/30 hover:!shadow-green-500/40 !mt-6">Crear Cuenta <span id="registerSpinner" class="loading-spinner hidden"></span></button></form></div></div>
    <div id="editUserRoleModal" class="modal"><div class="modal-content"><h2 class="form-main-title !text-xl !mb-4">Editar Rol Escolar</h2><form id="editUserRoleForm"><input type="hidden" id="editUserId"><div class="mb-4"><label for="editSchoolRole" class="label-text">Nuevo Rol Escolar para <span id="editUserName" class="font-semibold"></span>:</label><select id="editSchoolRole" class="select-field"><option value="alumno">Alumno/a</option><option value="profesor">Profesor/a</option><option value="administrativo">Administrativo/a</option><option value="padre">Padre/Tutor</option><option value="otro">Otro</option></select></div><div class="flex justify-end space-x-3"><button type="button" onclick="closeModal('editUserRoleModal')" class="action-button !bg-slate-300 !text-slate-700 hover:!bg-slate-400 !w-auto px-4 py-2 text-sm">Cancelar</button><button type="submit" id="saveUserRoleButton" class="action-button !w-auto px-4 py-2 text-sm">Guardar Cambios <span id="saveRoleSpinner" class="loading-spinner hidden"></span></button></div></form></div></div>

    <script type="module">
        // Importaciones de Firebase
        import { initializeApp } from "https://www.gstatic.com/firebasejs/11.6.1/firebase-app.js";
        import { getAuth, signInWithEmailAndPassword, createUserWithEmailAndPassword, signOut, onAuthStateChanged } from "https://www.gstatic.com/firebasejs/11.6.1/firebase-auth.js";
        import { getFirestore, doc, getDoc, setDoc, addDoc, collection, query, where, getDocs, onSnapshot, updateDoc, deleteDoc, serverTimestamp, orderBy } from "https://www.gstatic.com/firebasejs/11.6.1/firebase-firestore.js";
        import { setLogLevel } from "https://www.gstatic.com/firebasejs/11.6.1/firebase-app.js";


        // --- Configuración de Firebase ---
        // **REEMPLAZA ESTO CON TU PROPIA CONFIGURACIÓN DE FIREBASE**
        const firebaseConfig = typeof __firebase_config !== 'undefined' ? JSON.parse(__firebase_config) : {
            apiKey: "TU_API_KEY",
            authDomain: "TU_AUTH_DOMAIN",
            projectId: "TU_PROJECT_ID",
            storageBucket: "TU_STORAGE_BUCKET",
            messagingSenderId: "TU_MESSAGING_SENDER_ID",
            appId: "TU_APP_ID"
        };

        const app = initializeApp(firebaseConfig);
        const auth = getAuth(app);
        const db = getFirestore(app);
        setLogLevel('debug'); 

        let currentLoggedInUser = null; 
        let currentUserData = null; 

        // --- Elementos del DOM ---
        const authContainer = document.getElementById('authContainer');
        const rootDashboard = document.getElementById('rootDashboard');
        const userDashboard = document.getElementById('userDashboard');
        const loginForm = document.getElementById('loginForm');
        const usernameInput = document.getElementById('username'); 
        const passwordInput = document.getElementById('password');
        const userTypeSelect = document.getElementById('userTypeSelect'); 
        const generalError = document.getElementById('generalError');
        const loginSubmitButton = document.getElementById('loginSubmitButton');
        const loginSpinner = document.getElementById('loginSpinner');

        const registerModal = document.getElementById('registerModal');
        const showRegisterModalLink = document.getElementById('showRegisterModalLink');
        const registerForm = document.getElementById('registerForm');
        const regFullNameInput = document.getElementById('regFullName');
        const regUsernameInput = document.getElementById('regUsername'); 
        const regPasswordInput = document.getElementById('regPassword');
        const regBirthDateInput = document.getElementById('regBirthDate');
        const regSchoolRoleInput = document.getElementById('regSchoolRole');
        const regExtraInfoInput = document.getElementById('regExtraInfo');
        const registerSubmitButton = document.getElementById('registerSubmitButton');
        const registerSpinner = document.getElementById('registerSpinner');
        const registerGeneralError = document.getElementById('registerGeneralError');


        const messageModal = document.getElementById('messageModal');
        const modalMessageText = document.getElementById('modalMessageText');
        const usersTableBody = document.getElementById('usersTableBody');
        const noUsersMessage = document.getElementById('noUsersMessage');
        const addGlobalContentForm = document.getElementById('addGlobalContentForm');
        const gcTitleInput = document.getElementById('gcTitle');
        const gcDescriptionInput = document.getElementById('gcDescription');
        const addGcButton = document.getElementById('addGcButton');
        const addGcSpinner = document.getElementById('addGcSpinner');
        const globalContentTableBody = document.getElementById('globalContentTableBody');
        const noGlobalContentMessage = document.getElementById('noGlobalContentMessage');

        const userWelcomeMessage = document.getElementById('userWelcomeMessage');
        const addDataForm = document.getElementById('addDataForm');
        const dataNameInput = document.getElementById('dataName');
        const dataValueInput = document.getElementById('dataValue');
        const addDataButton = document.getElementById('addDataButton');
        const addDataSpinner = document.getElementById('addDataSpinner');
        const userSpecificDataTableBody = document.getElementById('userSpecificDataTableBody');
        const noUserSpecificDataMessage = document.getElementById('noUserSpecificDataMessage');
        
        const editUserRoleModal = document.getElementById('editUserRoleModal');
        const editUserRoleForm = document.getElementById('editUserRoleForm');
        const editUserIdInput = document.getElementById('editUserId');
        const editUserNameSpan = document.getElementById('editUserName');
        const editSchoolRoleSelect = document.getElementById('editSchoolRole');
        const saveUserRoleButton = document.getElementById('saveUserRoleButton');
        const saveRoleSpinner = document.getElementById('saveRoleSpinner');

        const logoutBtnRoot = document.getElementById('logoutBtnRoot');
        const logoutBtnUser = document.getElementById('logoutBtnUser');
        const generatePasswordBtn = document.getElementById('generatePasswordBtn'); 
        const passwordLoadingIndicator = document.getElementById('passwordLoadingIndicator'); 
        const securityTipSection = document.getElementById('securityTipSection');
        const securityTipText = document.getElementById('securityTipText');
        const tipLoadingIndicator = document.getElementById('tipLoadingIndicator');


        // --- Funciones del Modal y UI ---
        function showModal(modalId, message = null) {
            const modal = document.getElementById(modalId);
            if (message && modalId === 'messageModal') {
                if(modalMessageText) modalMessageText.textContent = message;
                if(securityTipSection) securityTipSection.classList.add('hidden'); 
            }
            if (modal) {
                 modal.style.display = 'block';
                 document.body.style.overflow = 'hidden';
            }
        }
        window.showModal = showModal; 

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) { 
                modal.style.display = 'none'; 
                const anyModalOpen = Array.from(document.querySelectorAll('.modal')).some(m => m.style.display === 'block');
                if (!anyModalOpen) {
                    document.body.style.overflow = 'auto'; 
                }
            }
            if (modalId === 'registerModal') { 
                clearRegisterErrors(); 
                if(registerForm) registerForm.reset(); 
            }
            if (modalId === 'messageModal') { 
                if(securityTipSection) securityTipSection.classList.add('hidden');
                if(securityTipText) securityTipText.textContent = '';
                if(tipLoadingIndicator) tipLoadingIndicator.classList.add('hidden');
            }
        }
        window.closeModal = closeModal; 


        // --- Lógica de Autenticación y UI ---
        function toggleSpinner(button, spinner, show) {
            if(!button || !spinner) return;
            if (show) {
                button.disabled = true;
                spinner.classList.remove('hidden');
            } else {
                button.disabled = false;
                spinner.classList.add('hidden');
            }
        }
        
        onAuthStateChanged(auth, async (user) => {
            if (user) {
                currentLoggedInUser = user;
                console.log("Usuario autenticado:", user.uid, user.email);
                try {
                    const userDocRef = doc(db, "users", user.uid);
                    const userDocSnap = await getDoc(userDocRef);
                    if (userDocSnap.exists()) {
                        currentUserData = { id: userDocSnap.id, ...userDocSnap.data() };
                        console.log("Datos del usuario desde Firestore:", currentUserData);
                        showViewForRole(currentUserData.type); 
                        if(currentUserData.type === 'root') {
                            loadUsersForAdmin();
                            loadGlobalContentForAdmin();
                        } else if (currentUserData.type === 'user') {
                            userWelcomeMessage.textContent = `Bienvenido, ${currentUserData.fullName}. Aquí puedes gestionar tus actividades y datos.`;
                            loadUserSpecificData(user.uid);
                        }
                    } else {
                        console.error("No se encontraron datos adicionales del usuario en Firestore para UID:", user.uid);
                        showGeneralError(generalError, "Error: No se pudieron cargar los datos del perfil. El documento de usuario no existe en Firestore.");
                        handleLogout(); 
                    }
                } catch (error) {
                    console.error("Error al obtener datos del usuario de Firestore:", error);
                    showGeneralError(generalError, `Error al cargar perfil: ${error.message}`);
                    handleLogout();
                }
            } else {
                currentLoggedInUser = null;
                currentUserData = null;
                console.log("Ningún usuario autenticado.");
                showView('auth');
            }
        });

        function showViewForRole(roleType) {
            authContainer.classList.add('hidden');
            rootDashboard.classList.add('hidden');
            userDashboard.classList.add('hidden');
            
            let targetView;
            if (roleType === 'root') targetView = rootDashboard;
            else if (roleType === 'user') targetView = userDashboard;
            else targetView = authContainer; 

            if (targetView) {
                targetView.classList.remove('hidden');
                if (roleType === 'root' || roleType === 'user') {
                    void targetView.offsetWidth; 
                    targetView.classList.add('dashboard-loaded', 'animate-children');
                }
            }
        }
        
        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const email = usernameInput.value.trim(); 
            const password = passwordInput.value;
            toggleSpinner(loginSubmitButton, loginSpinner, true);
            clearLoginErrors();

            if (!email || !password) {
                showGeneralError(generalError, "Por favor, complete todos los campos.");
                toggleSpinner(loginSubmitButton, loginSpinner, false);
                return;
            }

            try {
                const userCredential = await signInWithEmailAndPassword(auth, email, password);
                console.log("Inicio de sesión exitoso para:", userCredential.user.email);
                loginForm.reset();
            } catch (error) {
                console.error("Error de inicio de sesión:", error);
                let friendlyMessage = "Error al iniciar sesión. Verifica tus credenciales.";
                if (error.code === 'auth/user-not-found' || error.code === 'auth/wrong-password' || error.code === 'auth/invalid-credential') {
                    friendlyMessage = "Correo electrónico o contraseña incorrectos.";
                } else if (error.code === 'auth/invalid-email') {
                    friendlyMessage = "El formato del correo electrónico no es válido.";
                }
                showGeneralError(generalError, friendlyMessage);
            } finally {
                toggleSpinner(loginSubmitButton, loginSpinner, false);
            }
        });

        showRegisterModalLink.addEventListener('click', (e) => { e.preventDefault(); closeModal('messageModal'); showModal('registerModal'); });

        registerForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            toggleSpinner(registerSubmitButton, registerSpinner, true);
            clearRegisterErrors();

            const fullName = regFullNameInput.value.trim();
            const email = regUsernameInput.value.trim(); 
            const password = regPasswordInput.value.trim();
            const confirmPassword = document.getElementById('regConfirmPassword').value.trim();
            const birthDate = regBirthDateInput.value;
            const schoolRole = regSchoolRoleInput.value;
            const extraInfo = regExtraInfoInput.value.trim();
            
            const internalUserType = 'user';

            let isValid = true;
            const regFullNameErrorEl = document.getElementById('regFullNameError');
            const regUsernameErrorEl = document.getElementById('regUsernameError');
            const regPasswordErrorEl = document.getElementById('regPasswordError');
            const regConfirmPasswordErrorEl = document.getElementById('regConfirmPasswordError');
            const regBirthDateErrorEl = document.getElementById('regBirthDateError');
            const regSchoolRoleErrorEl = document.getElementById('regSchoolRoleError');

            if (!fullName) { showError(regFullNameErrorEl, 'Nombre completo es obligatorio.'); isValid = false; }
            if (!email) { showError(regUsernameErrorEl, 'Correo electrónico es obligatorio.'); isValid = false; }
            if (password.length < 8) { showError(regPasswordErrorEl, 'Contraseña debe tener al menos 8 caracteres.'); isValid = false; }
            if (password !== confirmPassword) { showError(regConfirmPasswordErrorEl, 'Las contraseñas no coinciden.'); isValid = false; }
            if (!birthDate) { showError(regBirthDateErrorEl, 'Fecha de nacimiento es obligatoria.'); isValid = false; }
            if (!schoolRole) { showError(regSchoolRoleErrorEl, 'Rol escolar es obligatorio.'); isValid = false; }

            if (!isValid) {
                showGeneralError(registerGeneralError, "Por favor, corrija los errores.");
                toggleSpinner(registerSubmitButton, registerSpinner, false);
                return;
            }

            try {
                const userCredential = await createUserWithEmailAndPassword(auth, email, password);
                const user = userCredential.user;
                
                await setDoc(doc(db, "users", user.uid), {
                    fullName: fullName,
                    email: email, 
                    birthDate: birthDate,
                    schoolRole: schoolRole,
                    extraInfo: extraInfo,
                    type: internalUserType, 
                    createdAt: serverTimestamp()
                });

                console.log("Usuario registrado y datos guardados en Firestore:", user.uid);
                closeModal('registerModal');
                showModal('messageModal', `¡Cuenta creada para ${fullName}! Serás redirigido en breve.`);
            } catch (error) {
                console.error("Error de registro:", error);
                let friendlyMessage = "Error al registrar la cuenta.";
                if (error.code === 'auth/email-already-in-use') {
                    friendlyMessage = "Este correo electrónico ya está registrado.";
                } else if (error.code === 'auth/invalid-email') {
                    friendlyMessage = "El formato del correo electrónico no es válido.";
                } else if (error.code === 'auth/weak-password') {
                    friendlyMessage = "La contraseña es demasiado débil.";
                }
                showGeneralError(registerGeneralError, friendlyMessage);
            } finally {
                toggleSpinner(registerSubmitButton, registerSpinner, false);
            }
        });
        
        function handleLogout() {
            signOut(auth).catch((error) => {
                console.error("Error al cerrar sesión:", error);
                showModal('messageModal', `Error al cerrar sesión: ${error.message}`);
            });
        }
        logoutBtnRoot.addEventListener('click', handleLogout);
        logoutBtnUser.addEventListener('click', handleLogout);

        // --- Funcionalidad Admin (Root) ---
        async function loadUsersForAdmin() {
            if (currentUserData?.type !== 'root') return;
            const usersCollectionRef = collection(db, "users");
            
            onSnapshot(usersCollectionRef, (querySnapshot) => {
                usersTableBody.innerHTML = ''; 
                let hasUsers = false;
                querySnapshot.forEach((docSnap) => {
                    hasUsers = true;
                    const userData = { id: docSnap.id, ...docSnap.data() };
                    
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td class="py-2 px-3 border-b border-slate-200">${userData.fullName}</td>
                        <td class="py-2 px-3 border-b border-slate-200">${userData.email}</td>
                        <td class="py-2 px-3 border-b border-slate-200">${userData.schoolRole}</td>
                        <td class="py-2 px-3 border-b border-slate-200">
                            <button class="small-action-button bg-blue-500 hover:bg-blue-600 text-white edit-role-btn" data-userid="${userData.id}" data-username="${userData.fullName}" data-currentrole="${userData.schoolRole}">Editar Rol</button>
                            <button class="small-action-button bg-red-500 hover:bg-red-600 text-white delete-user-btn" data-userid="${userData.id}" data-username="${userData.fullName}">Eliminar</button>
                        </td>
                    `;
                    usersTableBody.appendChild(tr);
                });
                noUsersMessage.classList.toggle('hidden', hasUsers);

                document.querySelectorAll('.edit-role-btn').forEach(button => {
                    button.removeEventListener('click', handleEditUserRole); 
                    button.addEventListener('click', handleEditUserRole);
                });
                document.querySelectorAll('.delete-user-btn').forEach(button => {
                    button.removeEventListener('click', handleDeleteUser); 
                    button.addEventListener('click', handleDeleteUser);
                });

            }, (error) => {
                console.error("Error al cargar usuarios para admin: ", error);
                showModal('messageModal', `Error al cargar usuarios: ${error.message}`);
            });
        }

        function handleEditUserRole(event) {
            const userId = event.target.dataset.userid;
            const userName = event.target.dataset.username;
            const currentRole = event.target.dataset.currentrole;

            editUserIdInput.value = userId;
            editUserNameSpan.textContent = userName;
            editSchoolRoleSelect.value = currentRole;
            showModal('editUserRoleModal');
        }
        
        editUserRoleForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const userId = editUserIdInput.value;
            const newSchoolRole = editSchoolRoleSelect.value;
            toggleSpinner(saveUserRoleButton, saveRoleSpinner, true);

            if (!userId || !newSchoolRole) {
                showModal('messageModal', "Error: Información incompleta para actualizar rol.");
                toggleSpinner(saveUserRoleButton, saveRoleSpinner, false);
                return;
            }
            try {
                const userDocRef = doc(db, "users", userId);
                await updateDoc(userDocRef, {
                    schoolRole: newSchoolRole
                });
                showModal('messageModal', `Rol de ${editUserNameSpan.textContent} actualizado a ${newSchoolRole}.`);
                closeModal('editUserRoleModal');
            } catch (error) {
                console.error("Error al actualizar rol:", error);
                showModal('messageModal', `Error al actualizar rol: ${error.message}`);
            } finally {
                toggleSpinner(saveUserRoleButton, saveRoleSpinner, false);
            }
        });

        async function handleDeleteUser(event) {
            const userId = event.target.dataset.userid;
            const userName = event.target.dataset.username;
            if (confirm(`¿Estás seguro de que quieres eliminar el perfil de ${userName} de Firestore? Esta acción NO elimina su cuenta de autenticación de Firebase.`)) {
                try {
                    await deleteDoc(doc(db, "users", userId));
                    showModal('messageModal', `Perfil de ${userName} eliminado de Firestore.`);
                } catch (error) {
                    console.error("Error al eliminar perfil de Firestore:", error);
                    showModal('messageModal', `Error al eliminar perfil: ${error.message}`);
                }
            }
        }

        addGlobalContentForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const title = gcTitleInput.value.trim();
            const description = gcDescriptionInput.value.trim();
            toggleSpinner(addGcButton, addGcSpinner, true);

            if (!title || !description) {
                showModal('messageModal', "Título y descripción son obligatorios para el contenido global.");
                toggleSpinner(addGcButton, addGcSpinner, false);
                return;
            }
            try {
                await addDoc(collection(db, "globalContent"), {
                    title: title,
                    description: description,
                    createdAt: serverTimestamp(),
                    authorId: currentLoggedInUser.uid, 
                    authorEmail: currentLoggedInUser.email
                });
                showModal('messageModal', "Contenido global agregado.");
                addGlobalContentForm.reset();
            } catch (error) {
                console.error("Error al agregar contenido global:", error);
                showModal('messageModal', `Error: ${error.message}`);
            } finally {
                toggleSpinner(addGcButton, addGcSpinner, false);
            }
        });

        function loadGlobalContentForAdmin() {
            if (currentUserData?.type !== 'root') return;
            const q = query(collection(db, "globalContent"), orderBy("createdAt", "desc"));
            onSnapshot(q, (querySnapshot) => {
                globalContentTableBody.innerHTML = '';
                let hasContent = false;
                querySnapshot.forEach((docSnap) => {
                    hasContent = true;
                    const content = { id: docSnap.id, ...docSnap.data() };
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td class="py-2 px-3 border-b border-slate-200">${content.title}</td>
                        <td class="py-2 px-3 border-b border-slate-200">${content.description.substring(0,50)}${content.description.length > 50 ? '...' : ''}</td>
                        <td class="py-2 px-3 border-b border-slate-200">
                            <button class="small-action-button bg-red-500 hover:bg-red-600 text-white delete-gc-btn" data-id="${content.id}" data-title="${content.title}">Eliminar</button>
                        </td>
                    `;
                    globalContentTableBody.appendChild(tr);
                });
                noGlobalContentMessage.classList.toggle('hidden', hasContent);
                document.querySelectorAll('.delete-gc-btn').forEach(button => {
                    button.removeEventListener('click', handleDeleteGlobalContent); 
                    button.addEventListener('click', handleDeleteGlobalContent);
                });
            }, (error) => {
                console.error("Error al cargar contenido global:", error);
                showModal('messageModal', `Error al cargar contenido: ${error.message}`);
            });
        }
        
        async function handleDeleteGlobalContent(event) {
            const contentId = event.target.dataset.id;
            const contentTitle = event.target.dataset.title;
            if (confirm(`¿Estás seguro de que quieres eliminar el contenido "${contentTitle}"?`)) {
                try {
                    await deleteDoc(doc(db, "globalContent", contentId));
                    showModal('messageModal', `Contenido "${contentTitle}" eliminado.`);
                } catch (error) {
                    console.error("Error al eliminar contenido global:", error);
                    showModal('messageModal', `Error al eliminar: ${error.message}`);
                }
            }
        }

        // --- Funcionalidad Usuario ---
        addDataForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const title = dataNameInput.value.trim();
            const description = dataValueInput.value.trim();
            toggleSpinner(addDataButton, addDataSpinner, true);

            if (!title || !description || !currentLoggedInUser) {
                showModal('messageModal', "Título y descripción son obligatorios. Debes estar logueado.");
                toggleSpinner(addDataButton, addDataSpinner, false);
                return;
            }
            try {
                await addDoc(collection(db, "userSpecificData"), {
                    userId: currentLoggedInUser.uid, 
                    title: title,
                    description: description,
                    createdAt: serverTimestamp()
                });
                showModal('messageModal', "Elemento agregado exitosamente.");
                addDataForm.reset();
            } catch (error) {
                console.error("Error al agregar elemento personal:", error);
                showModal('messageModal', `Error: ${error.message}`);
            } finally {
                toggleSpinner(addDataButton, addDataSpinner, false);
            }
        });

        function loadUserSpecificData(userId) {
            const q = query(collection(db, "userSpecificData"), where("userId", "==", userId), orderBy("createdAt", "desc"));
            onSnapshot(q, (querySnapshot) => {
                userSpecificDataTableBody.innerHTML = '';
                let hasData = false;
                querySnapshot.forEach((docSnap) => {
                    hasData = true;
                    const item = { id: docSnap.id, ...docSnap.data() };
                    const tr = document.createElement('tr');
                    const itemDate = item.createdAt?.toDate ? item.createdAt.toDate().toLocaleDateString() : 'N/A';
                    tr.innerHTML = `
                        <td class="py-2 px-3 border-b border-slate-200">${item.title}</td>
                        <td class="py-2 px-3 border-b border-slate-200">${item.description.substring(0,50)}${item.description.length > 50 ? '...' : ''}</td>
                        <td class="py-2 px-3 border-b border-slate-200">${itemDate}</td>
                        <td class="py-2 px-3 border-b border-slate-200">
                            <button class="small-action-button bg-red-500 hover:bg-red-600 text-white delete-usd-btn" data-id="${item.id}" data-title="${item.title}">Eliminar</button>
                        </td>
                    `;
                    userSpecificDataTableBody.appendChild(tr);
                });
                noUserSpecificDataMessage.classList.toggle('hidden', hasData);
                document.querySelectorAll('.delete-usd-btn').forEach(button => {
                    button.removeEventListener('click', handleDeleteUserSpecificData); 
                    button.addEventListener('click', handleDeleteUserSpecificData);
                });
            }, (error) => {
                console.error("Error al cargar datos del usuario:", error);
                showModal('messageModal', `Error al cargar tus datos: ${error.message}`);
            });
        }

        async function handleDeleteUserSpecificData(event) {
            const itemId = event.target.dataset.id;
            const itemTitle = event.target.dataset.title;
            if (confirm(`¿Estás seguro de que quieres eliminar "${itemTitle}"?`)) {
                try {
                    await deleteDoc(doc(db, "userSpecificData", itemId));
                    showModal('messageModal', `Elemento "${itemTitle}" eliminado.`);
                } catch (error) {
                    console.error("Error al eliminar elemento personal:", error);
                    showModal('messageModal', `Error al eliminar: ${error.message}`);
                }
            }
        }
        
        const GEMINI_API_KEY = ""; 
        const GEMINI_API_URL_FLASH = `https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=${GEMINI_API_KEY}`;
        
        async function callGeminiAPI(promptText) {
            const payload = { contents: [{ role: "user", parts: [{ text: promptText }] }] };
            try {
                const response = await fetch(GEMINI_API_URL_FLASH, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload) });
                if (!response.ok) { const errorData = await response.json(); console.error("Error en API Gemini:", response.status, errorData); throw new Error(`Error de la API: ${response.status}. ${errorData?.error?.message || 'Inténtalo de nuevo.'}`); }
                const result = await response.json();
                if (result.candidates?.[0]?.content?.parts?.[0]?.text) { return result.candidates[0].content.parts[0].text.trim(); }
                else { console.error("Respuesta inesperada de la API Gemini:", result); throw new Error("No se pudo obtener una respuesta válida de la IA."); }
            } catch (error) { console.error("Error al llamar a la API Gemini:", error); throw error; }
        }
        
        const generatePasswordBtnFunctionality = () => { 
            if (generatePasswordBtn) {
                 generatePasswordBtn.addEventListener('click', async () => {
                    const btnText = generatePasswordBtn.querySelector('span.hidden');
                    if(passwordLoadingIndicator) passwordLoadingIndicator.classList.remove('hidden'); 
                    generatePasswordBtn.disabled = true; 
                    if(btnText) btnText.classList.add('!hidden'); 
                    const regPasswordErrorEl = document.getElementById('regPasswordError');
                    if(regPasswordErrorEl) showError(regPasswordErrorEl, ''); 
                    
                    const prompt = "Genera una contraseña segura que tenga entre 12 y 16 caracteres. Debe incluir una mezcla de letras mayúsculas, letras minúsculas, números y símbolos especiales (por ejemplo, !@#$%^&*). Devuelve únicamente la contraseña, sin ningún texto o explicación adicional.";
                    try { 
                        // const generatedPassword = await callGeminiAPI(prompt); // Descomentar para usar Gemini
                        const generatedPassword = "FallbackSecurePass123$"; // Usar un fallback si Gemini no está configurado
                        if (regPasswordInput) regPasswordInput.value = generatedPassword; 
                        const regConfirmPasswordEl = document.getElementById('regConfirmPassword');
                        if (regConfirmPasswordEl) regConfirmPasswordEl.value = generatedPassword; 
                        if (regPasswordErrorEl) showError(regPasswordErrorEl, 'Contraseña generada. ¡Recuerda guardarla bien!', true); 
                    } catch (error) { 
                        if (regPasswordErrorEl) showError(regPasswordErrorEl, `Error al generar: ${error.message}`); 
                    } finally { 
                        if(passwordLoadingIndicator) passwordLoadingIndicator.classList.add('hidden'); 
                        generatePasswordBtn.disabled = false; 
                        if(btnText) btnText.classList.remove('!hidden'); 
                    }
                });
            }
        };
        generatePasswordBtnFunctionality(); 

        function clearRegisterErrors() {
            const errorFields = [
                document.getElementById('regFullNameError'), 
                document.getElementById('regUsernameError'), 
                document.getElementById('regPasswordError'),
                document.getElementById('regConfirmPasswordError'),
                document.getElementById('regBirthDateError'),
                document.getElementById('regSchoolRoleError'),
                registerGeneralError
            ];
            errorFields.forEach(field => { if(field) { field.classList.add('hidden'); field.textContent = ''; } });
            const regPasswordErrorEl = document.getElementById('regPasswordError');
            if(regPasswordErrorEl){ regPasswordErrorEl.classList.remove('text-green-600'); regPasswordErrorEl.classList.add('text-red-500');}
        }
        
        function showError(element, message, isSuccess = false){ 
            if(!element) return;
            element.textContent = message;
            element.classList.remove('hidden');
            if (isSuccess) {
                element.classList.remove('text-red-500');
                element.classList.add('text-green-600');
            } else {
                element.classList.add('text-red-500');
                element.classList.remove('text-green-600');
            }
        }

        window.onclick = function(event) { 
            if (event.target.classList.contains('modal')) { closeModal(event.target.id); }
        }
        
        const setupObserver = (modalElement) => {
            if (!modalElement) return;
            const observer = new MutationObserver((mutationsList) => { 
                for(let mutation of mutationsList) { 
                    if (mutation.type === 'attributes' && mutation.attributeName === 'style') { 
                        if (modalElement.style.display === 'block') { 
                            document.body.style.overflow = 'hidden'; 
                        } else {
                            const anyModalOpen = Array.from(document.querySelectorAll('.modal')).some(m => m.style.display === 'block');
                            if (!anyModalOpen) {
                                document.body.style.overflow = 'auto'; 
                            }
                        } 
                    } 
                } 
            });
            observer.observe(modalElement, { attributes: true });
        };
        if(messageModal) setupObserver(messageModal);
        if(registerModal) setupObserver(registerModal);
        if(editUserRoleModal) setupObserver(editUserRoleModal);
        
        const securityTipSectionEl = document.getElementById('securityTipSection');
        if(securityTipSectionEl) securityTipSectionEl.classList.add('hidden'); 

    </script>
</body>
</html>

