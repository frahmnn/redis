
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top" aria-label="Main navigation" style="font-family:'Nunito Sans',sans-serif;">
    <div class="container">
        <a class="navbar-brand fw-bold d-flex align-items-center" href="/" style="color:#1A4D2E; font-family:'Poppins',sans-serif;">
            <img src="/assets/images/logo.svg" alt="Logo Relawan Disabilitas UNJ" width="36" height="36" class="me-2" style="object-fit:contain;"> [nama aplikasi]
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-lg-center gap-lg-2">
                <li class="nav-item">
                    <a class="nav-link" href="/" style="color:#1A4D2E;">Beranda</a>
                </li>
                @if (!empty($wa_number))
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center" href="https://wa.me/{{ $wa_number }}" target="_blank" rel="noopener" style="color:#1A4D2E;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#25D366" class="me-1" viewBox="0 0 16 16" aria-hidden="true"><path d="M13.601 2.326A7.902 7.902 0 0 0 8.003.001C3.582.001.001 3.582.001 8c0 1.409.368 2.782 1.064 3.99L.057 15.25a.5.5 0 0 0 .693.592l3.34-1.36A7.953 7.953 0 0 0 8.003 16c4.421 0 8.002-3.581 8.002-8s-3.581-7.999-8.002-7.999zm0 0" fill-opacity=".15"/><path d="M8.003 1.5c3.592 0 6.5 2.908 6.5 6.5 0 3.592-2.908 6.5-6.5 6.5a6.47 6.47 0 0 1-3.162-.825.5.5 0 0 0-.41-.03l-2.13.868.41-2.13a.5.5 0 0 0-.03-.41A6.47 6.47 0 0 1 1.503 8c0-3.592 2.908-6.5 6.5-6.5zm3.13 8.97c-.17.48-.84.92-1.16.98-.3.06-.68.09-1.1-.07-.25-.09-.57-.19-.98-.38-.86-.37-1.42-1.25-1.46-1.3-.04-.06-.35-.46-.35-.88 0-.42.22-.62.3-.7.08-.08.17-.1.23-.1.06 0 .12.01.17.01.05 0 .13-.02.2.15.08.18.27.62.29.66.02.04.04.09.01.15-.03.06-.05.09-.1.14-.05.05-.09.1-.13.16-.04.06-.09.12-.04.22.05.1.22.36.47.59.32.29.59.38.69.42.1.04.16.03.22-.02.06-.05.25-.29.32-.39.07-.1.13-.08.22-.05.09.03.57.27.67.32.1.05.17.07.2.11.03.04.03.23-.04.47z"/></svg>
                        Whatsapp admin
                    </a>
                </li>
                @endif
                @if ($user_name)
                    <li class="nav-item d-flex align-items-center">
                        <span class="nav-link px-2 py-1 bg-secondary bg-opacity-25 rounded-pill fw-semibold text-dark" aria-current="page" style="cursor:default; font-size:1rem; letter-spacing:0.01em;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#1A4D2E" class="me-1 mb-1" viewBox="0 0 16 16" aria-hidden="true"><path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/><path fill-rule="evenodd" d="M8 9a5 5 0 0 0-5 5c0 .265.105.52.293.707A1 1 0 0 0 4 15h8a1 1 0 0 0 .707-1.707A1 1 0 0 0 13 14a5 5 0 0 0-5-5z"/></svg>
                            Halo, {{ $user_name }}
                        </span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('users.reservations') }}" style="color:#1A4D2E;">Pendampingan Saya</a>
                    </li>
                    @if ($special_role == "Admin")
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admins.index') }}" style="color:#1A4D2E;">Menu Admin</a>
                        </li>
                    @endif
                    <li class="nav-item">
                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-primary ms-lg-2" style="font-family:'Nunito Sans',sans-serif;" onclick="return confirm('Ingin Log Out?')">Keluar</button>
                        </form>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="btn btn-primary ms-lg-2" href="{{ route('login') }}" style="font-family:'Nunito Sans',sans-serif;">Log in</a>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</nav>