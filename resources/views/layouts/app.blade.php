<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="{{ asset('build/assets/app.df86ea73.css') }}">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
        @auth
        @include('chat.index')
        @endauth
        <style>
    .scroll-top {
        bottom: 20px !important;
        right: 20px !important;
        z-index: 999;
    }

    #open-chat {
        bottom: 200px !important;
        right: 20px !important;
        display: block !important;
        z-index: 1002 !important;
    }

    #chat-window {
        bottom: 250px !important;
        right: 20px !important;
        z-index: 1003 !important;
    }

    #chatbot-toggle {
        bottom: 70px !important;
        right: 20px !important;
        z-index: 1000 !important;
    }

    #chatbot {
        bottom: 120px !important;
        right: 20px !important;
        z-index: 1001 !important;
    }
        </style>
    <div id="chatbot" style="position: fixed; bottom: 120px; right: 20px; width: 300px; height: 400px; border: 1px solid #ccc; background: white; display: none; overflow-y: auto; z-index: 1001;">
    <div id="chat-messages" style="height: 350px; padding: 10px;"></div>
    <input type="text" id="chat-input" placeholder="Hỏi gì đi..." style="width: 80%;">
    <button onclick="sendBotMessage()">Gửi</button>
</div>
<button id="chatbot-toggle" style="position: fixed; bottom: 70px; right: 20px; z-index: 1000;" onclick="toggleChat()">Chat Bot</button>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function toggleChat() {
        document.getElementById('chatbot').style.display = document.getElementById('chatbot').style.display === 'none' ? 'block' : 'none';
    }
    function sendBotMessage() {
        var query = document.getElementById('chat-input').value;
        if (!query) return;
        document.getElementById('chat-messages').innerHTML += '<p><strong>You:</strong> ' + query + '</p>';
        $.ajax({
            url: 'http://localhost:8000/chat',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ query: query }),
            success: function(response) {
                document.getElementById('chat-messages').innerHTML += '<p><strong>Bot:</strong> ' + response.response + '</p>';
                document.getElementById('chat-messages').scrollTop = document.getElementById('chat-messages').scrollHeight;
            },
            error: function() {
                alert('Error connecting to chatbot');
            }
        });
        document.getElementById('chat-input').value = '';
    }

    // Force show chat icon nếu auth
    document.addEventListener('DOMContentLoaded', function() {
        if ({{ auth()->check() ? 'true' : 'false' }}) {
            document.getElementById('open-chat').style.display = 'block !important';
        }
    });
</script>
    </body>
</html>