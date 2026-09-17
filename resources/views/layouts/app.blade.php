<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Stok Obat')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    @yield('styles')
    @stack('styles')
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top shadow-sm"
        style="background: linear-gradient(90deg, #0d6efd 0%, #0dcaf0 100%);">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2 fw-bold" href="{{ route('obat.index') }}">
                <span class="d-inline-flex justify-content-center align-items-center bg-white rounded-3"
                    style="width: 38px; height: 38px;">
                    <i class="bi bi-capsule text-primary fs-5"></i>
                </span>
                <span>
                    Stok Obat
                    <small class="d-block fw-normal opacity-75" style="font-size: 0.7rem;">Fasilitas
                        Kesehatan</small>
                </span>
            </a>
            <span class="navbar-text text-white small d-none d-md-inline" id="navbar-clock">
                <i class="bi bi-clock me-1"></i><span>…</span>
            </span>
        </div>
    </nav>

    <main>
        @yield('content')
    </main>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Jam WIB live di navbar (waktu lokal browser)
        function tickNavbarClock() {
            try {
                $('#navbar-clock span').text(new Date().toLocaleString('id-ID', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit'
                }));
            } catch (e) {}
        }
        tickNavbarClock();
        setInterval(tickNavbarClock, 1000);
    </script>
    @yield('scripts')
    @stack('scripts')
</body>

</html>
