<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Smartify Tech')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/whatsapp.css') }}">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <link rel="icon" href="{{ asset('images/fav.ico') }}" type="image">

    <meta property="og:title" content="Smartify Tech - Your Trusted Partner in Consumer Electronics" />
    <meta property="og:description" content="Trusted specialist wholesaler of electronic devices with competitive pricing and reliable bulk supply.">
    <meta property="og:url" content="{{url()->current()}}" />
    <meta property="og:image" content="{{ asset('images/logo.png') }}">
    <meta name="author" content="Smartify Tech Team">
    <meta name="robots" content="index, follow">

</head>
<body>
    <!-- WhatsApp Button - Inline Styles -->
    <a href="https://api.whatsapp.com/send/?phone=32465595848&text&type=phone_number&app_absent=0" 
       target="_blank" 
       title="Contact us on WhatsApp"
       style="position: fixed !important; bottom: 20px !important; right: 20px !important; width: 60px !important; height: 60px !important; background: #25D366 !important; border-radius: 50% !important; display: flex !important; align-items: center !important; justify-content: center !important; text-decoration: none !important; box-shadow: 0 4px 12px rgba(37, 211, 102, 0.3) !important; transition: transform 0.3s ease !important; cursor: pointer !important; border: none !important; outline: none !important; z-index: 9999999 !important;">
        <svg width="30" height="30" viewBox="0 0 24 24" fill="white" style="pointer-events: none;">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298-.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.465 3.488"/>
        </svg>
    </a>

    <!-- Chatbox -->
    <button type="button" class="chatbox-toggle" id="chatboxToggle" title="Chat with us">
        <svg width="30" height="30" viewBox="0 0 24 24" fill="white">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
        </svg>
    </button>
    <div class="chatbox-panel" id="chatboxPanel">
        <div class="chatbox-header">
            <span class="chatbox-title">Customer Service</span>
            <button type="button" class="chatbox-close" id="chatboxClose" aria-label="Close">&times;</button>
        </div>
        <div class="chatbox-body">
            <div class="chatbox-prechat" id="chatboxPrechat">
                <p class="chatbox-prechat-text">Please introduce yourself so we can help you better.</p>
                <input type="text" id="chatboxName" class="chatbox-prechat-input" placeholder="Your name" autocomplete="name">
                <input type="email" id="chatboxEmail" class="chatbox-prechat-input" placeholder="Your email" autocomplete="email">
                <span class="chatbox-prechat-error" id="chatboxPrechatError"></span>
                <button type="button" id="chatboxStart" class="chatbox-send">Start chat</button>
            </div>
            <div class="chatbox-conversation" id="chatboxConversation">
                <div class="chatbox-messages" id="chatboxMessages">
                    <div class="chat-message agent">
                        <div class="chat-bubble">Hello! How can I help you today?</div>
                        <span class="chat-time">Now</span>
                    </div>
                </div>
                <div class="chatbox-input">
                    <input type="text" id="chatboxInput" placeholder="Type a message..." autocomplete="off">
                    <button type="button" id="chatboxSend" class="chatbox-send">Send</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modern Top Bar -->
    <header class="modern-header">
        <div class="header-container">
            <div class="header-left">
                <a href="{{ url('/') }}" class="logo-link">
                    <img src="{{ asset('images/logo.png') }}" alt="Smartify Tech Logo" class="header-logo">
                </a>
            </div>
            <nav class="header-nav">
                <!-- <a href="{{ url('/') }}" class="nav-link">{{ __('messages.About') }}</a> -->
                <a href="{{ url('/#about-section') }}" class="nav-link">{{ __('messages.About') }}</a>
                <a href="{{ route('contact') }}" class="nav-link">{{ __('messages.Contact') }}</a>
                <a href="{{ route('register') }}" class="nav-link">{{ __('messages.Register') }}</a>
                {{-- <a href="{{ $priceListUrl }}" target="_blank" class="price-list-btn">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14,2 14,8 20,8"/>
                        <line x1="16" y1="13" x2="8" y2="13"/>
                        <line x1="16" y1="17" x2="8" y2="17"/>
                    </svg>
                    <span>{{ __('messages.price_list') }}</span>
                </a> --}}
                <div class="lang-dropdown">
                    <button class="lang-btn">
                        @if(session()->get('locale') == 'nl')
                            <img src="https://flagcdn.com/24x18/nl.png" alt="NL Flag" class="flag-icon">
                            <span>NL</span>
                        @else
                            <img src="https://flagcdn.com/24x18/gb.png" alt="UK Flag" class="flag-icon">
                            <span>EN</span>
                        @endif
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M6 9l6 6 6-6"/>
                        </svg>
                    </button>
                    <div class="lang-menu">
                        <a href="{{ route('lang.switch', 'en') }}" class="lang-option {{ session()->get('locale') == 'en' ? 'active' : '' }}">
                            <img src="https://flagcdn.com/24x18/gb.png" alt="UK Flag">
                            <span>English</span>
                        </a>
                        <a href="{{ route('lang.switch', 'nl') }}" class="lang-option {{ session()->get('locale') == 'nl' ? 'active' : '' }}">
                            <img src="https://flagcdn.com/24x18/nl.png" alt="NL Flag">
                            <span>Nederlands</span>
                        </a>
                    </div>
                </div>
            </nav>
        </div>
    </header>

    <div id="et-main-area">
        <div id="main-content">
            <div class="container">
                @yield('content')
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible" id="success-alert" style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px; cursor: pointer;">
            <button type="button" class="alert-close" onclick="this.parentElement.style.display='none'">&times;</button>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible" id="error-alert">
            <button type="button" class="alert-close" onclick="this.parentElement.style.display='none'">&times;</button>
            <div class="error-title">
                <strong>{{ __('messages.validation_errors') }}</strong>
            </div>
            <ul class="error-list">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @include('partials.footer')
    
    <script src="{{ asset('js/app.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const successAlert = document.getElementById('success-alert');
            if (successAlert) {
                // Auto-hide after 5 seconds
                setTimeout(function() {
                    successAlert.style.transition = 'opacity 0.5s ease-out';
                    successAlert.style.opacity = '0';
                    setTimeout(function() {
                        successAlert.style.display = 'none';
                    }, 500);
                }, 5000);
                
                // Also add click to dismiss functionality
                successAlert.addEventListener('click', function() {
                    successAlert.style.transition = 'opacity 0.3s ease-out';
                    successAlert.style.opacity = '0';
                    setTimeout(function() {
                        successAlert.style.display = 'none';
                    }, 300);
                });
            }
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggle = document.getElementById('chatboxToggle');
            const panel = document.getElementById('chatboxPanel');
            const close = document.getElementById('chatboxClose');
            const messages = document.getElementById('chatboxMessages');
            const input = document.getElementById('chatboxInput');
            const send = document.getElementById('chatboxSend');
            const prechat = document.getElementById('chatboxPrechat');
            const prechatError = document.getElementById('chatboxPrechatError');
            const conversation = document.getElementById('chatboxConversation');
            const nameField = document.getElementById('chatboxName');
            const emailField = document.getElementById('chatboxEmail');
            const start = document.getElementById('chatboxStart');

            let visitor = { name: '', email: '' };

            function formatTime(date) {
                return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            }

            function escapeHtml(text) {
                return text.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
            }

            function addMessage(text, sender) {
                const wrapper = document.createElement('div');
                wrapper.className = 'chat-message ' + sender;
                wrapper.innerHTML = '<div class="chat-bubble">' + escapeHtml(text) + '</div><span class="chat-time">' + formatTime(new Date()) + '</span>';
                messages.appendChild(wrapper);
                messages.scrollTop = messages.scrollHeight;
            }

            function showTyping() {
                const wrapper = document.createElement('div');
                wrapper.className = 'chat-message agent typing';
                wrapper.innerHTML = '<div class="chat-bubble"><span class="typing-dots"><span class="dot"></span><span class="dot"></span><span class="dot"></span></span></div><span class="chat-time">Now</span>';
                messages.appendChild(wrapper);
                messages.scrollTop = messages.scrollHeight;
                return wrapper;
            }

            function agentReply(text) {
                const typing = showTyping();

                fetch('/api/chat/message', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ message: text, name: visitor.name, email: visitor.email }),
                })
                    .then(function(response) {
                        return response.json();
                    })
                    .then(function(data) {
                        typing.remove();
                        addMessage(data.message, 'agent');
                    })
                    .catch(function() {
                        typing.remove();
                        addMessage('Sorry, I could not process your request right now.', 'agent');
                    });
            }

            function sendMessage() {
                const text = input.value.trim();
                if (!text) return;
                addMessage(text, 'user');
                input.value = '';
                agentReply(text);
            }

            if (toggle && panel) {
                toggle.addEventListener('click', function() {
                    panel.classList.toggle('open');
                });

                if (close) {
                    close.addEventListener('click', function() {
                        panel.classList.remove('open');
                    });
                }
            }

            function startChat() {
                const name = nameField.value.trim();
                const email = emailField.value.trim();

                if (!name || !email) {
                    prechatError.textContent = 'Please enter your name and email.';
                    return;
                }

                if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                    prechatError.textContent = 'Please enter a valid email address.';
                    return;
                }

                prechatError.textContent = '';
                visitor = { name: name, email: email };
                prechat.classList.add('hidden');
                conversation.classList.add('active');
                messages.scrollTop = messages.scrollHeight;
                input.focus();
            }

            if (start) {
                start.addEventListener('click', startChat);
                [nameField, emailField].forEach(function(field) {
                    field.addEventListener('keypress', function(e) {
                        if (e.key === 'Enter') startChat();
                    });
                });
            }

            if (send && input) {
                send.addEventListener('click', sendMessage);
                input.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') sendMessage();
                });
            }
        });
    </script>
    
    @stack('scripts')
</body>
</html>
