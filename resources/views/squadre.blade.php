<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estrazione Squadra</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            text-align: center;
            padding: 50px;
        }

        h1 {
            color: #2c3e50;
            margin-bottom: 30px;
        }

        p {
            font-size: 1.5em;
            margin: 20px 0;
        }

        #squadra-estratta {
            font-weight: bold;
            color: #e74c3c;
        }

        /* Stile per i pulsanti principali */
        button.primary-button {
            background-color: #2ecc71; /* Verde */
            color: white;
            border: none;
            padding: 15px 30px;
            font-size: 1em;
            cursor: pointer;
            border-radius: 5px;
            margin: 10px;
            transition: background-color 0.3s;
        }

        button.primary-button:hover {
            background-color: #27ae60;
        }

        button.primary-button:disabled {
            background-color: #bdc3c7;
            cursor: not-allowed;
        }

        /* Stile per i pulsanti di reset */
        button.reset-button {
            background-color: #e74c3c; /* Rosso */
            color: white;
            border: none;
            padding: 15px 30px;
            font-size: 1em;
            cursor: pointer;
            border-radius: 5px;
            margin: 10px;
            transition: background-color 0.3s;
        }

        button.reset-button:hover {
            background-color: #c0392b;
        }

        button.reset-button:disabled {
            background-color: #bdc3c7;
            cursor: not-allowed;
        }

        /* Stile per le squadre restanti */
        ul {
            list-style-type: none;
            padding: 0;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            margin-top: 20px;
        }

        ul li {
            background-color: #3498db; /* Blu */
            margin: 10px;
            padding: 15px 20px;
            border-radius: 10px;
            font-size: 1.2em;
            color: white;
            width: 200px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        ul li:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
        }

        #squadre-restanti {
            cursor: pointer;
        }
    </style>
</head>
<body>
    <h1 style="margin-bottom:2px">Estrazione Squadra</h1>

    <p id="numero-estrazione">{{ $numeroEstrazione }}° squadra estratta</p>
    <p style="margin-bottom: 0px; margin-top: 0px"><strong id="squadra-estratta">{{ $squadra }}</strong></p>
    <p id="cicli-completati">Cicli completati: {{ $cicliCompletati }}</p>

    <button class="primary-button" id="estrai-squadra">Estrai Squadra</button>
    <button class="reset-button" id="reset-squadre">Reset</button>

    <h2>Squadre Restanti:</h2>
    <ul id="squadre-restanti">
        @foreach($squadreRestanti as $s)
            <li>{{ $s }}</li>
        @endforeach
    </ul>

    <script>
        $(document).ready(function() {
            $('#estrai-squadra').click(function() {
                $.ajax({
                    url: '{{ route("estrai") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        $('#squadra-estratta').html(response.squadra);
                        if (response.numeroEstrazione > 0) {
                            $('#numero-estrazione').text(response.numeroEstrazione + '° squadra estratta');
                        } else {
                            $('#numero-estrazione').text('');
                        }
                        $('#cicli-completati').text('Cicli completati: ' + response.cicliCompletati);

                        var squadreRestantiHtml = '';
                        response.squadreRestanti.forEach(function(squadra) {
                            squadreRestantiHtml += '<li>' + squadra + '</li>';
                        });

                        $('#squadre-restanti').html(squadreRestantiHtml);
                    }
                });
            });

            $('#reset-squadre').click(function() {
                $.ajax({
                    url: '{{ route("reset") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        $('#squadra-estratta').html('Nessuna squadra estratta');
                        $('#numero-estrazione').text('');
                        $('#cicli-completati').text('Cicli completati: 0');
                        
                        var squadreRestantiHtml = '';
                        response.squadreRestanti.forEach(function(squadra) {
                            squadreRestantiHtml += '<li>' + squadra + '</li>';
                        });

                        $('#squadre-restanti').html(squadreRestantiHtml);
                    }
                });
            });
        });
    </script>
</body>
</html>
