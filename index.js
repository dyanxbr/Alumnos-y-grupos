const express = require('express');
const mysql = require('mysql2/promise');
const app = express();

app.use(express.json());

// Configuración de la conexión a la base de datos
const db = mysql.createPool({
    host: 'localhost',
    user: 'root',
    password: 'tu_password',
    database: 'SistemaEscolar'
});

// 1. GESTIÓN DE CARRERAS (Catálogo y Estatus)

// Consultar todas las carreras (para el panel de administración)
app.get('/api/carreras', async (req, res) => {
    try {
        const [rows] = await db.query('SELECT * FROM Carreras');
        res.json(rows);
    } catch (err) {
        res.status(500).json({ error: err.message });
    }
});

// Activar o Desactivar Carrera
app.patch('/api/carreras/:id/estatus', async (req, res) => {
    const { id } = req.params;
    try {
        const [[carrera]] = await db.query('SELECT estatus FROM Carreras WHERE id_carrera = ?', [id]);
        if (!carrera) return res.status(404).json({ error: 'Carrera no encontrada' });

        const nuevoEstatus = carrera.estatus === 1 ? 0 : 1;
        await db.query('UPDATE Carreras SET estatus = ? WHERE id_carrera = ?', [nuevoEstatus, id]);
        res.json({ success: true, nuevoEstatus });
    } catch (err) {
        res.status(500).json({ error: err.message });
    }
});

// 2. GESTIÓN DE GRUPOS (Lógica de Negocio)

// Consultar catálogos ACTIVOS (Solo carreras activas para crear grupos)
app.get('/api/catalogos-registro', async (req, res) => {
    try {
        const [carreras] = await db.query('SELECT * FROM Carreras WHERE estatus = 1');
        const [turnos] = await db.query('SELECT * FROM Turnos');
        const [grados] = await db.query('SELECT * FROM Grados');
        res.json({ carreras, turnos, grados });
    } catch (err) {
        res.status(500).json({ error: err.message });
    }
});

// Registrar un Grupo con Reglas Automáticas
app.post('/api/grupos', async (req, res) => {
    let { id_carrera, id_grado, id_turno } = req.body;

    try {
        const [[carrera]] = await db.query('SELECT siglas FROM Carreras WHERE id_carrera = ?', [id_carrera]);
        const [[turnoRow]] = await db.query('SELECT sigla_turno FROM Turnos WHERE id_turno = ?', [id_turno]);
        const [[gradoRow]] = await db.query('SELECT numero_grado FROM Grados WHERE id_grado = ?', [id_grado]);

        let siglaFinal = turnoRow.sigla_turno;

        // REGLA: Grado >= 7 fuerza Vespertino (V), a menos que sea Mixto (MX)
        if (gradoRow.numero_grado >= 7 && siglaFinal !== 'MX') {
            siglaFinal = 'V';
            const [[turnoV]] = await db.query('SELECT id_turno FROM Turnos WHERE sigla_turno = "V"');
            id_turno = turnoV.id_turno;
        }

        // Generar consecutivo (01, 02...)
        const [[count]] = await db.query(
            'SELECT COUNT(*) as total FROM Grupos WHERE id_carrera = ? AND id_grado = ?', 
            [id_carrera, id_grado]
        );
        const consecutivo = (count.total + 1).toString().padStart(2, '0');

        // Formato: ISC1001-V
        const codigoGrupo = `${carrera.siglas}${gradoRow.numero_grado}${consecutivo}-${siglaFinal}`;

        await db.query(
            'INSERT INTO Grupos (codigo_grupo, id_carrera, id_grado, id_turno, consecutivo) VALUES (?, ?, ?, ?, ?)',
            [codigoGrupo, id_carrera, id_grado, id_turno, count.total + 1]
        );

        res.json({ success: true, codigo: codigoGrupo });
    } catch (err) {
        res.status(500).json({ error: err.message });
    }
});

// Listar grupos de carreras ACTIVAS
app.get('/api/grupos', async (req, res) => {
    try {
        const [rows] = await db.query(`
            SELECT g.* FROM Grupos g
            INNER JOIN Carreras c ON g.id_carrera = c.id_carrera
            WHERE c.estatus = 1
        `);
        res.json(rows);
    } catch (err) {
        res.status(500).json({ error: err.message });
    }
});

// 3. GESTIÓN DE ALUMNOS (Registro y Estatus)

// Registrar Alumno
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

// Alternar Estatus de Alumno
app.patch('/api/alumnos/:id/estatus', async (req, res) => {
    const { id } = req.params;
    try {
        const [[alumno]] = await db.query('SELECT estatus FROM Alumnos WHERE id_alumno = ?', [id]);
        if (!alumno) return res.status(404).json({ error: 'No existe' });

        const nuevo = alumno.estatus === 1 ? 0 : 1;
        await db.query('UPDATE Alumnos SET estatus = ? WHERE id_alumno = ?', [nuevo, id]);
        res.json({ success: true, nuevoEstatus: nuevo });
    } catch (err) {
        res.status(500).json({ error: err.message });
    }
});

// Consultar Alumnos (Solo de grupos cuya carrera esté activa)
app.get('/api/alumnos', async (req, res) => {
    try {
        const [rows] = await db.query(`
            SELECT a.*, g.codigo_grupo 
            FROM Alumnos a
            JOIN Grupos g ON a.id_grupo = g.id_grupo
            JOIN Carreras c ON g.id_carrera = c.id_carrera
            WHERE c.estatus = 1
            ORDER BY a.apellido_p ASC
        `);
        res.json(rows);
    } catch (err) {
        res.status(500).json({ error: err.message });
    }
});

// Servidor
const PORT = 3000;
app.listen(PORT, () => {
    console.log(`API Escolar corriendo en http://localhost:${PORT}`);
});