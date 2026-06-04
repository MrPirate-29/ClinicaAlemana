<!DOCTYPE html>
<html class="light" lang="es">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Sistema de Gestión | Centro Médico Alemana</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        .dashed-icon-container {
            border: 2px dashed #feae2c;
            border-radius: 9999px;
            display: flex; align-items: center; justify-content: center;
            padding: 8px;
        }
        .cursive-logo { font-family: 'Brush Script MT', cursive; }
        .hero-gradient { background: linear-gradient(90deg, #F7F8FA 0%, rgba(247,248,250,0) 100%); }

        @keyframes rotate-dots {
            from { transform: rotate(0deg); }
            to   { transform: rotate(360deg); }
        }
        .animated-circle-container {
            position: relative; width: 120px; height: 120px;
            display: flex; align-items: center; justify-content: center;
        }
        .orbit-track {
            position: absolute; width: 100%; height: 100%;
            border: 1px dashed #feae2c; border-radius: 50%;
            animation: rotate-dots 10s linear infinite;
            animation-play-state: paused;
        }
        .group:hover .orbit-track { animation-play-state: running; }
        .orbit-dot {
            position: absolute; width: 8px; height: 8px;
            background-color: #feae2c; border-radius: 50%;
            left: 50%; transform: translateX(-50%);
        }
        .dot-1 { top: -4px; }
        .dot-2 { bottom: -4px; }
        .orbit-track-green { border-color: #4CAF50; }
        .orbit-dot-green   { background-color: #4CAF50; }

        .tab-content-enter { animation: slideUp 0.5s ease-out forwards; }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "footer-dark":               "#014A8F",
                        "primary":                   "#002451",
                        "primary-container":         "#1a3a6b",
                        "on-primary":                "#ffffff",
                        "secondary":                 "#835500",
                        "secondary-container":       "#feae2c",
                        "on-secondary-container":    "#6b4500",
                        "bg-light":                  "#F7F8FA",
                        "surface-container-low":     "#f5f3f3",
                        "surface-container":         "#efeded",
                        "surface-container-high":    "#eae8e7",
                        "surface-variant":           "#e4e2e2",
                        "on-surface":                "#1b1c1c",
                        "on-surface-variant":        "#43474f",
                        "outline":                   "#747780",
                        "outline-variant":           "#c4c6d0",
                        "error":                     "#ba1a1a",
                        "accent-cyan":               "#7AD5D6",
                    },
                    fontFamily: {
                        sans: ["Montserrat", "ui-sans-serif", "system-ui"],
                    },
                },
            },
        }
    </script>
</head>
<body class="bg-bg-light text-on-surface font-sans overflow-x-hidden">

{{-- ══════════════════════ TOP BAR ══════════════════════ --}}
<div class="h-[36px] bg-white border-b border-outline-variant flex items-center px-[48px] justify-between">
    <div class="flex-1 text-center text-xs font-semibold text-on-surface-variant tracking-wide">
        Te cuidamos como en casa 🤍
    </div>
    <div class="flex gap-4">
        <a class="text-on-surface-variant hover:text-primary transition-colors duration-200" href="#"><span class="material-symbols-outlined text-[18px]">social_leaderboard</span></a>
        <a class="text-on-surface-variant hover:text-primary transition-colors duration-200" href="#"><span class="material-symbols-outlined text-[18px]">retweet</span></a>
        <a class="text-on-surface-variant hover:text-primary transition-colors duration-200" href="#"><span class="material-symbols-outlined text-[18px]">link</span></a>
        <a class="text-on-surface-variant hover:text-primary transition-colors duration-200" href="#"><span class="material-symbols-outlined text-[18px]">chat</span></a>
    </div>
</div>

{{-- ══════════════════════ HEADER ══════════════════════ --}}
<header class="h-[80px] bg-white shadow-sm sticky top-0 z-50 flex items-center px-[48px] justify-between">
    <div class="flex items-center gap-3">
        <img
            alt="Logo Alemana Red de Salud"
            class="h-14 w-auto object-contain"
            src="{{ asset('images/logo-alemana.png') }}"
            onerror="this.style.display='none';this.nextElementSibling.style.display='flex';"
        />
        <div style="display:none;" class="flex flex-col leading-tight">
            <span class="text-xl font-extrabold text-primary-container tracking-tight">ALEMANA</span>
            <span class="text-secondary cursive-logo text-[18px]">Red de Salud</span>
        </div>
    </div>
    <div class="hidden md:flex items-center gap-2">
        <div class="flex items-center gap-2 px-4 py-2">
            <span class="material-symbols-outlined text-secondary" style="font-variation-settings:'FILL' 1;">location_on</span>
            <span class="text-sm font-bold tracking-wide text-on-surface-variant">San Jorge</span>
        </div>
        <div class="flex items-center gap-2 px-4 py-2">
            <span class="material-symbols-outlined text-secondary" style="font-variation-settings:'FILL' 1;">location_on</span>
            <span class="text-sm font-bold tracking-wide text-on-surface-variant">Achumani</span>
        </div>
        <div class="flex items-center gap-2 px-4 py-2 bg-[#EEF4FF] rounded-lg">
            <span class="material-symbols-outlined text-secondary" style="font-variation-settings:'FILL' 1;">location_on</span>
            <span class="text-sm font-bold tracking-wide text-primary-container">Centro Médico</span>
        </div>
    </div>
    <a href="{{ url('/login') }}"
       class="border-2 border-primary-container text-primary-container text-sm font-bold tracking-wide px-6 py-2 rounded-lg hover:bg-primary-container hover:text-white transition-all duration-300">
        Acceso al Sistema →
    </a>
</header>

{{-- ══════════════════════ NAVBAR ══════════════════════ --}}
<nav class="bg-footer-dark h-[48px] flex items-center px-[48px] justify-between shadow-lg">
    <div class="flex gap-8 h-full items-center">
        <a class="text-white text-sm font-bold tracking-wide border-b-2 border-secondary h-full flex items-center px-2" href="#">Inicio</a>
        <a class="text-white opacity-80 hover:opacity-100 text-sm font-bold tracking-wide h-full flex items-center px-2 transition-opacity" href="#">Sobre Nosotros</a>
        <a class="text-white opacity-80 hover:opacity-100 text-sm font-bold tracking-wide h-full flex items-center px-2 transition-opacity" href="#">Servicios</a>
        <a class="text-white opacity-80 hover:opacity-100 text-sm font-bold tracking-wide h-full flex items-center px-2 transition-opacity" href="#">Sucursales</a>
        <a class="text-white opacity-80 hover:opacity-100 text-sm font-bold tracking-wide h-full flex items-center px-2 transition-opacity" href="#">Contactos</a>
    </div>
    <span class="material-symbols-outlined text-white cursor-pointer hover:scale-110 transition-transform">search</span>
</nav>

{{-- ══════════════════════ HERO ══════════════════════ --}}
<section class="h-[520px] bg-bg-light relative overflow-hidden flex items-center">
    <div class="absolute inset-0 opacity-20 pointer-events-none">
        <svg class="w-full h-full" preserveAspectRatio="none" viewBox="0 0 100 100">
            <pattern height="20" id="hexagons" patternUnits="userSpaceOnUse" width="20">
                <path d="M10 0 L20 5 L20 15 L10 20 L0 15 L0 5 Z" fill="none" stroke="#A8D8EA" stroke-width="0.2"/>
            </pattern>
            <rect fill="url(#hexagons)" height="100" width="100"/>
        </svg>
    </div>
    <div class="container mx-auto px-[48px] grid grid-cols-1 md:grid-cols-2 items-center relative z-10">
        <div class="flex flex-col gap-6">
            <h1 class="font-extrabold text-primary-container leading-tight text-[52px]">
                Sistema de Gestión<br/>Centro Médico
            </h1>
            <p class="text-lg font-normal text-on-surface-variant max-w-[480px] leading-relaxed">
                Optimice la administración clínica y la atención al paciente con nuestra plataforma centralizada de alta eficiencia.
            </p>
            <a href="{{ url('/login') }}"
               class="bg-secondary text-white text-xl font-bold px-8 py-4 rounded-xl flex items-center gap-3 w-fit shadow-lg hover:scale-105 transition-transform">
                <span class="material-symbols-outlined">lock</span>
                Acceder al Sistema
            </a>
        </div>
        <div class="hidden md:block relative h-[520px]">
            <div class="absolute inset-0 hero-gradient z-20"></div>
            <img class="absolute inset-0 w-full h-full object-cover z-10"
                 alt="Profesional médico"
                 src="https://lh3.googleusercontent.com/aida-public/AB6AXuBytuXywJU2uNaAUqTpgtrQziAvHy9RNL5fVWsnfRCd6YtfmFfjsR8PVQfVpYEij3iSNxA-1DrYzcDD3Q73ZsWfwaS9TMzfoxPugzfyvd_3d7MgXzbOX7T3CXCLi5C0q2bUJNZqKsuHpvZhWxpB1ZLlHN5J7ZByenzFV7YjiDHovatLK0IUk1DoDgUV3pTPIsyKO3iK8yXD6oetQRNKOIX1yJn6XF69v-AoaAPf3Dhy9mQIjEKCEg0o9q9RBfans7wmfbqTIWMg9sw"/>
        </div>
    </div>
</section>

{{-- ══════════════════════ ESTADÍSTICAS ══════════════════════ --}}
<section class="py-20 px-[48px] bg-white">
    <div class="text-center mb-12">
        <span class="text-sm font-bold tracking-widest text-secondary uppercase">— CENTRO MÉDICO —</span>
        <h2 class="text-3xl font-bold text-primary mt-2">Datos del Sistema</h2>
    </div>
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach([
            ['medical_services', '455',   'Consultas Registradas'],
            ['stethoscope',      '40',    'Especialidades'],
            ['trending_down',    '74.3%', 'Tasa de Fuga Detectada'],
            ['view_module',      '3',     'Módulos del Sistema'],
        ] as [$icon, $num, $label])
        <div class="p-8 border border-outline-variant rounded-xl flex flex-col items-center text-center hover:shadow-md transition-shadow">
            <div class="dashed-icon-container mb-4">
                <span class="material-symbols-outlined text-secondary text-3xl">{{ $icon }}</span>
            </div>
            <div class="text-3xl font-bold text-primary">{{ $num }}</div>
            <div class="text-sm font-bold tracking-wide text-on-surface-variant mt-1">{{ $label }}</div>
        </div>
        @endforeach
    </div>
</section>

{{-- ══════════════════════ NUESTROS SERVICIOS ══════════════════════ --}}
<section class="py-20 px-[48px] bg-bg-light">
    <div class="text-center mb-12">
        <span class="text-sm font-bold tracking-widest text-secondary uppercase">— DESCUBRE —</span>
        <h2 class="text-3xl font-bold text-primary mt-2 uppercase">Nuestros Servicios</h2>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 max-w-[1440px] mx-auto mb-16">
        @foreach([
            ['local_shipping',     'Ambulancia 24/7'],
            ['medical_information','Servicios Clínicos'],
            ['vital_signs',        'Atención Médico Quirúrgica'],
            ['person_search',      'Consulta por Especialidad'],
        ] as [$icon, $label])
        <div class="group bg-white p-8 rounded-xl shadow-sm border-b-4 border-transparent hover:border-secondary hover:-translate-y-2 transition-all flex flex-col items-center text-center">
            <div class="animated-circle-container mb-6">
                <div class="orbit-track border-dashed"><div class="orbit-dot dot-1"></div><div class="orbit-dot dot-2"></div></div>
                <span class="material-symbols-outlined text-secondary text-[48px] relative z-10">{{ $icon }}</span>
            </div>
            <h3 class="text-xl font-bold text-primary-container group-hover:text-primary transition-colors">{{ $label }}</h3>
        </div>
        @endforeach
    </div>

    <div class="text-center mb-12">
        <h2 class="text-3xl font-bold text-primary uppercase">Servicios Complementarios</h2>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 max-w-[1440px] mx-auto mb-16">
        @foreach([
            ['gastroenterology','Endoscopía'],
            ['pregnant_woman',  'Ecografía'],
            ['biotech',         'Laboratorio'],
            ['monitor_heart',   'Electrocardiografía'],
        ] as [$icon, $label])
        <div class="group bg-white p-8 rounded-xl shadow-sm hover:shadow-md hover:-translate-y-1 transition-all flex flex-col items-center text-center">
            <div class="animated-circle-container mb-6">
                <div class="orbit-track orbit-track-green border-dashed"><div class="orbit-dot orbit-dot-green dot-1"></div><div class="orbit-dot orbit-dot-green dot-2"></div></div>
                <span class="material-symbols-outlined text-[#4CAF50] text-[40px] relative z-10">{{ $icon }}</span>
            </div>
            <h3 class="text-xl font-bold text-primary-container group-hover:text-primary transition-colors">{{ $label }}</h3>
        </div>
        @endforeach
    </div>
    <div class="flex justify-center">
        <button class="bg-secondary text-white text-sm font-bold tracking-wide px-10 py-3 rounded-lg hover:opacity-90 transition-all duration-300">Ver más +</button>
    </div>
</section>

{{-- ══════════════════════ UNIDADES MÉDICAS ══════════════════════ --}}
<section class="py-20 px-[48px] bg-white">
    <div class="text-center mb-16">
        <span class="text-sm font-bold tracking-widest text-secondary uppercase">— ALEMANA RED DE SALUD —</span>
        <h2 class="text-3xl font-bold text-primary mt-2">Unidades Médicas</h2>
    </div>
    <div class="max-w-[1440px] mx-auto">
        <div class="flex items-center justify-between gap-8 py-4 opacity-70 grayscale hover:grayscale-0 transition-all duration-500 overflow-x-auto no-scrollbar">
            @foreach([
                ['Urocentro',   'Laser Alemana'],
                ['Fertivida',   'Fertilidad y Reproducción'],
                ['Unidad CT',   'S.R.L.'],
                ['EndoCenter',  'Clínica Alemana'],
                ['Hemodinamia', 'Alemana'],
            ] as [$name, $sub])
            <div class="flex-shrink-0 w-48 text-center px-4">
                <div class="h-16 flex items-center justify-center font-bold text-primary opacity-40">{{ $name }}</div>
                <p class="text-[10px] text-on-surface-variant uppercase tracking-widest">{{ $sub }}</p>
            </div>
            @endforeach
        </div>
        <div class="flex justify-center gap-2 mt-12">
            <div class="w-2 h-2 rounded-full bg-outline-variant"></div>
            <div class="w-6 h-2 rounded-full bg-primary"></div>
            <div class="w-2 h-2 rounded-full bg-outline-variant"></div>
            <div class="w-2 h-2 rounded-full bg-outline-variant"></div>
            <div class="w-2 h-2 rounded-full bg-outline-variant"></div>
        </div>
    </div>
</section>

{{-- ══════════════════════ SIENTE LA DIFERENCIA ══════════════════════ --}}
<section class="py-24 px-[48px] bg-white border-t border-outline-variant">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-20 items-center">
        <div class="relative">
            <div class="grid grid-cols-2 gap-4">
                <img alt="Registros médicos digitales" class="w-full h-64 object-cover rounded-xl shadow-md"
                     src="https://lh3.googleusercontent.com/aida-public/AB6AXuBtcTPFWnlHE30N8G4FvttS7ocRcbkjSvOoNHp-7RHhOvtvL3gfCAZDfyNJxXoY0LLWAE2GmYOw9fv6JQUk11SG_BEe7GSJHNIilIvtYgj1NL9chVP6fNKqmyZXTzImUGpjwELHHUcHz_TdQ7inLYRFqTGQVzkhxMz0RGplIG6FK_y8LTHDOmcLoWvJ5yqeQzj9xXa2ZklrA5K3TlNhAgbmDHpWjI3LVYWgxTQGjKv6-cp9knRIdJHvUFr27D51O2L8C-60-F6173c"/>
                <img alt="Recepción médica" class="w-full h-48 object-cover rounded-xl mt-8 shadow-md"
                     src="https://lh3.googleusercontent.com/aida-public/AB6AXuDOSQTFGAuD9sfrlrhyggNA7cbMiq4MFd8-lqrC1BJRyg3e_YoRQl4Re2Kqhyra_YcW9cLyMrE4rnop1Bh1GV3s5C4eZalBS4EZGvtTMTb-1VZxG-GOehdOHjsyAKpRiXlkKPjl23cptfx_GRXs8PoYtX9AZXyo1OL7iQyCIUTgWSPR-DlQ91FcHuDLVsa45V2HJCMLW1DrEn7s8kl9Ke6qofG-z0dqpo9OypQLJlhaWdSV-Ot97N_gtOtsFyvFip0VThfw7hHsmXg"/>
            </div>
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-primary-container p-6 rounded-full shadow-2xl">
                <span class="material-symbols-outlined text-white text-6xl">add</span>
            </div>
        </div>
        <div>
            <span class="text-sm font-bold tracking-widest text-secondary uppercase">Siente la diferencia —</span>
            <div class="flex flex-wrap gap-4 md:gap-8 mt-6 border-b border-outline-variant mb-8">
                <button class="tab-btn text-xl font-bold text-primary-container border-b-4 border-secondary pb-4 transition-all" id="tab-cercania"   onclick="switchTab('cercania')">Cercanía</button>
                <button class="tab-btn text-xl font-bold text-on-surface-variant opacity-60 pb-4 hover:opacity-100 transition-all" id="tab-excelencia" onclick="switchTab('excelencia')">Excelencia</button>
                <button class="tab-btn text-xl font-bold text-on-surface-variant opacity-60 pb-4 hover:opacity-100 transition-all" id="tab-confort"    onclick="switchTab('confort')">Confort</button>
                <button class="tab-btn text-xl font-bold text-on-surface-variant opacity-60 pb-4 hover:opacity-100 transition-all" id="tab-innovacion" onclick="switchTab('innovacion')">Innovación</button>
            </div>
            <div class="min-h-[300px]">
                <div class="tab-pane tab-content-enter" id="content-cercania">
                    <h2 class="text-3xl font-bold text-primary mb-6">Te cuidamos como en casa.</h2>
                    <p class="text-lg text-on-surface-variant mb-8 leading-relaxed">En Alemana Red de Salud creemos que la atención médica también debe sentirse humana. Por eso, cada consulta, diagnóstico y tratamiento se realiza con empatía, acompañamiento y calidez, para que nuestros pacientes se sientan seguros y tranquilos.</p>
                    <div class="flex flex-col gap-4">
                        <div class="flex items-center gap-4"><span class="material-symbols-outlined text-secondary">check_circle</span><span class="text-base text-on-surface-variant">Atención empática y personalizada</span></div>
                        <div class="flex items-center gap-4"><span class="material-symbols-outlined text-secondary">check_circle</span><span class="text-base text-on-surface-variant">Ambiente acogedor y profesional</span></div>
                    </div>
                </div>
                <div class="tab-pane hidden" id="content-excelencia">
                    <h2 class="text-3xl font-bold text-primary mb-6">El mejor centro de salud y cuidado de Bolivia.</h2>
                    <p class="text-lg text-on-surface-variant mb-8 leading-relaxed">Contamos con un equipo médico altamente capacitado, servicios especializados y atención integral. Nuestro compromiso es ofrecer excelencia médica, accesibilidad y confianza a cada paciente.</p>
                    <div class="flex flex-col gap-4">
                        <div class="flex items-center gap-4"><span class="material-symbols-outlined text-secondary">check_circle</span><span class="text-base text-on-surface-variant">Especialistas certificados</span></div>
                        <div class="flex items-center gap-4"><span class="material-symbols-outlined text-secondary">check_circle</span><span class="text-base text-on-surface-variant">Compromiso con la calidad</span></div>
                    </div>
                </div>
                <div class="tab-pane hidden" id="content-confort">
                    <h2 class="text-3xl font-bold text-primary mb-6">Una gran infraestructura a tu servicio.</h2>
                    <p class="text-lg text-on-surface-variant mb-8 leading-relaxed">Nuestras clínicas están diseñadas para ofrecer comodidad, accesibilidad y eficiencia. Espacios modernos, consultorios equipados y áreas pensadas para el bienestar de cada paciente.</p>
                    <div class="flex flex-col gap-4">
                        <div class="flex items-center gap-4"><span class="material-symbols-outlined text-secondary">check_circle</span><span class="text-base text-on-surface-variant">Espacios modernos y funcionales</span></div>
                        <div class="flex items-center gap-4"><span class="material-symbols-outlined text-secondary">check_circle</span><span class="text-base text-on-surface-variant">Accesibilidad garantizada</span></div>
                    </div>
                </div>
                <div class="tab-pane hidden" id="content-innovacion">
                    <h2 class="text-3xl font-bold text-primary mb-6">Tecnología de última generación.</h2>
                    <p class="text-lg text-on-surface-variant mb-8 leading-relaxed">Equipos médicos de vanguardia y procesos digitalizados nos permiten brindar resultados precisos, seguros y rápidos, garantizando la mejor atención en cada especialidad.</p>
                    <div class="flex flex-col gap-4">
                        <div class="flex items-center gap-4"><span class="material-symbols-outlined text-secondary">check_circle</span><span class="text-base text-on-surface-variant">Equipamiento médico avanzado</span></div>
                        <div class="flex items-center gap-4"><span class="material-symbols-outlined text-secondary">check_circle</span><span class="text-base text-on-surface-variant">Resultados rápidos y precisos</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ══════════════════════ SUCURSALES ══════════════════════ --}}
<section class="py-24 px-[48px] bg-bg-light">
    <div class="container mx-auto max-w-[1440px]">
        <div class="text-center mb-16 max-w-4xl mx-auto">
            <p class="text-base text-on-surface-variant leading-relaxed">
                Estamos cerca de ti para brindarte la mejor atención donde la necesites. Nuestras sucursales cuentan con instalaciones modernas, equipos de última generación y profesionales comprometidos con tu salud y bienestar.
            </p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach([
                ['Clínica San Jorge',  null,
                 'https://lh3.googleusercontent.com/aida-public/AB6AXuDOSQTFGAuD9sfrlrhyggNA7cbMiq4MFd8-lqrC1BJRyg3e_YoRQl4Re2Kqhyra_YcW9cLyMrE4rnop1Bh1GV3s5C4eZalBS4EZGvtTMTb-1VZxG-GOehdOHjsyAKpRiXlkKPjl23cptfx_GRXs8PoYtX9AZXyo1OL7iQyCIUTgWSPR-DlQ91FcHuDLVsa45V2HJCMLW1DrEn7s8kl9Ke6qofG-z0dqpo9OypQLJlhaWdSV-Ot97N_gtOtsFyvFip0VThfw7hHsmXg'],
                ['Clínica Achumani',   null,
                 'https://lh3.googleusercontent.com/aida-public/AB6AXuBtcTPFWnlHE30N8G4FvttS7ocRcbkjSvOoNHp-7RHhOvtvL3gfCAZDfyNJxXoY0LLWAE2GmYOw9fv6JQUk11SG_BEe7GSJHNIilIvtYgj1NL9chVP6fNKqmyZXTzImUGpjwELHHUcHz_TdQ7inLYRFqTGQVzkhxMz0RGplIG6FK_y8LTHDOmcLoWvJ5yqeQzj9xXa2ZklrA5K3TlNhAgbmDHpWjI3LVYWgxTQGjKv6-cp9knRIdJHvUFr27D51O2L8C-60-F6173c'],
                ['Centro Médico',      'Edif. Q Business Center, Piso 2 — Achumani',
                 'https://lh3.googleusercontent.com/aida-public/AB6AXuBytuXywJU2uNaAUqTpgtrQziAvHy9RNL5fVWsnfRCd6YtfmFfjsR8PVQfVpYEij3iSNxA-1DrYzcDD3Q73ZsWfwaS9TMzfoxPugzfyvd_3d7MgXzbOX7T3CXCLi5C0q2bUJNZqKsuHpvZhWxpB1ZLlHN5J7ZByenzFV7YjiDHovatLK0IUk1DoDgUV3pTPIsyKO3iK8yXD6oetQRNKOIX1yJn6XF69v-AoaAPf3Dhy9mQIjEKCEg0o9q9RBfans7wmfbqTIWMg9sw'],
            ] as [$title, $sub, $img])
            <div class="bg-white rounded-xl shadow-md overflow-hidden group hover:shadow-xl transition-shadow {{ $title === 'Centro Médico' ? 'ring-2 ring-secondary ring-offset-2' : '' }}">
                <div class="h-64 overflow-hidden relative">
                    <img alt="{{ $title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" src="{{ $img }}"/>
                    <div class="absolute bottom-4 right-4">
                        <button class="bg-secondary text-white w-10 h-10 rounded-lg flex items-center justify-center shadow-lg hover:bg-secondary-container transition-colors">
                            <span class="material-symbols-outlined">arrow_forward</span>
                        </button>
                    </div>
                </div>
                <div class="p-6 text-center">
                    <h3 class="text-xl font-bold text-primary-container">{{ $title }}</h3>
                    @if($sub)
                    <p class="text-xs text-secondary font-bold tracking-wide mt-1 uppercase">{{ $sub }}</p>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ══════════════════════ FOOTER ══════════════════════ --}}
<footer class="bg-footer-dark pt-16 pb-8 text-white relative">
    <div class="container mx-auto px-[48px]">
        <div class="text-center mb-12">
            <h3 class="text-xl font-semibold text-white">
                Te cuidamos como en casa
                <span class="material-symbols-outlined align-middle ml-1" style="font-variation-settings:'FILL' 1;">favorite</span>
            </h3>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-16 items-start mb-16">
            <div class="flex flex-col items-center md:items-start gap-6">
                <img alt="Logo Alemana" class="h-24 w-auto brightness-0 invert" src="{{ asset('images/logo-alemana.png') }}"/>
                <div class="flex gap-6 mt-4">
                    <a class="hover:text-secondary transition-colors" href="#"><span class="material-symbols-outlined">facebook</span></a>
                    <a class="hover:text-secondary transition-colors" href="#"><span class="material-symbols-outlined">photo_camera</span></a>
                    <a class="hover:text-secondary transition-colors" href="#"><span class="material-symbols-outlined">video_camera_front</span></a>
                    <a class="hover:text-secondary transition-colors" href="#"><span class="material-symbols-outlined">share</span></a>
                    <a class="hover:text-secondary transition-colors" href="#"><span class="material-symbols-outlined">chat</span></a>
                </div>
            </div>
            <div class="flex flex-col gap-6">
                <h4 class="text-sm font-bold tracking-widest border-b border-white/20 pb-2 w-fit uppercase">Contáctanos</h4>
                <div class="flex flex-col gap-4">
                    <div class="flex items-start gap-4">
                        <span class="material-symbols-outlined text-secondary mt-1">call</span>
                        <div class="flex flex-col">
                            <span class="text-base">(+591) 2 2432521</span>
                            <span class="text-base">(+591) 2 2433676</span>
                            <span class="text-xs opacity-70">Clínica San Jorge - La Paz</span>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <span class="material-symbols-outlined text-secondary mt-1">call</span>
                        <div class="flex flex-col">
                            <span class="text-base">(+591) 2 2432521</span>
                            <span class="text-xs opacity-70">Centro Médico</span>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <span class="material-symbols-outlined text-secondary mt-1">ambulance</span>
                        <div class="flex flex-col">
                            <span class="text-base font-bold text-secondary-container">(+591) 78991100</span>
                            <span class="text-xs opacity-70">Ambulancias 24/7</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex flex-col gap-6">
                <h4 class="text-sm font-bold tracking-widest border-b border-white/20 pb-2 w-fit uppercase">Visítanos</h4>
                <div class="flex flex-col gap-6">
                    <div class="flex items-start gap-4">
                        <span class="material-symbols-outlined text-secondary mt-1">location_on</span>
                        <p class="text-sm leading-relaxed">C 5, esq. Av Tomasa Murillo, Nº-586, junto al Centro Médico, Z. Achumani<br/><span class="font-bold">Clínica Achumani - La Paz</span></p>
                    </div>
                    <div class="flex items-start gap-4">
                        <span class="material-symbols-outlined text-secondary mt-1">location_on</span>
                        <p class="text-sm leading-relaxed">C 5, esq. Av. Tomasa Murillo, Nº-578, Edif. Q Business Center p. 2, Z. Achumani<br/><span class="font-bold">Centro Médico - La Paz</span></p>
                    </div>
                    <div class="flex items-start gap-4">
                        <span class="material-symbols-outlined text-secondary mt-1">location_on</span>
                        <p class="text-sm leading-relaxed">Av. 6 de Agosto, Nº 2821, Z. San Jorge<br/><span class="font-bold">Clínica San Jorge - La Paz</span></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="pt-8 border-t border-white/10 flex flex-col md:flex-row justify-between items-center gap-4">
            <p class="text-xs opacity-70">Alemana Red de Salud</p>
            <p class="text-xs opacity-70">Copyright © 2025</p>
        </div>
    </div>
    <div class="absolute bottom-8 right-[48px]">
        <button onclick="window.scrollTo({top:0,behavior:'smooth'})"
                class="bg-secondary p-3 rounded-lg shadow-lg hover:bg-secondary-container transition-colors">
            <span class="material-symbols-outlined text-white">arrow_upward</span>
        </button>
    </div>
</footer>

<script>
    function switchTab(tabId) {
        document.querySelectorAll('.tab-btn').forEach(function(btn) {
            btn.classList.remove('text-primary-container', 'border-b-4', 'border-secondary', 'opacity-100');
            btn.classList.add('text-on-surface-variant', 'opacity-60');
        });
        var btn = document.getElementById('tab-' + tabId);
        btn.classList.add('text-primary-container', 'border-b-4', 'border-secondary', 'opacity-100');
        btn.classList.remove('text-on-surface-variant', 'opacity-60');

        document.querySelectorAll('.tab-pane').forEach(function(pane) {
            pane.classList.add('hidden');
            pane.classList.remove('tab-content-enter');
        });
        var pane = document.getElementById('content-' + tabId);
        pane.classList.remove('hidden');
        void pane.offsetWidth;
        pane.classList.add('tab-content-enter');
    }
</script>
</body>
</html>
