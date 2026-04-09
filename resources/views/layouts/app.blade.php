<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'TaskFlow') — TaskFlow</title>

    {{-- Google Fonts: DM Serif Display (headings) + DM Sans (body) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        /* ── Design tokens ──────────────────────────────────────────────── */
        :root {
            --bg:          #F7F5F0;
            --surface:     #FFFFFF;
            --border:      #E4E0D8;
            --text:        #1C1917;
            --text-muted:  #78716C;
            --accent:      #2563EB;
            --accent-dark: #1D4ED8;
            --danger:      #DC2626;
            --success-bg:  #DCFCE7;
            --success-txt: #166534;
            --error-bg:    #FEE2E2;
            --error-txt:   #991B1B;
            --badge-bg:    #EEF2FF;
            --badge-txt:   #3730A3;
            --radius:      10px;
            --shadow:      0 1px 3px rgba(0,0,0,.08), 0 1px 2px rgba(0,0,0,.06);
            --shadow-lg:   0 4px 12px rgba(0,0,0,.10);
        }

        /* ── Reset & base ───────────────────────────────────────────────── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            font-size: 15px;
            line-height: 1.6;
        }

        /* ── Nav ────────────────────────────────────────────────────────── */
        .nav {
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            padding: 0 2rem;
            display: flex;
            align-items: center;
            gap: 2rem;
            height: 60px;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: var(--shadow);
        }

        .nav__brand {
            font-family: 'DM Serif Display', serif;
            font-size: 1.35rem;
            color: var(--text);
            text-decoration: none;
            letter-spacing: -.3px;
        }

        .nav__links { display: flex; gap: .25rem; margin-left: .5rem; }

        .nav__link {
            padding: .4rem .85rem;
            border-radius: 6px;
            text-decoration: none;
            color: var(--text-muted);
            font-weight: 500;
            font-size: .9rem;
            transition: background .15s, color .15s;
        }
        .nav__link:hover,
        .nav__link.active {
            background: var(--bg);
            color: var(--text);
        }

        /* ── Page wrapper ───────────────────────────────────────────────── */
        .page { max-width: 860px; margin: 2.5rem auto; padding: 0 1.5rem 4rem; }

        /* ── Page header ────────────────────────────────────────────────── */
        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.75rem;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .page-title {
            font-family: 'DM Serif Display', serif;
            font-size: 1.9rem;
            letter-spacing: -.4px;
            line-height: 1.2;
        }

        /* ── Buttons ────────────────────────────────────────────────────── */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            padding: .5rem 1.1rem;
            border-radius: var(--radius);
            font-family: inherit;
            font-size: .875rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            border: 1.5px solid transparent;
            transition: background .15s, border-color .15s, color .15s, transform .1s;
        }
        .btn:active { transform: scale(.97); }

        .btn--primary { background: var(--accent); color: #fff; border-color: var(--accent); }
        .btn--primary:hover { background: var(--accent-dark); border-color: var(--accent-dark); }

        .btn--ghost {
            background: transparent;
            color: var(--text-muted);
            border-color: var(--border);
        }
        .btn--ghost:hover { background: var(--bg); color: var(--text); }

        .btn--danger { background: transparent; color: var(--danger); border-color: var(--danger); }
        .btn--danger:hover { background: var(--danger); color: #fff; }

        .btn--sm { padding: .3rem .75rem; font-size: .8rem; }

        /* ── Alerts ─────────────────────────────────────────────────────── */
        .alert {
            padding: .8rem 1.1rem;
            border-radius: var(--radius);
            margin-bottom: 1.5rem;
            font-size: .9rem;
            font-weight: 500;
        }
        .alert--success { background: var(--success-bg); color: var(--success-txt); }
        .alert--error   { background: var(--error-bg);   color: var(--error-txt); }

        /* ── Card ───────────────────────────────────────────────────────── */
        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
        }

        /* ── Form ───────────────────────────────────────────────────────── */
        .form { display: flex; flex-direction: column; gap: 1.25rem; }

        .field { display: flex; flex-direction: column; gap: .4rem; }

        .field label {
            font-size: .875rem;
            font-weight: 600;
            color: var(--text);
        }

        .field input,
        .field select,
        .field textarea {
            padding: .6rem .85rem;
            border: 1.5px solid var(--border);
            border-radius: var(--radius);
            font-family: inherit;
            font-size: .9rem;
            color: var(--text);
            background: var(--surface);
            transition: border-color .15s, box-shadow .15s;
            width: 100%;
        }
        .field input:focus,
        .field select:focus,
        .field textarea:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(37,99,235,.12);
        }

        .field textarea { resize: vertical; min-height: 80px; }

        .field__error {
            font-size: .8rem;
            color: var(--danger);
            font-weight: 500;
        }

        .form-actions { display: flex; gap: .75rem; margin-top: .5rem; }

        /* ── Badge ──────────────────────────────────────────────────────── */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: .15rem .55rem;
            border-radius: 20px;
            font-size: .75rem;
            font-weight: 600;
            background: var(--badge-bg);
            color: var(--badge-txt);
        }

        /* ── Priority chip ──────────────────────────────────────────────── */
        .priority {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 26px;
            height: 26px;
            border-radius: 50%;
            background: var(--bg);
            border: 1.5px solid var(--border);
            font-size: .75rem;
            font-weight: 700;
            color: var(--text-muted);
            flex-shrink: 0;
        }

        /* ── Empty state ────────────────────────────────────────────────── */
        .empty {
            text-align: center;
            padding: 3.5rem 1rem;
            color: var(--text-muted);
        }
        .empty__icon { font-size: 2.5rem; margin-bottom: .75rem; }
        .empty__text { font-size: .95rem; margin-bottom: 1.25rem; }
    </style>

    @stack('styles')
</head>
<body>

<nav class="nav">
    <a href="{{ route('tasks.index') }}" class="nav__brand">✦ TaskFlow</a>
    <div class="nav__links">
        <a href="{{ route('tasks.index') }}"
           class="nav__link {{ request()->routeIs('tasks.*') ? 'active' : '' }}">
            Tasks
        </a>
        <a href="{{ route('projects.index') }}"
           class="nav__link {{ request()->routeIs('projects.*') ? 'active' : '' }}">
            Projects
        </a>
    </div>
</nav>

<main class="page">

    {{-- Flash messages --}}
    @if (session('success'))
        <div class="alert alert--success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert--error">{{ session('error') }}</div>
    @endif

    @yield('content')

</main>

@stack('scripts')
</body>
</html>
