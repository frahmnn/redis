
<x-header/>
<x-bootstrap/>

<main class="bg-light min-vh-100 d-flex flex-column justify-content-between position-relative overflow-hidden">
    <!-- Decorative SVG background shape (example, replace with your SVG) -->
    <img src="/assets/images/hero-shape.svg" alt="" class="position-absolute top-0 start-0 w-100" style="z-index:-1; pointer-events:none; max-height:400px; object-fit:cover;" aria-hidden="true">

    <section class="container py-5">
        <div class="row align-items-center">
            <div class="col-lg-7 text-center text-lg-start mb-4 mb-lg-0">
                <h1 class="display-4 fw-bold text-soft-shadow" style="color:#1A4D2E; font-family:'Poppins',sans-serif;">
                    Selamat Datang di <span class="d-block">[nama aplikasi]</span>
                </h1>
                <p class="lead mt-3 mb-4" style="color:#1B201D; font-family:'Nunito Sans',sans-serif;">
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
                </p>
                @if (Auth::check())
                    @if (Auth::user()->identity->special_role == null)
                        <a href="{{ route('reservations.index') }}" class="btn btn-lg btn-primary shadow-sm me-2 mb-2" style="font-family:'Nunito Sans',sans-serif;">
                            Minta Janji Pendampingan
                        </a>
                    @else
                        <a href="{{ route('assistants.index') }}" class="btn btn-lg btn-primary shadow-sm me-2 mb-2" style="font-family:'Nunito Sans',sans-serif;">
                            Menu Pendamping
                        </a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="btn btn-lg btn-primary shadow-sm me-2 mb-2" style="font-family:'Nunito Sans',sans-serif;">
                        Log in
                    </a>
                @endif
            </div>
            <div class="col-lg-5 d-flex justify-content-center align-items-center">
                <div class="img-overlay-green rounded-4 shadow-lg overflow-hidden" style="max-width:340px;">
                    <img src="/assets/images/hero-photo.jpg" alt="Home Image - Relawan Disabilitas UNJ" class="img-fluid" style="object-fit:cover; width:100%; height:320px;">
                </div>
            </div>
        </div>
    </section>

    <section class="container py-4">
        <h2 class="h4 fw-bold mb-4" style="color:#1A4D2E; font-family:'Poppins',sans-serif;">Kegiatan &amp; Cerita Kami</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card card-organic h-100 shadow-sm">
                    <div class="ratio ratio-1x1 position-relative">
                        <div class="bg-halftone-texture position-absolute top-0 start-0 w-100 h-100 rounded-3" aria-hidden="true"></div>
                        <iframe class="rounded-3" src="https://www.instagram.com/reel/DLr1c26TPkp/embed" title="Instagram Reel 1" allowfullscreen style="border:0; width:100%; height:100%; min-height:320px;" loading="lazy"></iframe>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-organic h-100 shadow-sm">
                    <div class="ratio ratio-1x1 position-relative">
                        <div class="bg-halftone-texture position-absolute top-0 start-0 w-100 h-100 rounded-3" aria-hidden="true"></div>
                        <iframe class="rounded-3" src="https://www.instagram.com/reel/DLr-FMVp3qC/embed" title="Instagram Reel 2" allowfullscreen style="border:0; width:100%; height:100%; min-height:320px;" loading="lazy"></iframe>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-organic h-100 shadow-sm">
                    <div class="ratio ratio-1x1 position-relative">
                        <div class="bg-halftone-texture position-absolute top-0 start-0 w-100 h-100 rounded-3" aria-hidden="true"></div>
                        <iframe class="rounded-3" src="https://www.instagram.com/reel/DLr-L0jpL_-/embed" title="Instagram Reel 3" allowfullscreen style="border:0; width:100%; height:100%; min-height:320px;" loading="lazy"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-primary text-white text-center py-4 mt-auto" style="font-family:'Nunito Sans',sans-serif;">
        <div class="container">
            <p class="mb-1">&copy; {{ date('Y') }} Relawan Disabilitas UNJ</p>
            <nav aria-label="Sosial Media" class="d-flex justify-content-center gap-3">
                <a href="https://www.instagram.com/relawandisabilitasunj/" class="text-white text-decoration-underline" target="_blank" rel="noopener" style="outline-offset:2px;">Instagram</a>
            </nav>
        </div>
    </footer>
</main>