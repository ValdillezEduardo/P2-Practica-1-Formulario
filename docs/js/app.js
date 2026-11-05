// Claves con las que se guardan los datos en el navegador
const KEY_USERS = 'nike_usuarios';
const KEY_SESSION = 'nike_sesion';

// Lee un valor guardado; si falla, devuelve el valor por defecto
const leer = (k, def) => { try { return JSON.parse(localStorage.getItem(k)) ?? def; } catch { return def; } };
// Leer y guardar la lista de usuarios y la sesión activa
const getUsers = () => leer(KEY_USERS, []);
const saveUsers = (u) => localStorage.setItem(KEY_USERS, JSON.stringify(u));
const getSession = () => leer(KEY_SESSION, null);

// Cifra la contraseña (SHA-256) para no guardarla tal cual
async function hash(texto) {
  if (window.crypto && crypto.subtle) {
    const buf = await crypto.subtle.digest('SHA-256', new TextEncoder().encode(texto));
    return [...new Uint8Array(buf)].map(b => b.toString(16).padStart(2, '0')).join('');
  }
  // Si el navegador no soporta cifrado, usa una codificación simple
  return btoa(unescape(encodeURIComponent(texto)));
}

// Muestra mensajes de error ("alert") o de éxito ("ok") en la página
function mostrar(tipo, mensajes) {
  const caja = document.getElementById('mensajes');
  caja.innerHTML = '';
  if (!mensajes.length) return;
  const div = document.createElement('div');
  div.className = tipo;
  div.setAttribute('role', tipo === 'alert' ? 'alert' : 'status');
  mensajes.forEach(m => { const p = document.createElement('p'); p.textContent = m; div.appendChild(p); });
  caja.appendChild(div);
}

// Cada página indica su nombre en data-page; el código solo actúa en la que corresponde
const pagina = document.body.dataset.page;
// Con sesión iniciada no se muestran el registro ni el inicio de sesión
if (getSession() && (pagina === 'login' || pagina === 'register')) location.href = 'index.html';

// Registro
if (pagina === 'register') {
  document.getElementById('form').addEventListener('submit', async (ev) => {
    ev.preventDefault();
    const f = new FormData(ev.target);
    const nombre = f.get('nombre').trim(), apellido = f.get('apellido').trim();
    const email = f.get('email').trim().toLowerCase();
    const pass = f.get('password'), pass2 = f.get('password2');
    // Validaciones de los campos
    const errores = [];
    if (!nombre || !apellido) errores.push('Escribe tu nombre y apellido.');
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) errores.push('Escribe un correo válido.');
    if (pass.length < 8) errores.push('La contraseña debe tener al menos 8 caracteres.');
    if (pass !== pass2) errores.push('Las contraseñas no coinciden.');
    const usuarios = getUsers();
    if (!errores.length && usuarios.some(u => u.email === email)) errores.push('Ya existe una cuenta con ese correo. Inicia sesión.');
    if (errores.length) return mostrar('alert', errores);

    // Guarda el nuevo usuario y va al inicio de sesión
    usuarios.push({
      id: Date.now(), nombre, apellido, email,
      password_hash: await hash(pass),
      fecha_registro: new Date().toISOString()
    });
    saveUsers(usuarios);
    location.href = 'login.html?registro=ok';
  });
}

// Inicio de sesión
if (pagina === 'login') {
  // Aviso al venir del registro
  if (new URLSearchParams(location.search).get('registro') === 'ok') {
    mostrar('ok', ['Cuenta creada. Ya puedes iniciar sesión.']);
  }
  document.getElementById('form').addEventListener('submit', async (ev) => {
    ev.preventDefault();
    const f = new FormData(ev.target);
    const email = f.get('email').trim().toLowerCase();
    const h = await hash(f.get('password'));
    // Busca un usuario con ese correo y esa contraseña
    const u = getUsers().find(x => x.email === email && x.password_hash === h);
    if (!u) return mostrar('alert', ['Correo o contraseña incorrectos.']);
    // Guarda la sesión y va a la página de inicio
    localStorage.setItem(KEY_SESSION, JSON.stringify({ id: u.id, nombre: u.nombre }));
    location.href = 'index.html';
  });
}

// Página de inicio
if (pagina === 'home') {
  const nav = document.getElementById('nav');
  const s = getSession();
  // Con sesión: saludo y cerrar sesión. Sin sesión: enlaces para entrar o registrarse
  if (s) {
    const span = document.createElement('span');
    span.textContent = 'Hola, ' + s.nombre;
    const salir = document.createElement('a');
    salir.href = '#'; salir.textContent = 'Cerrar sesión';
    salir.addEventListener('click', (e) => { e.preventDefault(); localStorage.removeItem(KEY_SESSION); location.reload(); });
    nav.append(span, salir);
  } else {
    nav.innerHTML = '<a href="login.html">Iniciar sesión</a><a href="register.html">Crear cuenta</a>';
  }
}
