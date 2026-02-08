// ==========================================
// SERVIDOR API REST - CONECTADO A RAILWAY ☁️
// ==========================================

const express = require('express');
const mysql = require('mysql2/promise'); // Asegúrate de tener: npm install mysql2
const cors = require('cors');

const app = express();
const PORT = 3000;

// --- MIDDLEWARES ---
app.use(cors());
app.use(express.json());

// ==========================================
// 🔌 CONFIGURACIÓN DE CONEXIÓN A RAILWAY
// ==========================================
const db = mysql.createPool({
    // 1. HOST PÚBLICO (Búscalo en Railway -> Connect -> Public Networking)
    // NO uses 'mysql.railway.internal', usa el que termina en .net o .com
    host: 'metro.proxy.rlwy.net', 

    // 2. USUARIO (Casi siempre es 'root')
    user: 'root',

    // 3. CONTRASEÑA (Pega la contraseña larga de Railway)
    password: 'dGfYzwEgpSapEInkwoeqoxjIwfHfuTBc', 

    // 4. BASE DE DATOS (En Railway se llama 'railway' por defecto)
    database: 'railway',

    // 5. PUERTO PÚBLICO (Búscalo en Railway -> Connect -> Public Networking)
    // OJO: No suele ser 3306. Es un número de 5 dígitos (ej. 12345)
    port: 23465, 

    waitForConnections: true,
    connectionLimit: 5, // Bajamos el límite para no saturar la conexión gratuita
    queueLimit: 0
});

// Prueba de conexión al iniciar
db.getConnection()
    .then(connection => {
        console.log('✅ ¡CONECTADO EXITOSAMENTE A RAILWAY!');
        connection.release();
    })
    .catch(err => {
        console.error('❌ ERROR DE CONEXIÓN A RAILWAY:', err.message);
        console.error('👉 Verifica Host Público, Puerto y Contraseña.');
    });

// ==========================================
// 1. GESTIÓN DE CARRERAS
// ==========================================

// Obtener TODAS las carreras (Activas e Inactivas)
app.get('/api/carreras-todas', async (req, res) => {
    try {
        const [rows] = await db.query('SELECT * FROM Carreras ORDER BY nombre_carrera ASC');
        res.json(rows);
    } catch (err) {
        res.status(500).json({ error: err.message });
    }
});

// Activar/Desactivar Carrera
app.patch('/api/carreras/:id/estatus', async (req, res) => {
    const { id } = req.params;
    try {
        await db.query('UPDATE Carreras SET estatus = NOT estatus WHERE id_carrera = ?', [id]);
        const [rows] = await db.query('SELECT estatus FROM Carreras WHERE id_carrera = ?', [id]);
        res.json({ success: true, nuevoEstatus: rows[0].estatus });
    } catch (err) {
        res.status(500).json({ error: err.message });
    }
});

// ==========================================
// 2. GESTIÓN DE GRUPOS
// ==========================================

// Catálogos para el formulario (SOLO ACTIVOS)
app.get('/api/catalogos-registro', async (req, res) => {
    try {
        const [carreras] = await db.query('SELECT * FROM Carreras WHERE estatus = 1 ORDER BY nombre_carrera ASC');
        const [turnos]   = await db.query('SELECT * FROM Turnos');
        const [grados]   = await db.query('SELECT * FROM Grados ORDER BY numero_grado ASC');
        res.json({ carreras, turnos, grados });
    } catch (err) {
        res.status(500).json({ error: err.message });
    }
});

// Crear Grupo (Lógica Automática)
app.post('/api/grupos', async (req, res) => {
    let { id_carrera, id_grado, id_turno } = req.body;

    try {
        const [[carrera]] = await db.query('SELECT siglas FROM Carreras WHERE id_carrera = ?', [id_carrera]);
        const [[turnoObj]] = await db.query('SELECT sigla_turno FROM Turnos WHERE id_turno = ?', [id_turno]);
        const [[gradoObj]] = await db.query('SELECT numero_grado FROM Grados WHERE id_grado = ?', [id_grado]);

        if (!carrera) return res.status(400).json({ error: "Carrera inválida" });

        let finalTurnoId = id_turno;
        let finalTurnoSigla = turnoObj.sigla_turno;

        // Regla: 7° grado o más -> Vespertino (excepto Mixto)
        if (gradoObj.numero_grado >= 7 && finalTurnoSigla !== 'MX') {
            const [[vesp]] = await db.query("SELECT id_turno, sigla_turno FROM Turnos WHERE nombre_turno = 'Vespertino'");
            if (vesp) {
                finalTurnoId = vesp.id_turno;
                finalTurnoSigla = vesp.sigla_turno;
            }
        }

        const [[count]] = await db.query(
            'SELECT COUNT(*) as total FROM Grupos WHERE id_carrera = ? AND id_grado = ? AND id_turno = ?', 
            [id_carrera, id_grado, finalTurnoId]
        );
        
        const siguienteNum = count.total + 1;
        const strConsecutivo = siguienteNum.toString().padStart(2, '0');
        const codigo = `${carrera.siglas}${gradoObj.numero_grado}${strConsecutivo}-${finalTurnoSigla}`;

        await db.query(
            'INSERT INTO Grupos (codigo_grupo, id_carrera, id_grado, id_turno, consecutivo, estatus) VALUES (?, ?, ?, ?, ?, 1)',
            [codigo, id_carrera, id_grado, finalTurnoId, siguienteNum]
        );

        res.json({ success: true, codigo: codigo });

    } catch (err) {
        res.status(500).json({ error: err.message });
    }
});

// Listar Grupos (Filtrado por Carrera Activa)
app.get('/api/grupos', async (req, res) => {
    try {
        const [rows] = await db.query(`
            SELECT g.id_grupo, g.codigo_grupo, c.nombre_carrera, g.estatus 
            FROM Grupos g
            JOIN Carreras c ON g.id_carrera = c.id_carrera
            WHERE c.estatus = 1
            ORDER BY g.codigo_grupo ASC
        `);
        res.json(rows);
    } catch (err) {
        res.status(500).json({ error: err.message });
    }
});

// ==========================================
// 3. GESTIÓN DE ALUMNOS
// ==========================================

// Registrar
app.post('/api/alumnos', async (req, res) => {
    const { nombre, apellido_p, apellido_m, id_grupo } = req.body;
    try {
        await db.query(
            'INSERT INTO Alumnos (nombre, apellido_p, apellido_m, id_grupo, estatus) VALUES (?, ?, ?, ?, 1)',
            [nombre, apellido_p, apellido_m, id_grupo]
        );
        res.json({ success: true });
    } catch (err) {
        res.status(500).json({ error: err.message });
    }
});

// Listar Alumnos
app.get('/api/alumnos', async (req, res) => {
    try {
        const [rows] = await db.query(`
            SELECT a.*, g.codigo_grupo 
            FROM Alumnos a
            LEFT JOIN Grupos g ON a.id_grupo = g.id_grupo
            LEFT JOIN Carreras c ON g.id_carrera = c.id_carrera
            WHERE (c.estatus = 1 OR c.estatus IS NULL)
            ORDER BY a.apellido_p ASC
        `);
        res.json(rows);
    } catch (err) {
        res.status(500).json({ error: err.message });
    }
});

// Editar
app.post('/api/alumnos/actualizar', async (req, res) => {
    const { id_alumno, nombre, apellido_p, apellido_m, id_grupo } = req.body;
    try {
        await db.query(
            'UPDATE Alumnos SET nombre=?, apellido_p=?, apellido_m=?, id_grupo=? WHERE id_alumno=?',
            [nombre, apellido_p, apellido_m, id_grupo, id_alumno]
        );
        res.json({ success: true });
    } catch (err) {
        res.status(500).json({ error: err.message });
    }
});

// Cambiar Estatus
app.patch('/api/alumnos/:id/estatus', async (req, res) => {
    const { id } = req.params;
    try {
        await db.query('UPDATE Alumnos SET estatus = NOT estatus WHERE id_alumno = ?', [id]);
        res.json({ success: true });
    } catch (err) {
        res.status(500).json({ error: err.message });
    }
});

// --- INICIAR SERVIDOR ---
app.listen(PORT, () => {
    console.log(`🚀 API conectada a Railway corriendo en http://localhost:${PORT}`);
});