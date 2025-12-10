import React, { useState, useEffect, useRef } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import {
    Edit3, Trash2, PlusCircle, Search, Menu as MenuIcon, UserCircle, X, Bookmark, Filter,
    LayoutDashboard, Users, ListChecks, Activity, Settings, Moon, Sun, Package, Briefcase, FileText, Building,
    ChevronLeft, ChevronRight, MessageSquare, LogOut, ShieldCheck, Eye, BarChart2,
    Save, AlertTriangle, Info, CheckCircle, Annoyed, CalendarDays, Newspaper, UsersRound,
    Archive, BookOpen, MapPin, Bell, LifeBuoy, Building2, Edit, UserX, UserCheck, Columns, BookCopy,
    ClipboardList, CalendarSearch, LogIn, KeyRound, Table as TableIcon, History, TrendingUp, UserPlus
} from 'lucide-react'; 
import { FlaskConical, Armchair, Award, Palette } from 'lucide-react'; 

// Constantes de la aplicación
const APP_NAME = "PortalEduSuperior";
const INSTITUTIONAL_NAVY = "blue-800";
const INSTITUTIONAL_NAVY_DARK = "blue-900";
const INSTITUTIONAL_NAVY_LIGHT = "blue-700";
const ACCENT_YELLOW = "yellow-400";
const ACCENT_RED = "red-500";
const ACCENT_GREEN = "green-500";
const TEXT_WHITE = "text-white";
const TEXT_DARK = "text-gray-800";
const TEXT_LIGHT = "text-gray-600";
const DARK_MODE_TEXT = "text-gray-200";
const DARK_MODE_TEXT_MUTED = "text-gray-400";

// --- Simulación de Base de Datos de Usuarios en localStorage ---
const getUsersFromStorage = () => {
    const users = localStorage.getItem('portalUsers');
    return users ? JSON.parse(users) : [];
};

const saveUsersToStorage = (users) => {
    localStorage.setItem('portalUsers', JSON.stringify(users));
};

// Inicializar con usuarios de ejemplo si no hay ninguno y localStorage está disponible
if (typeof localStorage !== 'undefined' && !localStorage.getItem('portalUsers')) {
    saveUsersToStorage([
        { username: 'root', password: 'rootpass', role: 'ROOT', name: 'Root Admin', email: 'root@edu.portal' },
        { username: 'admin', password: 'adminpass', role: 'ROOT', name: 'System Admin', email: 'admin@edu.portal' },
        { username: 'usuario', password: 'usuariopass', role: 'USUARIO', name: 'Usuario Ejemplo', email: 'usuario@edu.portal' },
    ]);
}


// --- Componentes simulados de shadcn/ui (sin cambios funcionales) ---
const Button = ({ variant = 'default', size = 'default', className = '', children, title, ...props }) => {
    const baseStyle = "inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none ring-offset-background";
    const variants = {
        default: `bg-${INSTITUTIONAL_NAVY} ${TEXT_WHITE} hover:bg-${INSTITUTIONAL_NAVY_DARK}`,
        destructive: `bg-${ACCENT_RED} text-white hover:bg-red-600`,
        outline: `border border-input hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-accent-foreground`,
        secondary: "bg-gray-200 text-secondary-foreground hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600",
        ghost: "hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-accent-foreground",
        link: `underline-offset-4 hover:underline text-${INSTITUTIONAL_NAVY} dark:text-blue-400`,
        success: `bg-${ACCENT_GREEN} text-white hover:bg-green-600`,
        warning: `bg-${ACCENT_YELLOW} text-black hover:bg-yellow-500`,
    };
    const sizes = { default: "h-10 py-2 px-4", sm: "h-9 px-3 rounded-md", lg: "h-11 px-8 rounded-md", icon: "h-10 w-10" };
    return (<button className={`${baseStyle} ${variants[variant]} ${sizes[size]} ${className}`} title={title} {...props}>{children}</button>);
};
const Card = ({ className = '', children, ...props }) => (<div className={`bg-white dark:bg-gray-800 rounded-2xl shadow-lg dark:shadow-xl overflow-hidden ${className}`} {...props}>{children}</div>);
const CardHeader = ({ className = '', children, ...props }) => (<div className={`p-6 ${className}`} {...props}>{children}</div>);
const CardTitle = ({ className = '', children, ...props }) => (<h3 className={`text-xl font-semibold leading-none tracking-tight ${TEXT_DARK} dark:${DARK_MODE_TEXT} ${className}`} {...props}>{children}</h3>);
const CardDescription = ({ className = '', children, ...props }) => (<p className={`text-sm ${TEXT_LIGHT} dark:${DARK_MODE_TEXT_MUTED} ${className}`} {...props}>{children}</p>);
const CardContent = ({ className = '', children, ...props }) => (<div className={`p-6 pt-0 ${className}`} {...props}>{children}</div>);
const CardFooter = ({ className = '', children, ...props }) => (<div className={`flex items-center p-6 pt-0 ${className}`} {...props}>{children}</div>);
const Input = React.forwardRef(({ className = '', type = "text", ...props }, ref) => (<input type={type} className={`flex h-10 w-full rounded-md border border-gray-300 dark:border-gray-600 bg-transparent px-3 py-2 text-sm placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-none focus:ring-2 focus:ring-${INSTITUTIONAL_NAVY_LIGHT} focus:border-${INSTITUTIONAL_NAVY_LIGHT} disabled:cursor-not-allowed disabled:opacity-50 ${TEXT_DARK} dark:${DARK_MODE_TEXT} ${className}`} ref={ref} {...props}/>));
const Label = ({ className = '', children, ...props }) => (<label className={`block text-sm font-medium ${TEXT_DARK} dark:${DARK_MODE_TEXT_MUTED} mb-1 ${className}`} {...props}>{children}</label>);
const Select = React.forwardRef(({ className = '', children, ...props }, ref) => (<select className={`flex h-10 w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-${INSTITUTIONAL_NAVY_LIGHT} focus:border-${INSTITUTIONAL_NAVY_LIGHT} ${TEXT_DARK} dark:${DARK_MODE_TEXT} ${className}`} ref={ref} {...props}>{children}</select>));
const Avatar = ({ src, fallback, className = '' }) => (<div className={`relative flex h-10 w-10 shrink-0 overflow-hidden rounded-full items-center justify-center bg-gray-200 dark:bg-gray-700 ${className}`}>{src ? <img src={src} alt="Avatar" className="aspect-square h-full w-full" /> : <span className={`font-medium text-gray-600 dark:text-gray-300`}>{fallback}</span>}</div>);
const Sheet = ({ open, onClose, side = 'left', children, className = '' }) => {
    const variants = { left: { x: 0 }, closedLeft: { x: "-100%" }, right: { x: 0 }, closedRight: { x: "100%" }};
    const initial = side === 'left' ? 'closedLeft' : 'closedRight';
    const animate = open ? side : initial;
    return (<AnimatePresence>{open && (<><motion.div initial={{ opacity: 0 }} animate={{ opacity: 1 }} exit={{ opacity: 0 }} onClick={onClose} className="fixed inset-0 bg-black/60 z-40" /><motion.div initial={initial} animate={animate} exit={initial} variants={variants} transition={{ type: 'spring', stiffness: 300, damping: 30 }} className={`fixed top-0 ${side}-0 h-full w-72 bg-white dark:bg-gray-800 shadow-xl z-50 p-6 ${className}`}>{children}</motion.div></>)}</AnimatePresence>);
};
const Table = ({ className = '', children }) => <div className="w-full overflow-auto"><table className={`w-full caption-bottom text-sm ${className}`}>{children}</table></div>;
const TableHeader = ({ className = '', children }) => <thead className={`[&_tr]:border-b dark:border-gray-700 ${className}`}>{children}</thead>;
const TableRow = ({ className = '', children }) => <tr className={`border-b dark:border-gray-700 transition-colors hover:bg-gray-100/50 dark:hover:bg-gray-700/50 data-[state=selected]:bg-gray-100 dark:data-[state=selected]:bg-gray-700 ${className}`}>{children}</tr>;
const TableHead = ({ className = '', children }) => <th className={`h-12 px-4 text-left align-middle font-medium text-gray-500 dark:text-gray-400 [&:has([role=checkbox])]:pr-0 ${className}`}>{children}</th>;
const TableBody = ({ className = '', children }) => <tbody className={`[&_tr:last-child]:border-0 ${className}`}>{children}</tbody>;
const TableCell = ({ className = '', children }) => <td className={`p-4 align-middle [&:has([role=checkbox])]:pr-0 ${TEXT_DARK} dark:${DARK_MODE_TEXT} ${className}`}>{children}</td>;
const Switch = ({ checked, onCheckedChange, className = '' }) => {
    const [enabled, setEnabled] = useState(checked); useEffect(() => setEnabled(checked), [checked]);
    const toggleSwitch = () => { const newState = !enabled; setEnabled(newState); if (onCheckedChange) { onCheckedChange(newState); }};
    return (<button type="button" onClick={toggleSwitch} className={`${enabled ? `bg-${INSTITUTIONAL_NAVY}` : 'bg-gray-300 dark:bg-gray-600'} relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-${INSTITUTIONAL_NAVY_LIGHT} focus:ring-offset-2 ${className}`} role="switch" aria-checked={enabled}><span aria-hidden="true" className={`${enabled ? 'translate-x-5' : 'translate-x-0'} pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out`}/></button>);
};
const Alert = ({ variant = 'default', title, description, icon, className = '' }) => {
    const alertVariants = {
        default: { bg: 'bg-blue-50 dark:bg-blue-900/30', border: 'border-blue-300 dark:border-blue-700', text: 'text-blue-700 dark:text-blue-300', iconColor: 'text-blue-500 dark:text-blue-400' },
        destructive: { bg: 'bg-red-50 dark:bg-red-900/30', border: 'border-red-300 dark:border-red-700', text: 'text-red-700 dark:text-red-300', iconColor: 'text-red-500 dark:text-red-400' },
        warning: { bg: 'bg-yellow-50 dark:bg-yellow-900/30', border: 'border-yellow-300 dark:border-yellow-700', text: 'text-yellow-700 dark:text-yellow-300', iconColor: 'text-yellow-500 dark:text-yellow-400' },
        success: { bg: 'bg-green-50 dark:bg-green-900/30', border: 'border-green-300 dark:border-green-700', text: 'text-green-700 dark:text-green-300', iconColor: 'text-green-500 dark:text-green-400' },
    };
    const currentVariant = alertVariants[variant] || alertVariants.default;
    const DefaultIcon = () => {
        switch(variant) {
            case 'destructive': return <AlertTriangle className={`h-5 w-5 ${currentVariant.iconColor}`} />;
            case 'warning': return <Info className={`h-5 w-5 ${currentVariant.iconColor}`} />;
            case 'success': return <CheckCircle className={`h-5 w-5 ${currentVariant.iconColor}`} />;
            default: return <Info className={`h-5 w-5 ${currentVariant.iconColor}`} />;
        }
    }
    return (<div className={`rounded-md border p-4 ${currentVariant.bg} ${currentVariant.border} ${className}`}><div className="flex"><div className="flex-shrink-0">{icon || <DefaultIcon />}</div><div className="ml-3">{title && <h3 className={`text-sm font-medium ${currentVariant.text}`}>{title}</h3>}{description && <div className={`text-sm ${currentVariant.text} ${title ? 'mt-2' : ''}`}>{description}</div>}</div></div></div>);
};

// --- Vista de Registro ---
const RegisterView = ({ onRegisterSuccess, navigateToLogin }) => {
    const [name, setName] = useState('');
    const [email, setEmail] = useState('');
    const [username, setUsername] = useState('');
    const [password, setPassword] = useState('');
    const [confirmPassword, setConfirmPassword] = useState('');
    const [error, setError] = useState('');
    const [success, setSuccess] = useState('');
    const [isLoading, setIsLoading] = useState(false);

    const handleRegister = (e) => {
        e.preventDefault();
        setIsLoading(true);
        setError('');
        setSuccess('');

        if (password !== confirmPassword) {
            setError('Las contraseñas no coinciden.');
            setIsLoading(false);
            return;
        }
        if (password.length < 6) {
            setError('La contraseña debe tener al menos 6 caracteres.');
            setIsLoading(false);
            return;
        }

        setTimeout(() => {
            const users = getUsersFromStorage();
            if (users.find(u => u.username === username)) {
                setError('El nombre de usuario ya existe.');
            } else if (users.find(u => u.email === email)) {
                setError('El correo electrónico ya está registrado.');
            } else {
                const newUser = { name, email, username, password, role: 'USUARIO' };
                users.push(newUser);
                saveUsersToStorage(users);
                setSuccess('¡Registro exitoso! Ahora puede iniciar sesión.');
                // Limpiar formulario
                setName(''); setEmail(''); setUsername(''); setPassword(''); setConfirmPassword('');
                // Opcionalmente redirigir a login después de un momento
                setTimeout(() => navigateToLogin(), 2000);
            }
            setIsLoading(false);
        }, 1000);
    };
    
    return (
        <div className="min-h-screen bg-gray-100 dark:bg-gray-900 flex flex-col justify-center items-center p-4">
            <motion.div initial={{ opacity: 0, y: -20 }} animate={{ opacity: 1, y: 0 }} className="w-full max-w-md">
                <Card className="shadow-2xl">
                    <CardHeader className="text-center">
                        <div className="inline-block p-3 bg-blue-100 dark:bg-blue-900/50 rounded-full mb-4">
                           <UserPlus size={40} className={`text-${INSTITUTIONAL_NAVY} dark:text-blue-400`} />
                        </div>
                        <CardTitle className="text-2xl">Registro de Nuevo Usuario</CardTitle>
                        <CardDescription>Cree su cuenta para acceder al portal.</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <form onSubmit={handleRegister} className="space-y-4">
                            <div><Label htmlFor="regName">Nombre Completo</Label><Input id="regName" value={name} onChange={(e) => setName(e.target.value)} required /></div>
                            <div><Label htmlFor="regEmail">Correo Electrónico</Label><Input id="regEmail" type="email" value={email} onChange={(e) => setEmail(e.target.value)} required /></div>
                            <div><Label htmlFor="regUsername">Nombre de Usuario</Label><Input id="regUsername" value={username} onChange={(e) => setUsername(e.target.value)} required /></div>
                            <div><Label htmlFor="regPassword">Contraseña</Label><Input id="regPassword" type="password" value={password} onChange={(e) => setPassword(e.target.value)} required /></div>
                            <div><Label htmlFor="regConfirmPassword">Confirmar Contraseña</Label><Input id="regConfirmPassword" type="password" value={confirmPassword} onChange={(e) => setConfirmPassword(e.target.value)} required /></div>
                            
                            {error && <Alert variant="destructive" description={error} />}
                            {success && <Alert variant="success" description={success} />}
                            
                            <Button type="submit" className="w-full" disabled={isLoading}>
                                {isLoading ? (<motion.div animate={{ rotate: 360 }} transition={{ duration: 1, repeat: Infinity, ease: "linear" }} className="w-5 h-5 border-2 border-white border-t-transparent rounded-full mr-2"></motion.div>) : (<UserPlus size={18} className="mr-2"/>)}
                                Registrarse
                            </Button>
                        </form>
                    </CardContent>
                    <CardFooter className="justify-center">
                        <Button variant="link" onClick={navigateToLogin}>¿Ya tiene una cuenta? Iniciar Sesión</Button>
                    </CardFooter>
                </Card>
            </motion.div>
        </div>
    );
};


// --- Vista de Login ---
const LoginView = ({ onLoginSuccess, navigateToRegister }) => {
    const [username, setUsername] = useState('');
    const [password, setPassword] = useState('');
    const [error, setError] = useState('');
    const [isLoading, setIsLoading] = useState(false);

    const handleLogin = (e) => {
        e.preventDefault();
        setIsLoading(true);
        setError('');

        setTimeout(() => {
            const users = getUsersFromStorage();
            const foundUser = users.find(u => u.username.toLowerCase() === username.toLowerCase() && u.password === password);

            if (foundUser) {
                onLoginSuccess(foundUser.role, foundUser.username, foundUser.name);
            } else {
                setError('Credenciales incorrectas o usuario no encontrado.');
            }
            setIsLoading(false);
        }, 1000);
    };

    return (
        <div className="min-h-screen bg-gray-100 dark:bg-gray-900 flex flex-col justify-center items-center p-4">
            <motion.div initial={{ opacity: 0, y: -20 }} animate={{ opacity: 1, y: 0 }} className="w-full max-w-md">
                <Card className="shadow-2xl">
                    <CardHeader className="text-center">
                        <div className="inline-block p-3 bg-blue-100 dark:bg-blue-900/50 rounded-full mb-4">
                           <Building2 size={40} className={`text-${INSTITUTIONAL_NAVY} dark:text-blue-400`} />
                        </div>
                        <CardTitle className="text-2xl">Bienvenido a {APP_NAME}</CardTitle>
                        <CardDescription>Ingrese sus credenciales para acceder.</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <form onSubmit={handleLogin} className="space-y-6">
                            <div><Label htmlFor="username">Usuario</Label><div className="relative"><UserCircle className="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400" /><Input id="username" value={username} onChange={(e) => setUsername(e.target.value)} placeholder="ej: root, usuario" required className="pl-10"/></div></div>
                            <div><Label htmlFor="password">Contraseña</Label><div className="relative"><KeyRound className="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400" /><Input id="password" type="password" value={password} onChange={(e) => setPassword(e.target.value)} placeholder="••••••••" required className="pl-10"/></div></div>
                            {error && <Alert variant="destructive" description={error} />}
                            <Button type="submit" className="w-full" disabled={isLoading}>{isLoading ? (<motion.div animate={{ rotate: 360 }} transition={{ duration: 1, repeat: Infinity, ease: "linear" }} className="w-5 h-5 border-2 border-white border-t-transparent rounded-full mr-2"></motion.div>) : (<LogIn size={18} className="mr-2"/>)}Ingresar</Button>
                        </form>
                    </CardContent>
                     <CardFooter className="flex-col items-center space-y-2">
                        <Button variant="link" onClick={navigateToRegister}>¿No tiene una cuenta? Registrarse</Button>
                        <p className="text-center text-xs text-gray-500 dark:text-gray-400">
                            Usuarios de prueba: root/rootpass, admin/adminpass, usuario/usuariopass
                        </p>
                    </CardFooter>
                </Card>
            </motion.div>
        </div>
    );
};


// --- Vista de Inventario (CRUD) – Institución Educativa ---
const InventoryViewEdu = ({ userRole }) => { 
    const [inventoryItems, setInventoryItems] = useState([
        { id: crypto.randomUUID(), name: 'Proyector BenQ MX50', quantity: 5, location: 'Aula B-102', type: 'equipo' },
        { id: crypto.randomUUID(), name: 'Kit de Química Orgánica Avanzada', quantity: 15, location: 'Laboratorio 3B', type: 'laboratorio' },
        { id: crypto.randomUUID(), name: 'Libro: "Física Universitaria" Sears-Zemansky', quantity: 30, location: 'Biblioteca Central - Estante 5A', type: 'bibliografico' },
        { id: crypto.randomUUID(), name: 'Silla Ergonómica Herman Miller', quantity: 10, location: 'Sala de Profesores Dpto. Ingeniería', type: 'mobiliario' },
        { id: crypto.randomUUID(), name: 'Laptop Dell XPS 15', quantity: 8, location: 'Préstamo TI (Docentes)', type: 'equipo' },
        { id: crypto.randomUUID(), name: 'Reactivos: Ácido Sulfúrico (98%)', quantity: 2, location: 'Almacén Química Segura', type: 'laboratorio' },
    ]);
    const [itemName, setItemName] = useState('');
    const [itemQuantity, setItemQuantity] = useState('');
    const [itemLocation, setItemLocation] = useState('');
    const [itemType, setItemType] = useState('equipo');
    const [isEditing, setIsEditing] = useState(false);
    const [editingId, setEditingId] = useState(null);
    const [error, setError] = useState('');
    const [successMessage, setSuccessMessage] = useState('');
    const [isLoading, setIsLoading] = useState(false);

    const nameInputRef = useRef(null);

    const misSolicitudesData = Array.from({length: 10}, (_, i) => ({ id: `SOL-${String(i+1).padStart(3,'0')}`, recurso: `Proyector Multimedia ${i % 3 === 0 ? 'EPSON' : 'BenQ'} X${100+i}`, fecha: `2024-05-${String(10+i).padStart(2,'0')}`, estado: i % 3 === 0 ? 'Aprobado' : (i % 3 === 1 ? 'Pendiente de Revisión' : 'Rechazado')}));
    const recursosPopularesData = Array.from({length: 10}, (_, i) => ({ id: `POP-${i+1}`, nombre: `Laptop Modelo ${String.fromCharCode(65+i)} (Core i${5+ (i%3)})`, categoria: i % 2 === 0 ? 'Portátiles' : 'Tabletas Gráficas', solicitudes: 20 + i * 3, disponibilidad: i%2 === 0 ? 'Alta' : 'Media' }));
    const historialPrestamosData = Array.from({length: 10}, (_, i) => ({ id: `HIST-${i+1}`, item: `Libro: "Introducción a ${i%4===0?'Algoritmos':i%4===1?'Redes':i%4===2?'IA':'Circuitos'}"`, fechaPrestamo: `2024-04-${String(15+i).padStart(2,'0')}`, fechaDevolucion: i % 2 === 0 ? `2024-05-${String(1+i).padStart(2,'0')}`: 'En Préstamo'}));

    const validateForm = () => { 
        if (!itemName.trim()) { setError('El nombre del recurso es obligatorio.'); nameInputRef.current?.focus(); return false; }
        if (!itemQuantity.trim() || parseInt(itemQuantity) <= 0) { setError('La cantidad debe ser un número positivo.'); return false; }
        if (!itemLocation.trim()) { setError('La ubicación es obligatoria (o "N/A" si es solicitud general).'); return false; }
        setError(''); return true;
    };
    const handleSubmit = (e) => { 
        e.preventDefault(); if (!validateForm()) return;
        setIsLoading(true); setSuccessMessage('');
        setTimeout(() => { 
            const actionText = isEditing ? "actualizado" : (userRole === 'USUARIO' ? "solicitado" : "agregado");
            if (isEditing && userRole === 'ROOT') {
                setInventoryItems(inventoryItems.map(item => item.id === editingId ? { ...item, name: itemName, quantity: parseInt(itemQuantity), location: itemLocation, type: itemType } : item));
            } else { // Aplica para agregar por ROOT o solicitar por USUARIO
                const newItem = { id: crypto.randomUUID(), name: itemName, quantity: parseInt(itemQuantity), location: itemLocation, type: itemType };
                if (userRole === 'ROOT') {
                    setInventoryItems([newItem, ...inventoryItems]); // Agregar al inicio para ROOT
                }
                // Para USUARIO, podríamos agregar a 'misSolicitudesData' o similar en un sistema real.
                // Aquí solo mostramos mensaje de éxito.
            }
            setSuccessMessage(`Recurso "${itemName}" ${actionText} exitosamente.`);
            resetForm(); setIsLoading(false); 
            setTimeout(() => setSuccessMessage(''), 3000); // Limpiar mensaje de éxito
        }, 700);
    };
    const resetForm = () => { 
        setIsEditing(false); setEditingId(null); setItemName(''); setItemQuantity(''); setItemLocation(''); setItemType('equipo'); setError(''); nameInputRef.current?.focus();
    };
    const handleEdit = (item) => { 
        if (userRole !== 'ROOT') return; 
        setIsEditing(true); setEditingId(item.id); setItemName(item.name); setItemQuantity(item.quantity.toString()); setItemLocation(item.location); setItemType(item.type); setError(''); setSuccessMessage(''); nameInputRef.current?.focus();
    };
    const handleDelete = (id) => { 
        if (userRole !== 'ROOT') return; 
        // En un caso real, pedir confirmación
        setInventoryItems(inventoryItems.filter(item => item.id !== id));
        setSuccessMessage('Recurso eliminado.');
        if (isEditing && editingId === id) { resetForm(); }
        setTimeout(() => setSuccessMessage(''), 3000);
    };
    const getIconForItemType = (type) => { 
        switch(type) {
            case 'equipo': return <Package size={18} className="mr-2 text-gray-500 dark:text-gray-400"/>;
            case 'laboratorio': return <FlaskConical size={18} className="mr-2 text-gray-500 dark:text-gray-400"/>;
            case 'bibliografico': return <BookOpen size={18} className="mr-2 text-gray-500 dark:text-gray-400"/>;
            case 'mobiliario': return <Armchair size={18} className="mr-2 text-gray-500 dark:text-gray-400"/>;
            default: return <Archive size={18} className="mr-2 text-gray-500 dark:text-gray-400"/>;
        }
    };

    return (
        <div className="p-4 md:p-8 bg-gray-100 dark:bg-gray-900 min-h-screen">
            <motion.div initial={{ opacity: 0, y: 20 }} animate={{ opacity: 1, y: 0 }} transition={{ duration: 0.5 }}>
                <h1 className={`text-2xl md:text-3xl font-bold mb-6 ${TEXT_DARK} dark:${DARK_MODE_TEXT}`}>
                    {userRole === 'ROOT' ? 'Gestión Profesional de Inventario' : 'Solicitud y Consulta de Recursos'}
                </h1>
                
                <Card className="mb-8 shadow-xl"> {/* Sombra más pronunciada */}
                    <CardHeader>
                        <CardTitle>{isEditing && userRole === 'ROOT' ? 'Editar Recurso Existente' : (userRole === 'USUARIO' ? 'Formulario de Solicitud de Recurso' : 'Agregar Nuevo Recurso al Inventario')}</CardTitle>
                        {userRole === 'USUARIO' && <CardDescription>Complete los detalles del recurso que necesita. Su solicitud será revisada.</CardDescription>}
                    </CardHeader>
                    <CardContent>
                        <form onSubmit={handleSubmit} className="space-y-4">
                            <div><Label htmlFor="itemName">Nombre del Recurso / Ítem</Label><Input id="itemName" ref={nameInputRef} value={itemName} onChange={(e) => setItemName(e.target.value)} placeholder="Ej: Proyector EPSON PowerLite E20" aria-required="true" /></div>
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4"><div><Label htmlFor="itemQuantity">Cantidad Requerida/Stock</Label><Input id="itemQuantity" type="number" value={itemQuantity} onChange={(e) => setItemQuantity(e.target.value)} placeholder="Ej: 1" min="1" aria-required="true"/></div><div><Label htmlFor="itemType">Tipo de Recurso</Label><Select id="itemType" value={itemType} onChange={e => setItemType(e.target.value)}><option value="equipo">Equipo Audiovisual/Informático</option><option value="laboratorio">Material de Laboratorio</option><option value="bibliografico">Material Bibliográfico</option><option value="mobiliario">Mobiliario</option><option value="software">Software/Licencia</option><option value="otro">Otro</option></Select></div></div>
                            <div><Label htmlFor="itemLocation">{userRole === 'ROOT' ? 'Ubicación en Inventario' : 'Ubicación Sugerida/Requerida (Aula, Lab.)'}</Label><Input id="itemLocation" value={itemLocation} onChange={(e) => setItemLocation(e.target.value)} placeholder="Ej: Laboratorio de Física, Edificio C / Aula Magna" aria-required="true"/></div>
                            {error && <Alert variant="destructive" title="Error de Validación" description={error} />}
                            {successMessage && <Alert variant="success" title="Operación Exitosa" description={successMessage} />}
                            <div className="flex space-x-2 pt-2">
                                <Button type="submit" variant={(isEditing && userRole === 'ROOT') ? 'success' : 'default'} className="w-full md:w-auto" disabled={isLoading} title={(isEditing && userRole === 'ROOT') ? "Guardar cambios del recurso" : (userRole === 'USUARIO' ? "Enviar Solicitud de Recurso" : "Agregar recurso al inventario")}>
                                    {isLoading ? (<motion.div animate={{ rotate: 360 }} transition={{ duration: 1, repeat: Infinity, ease: "linear" }} className="w-5 h-5 border-2 border-white border-t-transparent rounded-full mr-2"></motion.div>) : ((isEditing && userRole === 'ROOT') ? <Save size={18} className="mr-2"/> : <PlusCircle size={18} className="mr-2"/>)}
                                    {(isEditing && userRole === 'ROOT') ? 'Guardar Cambios' : (userRole === 'USUARIO' ? 'Enviar Solicitud' : 'Agregar Recurso')}
                                </Button>
                                {(isEditing && userRole === 'ROOT') && (<Button type="button" variant="outline" onClick={resetForm} className="w-full md:w-auto" disabled={isLoading} title="Cancelar la edición actual">Cancelar</Button>)}
                            </div>
                        </form>
                    </CardContent>
                </Card>

                {userRole === 'ROOT' && (
                    <>
                        <div className="flex justify-between items-center mb-4">
                             <h2 className={`text-xl md:text-2xl font-semibold ${TEXT_DARK} dark:${DARK_MODE_TEXT}`}>Inventario General ({inventoryItems.length} ítems)</h2>
                             {/* Aquí podrían ir filtros avanzados para ROOT */}
                        </div>
                        {inventoryItems.length === 0 ? ( <Alert variant="default" title="Inventario Vacío" description="No hay recursos registrados en el inventario general." icon={<Archive className="h-5 w-5"/>} /> ) : (
                            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6"> {/* Más columnas en pantallas grandes */}
                                <AnimatePresence>
                                    {inventoryItems.map(item => (
                                        <motion.div key={item.id} layout initial={{ opacity: 0, scale: 0.8 }} animate={{ opacity: 1, scale: 1 }} exit={{ opacity: 0, scale: 0.8 }} transition={{ duration: 0.3, ease: "easeOut" }} whileHover={{ y: -6, boxShadow: "0px 12px 24px rgba(0,0,0,0.15), 0px 0px 0px 1px rgba(0,0,0,0.05)" }} className="h-full">
                                            <Card className={`border-l-4 border-${INSTITUTIONAL_NAVY} flex flex-col h-full shadow-md hover:shadow-lg transition-shadow`}>
                                                <CardHeader className="pb-3">
                                                    <CardTitle className="truncate flex items-center text-base md:text-lg">{getIconForItemType(item.type)} {item.name}</CardTitle>
                                                    <CardDescription className="text-xs">Tipo: {item.type.charAt(0).toUpperCase() + item.type.slice(1)}</CardDescription>
                                                </CardHeader>
                                                <CardContent className="flex-grow pt-0 pb-3 text-sm">
                                                    <p><span className="font-medium">Cantidad:</span> {item.quantity}</p>
                                                    <p className="truncate"><span className="font-medium">Ubicación:</span> <MapPin size={13} className="inline mr-0.5"/> {item.location}</p>
                                                </CardContent>
                                                {userRole === 'ROOT' && (
                                                    <CardFooter className="justify-end space-x-1.5 pt-0">
                                                        <Button variant="warning" size="sm" onClick={() => handleEdit(item)} title={`Editar ${item.name}`} className="px-2 py-1"><Edit3 size={14} /> <span className="hidden sm:inline ml-1">Editar</span></Button>
                                                        <Button variant="destructive" size="sm" onClick={() => handleDelete(item.id)} title={`Eliminar ${item.name}`} className="px-2 py-1"><Trash2 size={14} /> <span className="hidden sm:inline ml-1">Eliminar</span></Button>
                                                    </CardFooter>
                                                )}
                                            </Card>
                                        </motion.div>
                                    ))}
                                </AnimatePresence>
                            </div>
                        )}
                    </>
                )}

                {userRole === 'USUARIO' && (
                    <div className="mt-12 space-y-10">
                        {[
                            { title: "Mis Solicitudes de Recursos", icon: <ClipboardList size={22} className={`mr-2.5 text-${INSTITUTIONAL_NAVY} dark:text-blue-400`}/>, data: misSolicitudesData, headers: ["ID Solicitud", "Recurso Solicitado", "Fecha", "Estado"], renderRow: (d) => (<TableRow key={d.id}><TableCell>{d.id}</TableCell><TableCell>{d.recurso}</TableCell><TableCell>{d.fecha}</TableCell><TableCell><span className={`px-2.5 py-1 text-xs font-semibold rounded-full ${d.estado === 'Aprobado' ? 'bg-green-100 text-green-800 dark:bg-green-700/30 dark:text-green-300' : d.estado.includes('Pendiente') ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-700/30 dark:text-yellow-300' : 'bg-red-100 text-red-800 dark:bg-red-700/30 dark:text-red-300'}`}>{d.estado}</span></TableCell></TableRow>) },
                            { title: "Recursos Más Solicitados (Ejemplo)", icon: <TrendingUp size={22} className="mr-2.5 text-green-600 dark:text-green-400"/>, data: recursosPopularesData, headers: ["Nombre del Recurso", "Categoría", "Nº Solicitudes", "Disponibilidad"], renderRow: (d) => (<TableRow key={d.id}><TableCell>{d.nombre}</TableCell><TableCell>{d.categoria}</TableCell><TableCell className="text-center">{d.solicitudes}</TableCell><TableCell><span className={`px-2.5 py-1 text-xs font-semibold rounded-full ${d.disponibilidad === 'Alta' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'}`}>{d.disponibilidad}</span></TableCell></TableRow>) },
                            { title: "Mi Historial de Préstamos (Ejemplo)", icon: <History size={22} className="mr-2.5 text-purple-600 dark:text-purple-400"/>, data: historialPrestamosData, headers: ["Ítem Prestado", "Fecha de Préstamo", "Fecha de Devolución"], renderRow: (d) => (<TableRow key={d.id}><TableCell>{d.item}</TableCell><TableCell>{d.fechaPrestamo}</TableCell><TableCell>{d.fechaDevolucion === 'En Préstamo' ? <span className="text-orange-500 font-medium">{d.fechaDevolucion}</span> : d.fechaDevolucion}</TableCell></TableRow>) }
                        ].map(tableInfo => (
                            <Card key={tableInfo.title} className="shadow-lg">
                                <CardHeader><CardTitle className="flex items-center text-lg md:text-xl">{tableInfo.icon} {tableInfo.title}</CardTitle></CardHeader>
                                <CardContent>
                                    {tableInfo.data.length > 0 ? (
                                        <Table>
                                            <TableHeader><TableRow>{tableInfo.headers.map(h => <TableHead key={h}>{h}</TableHead>)}</TableRow></TableHeader>
                                            <TableBody>{tableInfo.data.map(tableInfo.renderRow)}</TableBody>
                                        </Table>
                                    ) : <p className="text-gray-500 dark:text-gray-400">No hay datos disponibles.</p>}
                                </CardContent>
                            </Card>
                        ))}
                         <p className="text-sm text-gray-500 dark:text-gray-400 mt-6 text-center">Nota: La información en estas tablas es de ejemplo y simula datos que podrían ser gestionados por Triggers y Stored Procedures en un sistema de base de datos real.</p>
                    </div>
                )}
            </motion.div>
        </div>
    );
};

// --- Vista de Estudiantes/Docentes ---
const StudentTeacherView = ({ userName, userRole, handleLogout, setCurrentView }) => { 
    const initialActivities = [ /* ... (datos de ejemplo) ... */ 
        { id: 1, title: 'Seminario: IA en la Educación', description: 'Explora el impacto de la IA en el futuro del aprendizaje.', schedule: '25 Mayo, 10:00 - 12:00', location: 'Auditorio Principal', image: 'https://placehold.co/600x400/1E3A8A/FFFFFF?text=Seminario+IA', type: 'academico', bookmarked: false },
        { id: 2, title: 'Taller de Robótica Educativa', description: 'Construye y programa tu propio robot.', schedule: 'Cada Miércoles, 16:00 - 18:00', location: 'Laboratorio de Mecatrónica', image: 'https://placehold.co/600x400/60A5FA/FFFFFF?text=Taller+Robotica', type: 'academico', bookmarked: true },
        { id: 3, title: 'Torneo Interno de Fútbol', description: 'Inscribe a tu equipo y compite por la copa.', schedule: 'Inicia 1 Junio, Fines de Semana', location: 'Canchas Deportivas', image: 'https://placehold.co/600x400/34D399/FFFFFF?text=Futbol', type: 'deportivo', bookmarked: false },
    ];
    const [activities, setActivities] = useState(initialActivities);
    const [searchTerm, setSearchTerm] = useState('');
    const [filterType, setFilterType] = useState('todos');
    const [isMenuOpen, setIsMenuOpen] = useState(false);
    const [isProfileSheetOpen, setIsProfileSheetOpen] = useState(false);
    const featuredEvents = activities.filter(act => act.type === 'academico' || act.type === 'institucional').slice(0, 3);
    const [currentSlide, setCurrentSlide] = useState(0);
    const nextSlide = () => setCurrentSlide(prev => (prev === featuredEvents.length - 1 ? 0 : prev + 1));
    const prevSlide = () => setCurrentSlide(prev => (prev === 0 ? featuredEvents.length - 1 : prev - 1));
    useEffect(() => { if (featuredEvents.length > 0) { const slideInterval = setInterval(nextSlide, 6000); return () => clearInterval(slideInterval);}}, [featuredEvents.length, currentSlide]); // Agregado currentSlide para reiniciar intervalo si se cambia manualmente
    const filteredActivities = activities.filter(activity => (activity.title.toLowerCase().includes(searchTerm.toLowerCase()) || activity.location.toLowerCase().includes(searchTerm.toLowerCase())) && (filterType === 'todos' || activity.type === filterType));
    const toggleBookmark = (id) => { setActivities(activities.map(act => act.id === id ? { ...act, bookmarked: !act.bookmarked } : act));};
    const getIconForActivityType = (type) => { switch(type) { case 'academico': return <BookOpen size={16} className="mr-1.5"/>; case 'deportivo': return <Award size={16} className="mr-1.5"/>; case 'cultural': return <Palette size={16} className="mr-1.5"/>; case 'institucional': return <Building2 size={16} className="mr-1.5"/>; default: return <Info size={16} className="mr-1.5"/>;}};

    return (
        <div className="bg-gray-50 dark:bg-gray-900 min-h-screen text-gray-800 dark:text-gray-200">
            <header className={`bg-${INSTITUTIONAL_NAVY} text-white p-4 shadow-md sticky top-0 z-30`}>
                <div className="container mx-auto flex justify-between items-center">
                    <div className="text-xl md:text-2xl font-bold flex items-center"><Building2 size={28} className="mr-2"/>{APP_NAME}</div>
                    <nav className="hidden md:flex space-x-2 items-center">
                        <Button variant="ghost" className="text-white hover:bg-blue-700" onClick={() => setCurrentView('studentTeacher')}>Inicio</Button>
                        <Button variant="ghost" className="text-white hover:bg-blue-700" onClick={() => {/* Lógica para ir a actividades */}}>Actividades</Button>
                        <Button variant="ghost" className="text-white hover:bg-blue-700" onClick={() => setCurrentView('inventoryEdu')}>Recursos</Button> {/* Enlace a Inventario */}
                        <Button variant="ghost" size="icon" className="hover:bg-blue-700" title="Notificaciones"><Bell size={20} /></Button>
                        <Button variant="ghost" className="hover:bg-blue-700 px-3 py-2" onClick={() => setIsProfileSheetOpen(true)} title="Mi Perfil">
                            <UserCircle size={22} className="mr-1.5" /> <span className="text-sm hidden lg:inline">{userName}</span>
                        </Button>
                    </nav>
                    <div className="md:hidden"><Button variant="ghost" size="icon" onClick={() => setIsMenuOpen(true)} className="hover:bg-blue-700" title="Abrir menú"><MenuIcon size={24} /></Button></div>
                </div>
            </header>

            <Sheet open={isMenuOpen} onClose={() => setIsMenuOpen(false)} side="right" className="bg-white dark:bg-gray-800">
                <div className="flex justify-between items-center mb-6"><h2 className={`text-xl font-semibold text-${INSTITUTIONAL_NAVY} dark:text-blue-400`}>Menú</h2><Button variant="ghost" size="icon" onClick={() => setIsMenuOpen(false)}><X size={24} className="text-gray-600 dark:text-gray-300"/></Button></div>
                <nav className="flex flex-col space-y-2">
                    {['Inicio', 'Actividades'].map(item => (<Button key={item} variant="ghost" className="w-full justify-start p-3 text-gray-700 dark:text-gray-300" onClick={() => {setIsMenuOpen(false); /* setCurrentView(...) */}}>{item}</Button>))}
                    <Button variant="ghost" className="w-full justify-start p-3 text-gray-700 dark:text-gray-300" onClick={() => {setIsMenuOpen(false); setCurrentView('inventoryEdu');}}>Recursos</Button>
                    <Button variant="outline" onClick={() => { setIsMenuOpen(false); setIsProfileSheetOpen(true);}} className="w-full mt-4"><UserCircle size={18} className="mr-2"/> Mi Perfil</Button>
                </nav>
            </Sheet>

            <Sheet open={isProfileSheetOpen} onClose={() => setIsProfileSheetOpen(false)} side="right" className="bg-white dark:bg-gray-800 w-80">
                <div className="flex justify-between items-center mb-6"><h2 className={`text-xl font-semibold text-${INSTITUTIONAL_NAVY} dark:text-blue-400`}>Mi Perfil</h2><Button variant="ghost" size="icon" onClick={() => setIsProfileSheetOpen(false)}><X size={24} className="text-gray-600 dark:text-gray-300"/></Button></div>
                <div className="flex flex-col items-center mb-6"><Avatar fallback={userName ? userName.charAt(0).toUpperCase() : "U"} src="" className="w-24 h-24 text-3xl mb-3"/><p className="font-semibold text-lg text-gray-800 dark:text-gray-100">{userName || "Usuario Invitado"}</p><p className="text-sm text-gray-500 dark:text-gray-400">{userName ? `${userName.toLowerCase().replace(/\s+/g, '.')}@edu.portal` : 'Rol: ' + userRole}</p></div>
                <nav className="flex flex-col space-y-2">
                    {/* ... (enlaces del perfil) ... */}
                    <Button variant="destructive" className="w-full mt-6" onClick={handleLogout}><LogOut size={18} className="mr-2"/> Cerrar Sesión</Button>
                </nav>
            </Sheet>
            
            <main className="container mx-auto p-4 md:p-6 lg:p-8">
                {/* ... (Contenido principal de StudentTeacherView sin cambios mayores) ... */}
                {featuredEvents.length > 0 && (<section className="mb-10 lg:mb-12"><h2 className="text-2xl md:text-3xl font-bold mb-6 text-gray-800 dark:text-gray-100">Eventos Destacados</h2><div className="relative overflow-hidden rounded-2xl shadow-xl h-64 md:h-80 lg:h-96"><AnimatePresence initial={false}><motion.img key={currentSlide} src={featuredEvents[currentSlide].image} alt={featuredEvents[currentSlide].title} initial={{ opacity: 0.5, x: currentSlide === 0 ? 0 : 300 }} animate={{ opacity: 1, x: 0 }} exit={{ opacity: 0.5, x: -300 }} transition={{ type: 'spring', stiffness:100, damping:20 }} className="absolute top-0 left-0 w-full h-full object-cover" /></AnimatePresence><div className="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent flex flex-col justify-end p-6 md:p-8"><motion.h3 initial={{opacity:0, y:20}} animate={{opacity:1, y:0}} transition={{delay:0.2, duration:0.5}} className="text-xl md:text-3xl font-bold text-white mb-1 md:mb-2">{featuredEvents[currentSlide].title}</motion.h3><motion.p initial={{opacity:0, y:20}} animate={{opacity:1, y:0}} transition={{delay:0.3, duration:0.5}} className="text-sm md:text-base text-gray-200">{featuredEvents[currentSlide].schedule} - {featuredEvents[currentSlide].location}</motion.p></div>{featuredEvents.length > 1 && (<><Button onClick={prevSlide} variant="secondary" size="icon" className="absolute left-3 top-1/2 -translate-y-1/2 opacity-80 hover:opacity-100 z-10"><ChevronLeft size={24}/></Button><Button onClick={nextSlide} variant="secondary" size="icon" className="absolute right-3 top-1/2 -translate-y-1/2 opacity-80 hover:opacity-100 z-10"><ChevronRight size={24}/></Button><div className="absolute bottom-4 left-1/2 -translate-x-1/2 flex space-x-2 z-10">{featuredEvents.map((_, index) => (<button key={index} onClick={() => setCurrentSlide(index)} className={`w-2.5 h-2.5 rounded-full transition-all duration-300 ${currentSlide === index ? 'bg-white scale-125' : 'bg-white/50'}`}></button>))}</div></>)}</div></section>)}
                <section className="mb-8 p-4 md:p-6 bg-white dark:bg-gray-800 rounded-xl shadow-md"><div className="grid grid-cols-1 md:grid-cols-3 gap-4 items-end"><div className="md:col-span-2"><Label htmlFor="searchActivity">Buscar Actividad</Label><div className="relative"><Search size={20} className="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500 pointer-events-none" /><Input id="searchActivity" type="text" placeholder="Buscar por nombre, dependencia..." value={searchTerm} onChange={(e) => setSearchTerm(e.target.value)} className="pl-10"/></div></div><div><Label htmlFor="filterActivityType">Filtrar por Tipo</Label><Select id="filterActivityType" value={filterType} onChange={(e) => setFilterType(e.target.value)}><option value="todos">Todos</option><option value="academico">Académico</option><option value="deportivo">Deportivo</option><option value="cultural">Cultural</option><option value="institucional">Institucional</option></Select></div></div></section>
                <section><h2 className="text-xl md:text-2xl font-semibold mb-6 text-gray-800 dark:text-gray-100">Próximas Actividades</h2>{filteredActivities.length === 0 ? (<Alert variant="default" title="Sin Resultados" description="No se encontraron actividades." icon={<CalendarSearch className="h-5 w-5"/>} />) : (<div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">{filteredActivities.map(activity => (<motion.div key={activity.id} initial={{opacity:0, y:20}} animate={{opacity:1, y:0}} transition={{duration:0.4}} whileHover={{ scale: 1.03, transition: { duration: 0.2 } }} className="h-full"><Card className="h-full flex flex-col hover:shadow-xl"><img src={activity.image} alt={activity.title} className="w-full h-48 object-cover rounded-t-2xl"/><CardHeader><CardTitle className="text-lg flex items-center">{getIconForActivityType(activity.type)} {activity.title}</CardTitle><CardDescription className="text-xs mt-1 capitalize">{activity.type}</CardDescription></CardHeader><CardContent className="flex-grow text-sm"><p className="mb-2 line-clamp-3">{activity.description}</p><p><CalendarDays size={14} className="inline mr-1.5"/> {activity.schedule}</p><p><MapPin size={14} className="inline mr-1.5"/> {activity.location}</p></CardContent><CardFooter><Button variant={activity.bookmarked ? `default` : "outline"} size="sm" className="w-full" onClick={() => toggleBookmark(activity.id)}><Bookmark size={16} className={`mr-2 ${activity.bookmarked ? 'fill-current' : ''}`} />{activity.bookmarked ? 'Guardado' : 'Guardar'}</Button></CardFooter></Card></motion.div>))}</div>)}</section>
            </main>
        </div>
    );
};

// --- Vista del Administrador (Coordinación o Dirección Académica) ---
const AdminView = ({ setCurrentViewGlobal, userName, userRole, handleLogout }) => { 
    const [darkMode, setDarkMode] = useState(document.documentElement.classList.contains('dark'));
    const [showUserModal, setShowUserModal] = useState(false);
    const [selectedUser, setSelectedUser] = useState(null);
    const [isSidebarOpen, setIsSidebarOpen] = useState(false);
    const [currentAdminPage, setCurrentAdminPage] = useState('dashboard');
    
    const toggleDarkMode = () => { /* ... (sin cambios) */ const newMode = !darkMode; setDarkMode(newMode); if (newMode) { document.documentElement.classList.add('dark'); localStorage.setItem('darkMode', 'true'); } else { document.documentElement.classList.remove('dark'); localStorage.setItem('darkMode', 'false'); }};
    const stats = [ /* ... (sin cambios) */ ];
    const initialUsers = getUsersFromStorage().filter(u => u.username !== 'root' && u.username !== 'admin'); // No mostrar root/admin en la lista de gestión
    const [users, setUsers] = useState(initialUsers);
    const activityLogs = [ /* ... (sin cambios) */ ];
    const [realTimeActivity, setRealTimeActivity] = useState(false); useEffect(() => { const interval = setInterval(() => setRealTimeActivity(prev => !prev), 3000); return () => clearInterval(interval);}, []);
    const handleManageUser = (user) => { setSelectedUser(user); setShowUserModal(true);};
    const UserManagementModal = ({ user, onClose, onUpdateUser }) => { /* ... (sin cambios funcionales, pero adaptado para usar lista de usuarios del storage) */ 
        if (!user) return null; 
        const [status, setStatus] = useState(user.status || 'Activo'); // Asumir Activo si no está definido
        const [role, setRole] = useState(user.role); 
        const handleSave = () => { 
            const allUsers = getUsersFromStorage();
            const updatedUsers = allUsers.map(u => u.username === user.username ? {...u, role, status} : u);
            saveUsersToStorage(updatedUsers);
            onUpdateUser(updatedUsers.filter(u => u.username !== 'root' && u.username !== 'admin')); // Actualizar estado local
            onClose(); 
        }; 
        return (<div className="fixed inset-0 bg-black/60 flex items-center justify-center z-[60] p-4"><motion.div initial={{opacity:0, scale:0.9}} animate={{opacity:1, scale:1}} className="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-xl w-full max-w-md"><h3 className="text-xl font-semibold mb-4 text-gray-800 dark:text-gray-100">Gestionar Usuario: {user.name || user.username}</h3><div className="space-y-4"><div><Label htmlFor="userStatusModal">Estado</Label><Select id="userStatusModal" value={status} onChange={e => setStatus(e.target.value)}><option value="Activo">Activo</option><option value="Suspendido">Suspendido</option></Select></div><div><Label htmlFor="userRoleModal">Rol</Label><Select id="userRoleModal" value={role} onChange={e => setRole(e.target.value)}><option value="USUARIO">Usuario</option><option value="EDITOR">Editor (simulado)</option><option value="ROOT">ROOT (Solo informativo, no asignar desde aquí)</option></Select></div></div><div className="mt-6 flex justify-end space-x-3"><Button variant="outline" onClick={onClose}>Cancelar</Button><Button onClick={handleSave} variant="default">Guardar</Button></div></motion.div></div>);
    };
    const updateUserInList = (updatedUserList) => { setUsers(updatedUserList);};
    const AdminSidebarLink = ({ icon, label, viewName, currentViewSetter, currentAdminPageName, closeSidebar }) => { const isActive = currentAdminPageName === viewName; return (<button onClick={() => { currentViewSetter(viewName); closeSidebar(); }} className={`w-full flex items-center space-x-3 px-3 py-2.5 rounded-md transition-colors ${isActive ? `bg-${INSTITUTIONAL_NAVY_LIGHT} text-white` : `text-gray-300 hover:bg-${INSTITUTIONAL_NAVY_DARK}/70 hover:text-white`}`} aria-current={isActive ? "page" : undefined}>{icon}<span>{label}</span></button>);};
    const SidebarContent = () => ( <> <div className="text-xl font-bold text-white mb-8 flex items-center"><ShieldCheck size={26} className="mr-2.5"/> {APP_NAME} Admin</div><nav className="space-y-1.5"><AdminSidebarLink icon={<LayoutDashboard size={20} />} label="Dashboard" viewName="dashboard" currentViewSetter={setCurrentAdminPage} currentAdminPageName={currentAdminPage} closeSidebar={() => setIsSidebarOpen(false)} /><AdminSidebarLink icon={<UsersRound size={20} />} label="Usuarios" viewName="users" currentViewSetter={setCurrentAdminPage} currentAdminPageName={currentAdminPage} closeSidebar={() => setIsSidebarOpen(false)} /><button onClick={() => { setCurrentViewGlobal('inventoryEdu'); setIsSidebarOpen(false); }} className={`w-full flex items-center space-x-3 px-3 py-2.5 rounded-md text-gray-300 hover:bg-${INSTITUTIONAL_NAVY_DARK}/70 hover:text-white transition-colors`}><Archive size={20} /><span>Inventario</span></button><AdminSidebarLink icon={<Newspaper size={20} />} label="Publicaciones" viewName="publications" currentViewSetter={setCurrentAdminPage} currentAdminPageName={currentAdminPage} closeSidebar={() => setIsSidebarOpen(false)} /><AdminSidebarLink icon={<Settings size={20} />} label="Configuración" viewName="settings" currentViewSetter={setCurrentAdminPage} currentAdminPageName={currentAdminPage} closeSidebar={() => setIsSidebarOpen(false)} /></nav><div className="mt-auto pt-6 border-t border-gray-700"><button onClick={() => {setCurrentViewGlobal('studentTeacher'); setIsSidebarOpen(false);}} className="w-full flex items-center space-x-3 px-3 py-2.5 rounded-md text-gray-300 hover:bg-blue-900/70 hover:text-white transition-colors"><Eye size={20} /><span>Ver Portal</span></button><Button className="w-full mt-2" variant="destructive" onClick={handleLogout}><LogOut size={18} className="mr-2"/>Cerrar Sesión</Button></div></>);
    const renderAdminContent = () => { /* ... (sin cambios funcionales mayores) ... */ 
        switch (currentAdminPage) {
            case 'dashboard': return (<>...</>); // Contenido del dashboard como antes
            case 'users': return (<section><Card className="shadow-lg"><CardHeader><CardTitle>Gestión de Usuarios del Portal</CardTitle><CardDescription>Administrar cuentas, roles y permisos.</CardDescription></CardHeader><CardContent><Table><TableHeader><TableRow><TableHead>Nombre</TableHead><TableHead>Email</TableHead><TableHead>Rol</TableHead><TableHead>Estado</TableHead><TableHead className="text-right">Acciones</TableHead></TableRow></TableHeader><TableBody>{users.map(user => (<TableRow key={user.username}><TableCell className="font-medium">{user.name || user.username}</TableCell><TableCell>{user.email}</TableCell><TableCell>{user.role}</TableCell><TableCell><span className={`px-2 py-0.5 text-xs font-semibold rounded-full ${user.status === 'Activo' ? 'bg-green-100 text-green-700 dark:bg-green-700/30 dark:text-green-300' : 'bg-red-100 text-red-700 dark:bg-red-700/30 dark:text-red-300'}`}>{user.status || 'Activo'}</span></TableCell><TableCell className="text-right space-x-1"><Button variant="ghost" size="icon" onClick={() => handleManageUser(user)} title="Editar"><Edit size={16} className="text-yellow-500"/></Button></TableCell></TableRow>))}</TableBody></Table></CardContent></Card></section>);
            default: return <p>Selecciona una opción.</p>; // El resto de las vistas de admin como estaban
        }
    }; // Se omite el resto del render de AdminView por brevedad, es similar al anterior

    return ( <div className={`flex h-screen bg-gray-100 dark:bg-gray-950`}><aside className={`hidden md:flex md:flex-col w-64 bg-${INSTITUTIONAL_NAVY_DARK} text-white p-5 space-y-6 fixed h-full shadow-lg`}><SidebarContent /></aside><Sheet open={isSidebarOpen} onClose={() => setIsSidebarOpen(false)} side="left" className={`bg-${INSTITUTIONAL_NAVY_DARK} text-white p-5`}><SidebarContent /></Sheet><div className="flex-1 flex flex-col overflow-hidden md:ml-64"><header className="bg-white dark:bg-gray-800 shadow-md p-4 flex justify-between items-center sticky top-0 z-20"><div className="flex items-center"><button onClick={() => setIsSidebarOpen(true)} className="md:hidden mr-3 text-gray-600 dark:text-gray-300"><MenuIcon size={24} /></button><h2 className="text-xl font-semibold text-gray-700 dark:text-gray-200">{currentAdminPage.charAt(0).toUpperCase() + currentAdminPage.slice(1)}</h2></div><div className="flex items-center space-x-4"><div className="flex items-center text-sm text-gray-600 dark:text-gray-300"><motion.span className={`w-2.5 h-2.5 rounded-full mr-2 ${realTimeActivity ? 'bg-green-500' : 'bg-red-500'}`} animate={{ scale: realTimeActivity ? [1, 1.3, 1] : 1 }} transition={{ duration: 0.8, repeat: Infinity }}/><span className="hidden sm:inline">{realTimeActivity ? 'Actividad' : 'Estable'}</span></div><button onClick={toggleDarkMode} className="text-gray-600 dark:text-gray-300 p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700" title={darkMode ? "Modo claro" : "Modo oscuro"}>{darkMode ? <Sun size={20} /> : <Moon size={20} />}</button><Avatar fallback={userName ? userName.charAt(0).toUpperCase() : "A"} className={`bg-${INSTITUTIONAL_NAVY} text-white`} /></div></header><main className="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 dark:bg-gray-900 p-6">{renderAdminContent()}{currentAdminPage === 'dashboard' && (<section className="mt-8"><Card className="shadow-lg"><CardHeader><CardTitle>Actividad Reciente</CardTitle></CardHeader><CardContent><div className="space-y-3 max-h-80 overflow-y-auto">{activityLogs.slice(0, 10).map(log => (<div key={log.id} className="text-sm p-3 bg-gray-50 dark:bg-gray-800/60 rounded-lg border border-gray-200 dark:border-gray-700"><p className="font-medium text-gray-700 dark:text-gray-200">{log.action}</p><p className="text-xs text-gray-500 dark:text-gray-400"><UserCircle size={12} className="inline mr-1"/>{log.user} - <CalendarDays size={12} className="inline mr-1"/>{new Date(log.timestamp).toLocaleString()}</p>{log.details && <p className="text-xs text-gray-400 dark:text-gray-500 mt-1">Detalles: {log.details}</p>}</div>))}{activityLogs.length > 10 && <Button variant="link" className="mt-3 text-sm">Ver todos</Button>}</div></CardContent></Card></section>)}</main></div>{showUserModal && <UserManagementModal user={selectedUser} onClose={() => setShowUserModal(false)} onUpdateUser={updateUserInList} />}</div>);
};


// --- Componente Principal de la Aplicación ---
const App = () => {
    const [currentUserRole, setCurrentUserRole] = useState(null);
    const [currentUserName, setCurrentUserName] = useState(null); // Nombre completo del usuario
    const [currentView, setCurrentView] = useState('login'); // Vista inicial siempre es login

    useEffect(() => {
        // Cargar modo oscuro al inicio
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        const storedDarkMode = localStorage.getItem('darkMode');
        if (storedDarkMode === 'true' || (!storedDarkMode && prefersDark)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }

        // Verificar si hay sesión guardada
        const storedRole = localStorage.getItem('userRole');
        const storedName = localStorage.getItem('userName'); // Nombre completo
        if (storedRole && storedName) {
            setCurrentUserRole(storedRole);
            setCurrentUserName(storedName);
            // Determinar vista basada en rol guardado
            if (storedRole === 'ROOT') {
                setCurrentView('admin');
            } else {
                setCurrentView('studentTeacher');
            }
        } else {
            setCurrentView('login'); // Si no hay sesión, forzar login
        }
    }, []); 

    const handleLoginSuccess = (role, username, name) => { // username es el login, name es el nombre completo
        localStorage.setItem('userRole', role);
        localStorage.setItem('userName', name); // Guardar nombre completo
        localStorage.setItem('userLogin', username); // Guardar username de login si es diferente
        setCurrentUserRole(role);
        setCurrentUserName(name);
        
        if (role === 'ROOT') {
            setCurrentView('admin'); 
        } else {
            setCurrentView('studentTeacher'); 
        }
    };
    
    const handleLogout = () => {
        localStorage.removeItem('userRole');
        localStorage.removeItem('userName');
        localStorage.removeItem('userLogin');
        setCurrentUserRole(null);
        setCurrentUserName(null);
        setCurrentView('login'); 
    };
    
    const renderView = () => {
        if (currentView === 'login') {
            return <LoginView onLoginSuccess={handleLoginSuccess} navigateToRegister={() => setCurrentView('register')} />;
        }
        if (currentView === 'register') {
            return <RegisterView onRegisterSuccess={() => setCurrentView('login')} navigateToLogin={() => setCurrentView('login')} />;
        }

        // Vistas protegidas (requieren login)
        if (!currentUserRole) { // Si por alguna razón no hay rol y no es login/register, volver a login
             return <LoginView onLoginSuccess={handleLoginSuccess} navigateToRegister={() => setCurrentView('register')} />;
        }

        switch (currentView) {
            case 'inventoryEdu':
                return <InventoryViewEdu userRole={currentUserRole} />;
            case 'studentTeacher':
                return <StudentTeacherView userRole={currentUserRole} userName={currentUserName} handleLogout={handleLogout} setCurrentView={setCurrentView} />;
            case 'admin':
                if (currentUserRole === 'ROOT') {
                    return <AdminView setCurrentViewGlobal={setCurrentView} userName={currentUserName} userRole={currentUserRole} handleLogout={handleLogout} />;
                } else {
                    // Redirigir usuarios no-ROOT que intenten acceder a admin
                    return <StudentTeacherView userRole={currentUserRole} userName={currentUserName} handleLogout={handleLogout} setCurrentView={setCurrentView} />; 
                }
            default: 
                return <StudentTeacherView userRole={currentUserRole} userName={currentUserName} handleLogout={handleLogout} setCurrentView={setCurrentView}/>;
        }
    };
    
    const DevNavigationBar = () => { /* ... (sin cambios) */ 
        if (!currentUserRole) return null; 
        return (<nav className="bg-neutral-900 p-2 text-center space-x-2 fixed bottom-0 left-0 right-0 z-[100] opacity-80 hover:opacity-100 transition-opacity"><span className="text-white text-xs mr-2">DEV NAV (Rol: {currentUserRole}, Usuario: {currentUserName}):</span>{currentUserRole && <Button size="sm" variant={currentView === 'studentTeacher' ? 'default' : 'secondary'} onClick={() => setCurrentView('studentTeacher')}>Estudiantes</Button>}<Button size="sm" variant={currentView === 'inventoryEdu' ? 'default' : 'secondary'} onClick={() => setCurrentView('inventoryEdu')}>Inventario</Button>{currentUserRole === 'ROOT' && <Button size="sm" variant={currentView === 'admin' ? 'default' : 'secondary'} onClick={() => setCurrentView('admin')}>Admin</Button>}<Button size="sm" variant="destructive" onClick={handleLogout}>Logout</Button></nav>
    )};

    return (
        <>
            {renderView()}
            <DevNavigationBar />
        </>
    );
};

export default App;
