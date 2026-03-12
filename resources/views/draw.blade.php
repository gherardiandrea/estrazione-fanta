<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Estrazione Fanta</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Space+Grotesk:wght@400;500;700&display=swap" rel="stylesheet">
    @vite('resources/js/draw.js')
    <style>
        :root {
            --bg-deep: #0b1f3a;
            --bg-mid: #143a62;
            --bg-warm: #f29f05;
            --ink: #10213a;
            --card: #f8f3e8;
            --accent: #e63946;
            --accent-2: #2a9d8f;
            --muted: #5c6f83;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Space Grotesk', sans-serif;
            color: var(--ink);
            background:
                radial-gradient(circle at 15% 20%, rgba(242, 159, 5, 0.35), transparent 40%),
                radial-gradient(circle at 85% 15%, rgba(42, 157, 143, 0.32), transparent 35%),
                linear-gradient(140deg, var(--bg-deep), var(--bg-mid));
            display: grid;
            place-items: center;
            padding: 24px;
            overflow-x: hidden;
        }

        body::before,
        body::after {
            content: '';
            position: fixed;
            width: 220px;
            height: 220px;
            border-radius: 50%;
            z-index: 0;
            filter: blur(16px);
            opacity: 0.5;
        }

        body::before {
            top: -40px;
            left: -50px;
            background: rgba(230, 57, 70, 0.5);
        }

        body::after {
            right: -30px;
            bottom: -50px;
            background: rgba(242, 159, 5, 0.45);
        }

        .panel {
            width: min(980px, 100%);
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.96), rgba(248, 243, 232, 0.95));
            border-radius: 28px;
            box-shadow: 0 24px 80px rgba(7, 16, 30, 0.45);
            border: 1px solid rgba(255, 255, 255, 0.55);
            position: relative;
            z-index: 1;
            overflow: hidden;
            animation: riseIn 650ms ease-out both;
        }

        .panel::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: linear-gradient(90deg, rgba(16, 33, 58, 0.06) 1px, transparent 1px), linear-gradient(rgba(16, 33, 58, 0.06) 1px, transparent 1px);
            background-size: 22px 22px;
            pointer-events: none;
        }

        .content {
            position: relative;
            padding: 28px;
        }

        .hero {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 18px;
        }

        .title {
            margin: 0;
            font-family: 'Bebas Neue', sans-serif;
            font-size: clamp(2.2rem, 4.2vw, 4.2rem);
            letter-spacing: 1.5px;
            color: #11284a;
            line-height: 1;
            text-wrap: balance;
        }

        .badge {
            border: 2px dashed rgba(16, 33, 58, 0.35);
            padding: 8px 14px;
            border-radius: 999px;
            font-size: 0.88rem;
            color: #2c4364;
            background: rgba(255, 255, 255, 0.6);
            white-space: nowrap;
        }

        .setup-card {
            background: var(--card);
            border-radius: 20px;
            padding: 18px;
            border: 1px solid rgba(16, 33, 58, 0.15);
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.65);
            animation: floatIn 620ms ease-out 80ms both;
        }

        .setup-text {
            margin: 0 0 14px;
            color: #3a536f;
            line-height: 1.45;
        }

        .mode-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-bottom: 12px;
        }

        .mode-option {
            border: 1px solid rgba(16, 33, 58, 0.18);
            border-radius: 12px;
            padding: 12px;
            background: rgba(255, 255, 255, 0.8);
        }

        .mode-option input {
            margin-right: 6px;
        }

        .mode-option small {
            display: block;
            color: #5f7186;
            margin-top: 6px;
        }

        .setup-label {
            display: block;
            font-size: 0.9rem;
            color: #294764;
            margin: 4px 0 6px;
            font-weight: 700;
        }

        .setup-input {
            width: 100%;
            border-radius: 12px;
            border: 1px solid rgba(16, 33, 58, 0.2);
            padding: 10px 12px;
            font-family: 'Space Grotesk', sans-serif;
            font-size: 0.92rem;
            min-height: 130px;
            resize: vertical;
            background: rgba(255, 255, 255, 0.9);
        }

        .hint {
            margin: 6px 0 0;
            color: #607388;
            font-size: 0.84rem;
        }

        .errors {
            margin: 10px 0 0;
            padding-left: 18px;
            color: #b5182a;
        }

        .reconfigure-form {
            margin: 0;
        }

        .button-secondary {
            background: linear-gradient(90deg, #405a75, #516f8e);
            color: #fff;
        }

        .draw-card {
            background: var(--card);
            border-radius: 20px;
            padding: 18px;
            border: 1px solid rgba(16, 33, 58, 0.15);
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.65);
            margin-bottom: 20px;
            animation: floatIn 620ms ease-out 80ms both;
        }

        .meta-line {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
        }

        .help-pill {
            width: 20px;
            height: 20px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(16, 33, 58, 0.25);
            background: rgba(255, 255, 255, 0.75);
            font-size: 0.72rem;
            font-weight: 700;
            color: #2b4463;
            cursor: help;
            user-select: none;
        }

        #draw-number {
            margin: 0;
            color: #4f6078;
            font-size: 1rem;
        }

        #drawn-team {
            display: block;
            font-family: 'Bebas Neue', sans-serif;
            font-size: clamp(2rem, 5vw, 3.8rem);
            letter-spacing: 1px;
            color: var(--accent);
            margin: 8px 0 4px;
            min-height: 1.2em;
            text-wrap: balance;
        }

        #completed-cycles {
            margin: 0;
            font-weight: 700;
            color: #294764;
            font-size: 1rem;
        }

        .actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 8px;
        }

        button {
            border: none;
            cursor: pointer;
            border-radius: 12px;
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 700;
            font-size: 0.96rem;
            padding: 12px 18px;
            transition: transform 150ms ease, filter 150ms ease, opacity 150ms ease, box-shadow 150ms ease;
            box-shadow: 0 10px 18px rgba(16, 33, 58, 0.24);
        }

        button:hover {
            transform: translateY(-1px);
            filter: brightness(1.05);
        }

        button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        button:active {
            transform: translateY(1px) scale(0.99);
            filter: brightness(0.98);
        }

        button.primary-button {
            background: linear-gradient(90deg, #1f8a78, var(--accent-2));
            color: white;
        }

        button.reset-button {
            background: linear-gradient(90deg, #d62839, var(--accent));
            color: white;
        }

        #feedback {
            min-height: 1.35em;
            margin: 10px 0 6px;
            font-size: 0.96rem;
            font-weight: 700;
        }

        #feedback.info {
            color: var(--muted);
        }

        #feedback.success {
            color: #1f8a78;
        }

        #feedback.error {
            color: #b5182a;
        }

        .list-title {
            font-size: 1rem;
            letter-spacing: 0.7px;
            color: #1f3654;
            margin: 18px 0 10px;
            text-transform: uppercase;
        }

        ul {
            list-style: none;
            margin: 0;
            padding: 0;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 10px;
        }

        ul li {
            background: linear-gradient(140deg, #1f3654, #23527f);
            color: #fff;
            border-radius: 12px;
            padding: 12px 10px;
            text-align: center;
            font-weight: 700;
            letter-spacing: 0.4px;
            box-shadow: 0 8px 20px rgba(20, 58, 98, 0.25);
            animation: fadeSlide 360ms ease both;
        }

        ul li.empty-state {
            grid-column: 1 / -1;
            background: linear-gradient(120deg, #506b88, #405a75);
            font-weight: 500;
            letter-spacing: 0.2px;
            border: 1px dashed rgba(255, 255, 255, 0.3);
        }

        @media (max-width: 740px) {
            .content {
                padding: 18px;
            }

            .hero {
                flex-direction: column;
                align-items: flex-start;
            }

            .actions {
                width: 100%;
            }

            button {
                flex: 1;
            }

            .mode-grid {
                grid-template-columns: 1fr;
            }
        }

        @keyframes riseIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes floatIn {
            from {
                opacity: 0;
                transform: translateY(12px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeSlide {
            from {
                opacity: 0;
                transform: translateY(8px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body data-draw-url="{{ route('draw') }}" data-reset-url="{{ route('reset') }}">
    <main class="panel">
        <section class="content">
            <header class="hero">
                <h1 class="title">Estrazione Fanta</h1>
                <span class="badge">Randomizer squadre</span>
            </header>

            @if($needsSetup)
                <section class="setup-card">
                    <p class="setup-text">
                        Scegli se partire dalle squadre predefinite oppure inserire una lista personalizzata.
                        Dopo la configurazione puoi iniziare subito le estrazioni.
                    </p>

                    <form method="POST" action="{{ route('setup') }}">
                        @csrf
                        <div class="mode-grid">
                            <label class="mode-option">
                                <input type="radio" name="mode" value="default" {{ old('mode', 'default') === 'default' ? 'checked' : '' }}>
                                Usa squadre predefinite
                                <small>{{ count($defaultTeams) }} squadre pronte all'uso.</small>
                            </label>

                            <label class="mode-option">
                                <input type="radio" name="mode" value="custom" {{ old('mode') === 'custom' ? 'checked' : '' }}>
                                Inserisci squadre personalizzate
                                <small>Separate da a capo, virgola o punto e virgola.</small>
                            </label>
                        </div>

                        <label class="setup-label" for="custom_teams">Lista personalizzata (almeno 2 squadre)</label>
                        <textarea class="setup-input" id="custom_teams" name="custom_teams" placeholder="Es: Team A&#10;Team B&#10;Team C">{{ old('custom_teams') }}</textarea>
                        <p class="hint">Se scegli "predefinite", questo campo viene ignorato.</p>

                        @if($errors->any())
                            <ul class="errors">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        @endif

                        <div class="actions" style="margin-top: 14px;">
                            <button class="primary-button" type="submit">Avvia estrazione</button>
                        </div>
                    </form>
                </section>
            @else
                <section class="draw-card">
                    <p id="draw-number">{{ $drawNumber > 0 ? $drawNumber . '° squadra estratta' : '' }}</p>
                    <strong id="drawn-team">{{ $lastDrawnTeam }}</strong>
                    <div class="meta-line">
                        <p id="completed-cycles">Cicli completati: {{ $completedCycles }}</p>
                        <span class="help-pill" title="Un ciclo e completo quando vengono estratte tutte le squadre una volta.">?</span>
                    </div>
                </section>

                <div class="actions">
                    <button class="primary-button" id="draw-team-button">Estrai squadra</button>
                    <button class="reset-button" id="reset-cycle-button">Resetta ciclo</button>
                    <form class="reconfigure-form" method="POST" action="{{ route('new-configuration') }}">
                        @csrf
                        <button class="button-secondary" type="submit">Nuova configurazione</button>
                    </form>
                </div>

                <p id="feedback" class="info"></p>

                <h2 class="list-title">Squadre restanti</h2>
                <ul id="remaining-teams">
                    @forelse($remainingTeams as $team)
                        <li>{{ $team }}</li>
                    @empty
                        <li class="empty-state">Nessuna squadra rimanente: al prossimo click parte un nuovo ciclo.</li>
                    @endforelse
                </ul>
            @endif
        </section>
    </main>

</body>
</html>
