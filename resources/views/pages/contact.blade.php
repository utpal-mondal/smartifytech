@extends('layouts.app')

@section('title', 'Contact - Smartify Tech')

@section('content')
<!-- Modern Contact Hero Section -->
<section class="contact-hero" style="background-image: url('{{ asset('images/contact-hero-bg.png') }}'); background-size: cover; background-position: center; background-repeat: no-repeat;">
    <div class="contact-hero-container">
        <div class="contact-hero-content">
            <div class="contact-hero-left">
                <div class="contact-logo">
                    <img src="{{ asset('images/logo.png') }}" alt="Smartify Tech Logo" class="contact-logo-img">
                </div>
                <div class="contact-hero-text">
                    <h1 class="contact-hero-title">{{ __('messages.contact_hero_title') }}</h1>
                    <p class="contact-hero-subtitle">{{ __('messages.contact_hero_subtitle') }}</p>
                    {{-- <a href="{{ $priceListUrl }}" target="_blank" class="price-list-btn">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline points="14,2 14,8 20,8"/>
                            <line x1="16" y1="13" x2="8" y2="13"/>
                            <line x1="16" y1="17" x2="8" y2="17"/>
                        </svg>
                        <span>{{ __('messages.price_list') }}</span>
                    </a> --}}
                </div>
            </div>
            <div class="contact-hero-right">
                <!-- Right side content can be used for additional elements or left empty -->
            </div>
        </div>
    </div>
</section>

<!-- Supplier/Client Section -->
{{-- <section class="supplier-client-section">
    <div class="supplier-client-container">
        <div class="supplier-client-grid">
            <div class="supplier-card">
                <div class="supplier-icon">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#6c757d" stroke-width="2">
                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                        <polyline points="3.27,6.96 12,12.01 20.73,6.96"/>
                        <line x1="12" y1="22.08" x2="12" y2="12"/>
                    </svg>
                </div>
                <h3 class="supplier-title">{{ __('messages.supplier') }}</h3>
                <a href="{{ route('register', ['partner_type' => 'supplier']) }}" target="_blank" class="supplier-btn">{{ __('messages.apply') }}</a>
            </div>
            
            <div class="client-card">
                <div class="client-icon">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#6c757d" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </div>
                <h3 class="client-title">{{ __('messages.client') }}</h3>
                <a href="{{ route('register', ['partner_type' => 'client']) }}" target="_blank" class="client-btn">{{ __('messages.apply') }}</a>
            </div>
        </div>
    </div>
</section> --}}

<!-- Contact Form Section -->
{{-- <section class="contact-form-section">
    <div class="contact-form-container">
        <div class="contact-form-content">
            <div class="contact-form-header">
                <h2 class="contact-form-title">{{ __('messages.get_in_touch') }}</h2>
                <p class="contact-form-subtitle">{{ __('messages.get_in_touch_subtitle') }}</p>
            </div>
            
            <div class="contact-form-wrapper">
                <form class="modern-contact-form" method="post" action="{{ route('contact.submit') }}">
                    @csrf
                    <div class="form-row">
                        <div class="form-group">
                            <label for="name" class="form-label">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                    <circle cx="12" cy="7" r="4"/>
                                </svg>
                                {{ __('messages.full_name') }}
                            </label>
                            <input type="text" id="name" name="name" class="form-input" value="{{ old('name') }}" placeholder="John Doe" required>
                            @error('name')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="email" class="form-label">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                                    <polyline points="22,6 12,13 2,6"/>
                                </svg>
                                {{ __('messages.email_address') }}
                            </label>
                            <input type="email" id="email" name="email" class="form-input" value="{{ old('email') }}" placeholder="john@example.com" required>
                            @error('email')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="phone" class="form-label">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                                </svg>
                                {{ __('messages.phone_number') }}
                            </label>
                            <input type="tel" id="phone" name="phone" class="form-input" value="{{ old('phone') }}" placeholder="+32 4 65 59 58 48" pattern="[0-9\s\-]{3,17}" title="Only numbers allowed. Minimum length: 3 characters. Maximum length: 17 characters." maxlength="17" required>
                            @error('phone')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="subject" class="form-label">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
                                </svg>
                                {{ __('messages.subject') }}
                            </label>
                            <input type="text" id="subject" name="subject" class="form-input" value="{{ old('subject') }}" placeholder="How can we help?" required>
                            @error('subject')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="message" class="form-label">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                <polyline points="14,2 14,8 20,8"/>
                                <line x1="16" y1="13" x2="8" y2="13"/>
                                <line x1="16" y1="17" x2="8" y2="17"/>
                            </svg>
                            {{ __('messages.message') }}
                        </label>
                        <textarea id="message" name="message" class="form-textarea" placeholder="Tell us more about your needs and requirements...">{{ old('message') }}</textarea>
                        @error('message')
                            <span class="error-message">{{ $message }}</span>
                            @enderror
                    </div>
                    
                    @if(config('app.env') === 'production')
                    <div class="form-group">
                        <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}" style="display: flex; justify-content: center;"></div>
                        @error('g-recaptcha-response')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                    @endif
                    
                    <div class="form-actions">
                        <button type="submit" class="submit-btn">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="22" y1="2" x2="11" y2="13"/>
                                <polygon points="22,2 15,22 11,13"/>
                                <circle cx="11" cy="11" r="9"/>
                            </svg>
                            <span>{{ __('messages.send_message') }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section> --}}

<!-- Office Location Section -->
<section class="office-location-section">
    <div class="office-location-container">
        <div class="office-location-header">
            <h2 class="office-location-title">{{ __('messages.visit_office') }}</h2>
            <p class="office-location-subtitle">{{ __('messages.visit_office_subtitle') }}</p>
        </div>
        
        <div class="office-location-content">
            <div class="office-map">
                <iframe loading="lazy" width="100%" height="450" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?width=100%25&height=450&hl=en&q=Ringlaan+17a&t=&z=14&ie=UTF8&iwloc=B&output=embed"></iframe>
            </div>
            
            <div class="office-info">
                <div class="office-details">
                    <h3 class="office-name">Smartify Tech B.V.</h3>
                    <div class="office-address">
                        <p class="address-line">Ringlaan 17A -2960</p>
                        <p class="address-line">Brecht- Belgium</p>
                        <p class="address-line">BE 1035.602.385</p>
                    </div>
                    <div class="office-contact">
                        <p class="contact-email">finance@smartify-tech.com</p>
                        <p class="contact-phone">+32  465595848</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
