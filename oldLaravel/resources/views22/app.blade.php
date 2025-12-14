<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Create</title>
    <link href="{{ asset('css/app.css') }}?id=<?php echo rand(1, 10000000);?>" rel="stylesheet">
    <link href="{{ asset('css/app_ms.css') }}?id=<?php echo rand(1, 10000000);?>" rel="stylesheet">
    
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <!-- Tus links de navegación aquí -->
                </ul>
            </div>
        </nav>

        <div class="container-fluid">
            <div class="row">
                <div class="col-md-3">
                    <!-- Área a la izquierda con scroll -->
                    <div class="left-area" style="overflow-y: auto; height: 100vh;">
                        <!-- Tu contenido aquí -->
                        Enlaces
                    </div>
                </div>
                <div class="col-md-9">
                    <!-- Área en el centro -->
                    <div class="center-area">
                        @yield('content')
                        contenido
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/app.js') }}?id=<?php echo rand(1, 10000000);?>"></script>
</body>
</html>
