<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestión Universitaria</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f0f2f5; /* Un gris más claro para el fondo general */
        }
        .sidebar {
            transition: width 0.3s ease-in-out;
        }
        .sidebar-item:hover {
            background-color: #374151; /* Gris oscuro para hover en sidebar */
        }
        .sidebar-item.active {
            background-color: #1e40af; /* Azul oscuro para item activo */
            border-left: 4px solid #60a5fa; /* Borde azul claro */
        }
        .modal {
            display: none;
            transition: opacity 0.3s ease;
        }
        .modal.active {
            display: flex;
            opacity: 1;
        }
        .modal-content {
            transition: transform 0.3s ease-out;
            transform: translateY(-20px);
        }
        .modal.active .modal-content {
            transform: translateY(0);
        }
        .table th {
            background-color: #e5e7eb; /* Gris claro para cabeceras de tabla */
        }
        .table td, .table th {
            padding: 0.75rem 1rem; /* Ajuste de padding en tablas */
        }
        /* Estilos para scrollbar en historial */
        .history-log::-webkit-scrollbar {
            width: 6px;
        }
        .history-log::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        .history-log::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }
        .history-log::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
        [data-role="admin-only"] { display: none; }
        body.admin-mode [data-role="admin-only"] { display: revert; } /* O 'block', 'flex', etc. según el elemento */
        body.admin-mode [data-role="user-only"] { display: none; }

        /* Responsive sidebar */
        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                left: -256px; /* Oculto por defecto en móviles */
                z-index: 50;
                height: 100vh;
                transition: left 0.3s ease-in-out;
            }
            .sidebar.open {
                left: 0;
            }
            .content-area {
                margin-left: 0 !important; /* El contenido ocupa todo el ancho */
            }
            .sidebar-overlay {
                display: none;
                position: fixed;
                inset: 0;
                background-color: rgba(0,0,0,0.5);
                z-index: 40;
            }
            .sidebar.open + .sidebar-overlay {
                display: block;
            }
        }
    </style>
</head>
<body class="text-gray-800">

    <div class="flex h-screen">
        <aside id="sidebar" class="sidebar w-64 bg-gray-800 text-white flex flex-col fixed md:relative md:translate-x-0 transform -translate-x-full md:shadow-lg">
            <div class="p-6 text-2xl font-bold border-b border-gray-700">
                <i class="fas fa-university mr-2"></i>UniSystem
            </div>
            <nav class="flex-grow mt-4">
                <a href="#" class="sidebar-item flex items-center py-3 px-6" data-view="dashboard">
                    <i class="fas fa-tachometer-alt fa-fw mr-3"></i>Dashboard
                </a>
                <a href="#" class="sidebar-item flex items-center py-3 px-6" data-view="estudiantes">
                    <i class="fas fa-user-graduate fa-fw mr-3"></i>Estudiantes
                </a>
                <a href="#" class="sidebar-item flex items-center py-3 px-6" data-view="cursos">
                    <i class="fas fa-book fa-fw mr-3"></i>Cursos
                </a>
                <a href="#" class="sidebar-item flex items-center py-3 px-6" data-view="recursos">
                    <i class="fas fa-boxes-stacked fa-fw mr-3"></i>Recursos/Inventario
                </a>
                 <a href="#" class="sidebar-item flex items-center py-3 px-6" data-view="historial" data-role="admin-only">
                    <i class="fas fa-history fa-fw mr-3"></i>Historial Inventario
                </a>
            </nav>
            <div class="p-6 border-t border-gray-700">
                <div class="mb-3">
                    <label for="roleSelector" class="block text-sm font-medium text-gray-300 mb-1">Simular Rol:</label>
                    <select id="roleSelector" class="w-full p-2 bg-gray-700 border border-gray-600 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="user">Usuario</option>
                        <option value="admin">Administrador</option>
                    </select>
                </div>
                <button id="logoutButton" class="w-full bg-red-500 hover:bg-red-600 text-white py-2 px-4 rounded-md text-sm flex items-center justify-center">
                    <i class="fas fa-sign-out-alt mr-2"></i>Cerrar Sesión
                </button>
            </div>
        </aside>
        <div id="sidebarOverlay" class="sidebar-overlay md:hidden"></div>


        <main id="contentArea" class="flex-1 flex flex-col overflow-y-auto content-area md:ml-64">
            <header class="bg-white shadow-md p-4 flex justify-between items-center">
                <div>
                    <button id="mobileMenuButton" class="text-gray-600 focus:outline-none md:hidden">
                        <i class="fas fa-bars fa-lg"></i>
                    </button>
                    <h1 id="viewTitle" class="text-2xl font-semibold text-gray-700 ml-2 md:ml-0">Dashboard</h1>
                </div>
                <div class="flex items-center">
                    <i class="fas fa-user-circle text-2xl text-gray-600 mr-2"></i>
                    <span id="userName" class="text-sm">Usuario Ejemplo</span>
                </div>
            </header>

            <div class="p-4 md:p-6 flex-grow">
                
                <section id="dashboard-view" class="view active-view">
                    <h2 class="text-xl font-semibold mb-4">Bienvenido al Sistema de Gestión</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div class="bg-white p-6 rounded-lg shadow-lg">
                            <h3 class="text-lg font-semibold text-blue-600 mb-2">Estudiantes Registrados</h3>
                            <p class="text-3xl font-bold" id="statsEstudiantes">0</p>
                        </div>
                        <div class="bg-white p-6 rounded-lg shadow-lg">
                            <h3 class="text-lg font-semibold text-green-600 mb-2">Cursos Disponibles</h3>
                            <p class="text-3xl font-bold" id="statsCursos">0</p>
                        </div>
                        <div class="bg-white p-6 rounded-lg shadow-lg">
                            <h3 class="text-lg font-semibold text-indigo-600 mb-2">Recursos en Inventario</h3>
                            <p class="text-3xl font-bold" id="statsRecursos">0</p>
                        </div>
                    </div>
                </section>

                <section id="estudiantes-view" class="view hidden">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-semibold">Gestión de Estudiantes</h2>
                        <button id="addEstudianteBtn" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md flex items-center" data-role="admin-only">
                            <i class="fas fa-plus mr-2"></i>Agregar Estudiante
                        </button>
                    </div>
                    <div class="bg-white shadow-xl rounded-lg overflow-x-auto">
                        <table class="min-w-full table">
                            <thead>
                                <tr>
                                    <th class="text-left">Matrícula</th>
                                    <th class="text-left">Nombre</th>
                                    <th class="text-left">Carrera</th>
                                    <th class="text-left">Email</th>
                                    <th class="text-center" data-role="admin-only">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="estudiantesTableBody">
                                </tbody>
                        </table>
                    </div>
                </section>

                <section id="cursos-view" class="view hidden">
                     <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-semibold">Gestión de Cursos</h2>
                        <button id="addCursoBtn" class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md flex items-center" data-role="admin-only">
                            <i class="fas fa-plus mr-2"></i>Agregar Curso
                        </button>
                    </div>
                    <div class="bg-white shadow-xl rounded-lg overflow-x-auto">
                        <table class="min-w-full table">
                            <thead>
                                <tr>
                                    <th class="text-left">Clave</th>
                                    <th class="text-left">Nombre del Curso</th>
                                    <th class="text-left">Créditos</th>
                                    <th class="text-left">Profesor Asignado</th>
                                    <th class="text-center" data-role="admin-only">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="cursosTableBody">
                                </tbody>
                        </table>
                    </div>
                </section>

                <section id="recursos-view" class="view hidden">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-semibold">Gestión de Recursos/Inventario</h2>
                        <button id="addRecursoBtn" class="bg-indigo-500 hover:bg-indigo-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md flex items-center" data-role="admin-only">
                            <i class="fas fa-plus mr-2"></i>Agregar Recurso
                        </button>
                    </div>
                    <div class="bg-white shadow-xl rounded-lg overflow-x-auto">
                        <table class="min-w-full table">
                            <thead>
                                <tr>
                                    <th class="text-left">ID Recurso</th>
                                    <th class="text-left">Nombre</th>
                                    <th class="text-left">Descripción</th>
                                    <th class="text-center">Stock</th>
                                    <th class="text-right">Precio Unit. (MXN)</th>
                                    <th class="text-center" data-role="admin-only">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="recursosTableBody">
                                </tbody>
                        </table>
                    </div>
                </section>

                <section id="historial-view" class="view hidden" data-role="admin-only">
                    <h2 class="text-xl font-semibold mb-6">Historial de Cambios en Inventario</h2>
                    <div class="bg-white shadow-xl rounded-lg p-6">
                        <div id="inventoryLogContainer" class="history-log max-h-96 overflow-y-auto space-y-3">
                            <p class="text-gray-500">No hay movimientos registrados aún.</p>
                        </div>
                    </div>
                </section>

            </div>
        </main>
    </div>

    <div id="formModal" class="modal fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full items-center justify-center z-[100] px-4">
        <div class="modal-content relative mx-auto p-6 border w-full max-w-lg md:max-w-xl shadow-2xl rounded-xl bg-white">
            <div class="flex justify-between items-center mb-6 pb-3 border-b border-gray-200">
                <h3 class="text-xl leading-6 font-bold text-gray-900" id="modalTitle">Formulario</h3>
                <button id="closeModalBtn" class="text-gray-400 hover:text-gray-600 transition duration-150 ease-in-out">
                    <i class="fas fa-times fa-lg"></i>
                </button>
            </div>
            <form id="genericForm" class="space-y-4">
                </form>
        </div>
    </div>

    <div id="notificationContainer" class="fixed bottom-5 right-5 z-[150] space-y-3 w-full max-w-xs sm:max-w-sm">
        </div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Elementos UI
    const sidebarItems = document.querySelectorAll('.sidebar-item');
    const views = document.querySelectorAll('.view');
    const viewTitle = document.getElementById('viewTitle');
    const roleSelector = document.getElementById('roleSelector');
    const mobileMenuButton = document.getElementById('mobileMenuButton');
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    const contentArea = document.getElementById('contentArea');

    // Modal
    const formModal = document.getElementById('formModal');
    const modalTitleEl = document.getElementById('modalTitle');
    const genericForm = document.getElementById('genericForm');
    const closeModalBtn = document.getElementById('closeModalBtn');

    // Contadores Dashboard
    const statsEstudiantes = document.getElementById('statsEstudiantes');
    const statsCursos = document.getElementById('statsCursos');
    const statsRecursos = document.getElementById('statsRecursos');

    // Tabla Bodies
    const estudiantesTableBody = document.getElementById('estudiantesTableBody');
    const cursosTableBody = document.getElementById('cursosTableBody');
    const recursosTableBody = document.getElementById('recursosTableBody');
    const inventoryLogContainer = document.getElementById('inventoryLogContainer');

    // Botones "Agregar"
    const addEstudianteBtn = document.getElementById('addEstudianteBtn');
    const addCursoBtn = document.getElementById('addCursoBtn');
    const addRecursoBtn = document.getElementById('addRecursoBtn');

    // --- DATOS SIMULADOS ---
    let estudiantesData = [
        { id: 'S001', nombre: 'Ana López', carrera: 'Ing. en Sistemas', email: 'ana.lopez@example.com' },
        { id: 'S002', nombre: 'Carlos Pérez', carrera: 'Lic. en Administración', email: 'carlos.perez@example.com' },
    ];
    let cursosData = [
        { id: 'C001', nombre: 'Programación Avanzada', creditos: 8, profesor: 'Dr. Alan Turing' },
        { id: 'C002', nombre: 'Bases de Datos', creditos: 6, profesor: 'Dra. Ada Lovelace' },
    ];
    let recursosData = []; // Se cargará de localStorage
    let inventoryLog = []; // Se cargará de localStorage

    const RECURSOS_STORAGE_KEY = 'uniSystemRecursos';
    const LOG_STORAGE_KEY = 'uniSystemInventoryLog';

    // --- ESTADO DE LA APLICACIÓN ---
    let currentRole = 'user'; // 'user' o 'admin'
    let currentEditingId = null;
    let currentFormType = ''; // 'estudiante', 'curso', 'recurso'

    // --- FUNCIONES DE NAVEGACIÓN Y UI ---
    function setActiveView(viewName) {
        views.forEach(view => {
            view.classList.add('hidden');
            view.classList.remove('active-view');
        });
        sidebarItems.forEach(item => item.classList.remove('active'));

        const activeView = document.getElementById(`${viewName}-view`);
        const activeSidebarItem = document.querySelector(`.sidebar-item[data-view="${viewName}"]`);

        if (activeView) {
            activeView.classList.remove('hidden');
            activeView.classList.add('active-view');
            viewTitle.textContent = activeSidebarItem ? activeSidebarItem.textContent.trim() : 'Dashboard';
        }
        if (activeSidebarItem) {
            activeSidebarItem.classList.add('active');
        }
        // Cerrar sidebar en móvil después de seleccionar
        if (sidebar.classList.contains('open')) {
            sidebar.classList.remove('open');
            sidebarOverlay.style.display = 'none';
        }
        updateDashboardStats();
    }

    sidebarItems.forEach(item => {
        item.addEventListener('click', (e) => {
            e.preventDefault();
            const viewName = item.dataset.view;
            setActiveView(viewName);
        });
    });

    mobileMenuButton.addEventListener('click', () => {
        sidebar.classList.toggle('open');
        sidebarOverlay.style.display = sidebar.classList.contains('open') ? 'block' : 'none';
    });
    sidebarOverlay.addEventListener('click', () => {
        sidebar.classList.remove('open');
        sidebarOverlay.style.display = 'none';
    });


    function updateRoleUI() {
        document.body.classList.toggle('admin-mode', currentRole === 'admin');
        // Forzar re-renderizado de vistas para aplicar cambios de visibilidad por rol
        renderEstudiantesTable();
        renderCursosTable();
        renderRecursosTable();
        renderInventoryLog(); // El historial en sí es admin-only por data-role
        // Habilitar/deshabilitar botones de agregar
        [addEstudianteBtn, addCursoBtn, addRecursoBtn].forEach(btn => {
            if (btn) btn.style.display = currentRole === 'admin' ? 'flex' : 'none';
        });
        // Visibilidad del item de menú Historial
        const historialMenuItem = document.querySelector('.sidebar-item[data-view="historial"]');
        if(historialMenuItem) historialMenuItem.style.display = currentRole === 'admin' ? 'flex' : 'none';

        // Si el usuario está en una vista que se vuelve admin-only y cambia a user, redirigir a dashboard
        const currentActiveView = document.querySelector('.view.active-view');
        if (currentRole === 'user' && currentActiveView && currentActiveView.dataset.role === 'admin-only') {
            setActiveView('dashboard');
        }


    }

    roleSelector.addEventListener('change', (e) => {
        currentRole = e.target.value;
        document.getElementById('userName').textContent = currentRole === 'admin' ? 'Administrador' : 'Usuario Ejemplo';
        updateRoleUI();
        showNotification(`Rol cambiado a: ${currentRole === 'admin' ? 'Administrador' : 'Usuario'}`, 'info');
    });

    // --- FUNCIONES DE MODAL ---
    function openModal(title, formType, data = null) {
        modalTitleEl.textContent = title;
        currentFormType = formType;
        currentEditingId = data ? data.id : null;
        populateForm(formType, data);
        formModal.classList.add('active');
    }

    function closeModal() {
        formModal.classList.remove('active');
        genericForm.innerHTML = ''; // Limpiar contenido del formulario
        currentEditingId = null;
        currentFormType = '';
    }
    closeModalBtn.addEventListener('click', closeModal);
    formModal.addEventListener('click', (e) => {
        if (e.target === formModal) closeModal(); // Cerrar si se hace clic fuera del contenido
    });

    function populateForm(formType, data) {
        genericForm.innerHTML = ''; // Limpiar
        let fields = [];
        let submitButtonText = 'Guardar';

        switch(formType) {
            case 'estudiante':
                fields = [
                    { label: 'Matrícula', name: 'id', type: 'text', required: true, value: data?.id || '', readonly: !!data },
                    { label: 'Nombre Completo', name: 'nombre', type: 'text', required: true, value: data?.nombre || '' },
                    { label: 'Carrera', name: 'carrera', type: 'text', required: true, value: data?.carrera || '' },
                    { label: 'Email', name: 'email', type: 'email', required: true, value: data?.email || '' },
                ];
                submitButtonText = data ? 'Actualizar Estudiante' : 'Agregar Estudiante';
                break;
            case 'curso':
                fields = [
                    { label: 'Clave del Curso', name: 'id', type: 'text', required: true, value: data?.id || '', readonly: !!data },
                    { label: 'Nombre del Curso', name: 'nombre', type: 'text', required: true, value: data?.nombre || '' },
                    { label: 'Créditos', name: 'creditos', type: 'number', required: true, value: data?.creditos || '', min:1 },
                    { label: 'Profesor Asignado', name: 'profesor', type: 'text', required: true, value: data?.profesor || '' },
                ];
                submitButtonText = data ? 'Actualizar Curso' : 'Agregar Curso';
                break;
            case 'recurso':
                 fields = [
                    { label: 'ID del Recurso', name: 'id', type: 'text', required: true, value: data?.id || '', readonly: !!data },
                    { label: 'Nombre del Recurso', name: 'nombre', type: 'text', required: true, value: data?.nombre || '' },
                    { label: 'Descripción', name: 'descripcion', type: 'textarea', value: data?.descripcion || '' },
                    { label: 'Stock Actual', name: 'stock', type: 'number', required: true, value: data?.stock || 0, min:0 },
                    { label: 'Precio Unitario (MXN)', name: 'precio', type: 'number', step: '0.01', required: true, value: data?.precio || 0, min:0 },
                ];
                submitButtonText = data ? 'Actualizar Recurso' : 'Agregar Recurso';
                break;
        }

        fields.forEach(field => {
            const fieldWrapper = document.createElement('div');
            const label = document.createElement('label');
            label.className = 'block text-sm font-medium text-gray-700 mb-1';
            label.textContent = field.label;
            label.htmlFor = `field-${field.name}`;

            let input;
            if (field.type === 'textarea') {
                input = document.createElement('textarea');
                input.rows = 3;
            } else {
                input = document.createElement('input');
                input.type = field.type;
            }
            input.id = `field-${field.name}`;
            input.name = field.name;
            input.className = 'mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm';
            if(field.required) input.required = true;
            if(field.value !== undefined) input.value = field.value;
            if(field.readonly) input.readOnly = true;
            if(field.min !== undefined) input.min = field.min;
            if(field.step !== undefined) input.step = field.step;
            
            fieldWrapper.appendChild(label);
            fieldWrapper.appendChild(input);
            genericForm.appendChild(fieldWrapper);
        });
        
        const submitButton = document.createElement('button');
        submitButton.type = 'submit';
        submitButton.className = 'w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 px-4 rounded-lg shadow-md transition duration-150 ease-in-out';
        submitButton.textContent = submitButtonText;
        genericForm.appendChild(submitButton);
    }
    
    genericForm.addEventListener('submit', handleFormSubmit);

    // --- FUNCIONES CRUD Y RENDER ---

    // Estudiantes
    function renderEstudiantesTable() {
        estudiantesTableBody.innerHTML = '';
        if (estudiantesData.length === 0) {
            estudiantesTableBody.innerHTML = `<tr><td colspan="${currentRole === 'admin' ? 5 : 4}" class="text-center py-4 text-gray-500">No hay estudiantes registrados.</td></tr>`;
            return;
        }
        estudiantesData.forEach(est => {
            const row = estudiantesTableBody.insertRow();
            row.innerHTML = `
                <td class="border-b border-gray-200">${est.id}</td>
                <td class="border-b border-gray-200">${est.nombre}</td>
                <td class="border-b border-gray-200">${est.carrera}</td>
                <td class="border-b border-gray-200">${est.email}</td>
                ${currentRole === 'admin' ? `
                <td class="border-b border-gray-200 text-center">
                    <button class="text-blue-500 hover:text-blue-700 p-1 edit-btn" data-id="${est.id}" data-type="estudiante" title="Editar"><i class="fas fa-edit"></i></button>
                    <button class="text-red-500 hover:text-red-700 p-1 delete-btn" data-id="${est.id}" data-type="estudiante" title="Eliminar"><i class="fas fa-trash"></i></button>
                </td>` : '<td class="border-b border-gray-200 text-center" data-role="admin-only"></td>'}
            `;
        });
        updateDashboardStats();
    }

    // Cursos
    function renderCursosTable() {
        cursosTableBody.innerHTML = '';
        if (cursosData.length === 0) {
            cursosTableBody.innerHTML = `<tr><td colspan="${currentRole === 'admin' ? 5 : 4}" class="text-center py-4 text-gray-500">No hay cursos registrados.</td></tr>`;
            return;
        }
        cursosData.forEach(curso => {
            const row = cursosTableBody.insertRow();
            row.innerHTML = `
                <td class="border-b border-gray-200">${curso.id}</td>
                <td class="border-b border-gray-200">${curso.nombre}</td>
                <td class="border-b border-gray-200">${curso.creditos}</td>
                <td class="border-b border-gray-200">${curso.profesor}</td>
                ${currentRole === 'admin' ? `
                <td class="border-b border-gray-200 text-center">
                    <button class="text-blue-500 hover:text-blue-700 p-1 edit-btn" data-id="${curso.id}" data-type="curso" title="Editar"><i class="fas fa-edit"></i></button>
                    <button class="text-red-500 hover:text-red-700 p-1 delete-btn" data-id="${curso.id}" data-type="curso" title="Eliminar"><i class="fas fa-trash"></i></button>
                </td>` : '<td class="border-b border-gray-200 text-center" data-role="admin-only"></td>'}
            `;
        });
        updateDashboardStats();
    }

    // Recursos (Inventario)
    function loadRecursosFromLocalStorage() {
        const stored = localStorage.getItem(RECURSOS_STORAGE_KEY);
        recursosData = stored ? JSON.parse(stored) : [
            { id: 'R001', nombre: 'Proyector Epson', descripcion: 'Proyector para aulas magnas', stock: 5, precio: 8500.00 },
            { id: 'R002', nombre: 'Laptop Dell Inspiron', descripcion: 'Laptop para préstamo a estudiantes', stock: 10, precio: 12000.00 },
        ];
        const storedLog = localStorage.getItem(LOG_STORAGE_KEY);
        inventoryLog = storedLog ? JSON.parse(storedLog) : [];
    }
    function saveRecursosToLocalStorage() {
        localStorage.setItem(RECURSOS_STORAGE_KEY, JSON.stringify(recursosData));
        localStorage.setItem(LOG_STORAGE_KEY, JSON.stringify(inventoryLog));
    }
    function addInventoryLog(action, itemName, details = "") {
        const timestamp = new Date().toLocaleString('es-MX');
        inventoryLog.unshift({ timestamp, action, itemName, details, user: currentRole }); // Guardar el rol que hizo la acción
        if (inventoryLog.length > 50) inventoryLog.pop(); // Limitar historial
        saveRecursosToLocalStorage();
        renderInventoryLog();
    }

    function renderRecursosTable() {
        recursosTableBody.innerHTML = '';
        if (recursosData.length === 0) {
            recursosTableBody.innerHTML = `<tr><td colspan="${currentRole === 'admin' ? 6 : 5}" class="text-center py-4 text-gray-500">No hay recursos en el inventario.</td></tr>`;
            return;
        }
        recursosData.forEach(rec => {
            const row = recursosTableBody.insertRow();
            row.innerHTML = `
                <td class="border-b border-gray-200">${rec.id}</td>
                <td class="border-b border-gray-200">${rec.nombre}</td>
                <td class="border-b border-gray-200 truncate max-w-xs" title="${rec.descripcion}">${rec.descripcion.substring(0,40)}${rec.descripcion.length > 40 ? '...' : ''}</td>
                <td class="border-b border-gray-200 text-center">${rec.stock}</td>
                <td class="border-b border-gray-200 text-right">${parseFloat(rec.precio).toFixed(2)}</td>
                ${currentRole === 'admin' ? `
                <td class="border-b border-gray-200 text-center">
                    <button class="text-blue-500 hover:text-blue-700 p-1 edit-btn" data-id="${rec.id}" data-type="recurso" title="Editar"><i class="fas fa-edit"></i></button>
                    <button class="text-red-500 hover:text-red-700 p-1 delete-btn" data-id="${rec.id}" data-type="recurso" title="Eliminar"><i class="fas fa-trash"></i></button>
                </td>` : '<td class="border-b border-gray-200 text-center" data-role="admin-only"></td>'}
            `;
        });
        updateDashboardStats();
    }
    
    function renderInventoryLog() {
        inventoryLogContainer.innerHTML = '';
        if (inventoryLog.length === 0) {
            inventoryLogContainer.innerHTML = `<p class="text-gray-500">No hay movimientos registrados aún.</p>`;
            return;
        }
        inventoryLog.forEach(log => {
            const logEntry = document.createElement('div');
            logEntry.className = 'p-3 bg-gray-50 rounded-md border border-gray-200 text-sm';
            logEntry.innerHTML = `
                <p class="font-semibold text-gray-700">${log.action}: <span class="font-normal text-gray-600">${log.itemName}</span></p>
                ${log.details ? `<p class="text-xs text-gray-500">${log.details}</p>` : ''}
                <p class="text-xs text-gray-400 mt-1">${log.timestamp} (Rol: ${log.user})</p>
            `;
            inventoryLogContainer.appendChild(logEntry);
        });
    }


    // --- MANEJO DE FORMULARIOS Y ACCIONES CRUD ---
    function handleFormSubmit(e) {
        e.preventDefault();
        const formData = new FormData(genericForm);
        const data = Object.fromEntries(formData.entries());

        // Convertir números
        if (data.creditos) data.creditos = parseInt(data.creditos);
        if (data.stock) data.stock = parseInt(data.stock);
        if (data.precio) data.precio = parseFloat(data.precio);

        let itemExists;

        switch(currentFormType) {
            case 'estudiante':
                itemExists = estudiantesData.some(item => item.id === data.id && item.id !== currentEditingId);
                if (itemExists && !currentEditingId) { // Solo checar duplicados al agregar
                     showNotification(`La matrícula '${data.id}' ya existe.`, 'error'); return;
                }
                if (currentEditingId) {
                    estudiantesData = estudiantesData.map(item => item.id === currentEditingId ? {...item, ...data} : item);
                    showNotification('Estudiante actualizado exitosamente.', 'success');
                } else {
                    estudiantesData.push(data);
                    showNotification('Estudiante agregado exitosamente.', 'success');
                }
                renderEstudiantesTable();
                break;
            case 'curso':
                itemExists = cursosData.some(item => item.id === data.id && item.id !== currentEditingId);
                 if (itemExists && !currentEditingId) {
                     showNotification(`La clave de curso '${data.id}' ya existe.`, 'error'); return;
                }
                if (currentEditingId) {
                    cursosData = cursosData.map(item => item.id === currentEditingId ? {...item, ...data} : item);
                    showNotification('Curso actualizado exitosamente.', 'success');
                } else {
                    cursosData.push(data);
                    showNotification('Curso agregado exitosamente.', 'success');
                }
                renderCursosTable();
                break;
            case 'recurso':
                itemExists = recursosData.some(item => item.id === data.id && item.id !== currentEditingId);
                if (itemExists && !currentEditingId) {
                     showNotification(`El ID de recurso '${data.id}' ya existe.`, 'error'); return;
                }
                if (currentEditingId) {
                    const oldRecurso = recursosData.find(r => r.id === currentEditingId);
                    recursosData = recursosData.map(item => item.id === currentEditingId ? {...item, ...data} : item);
                    addInventoryLog('Recurso Actualizado', data.nombre, `ID: ${data.id}. Stock: ${oldRecurso.stock} -> ${data.stock}. Precio: ${oldRecurso.precio} -> ${data.precio}`);
                    showNotification('Recurso actualizado exitosamente.', 'success');
                } else {
                    recursosData.push(data);
                    addInventoryLog('Nuevo Recurso Agregado', data.nombre, `ID: ${data.id}, Stock: ${data.stock}, Precio: ${data.precio}`);
                    showNotification('Recurso agregado exitosamente.', 'success');
                }
                saveRecursosToLocalStorage();
                renderRecursosTable();
                break;
        }
        closeModal();
    }

    // Event listeners para botones "Agregar"
    if(addEstudianteBtn) addEstudianteBtn.addEventListener('click', () => openModal('Agregar Nuevo Estudiante', 'estudiante'));
    if(addCursoBtn) addCursoBtn.addEventListener('click', () => openModal('Agregar Nuevo Curso', 'curso'));
    if(addRecursoBtn) addRecursoBtn.addEventListener('click', () => openModal('Agregar Nuevo Recurso', 'recurso'));

    // Event delegation para botones Editar/Eliminar en tablas
    document.getElementById('contentArea').addEventListener('click', (e) => {
        const targetButton = e.target.closest('button');
        if (!targetButton) return;

        const id = targetButton.dataset.id;
        const type = targetButton.dataset.type;

        if (targetButton.classList.contains('edit-btn')) {
            if (currentRole !== 'admin') {
                showNotification('No tiene permisos para editar.', 'error'); return;
            }
            let dataToEdit;
            let title = 'Editar';
            switch(type) {
                case 'estudiante': dataToEdit = estudiantesData.find(item => item.id === id); title += ' Estudiante'; break;
                case 'curso': dataToEdit = cursosData.find(item => item.id === id); title += ' Curso'; break;
                case 'recurso': dataToEdit = recursosData.find(item => item.id === id); title += ' Recurso'; break;
            }
            if (dataToEdit) openModal(title, type, dataToEdit);

        } else if (targetButton.classList.contains('delete-btn')) {
            if (currentRole !== 'admin') {
                showNotification('No tiene permisos para eliminar.', 'error'); return;
            }
            if (confirm(`¿Está seguro de que desea eliminar este ${type}? Esta acción no se puede deshacer.`)) {
                let itemName = '';
                switch(type) {
                    case 'estudiante': 
                        itemName = estudiantesData.find(i => i.id === id)?.nombre || id;
                        estudiantesData = estudiantesData.filter(item => item.id !== id);
                        renderEstudiantesTable();
                        break;
                    case 'curso':
                        itemName = cursosData.find(i => i.id === id)?.nombre || id;
                        cursosData = cursosData.filter(item => item.id !== id);
                        renderCursosTable();
                        break;
                    case 'recurso':
                        const recurso = recursosData.find(item => item.id === id);
                        if (recurso) {
                            itemName = recurso.nombre;
                            recursosData = recursosData.filter(item => item.id !== id);
                            addInventoryLog('Recurso Eliminado', itemName, `ID: ${id}`);
                            saveRecursosToLocalStorage();
                            renderRecursosTable();
                        }
                        break;
                }
                showNotification(`${type.charAt(0).toUpperCase() + type.slice(1)} "${itemName}" eliminado.`, 'info');
            }
        }
    });
    
    // --- NOTIFICACIONES ---
    function showNotification(message, type = 'success') { // success, error, info
        const notification = document.createElement('div');
        let bgColor, iconClass;

        switch(type) {
            case 'error': 
                bgColor = 'bg-red-500'; iconClass = 'fa-exclamation-triangle'; break;
            case 'info': 
                bgColor = 'bg-blue-500'; iconClass = 'fa-info-circle'; break;
            default: // success
                bgColor = 'bg-green-500'; iconClass = 'fa-check-circle'; break;
        }

        notification.className = `p-4 rounded-lg shadow-xl text-white text-sm ${bgColor} flex items-center transform transition-all duration-300 ease-in-out opacity-0 translate-y-2`;
        notification.innerHTML = `<i class="fas ${iconClass} mr-3 fa-lg"></i> <span class="flex-1">${message}</span>`;
        
        notificationContainer.appendChild(notification);

        // Forzar reflujo para animación de entrada
        requestAnimationFrame(() => {
            notification.classList.remove('opacity-0', 'translate-y-2');
            notification.classList.add('opacity-100', 'translate-y-0');
        });
        
        setTimeout(() => {
            notification.classList.remove('opacity-100', 'translate-y-0');
            notification.classList.add('opacity-0', 'translate-x-full'); // Animación de salida hacia la derecha
            setTimeout(() => notification.remove(), 300);
        }, 4000);
    }

    // --- DASHBOARD STATS ---
    function updateDashboardStats() {
        statsEstudiantes.textContent = estudiantesData.length;
        statsCursos.textContent = cursosData.length;
        statsRecursos.textContent = recursosData.length;
    }

    // --- INICIALIZACIÓN ---
    loadRecursosFromLocalStorage(); // Cargar datos de inventario y log
    setActiveView('dashboard'); // Vista inicial
    updateRoleUI(); // Aplicar rol inicial
    renderEstudiantesTable();
    renderCursosTable();
    renderRecursosTable();
    renderInventoryLog();
});
</script>

</body>
</html>
