<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cuenta de cobro</title>
    <style>
        :root {
            color-scheme: light;
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            margin: 0;
            padding: 32px;
            background: #fff;
            color: #111827;
        }
        h1, h2, h3, h4, h5, h6 {
            margin: 0;
        }
        .page {
            max-width: 800px;
            margin: 0 auto;
        }
        .muted {
            color: #6b7280;
            font-size: 13px;
        }
        .title {
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 700;
        }
        .section {
            margin-top: 20px;
        }
        .summary {
            border: 1px dashed #e5e7eb;
            border-radius: 12px;
            padding: 16px;
            background: #f9fafb;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
            font-size: 13px;
        }
        th, td {
            padding: 8px;
            border-bottom: 1px solid #e5e7eb;
            text-align: left;
        }
        th {
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 0.5px;
            color: #6b7280;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .amount {
            font-size: 22px;
            font-weight: 800;
            color: #059669;
        }
        .badge {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 9999px;
            background: #f3f4f6;
            font-size: 12px;
            font-weight: 600;
        }
        .declaration {
            font-size: 13px;
            line-height: 1.6;
        }
        @media print {
            body {
                padding: 0;
            }
            .page {
                width: 100%;
                max-width: none;
                margin: 0;
                padding: 32px;
            }
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="text-right muted">{{ now()->locale('es')->translatedFormat('F j, Y') }}</div>

        <div class="text-center section">
            <p class="title">MY SEO COMPANY</p>
            <p class="muted">NIT 900.489.574-1</p>
        </div>

        <div class="text-center section">
            <h1 class="title">Cuenta de cobro</h1>
            <p class="muted">{{ ucfirst($selectedMonth->locale('es')->translatedFormat('F Y')) }}</p>
        </div>

        <div class="text-center section">
            <p class="muted">Debe a</p>
            <p><strong>{{ $selectedUser?->name }}</strong></p>
            @if($selectedUser?->document)
                <p class="muted">Documento: {{ $selectedUser->document }}</p>
            @endif
            @if($selectedUser?->address)
                <p class="muted">{{ $selectedUser->address }}</p>
            @endif
            @if($selectedUser?->phone)
                <p class="muted">Tel: {{ $selectedUser->phone }}</p>
            @endif
        </div>

        <div class="section summary text-center">
            <p class="muted title">La suma de</p>
            <p class="amount">{{ $summary['amount'] > 0 ? '$'.number_format($summary['amount'], 2, '.', ',') : '—' }}</p>
            <p class="muted">{{ number_format($summary['points'], 2, '.', ',') }} puntos x {{ number_format($summary['hourly_rate'], 2, '.', ',') }} / hora</p>
            <p class="muted">Periodo: {{ $range['from']->translatedFormat('d M') }} - {{ $range['to']->translatedFormat('d M') }}</p>
            <span class="badge">{{ $summary['tasks'] }} tareas</span>
        </div>

        @if($projects->isNotEmpty())
            <div class="section">
                <h3 class="title">Distribución por proyecto</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Proyecto</th>
                            <th class="text-right">Puntos</th>
                            <th class="text-right">% del total</th>
                            <th class="text-right">Tareas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($projects as $projectName => $data)
                            <tr>
                                <td>{{ $projectName }}</td>
                                <td class="text-right">{{ number_format($data['points'], 2, '.', ',') }}</td>
                                <td class="text-right">{{ number_format($data['percentage'], 2, '.', ',') }}%</td>
                                <td class="text-right">{{ $data['tasks'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        @if($tasks->isNotEmpty())
            <div class="section">
                <h3 class="title">Detalle de tareas</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Tarea</th>
                            <th>Proyecto</th>
                            <th class="text-right">Puntos</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tasks as $task)
                            <tr>
                                <td class="muted">{{ $task->due_date?->translatedFormat('d M') }}</td>
                                <td>{{ $task->name }}</td>
                                <td class="muted">{{ $task->project?->name ?? '—' }}</td>
                                <td class="text-right">{{ number_format((float) ($task->points ?? 0), 2, '.', ',') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <div class="section declaration">
            <p><strong>Declaración</strong></p>
            <p>Pertenezco al régimen simplificado, por tanto, no estoy obligado a cobrar el impuesto sobre las ventas (IVA).</p>
            <p>No estoy obligado a expedir factura de venta, conforme al artículo 616-2 del Estatuto Tributario.</p>
        </div>
    </div>
</body>
</html>
