@extends('layouts.premium')

@section('content')
<div style="max-width: 900px; margin: 0 auto; animation: fadeInUp 0.6s ease-out;">
    <div style="margin-bottom: 2.5rem;">
        <a href="{{ route('ads.index') }}" style="color: var(--text-muted); text-decoration: none; display: inline-flex; align-items: center; gap: 8px; font-weight: 700; margin-bottom: 1rem; transition: 0.3s;" onmouseover="this.style.color='var(--accent)'" onmouseout="this.style.color='var(--text-muted)'">
            <i data-lucide="arrow-left" style="width: 16px; height: 16px;"></i> Back to Ads Manager
        </a>
        <h1 style="font-size: 2.5rem; font-weight: 900; letter-spacing: -0.04em; margin-bottom: 0.5rem;">Create Full Ad Campaign</h1>
        <p style="color: var(--text-muted); font-size: 1.1rem;">Launch a complete ad with targeting and creatives directly from your dashboard.</p>
    </div>

    <form action="{{ route('ads.store') }}" method="POST" enctype="multipart/form-data" class="premium-card" style="padding: 3rem; border-width: 2px;">
        @csrf

        @if(session('payment_error'))
            <div style="background: rgba(245, 158, 11, 0.1); color: #d97706; padding: 2rem; border-radius: 16px; margin-bottom: 2rem; border: 1px solid rgba(245, 158, 11, 0.2); display: flex; flex-direction: column; gap: 1rem; align-items: flex-start;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <i data-lucide="credit-card" style="width: 24px; height: 24px;"></i>
                    <h3 style="margin: 0; font-size: 1.25rem; font-weight: 800; color: #b45309;">Payment Method Required</h3>
                </div>
                <p style="margin: 0; font-size: 1.05rem;">Your ad setup is perfect, but Facebook requires a payment method on your Ad Account before publishing.</p>
                <a href="https://business.facebook.com/billing_hub/payment_methods" target="_blank" style="background: #f59e0b; color: white; padding: 0.75rem 1.5rem; border-radius: 12px; text-decoration: none; font-weight: 700; display: inline-flex; align-items: center; gap: 8px; margin-top: 0.5rem; box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);">
                    <i data-lucide="external-link" style="width: 18px; height: 18px;"></i> Add Payment on Facebook
                </a>
            </div>
        @endif

        @if($errors->any())
            <div style="background: rgba(239, 68, 68, 0.1); color: #ef4444; padding: 1.5rem; border-radius: 16px; margin-bottom: 2rem; border: 1px solid rgba(239, 68, 68, 0.2);">
                <ul style="margin: 0; padding-left: 1.5rem;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('error'))
            <div style="background: rgba(239, 68, 68, 0.1); color: #ef4444; padding: 1.5rem; border-radius: 16px; margin-bottom: 2rem; border: 1px solid rgba(239, 68, 68, 0.2);">
                {{ session('error') }}
            </div>
        @endif

        <div style="display: flex; flex-direction: column; gap: 3rem;">
            
            <!-- SECTION 1: ACCOUNT & CAMPAIGN -->
            <div>
                <h3 style="font-size: 1.25rem; font-weight: 800; border-bottom: 2px solid var(--glass-border); padding-bottom: 0.5rem; margin-bottom: 1.5rem; color: var(--text-main);">1. Campaign Setup</h3>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                    <div>
                        <label style="display: block; font-weight: 800; margin-bottom: 0.75rem; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.1em; color: var(--accent);">Select Ad Account</label>
                        <select name="ad_account_id" required style="width: 100%; padding: 1rem; border: 2px solid var(--glass-border); border-radius: 16px; background: var(--bg-main); color: var(--text-main); font-size: 1rem; outline: none; cursor: pointer;">
                            @foreach($accounts as $account)
                                <option value="{{ $account->id }}" {{ old('ad_account_id') == $account->id ? 'selected' : '' }}>{{ $account->name }} ({{ $account->account_id }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label style="display: block; font-weight: 800; margin-bottom: 0.75rem; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.1em; color: var(--accent);">Facebook Page (For Ad Identity)</label>
                        <select name="page_id" required style="width: 100%; padding: 1rem; border: 2px solid var(--glass-border); border-radius: 16px; background: var(--bg-main); color: var(--text-main); font-size: 1rem; outline: none; cursor: pointer;">
                            @foreach($pages as $page)
                                <option value="{{ $page->page_id }}" {{ old('page_id') == $page->page_id ? 'selected' : '' }}>{{ $page->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                    <div>
                        <label style="display: block; font-weight: 800; margin-bottom: 0.75rem; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.1em; color: var(--accent);">Campaign Name</label>
                        <input type="text" name="name" required value="{{ old('name') }}" placeholder="e.g. Diwali Offer 2026" style="width: 100%; padding: 1rem; border: 2px solid var(--glass-border); border-radius: 16px; background: var(--bg-main); color: var(--text-main); font-size: 1rem; outline: none;">
                    </div>

                    <div>
                        <label style="display: block; font-weight: 800; margin-bottom: 0.75rem; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.1em; color: var(--accent);">Campaign Objective</label>
                        <select name="objective" required style="width: 100%; padding: 1rem; border: 2px solid var(--glass-border); border-radius: 16px; background: var(--bg-main); color: var(--text-main); font-size: 1rem; outline: none; cursor: pointer;">
                            <option value="OUTCOME_TRAFFIC" {{ old('objective') == 'OUTCOME_TRAFFIC' ? 'selected' : '' }}>Traffic (Link Clicks)</option>
                            <option value="OUTCOME_ENGAGEMENT" {{ old('objective') == 'OUTCOME_ENGAGEMENT' ? 'selected' : '' }}>Engagement (Likes, Comments)</option>
                            <option value="OUTCOME_AWARENESS" {{ old('objective') == 'OUTCOME_AWARENESS' ? 'selected' : '' }}>Brand Awareness</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- SECTION 2: AD SET (TARGETING) -->
            <div>
                <h3 style="font-size: 1.25rem; font-weight: 800; border-bottom: 2px solid var(--glass-border); padding-bottom: 0.5rem; margin-bottom: 1.5rem; color: var(--text-main);">2. Audience & Budget</h3>
                
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; font-weight: 800; margin-bottom: 0.75rem; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.1em; color: var(--accent);">Daily Budget</label>
                    <div style="position: relative;">
                        <span style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-weight: 800; font-size: 1.2rem;">₹</span>
                        <input type="number" step="0.01" name="daily_budget" value="{{ old('daily_budget') }}" required placeholder="500.00" style="width: 100%; padding: 1rem 1rem 1rem 2rem; border: 2px solid var(--glass-border); border-radius: 16px; background: var(--bg-main); color: var(--text-main); font-size: 1rem; outline: none;">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                    <div>
                        <label style="display: block; font-weight: 800; margin-bottom: 0.75rem; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.1em; color: var(--accent);">Min Age</label>
                        <input type="number" name="age_min" value="{{ old('age_min', 18) }}" min="13" max="65" required style="width: 100%; padding: 1rem; border: 2px solid var(--glass-border); border-radius: 16px; background: var(--bg-main); color: var(--text-main); font-size: 1rem; outline: none;">
                    </div>
                    <div>
                        <label style="display: block; font-weight: 800; margin-bottom: 0.75rem; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.1em; color: var(--accent);">Max Age</label>
                        <input type="number" name="age_max" value="{{ old('age_max', 65) }}" min="13" max="65" required style="width: 100%; padding: 1rem; border: 2px solid var(--glass-border); border-radius: 16px; background: var(--bg-main); color: var(--text-main); font-size: 1rem; outline: none;">
                    </div>
                </div>
            </div>

            <!-- SECTION 3: CREATIVE -->
            <div>
                <h3 style="font-size: 1.25rem; font-weight: 800; border-bottom: 2px solid var(--glass-border); padding-bottom: 0.5rem; margin-bottom: 1.5rem; color: var(--text-main);">3. Ad Creative</h3>
                
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; font-weight: 800; margin-bottom: 0.75rem; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.1em; color: var(--accent);">Upload Image</label>
                    <input type="file" name="media" accept="image/jpeg,image/png" required style="width: 100%; padding: 1rem; border: 2px dashed var(--glass-border); border-radius: 16px; background: var(--bg-main); color: var(--text-main); font-size: 1rem; outline: none; cursor: pointer;">
                    <p style="font-size: 0.8rem; color: var(--text-muted); margin-top: 0.5rem;">Recommended size: 1080x1080 pixels.</p>
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; font-weight: 800; margin-bottom: 0.75rem; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.1em; color: var(--accent);">Primary Text (Caption)</label>
                    <textarea name="primary_text" rows="3" required placeholder="Write a catchy caption for your ad..." style="width: 100%; padding: 1rem; border: 2px solid var(--glass-border); border-radius: 16px; background: var(--bg-main); color: var(--text-main); font-size: 1rem; outline: none; resize: vertical;">{{ old('primary_text') }}</textarea>
                </div>

                <div>
                    <label style="display: block; font-weight: 800; margin-bottom: 0.75rem; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.1em; color: var(--accent);">Website URL (Optional)</label>
                    <input type="url" name="website_url" value="{{ old('website_url') }}" placeholder="https://yourwebsite.com/product" style="width: 100%; padding: 1rem; border: 2px solid var(--glass-border); border-radius: 16px; background: var(--bg-main); color: var(--text-main); font-size: 1rem; outline: none;">
                    <p style="font-size: 0.8rem; color: var(--text-muted); margin-top: 0.5rem;">Where should people go when they click the ad?</p>
                </div>
            </div>

        </div>

        <div style="margin-top: 3.5rem; padding-top: 2.5rem; border-top: 2px solid var(--glass-border); display: flex; justify-content: space-between; align-items: center;">
            <p style="font-size: 0.9rem; color: var(--text-muted); margin: 0; max-width: 60%;">
                <i data-lucide="info" style="width: 16px; height: 16px; display: inline; vertical-align: text-bottom; margin-right: 4px;"></i>
                Your ad will be created in a <b>PAUSED</b> state. You can review and publish it from the FB Ads Manager.
            </p>
            <button type="submit" class="btn-primary" style="padding: 1rem 2.5rem; font-size: 1.1rem; border-radius: 16px;">
                <i data-lucide="rocket" style="width: 20px; height: 20px; margin-right: 8px;"></i> Publish Ad
            </button>
        </div>
    </form>
</div>
@endsection
