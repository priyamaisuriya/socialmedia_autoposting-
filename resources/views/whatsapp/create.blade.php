@extends('layouts.premium')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2.5rem; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 class="dash-header-gradient" style="font-size: 2.25rem; margin: 0; background: linear-gradient(135deg, var(--text-main), var(--accent)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; font-weight: 800; letter-spacing: -0.03em;">WhatsApp Connect</h1>
        <p style="color: var(--text-muted); font-size: 0.95rem; margin-top: 0.25rem;">Broadcast personalized messages directly to your users' WhatsApp.</p>
    </div>
</div>

<div class="premium-card" style="max-width: 800px; margin: 0 auto;">
    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 2rem;">
        <div style="width: 56px; height: 56px; border-radius: 16px; background: linear-gradient(135deg, #10b981, #059669); display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 24px rgba(16, 185, 129, 0.3);">
            <i data-lucide="message-circle" style="color: #fff; width: 28px; height: 28px;"></i>
        </div>
        <div>
            <h2 style="font-size: 1.5rem; font-weight: 800; margin: 0;">Send Message</h2>
            <p style="color: var(--text-muted); font-size: 0.9rem; margin: 0;">Configure your target number and message content below.</p>
        </div>
    </div>

    <form action="{{ route('whatsapp.send') }}" method="POST">
        @csrf
        
        <div style="margin-bottom: 1.5rem;">
            <label for="phone_number" style="display: block; font-weight: 700; margin-bottom: 0.5rem; color: var(--text-main);">Recipient Phone Number</label>
            <div style="position: relative;">
                <div style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-muted);">
                    <i data-lucide="phone" style="width: 18px; height: 18px;"></i>
                </div>
                <input type="text" name="phone_number" id="phone_number" placeholder="e.g., 15551234567" value="{{ old('phone_number') }}" required
                    style="width: 100%; padding: 1rem 1rem 1rem 3rem; background: var(--nav-active); border: 1px solid var(--glass-border); border-radius: 12px; color: var(--text-main); font-size: 0.95rem; outline: none; transition: 0.3s;"
                    onfocus="this.style.borderColor='var(--accent)'; this.style.boxShadow='0 0 0 4px var(--accent-glow)';" 
                    onblur="this.style.borderColor='var(--glass-border)'; this.style.boxShadow='none';">
            </div>
            @error('phone_number')
                <p style="color: #ef4444; font-size: 0.8rem; margin-top: 0.5rem; font-weight: 600;">{{ $message }}</p>
            @enderror
            <p style="color: var(--text-muted); font-size: 0.75rem; margin-top: 0.5rem; display: flex; align-items: center; gap: 4px;">
                <i data-lucide="info" style="width: 12px; height: 12px;"></i> Include the country code. Do not include +, -, or spaces.
            </p>
        </div>

        <div style="margin-bottom: 2rem;">
            <label for="message" style="display: block; font-weight: 700; margin-bottom: 0.5rem; color: var(--text-main);">Message Content</label>
            <textarea name="message" id="message" rows="5" placeholder="Type your broadcast message here..." required
                style="width: 100%; padding: 1rem; background: var(--nav-active); border: 1px solid var(--glass-border); border-radius: 12px; color: var(--text-main); font-size: 0.95rem; outline: none; transition: 0.3s; resize: vertical;"
                onfocus="this.style.borderColor='var(--accent)'; this.style.boxShadow='0 0 0 4px var(--accent-glow)';" 
                onblur="this.style.borderColor='var(--glass-border)'; this.style.boxShadow='none';">{{ old('message') }}</textarea>
            @error('message')
                <p style="color: #ef4444; font-size: 0.8rem; margin-top: 0.5rem; font-weight: 600;">{{ $message }}</p>
            @enderror
        </div>

        <div style="display: flex; justify-content: flex-end;">
            <button type="submit" class="btn-primary" style="background: linear-gradient(135deg, #10b981, #059669); box-shadow: 0 10px 20px rgba(16, 185, 129, 0.2);">
                <i data-lucide="send" style="width: 18px; height: 18px;"></i> Send via WhatsApp
            </button>
        </div>
    </form>
</div>
@endsection
