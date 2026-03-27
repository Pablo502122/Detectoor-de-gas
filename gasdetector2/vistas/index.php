<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gas Monitor · Panel de Control</title>
    <meta name="description" content="Panel de monitoreo de gas en tiempo real con alertas visuales y gráfica histórica de lecturas.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* ─── CSS Reset & Variables ─────────────────────────── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg-primary:    #0d0f14;
            --bg-card:       #141720;
            --bg-card-hover: #1a1e2a;
            --border:        rgba(255,255,255,0.07);
            --text-primary:  #e8eaf0;
            --text-muted:    #6b7280;
            --text-sub:      #9ca3af;

            /* Status colours */
            --green:         #10d76a;
            --green-glow:    rgba(16,215,106,0.4);
            --green-dim:     rgba(16,215,106,0.12);
            --red:           #ff2d55;
            --red-glow:      rgba(255,45,85,0.45);
            --red-dim:       rgba(255,45,85,0.12);

            --accent-blue:   #3b82f6;
            --radius-lg:     18px;
            --radius-md:     12px;
            --radius-sm:     8px;
            --transition:    0.3s ease;
        }

        /* ─── Base ──────────────────────────────────────────── */
        html { scroll-behavior: smooth; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            min-height: 100vh;
            padding: 24px 16px 48px;
        }

        /* ─── Header ────────────────────────────────────────── */
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            max-width: 1100px;
            margin: 0 auto 32px;
            flex-wrap: wrap;
            gap: 12px;
        }

        .header-brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .header-icon {
            width: 44px;
            height: 44px;
            border-radius: var(--radius-md);
            background: linear-gradient(135deg, #10d76a22, #10d76a44);
            border: 1px solid rgba(16,215,106,0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }

        .header-title h1 {
            font-size: 1.25rem;
            font-weight: 700;
            letter-spacing: -0.3px;
        }

        .header-title p {
            font-size: 0.75rem;
            color: var(--text-muted);
            margin-top: 1px;
        }

        .header-time {
            font-size: 0.78rem;
            color: var(--text-muted);
            background: var(--bg-card);
            border: 1px solid var(--border);
            padding: 6px 14px;
            border-radius: 999px;
            letter-spacing: 0.3px;
        }

        /* ─── Layout Grid ───────────────────────────────────── */
        .dashboard {
            max-width: 1100px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 320px 1fr;
            grid-template-rows: auto auto auto;
            gap: 20px;
        }

        /* ─── Card Base ─────────────────────────────────────── */
        .card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 24px;
            transition: border-color var(--transition);
        }

        .card:hover {
            border-color: rgba(255,255,255,0.12);
        }

        .card-label {
            font-size: 0.7rem;
            font-weight: 600;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            color: var(--text-muted);
            margin-bottom: 16px;
        }

        /* ─── Traffic Light Card ────────────────────────────── */
        .status-card {
            grid-column: 1;
            grid-row: 1 / 3;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 20px;
            text-align: center;
        }

        .traffic-light {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 14px;
            background: #0a0c10;
            border: 1px solid var(--border);
            border-radius: 50px;
            padding: 22px 26px;
            width: fit-content;
        }

        .light {
            width: 68px;
            height: 68px;
            border-radius: 50%;
            transition: box-shadow 0.5s ease, opacity 0.5s ease;
        }

        .light.red-light  { background: radial-gradient(circle at 35% 35%, #ff6680, #cc0022); }
        .light.green-light { background: radial-gradient(circle at 35% 35%, #4dffaa, #00aa44); }

        /* Active / inactive states */
        .light.inactive   { opacity: 0.12; box-shadow: none; }
        .light.active-red { opacity: 1; box-shadow: 0 0 30px 10px var(--red-glow), 0 0 60px 20px rgba(255,45,85,0.2); }
        .light.active-green { opacity: 1; box-shadow: 0 0 30px 10px var(--green-glow), 0 0 60px 20px rgba(16,215,106,0.2); animation: pulse-green 2s ease-in-out infinite; }

        @keyframes pulse-green {
            0%, 100% { box-shadow: 0 0 30px 10px var(--green-glow), 0 0 60px 20px rgba(16,215,106,0.2); }
            50%       { box-shadow: 0 0 40px 16px var(--green-glow), 0 0 80px 30px rgba(16,215,106,0.15); }
        }

        @keyframes pulse-red {
            0%, 100% { box-shadow: 0 0 30px 10px var(--red-glow), 0 0 60px 20px rgba(255,45,85,0.2); }
            50%       { box-shadow: 0 0 50px 20px var(--red-glow), 0 0 90px 35px rgba(255,45,85,0.15); }
        }

        .light.active-red { animation: pulse-red 0.8s ease-in-out infinite; }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 18px;
            border-radius: 999px;
            font-size: 0.9rem;
            font-weight: 600;
            transition: all var(--transition);
        }

        .status-badge.safe {
            background: var(--green-dim);
            color: var(--green);
            border: 1px solid rgba(16,215,106,0.35);
        }

        .status-badge.danger {
            background: var(--red-dim);
            color: var(--red);
            border: 1px solid rgba(255,45,85,0.35);
        }

        .status-dot {
            width: 8px; height: 8px;
            border-radius: 50%;
            display: inline-block;
        }

        .safe .status-dot   { background: var(--green); }
        .danger .status-dot { background: var(--red); animation: blink-dot 0.8s infinite; }

        @keyframes blink-dot { 0%,100%{opacity:1} 50%{opacity:0.2} }

        /* ─── Main Value Card ───────────────────────────────── */
        .value-card {
            grid-column: 2;
            grid-row: 1;
        }

        .value-display {
            display: flex;
            align-items: baseline;
            gap: 10px;
            margin-bottom: 8px;
        }

        .value-number {
            font-size: 4.5rem;
            font-weight: 800;
            letter-spacing: -3px;
            line-height: 1;
            transition: color var(--transition);
        }

        .value-number.safe-color   { color: var(--green); }
        .value-number.danger-color { color: var(--red); }

        .value-unit {
            font-size: 1.4rem;
            font-weight: 400;
            color: var(--text-muted);
            letter-spacing: 0;
        }

        .value-sub {
            font-size: 0.82rem;
            color: var(--text-muted);
            margin-top: 4px;
        }

        .value-meta {
            display: flex;
            gap: 16px;
            margin-top: 20px;
            flex-wrap: wrap;
        }

        .meta-chip {
            background: rgba(255,255,255,0.04);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            padding: 8px 14px;
            font-size: 0.78rem;
            color: var(--text-sub);
        }

        .meta-chip span {
            color: var(--text-primary);
            font-weight: 600;
            margin-left: 4px;
        }

        /* ─── Chart Card ────────────────────────────────────── */
        .chart-card {
            grid-column: 2;
            grid-row: 2;
        }

        .chart-wrapper {
            height: 180px;
            position: relative;
        }

        /* ─── Observations Card ─────────────────────────────── */
        .obs-card {
            grid-column: 1;
            grid-row: 3;
        }

        .obs-content {
            display: flex;
            gap: 14px;
            align-items: flex-start;
        }

        .obs-icon {
            font-size: 28px;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .obs-text {
            flex: 1;
        }

        .obs-title {
            font-size: 0.95rem;
            font-weight: 600;
            margin-bottom: 6px;
        }

        .obs-title.safe-color   { color: var(--green); }
        .obs-title.danger-color { color: var(--red); }

        .obs-desc {
            font-size: 0.82rem;
            color: var(--text-sub);
            line-height: 1.6;
        }

        /* ─── Table Card ────────────────────────────────────── */
        .table-card {
            grid-column: 2;
            grid-row: 3;
        }

        .table-scroll {
            overflow-x: auto;
            margin-top: 4px;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 4px;
            font-size: 0.82rem;
        }

        thead th {
            text-align: left;
            padding: 8px 12px;
            font-size: 0.68rem;
            font-weight: 600;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            color: var(--text-muted);
        }

        tbody tr {
            background: rgba(255,255,255,0.025);
            border-radius: var(--radius-sm);
            transition: background var(--transition);
        }

        tbody tr:hover { background: rgba(255,255,255,0.055); }

        tbody td {
            padding: 11px 12px;
            color: var(--text-sub);
        }

        tbody td:first-child { border-radius: var(--radius-sm) 0 0 var(--radius-sm); }
        tbody td:last-child  { border-radius: 0 var(--radius-sm) var(--radius-sm) 0; }

        tbody td:nth-child(2) {
            font-weight: 600;
            font-size: 0.9rem;
            color: var(--text-primary);
        }

        .pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 3px 10px;
            border-radius: 999px;
            font-size: 0.71rem;
            font-weight: 600;
        }

        .pill.safe   { background: var(--green-dim); color: var(--green); }
        .pill.danger { background: var(--red-dim); color: var(--red); }

        /* ─── Refresh indicator ─────────────────────────────── */
        .refresh-bar {
            position: fixed;
            bottom: 0; left: 0; right: 0;
            height: 3px;
            background: rgba(255,255,255,0.05);
            z-index: 99;
        }

        .refresh-progress {
            height: 100%;
            background: linear-gradient(90deg, var(--green), rgba(16,215,106,0.4));
            width: 100%;
            transform-origin: left;
            animation: fill-bar 5s linear infinite;
        }

        @keyframes fill-bar {
            from { transform: scaleX(1); }
            to   { transform: scaleX(0); }
        }

        /* ─── Responsive ────────────────────────────────────── */
        @media (max-width: 768px) {
            .dashboard {
                grid-template-columns: 1fr;
                grid-template-rows: auto;
            }
            .status-card  { grid-column: 1; grid-row: 1; }
            .value-card   { grid-column: 1; grid-row: 2; }
            .chart-card   { grid-column: 1; grid-row: 3; }
            .obs-card     { grid-column: 1; grid-row: 4; }
            .table-card   { grid-column: 1; grid-row: 5; }

            .status-card { flex-direction: row; text-align: left; justify-content: flex-start; }
            .value-number { font-size: 3.2rem; }
        }

        @media (max-width: 480px) {
            body { padding: 16px 12px 40px; }
            .card { padding: 18px; }
            .header { margin-bottom: 20px; }
        }
    </style>
</head>
<body>
<?php
$conn = new mysqli("localhost", "root", "", "gas_db");
$result = $conn->query("SELECT * FROM lecturas ORDER BY id DESC LIMIT 1");
$row = $result->fetch_assoc();
$estatus = $row['estatus'] ?? 'Sin datos';
$valor   = (int)($row['valor']  ?? 0);
$fecha   = $row['fecha']   ?? '—';
$esDanger = ($estatus === 'Nivel Peligroso');

// Últimas 20 lecturas para la tabla
$res20 = $conn->query("SELECT id, valor, estatus, fecha FROM lecturas ORDER BY id DESC LIMIT 20");
$registros = [];
while ($r = $res20->fetch_assoc()) { $registros[] = $r; }
$conn->close();

// Observaciones dinámicas
$obsData = [
    'safe' => [
        'icon'  => '✅',
        'title' => 'Entorno Seguro',
        'desc'  => 'Los niveles de gas se encuentran dentro del rango normal. Ventilación adecuada. No se requiere ninguna acción inmediata. Continúe el monitoreo de rutina.',
    ],
    'danger' => [
        'icon'  => '⚠️',
        'title' => '¡Alerta! Nivel Peligroso',
        'desc'  => 'Se ha detectado una concentración elevada de gas. Ventile el área de inmediato, evacúe si es necesario y verifique posibles fugas en tuberías o equipos. Contacte a personal especializado.',
    ],
];
$obs = $esDanger ? $obsData['danger'] : $obsData['safe'];
?>

<!-- Refresh bar -->
<div class="refresh-bar"><div class="refresh-progress"></div></div>

<!-- Header -->
<header class="header">
    <div class="header-brand">
        <div class="header-icon">🛢️</div>
        <div class="header-title">
            <h1>Gas Monitor</h1>
            <p>Panel de Control · Tiempo Real</p>
        </div>
    </div>
    <div class="header-time" id="clock">—</div>
</header>

<!-- Dashboard Grid -->
<main class="dashboard">

    <!-- 1. Traffic Light Status Card -->
    <section class="card status-card">
        <p class="card-label">Estado Actual</p>

        <div class="traffic-light">
            <div class="light red-light <?= $esDanger ? 'active-red' : 'inactive' ?>"></div>
            <div class="light green-light <?= !$esDanger ? 'active-green' : 'inactive' ?>"></div>
        </div>

        <div class="status-badge <?= $esDanger ? 'danger' : 'safe' ?>">
            <span class="status-dot"></span>
            <?= $esDanger ? 'Peligroso' : 'Seguro' ?>
        </div>

        <p style="font-size:0.73rem;color:var(--text-muted);margin-top:-8px;">
            Umbral crítico: 2,000 PPM
        </p>
    </section>

    <!-- 2. Main Value Card -->
    <section class="card value-card">
        <p class="card-label">Concentración de Gas</p>
        <div class="value-display">
            <span class="value-number <?= $esDanger ? 'danger-color' : 'safe-color' ?>">
                <?= number_format($valor) ?>
            </span>
            <span class="value-unit">PPM</span>
        </div>
        <p class="value-sub">Partes por millón · Última lectura registrada</p>
        <div class="value-meta">
            <div class="meta-chip">Estado <span><?= htmlspecialchars($estatus) ?></span></div>
            <div class="meta-chip">Registro <span>#<?= $row['id'] ?? '—' ?></span></div>
            <div class="meta-chip">Sensor <span>ESP32 · MQ-2</span></div>
        </div>
    </section>

    <!-- 3. Chart Card -->
    <section class="card chart-card">
        <p class="card-label">Historial · Últimas 20 lecturas</p>
        <div class="chart-wrapper">
            <canvas id="gasChart"></canvas>
        </div>
    </section>

    <!-- 4. Observations Card -->
    <section class="card obs-card">
        <p class="card-label">Observaciones</p>
        <div class="obs-content">
            <div class="obs-icon"><?= $obs['icon'] ?></div>
            <div class="obs-text">
                <div class="obs-title <?= $esDanger ? 'danger-color' : 'safe-color' ?>">
                    <?= $obs['title'] ?>
                </div>
                <p class="obs-desc"><?= $obs['desc'] ?></p>
            </div>
        </div>
    </section>

    <!-- 5. Registros Table -->
    <section class="card table-card">
        <p class="card-label">Registros Recientes</p>
        <div class="table-scroll">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Valor (PPM)</th>
                        <th>Estado</th>
                        <th>Fecha / Hora</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($registros as $reg):
                        $danger = ($reg['estatus'] === 'Nivel Peligroso');
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($reg['id']) ?></td>
                        <td><?= number_format((int)$reg['valor']) ?></td>
                        <td>
                            <span class="pill <?= $danger ? 'danger' : 'safe' ?>">
                                <?= $danger ? '⚠ Peligroso' : '✔ Seguro' ?>
                            </span>
                        </td>
                        <td><?= htmlspecialchars($reg['fecha'] ?? '—') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

</main>

<!-- ─── Scripts ──────────────────────────────────────────── -->
<script>
    /* ── Clock ── */
    function updateClock() {
        const now = new Date();
        document.getElementById('clock').textContent =
            now.toLocaleDateString('es-MX', { weekday:'short', day:'2-digit', month:'short' })
            + ' · '
            + now.toLocaleTimeString('es-MX', { hour:'2-digit', minute:'2-digit', second:'2-digit' });
    }
    updateClock();
    setInterval(updateClock, 1000);

    /* ── Chart.js ── */
    const API_URL = '../apis/get-data.php';
    let chart;

    const chartColors = {
        safe:   { line: '#10d76a', fill: 'rgba(16,215,106,0.08)', point: '#10d76a' },
        danger: { line: '#ff2d55', fill: 'rgba(255,45,85,0.08)',  point: '#ff2d55' },
    };

    function buildChart(labels, data, isDanger) {
        const ctx = document.getElementById('gasChart').getContext('2d');
        const col = isDanger ? chartColors.danger : chartColors.safe;

        const gradient = ctx.createLinearGradient(0, 0, 0, 180);
        gradient.addColorStop(0, isDanger ? 'rgba(255,45,85,0.18)' : 'rgba(16,215,106,0.18)');
        gradient.addColorStop(1, 'rgba(0,0,0,0)');

        if (chart) chart.destroy();

        chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'PPM',
                    data: data,
                    borderColor: col.line,
                    backgroundColor: gradient,
                    borderWidth: 2.5,
                    pointBackgroundColor: col.point,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    tension: 0.45,
                    fill: true,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1a1e2a',
                        borderColor: 'rgba(255,255,255,0.1)',
                        borderWidth: 1,
                        titleColor: '#9ca3af',
                        bodyColor: col.line,
                        bodyFont: { family: 'Inter', weight: '700', size: 14 },
                        callbacks: {
                            label: ctx => ` ${ctx.parsed.y.toLocaleString()} PPM`,
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { color: 'rgba(255,255,255,0.04)' },
                        ticks: { color: '#4b5563', font: { family: 'Inter', size: 10 }, maxRotation: 0 },
                    },
                    y: {
                        grid: { color: 'rgba(255,255,255,0.04)' },
                        ticks: { color: '#4b5563', font: { family: 'Inter', size: 10 } },
                    }
                },
                animation: { duration: 600, easing: 'easeInOutQuart' }
            }
        });
    }

    async function fetchAndRender() {
        try {
            const res  = await fetch(API_URL + '?t=' + Date.now());
            const rows = await res.json();

            const labels   = rows.map((_, i) => `#${rows.length - i}`).reverse().map((_, i) => `L${i+1}`);
            const values   = rows.map(r => parseInt(r.valor));
            const anyDanger = rows.some(r => r.estatus === 'Nivel Peligroso');

            buildChart(labels, values, anyDanger);
        } catch (e) {
            console.error('Error al cargar datos:', e);
        }
    }

    fetchAndRender();

    /* ── Auto-refresh every 5s ── */
    setInterval(async () => {
        try {
            const res  = await fetch(API_URL + '?t=' + Date.now());
            const rows = await res.json();
            if (!rows.length) return;

            const last     = rows[rows.length - 1];
            const val      = parseInt(last.valor);
            const isDanger = last.estatus === 'Nivel Peligroso';

            /* Update value number */
            const numEl = document.querySelector('.value-number');
            numEl.textContent = val.toLocaleString('es-MX');
            numEl.className = 'value-number ' + (isDanger ? 'danger-color' : 'safe-color');

            /* Update traffic light */
            document.querySelector('.red-light').className   = 'light red-light '   + (isDanger ? 'active-red'   : 'inactive');
            document.querySelector('.green-light').className = 'light green-light ' + (isDanger ? 'inactive'      : 'active-green');

            /* Update status badge */
            const badge = document.querySelector('.status-badge');
            badge.className = 'status-badge ' + (isDanger ? 'danger' : 'safe');
            badge.innerHTML = `<span class="status-dot"></span>${isDanger ? 'Peligroso' : 'Seguro'}`;

            /* Update obs */
            const obsTitle = document.querySelector('.obs-title');
            const obsDesc  = document.querySelector('.obs-desc');
            const obsIcon  = document.querySelector('.obs-icon');
            obsTitle.className = 'obs-title ' + (isDanger ? 'danger-color' : 'safe-color');

            if (isDanger) {
                obsIcon.textContent  = '⚠️';
                obsTitle.textContent = '¡Alerta! Nivel Peligroso';
                obsDesc.textContent  = 'Se ha detectado una concentración elevada de gas. Ventile el área de inmediato, evacúe si es necesario y verifique posibles fugas en tuberías o equipos. Contacte a personal especializado.';
            } else {
                obsIcon.textContent  = '✅';
                obsTitle.textContent = 'Entorno Seguro';
                obsDesc.textContent  = 'Los niveles de gas se encuentran dentro del rango normal. Ventilación adecuada. No se requiere ninguna acción inmediata. Continúe el monitoreo de rutina.';
            }

            /* Rebuild chart */
            const labels = rows.map((_, i) => `L${i+1}`);
            const values = rows.map(r => parseInt(r.valor));
            buildChart(labels, values, isDanger);

        } catch(e) { /* silencioso */ }
    }, 5000);
</script>

</body>
</html>