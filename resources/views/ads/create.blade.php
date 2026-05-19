@extends('layouts.premium')

@section('content')
    <div style="margin-bottom: 2.5rem;">
        <a href="{{ route('ads.index') }}" style="text-decoration: none; color: var(--text-muted); font-size: 0.9rem; display: inline-flex; align-items: center; gap: 6px; font-weight: 600; transition: 0.2s;" onmouseover="this.style.color='var(--accent)'" onmouseout="this.style.color='var(--text-muted)'">
            <i data-lucide="arrow-left" style="width: 16px; height: 16px;"></i> Back to Ads Manager
        </a>
        <h1 style="font-size: 2.25rem; font-weight: 800; letter-spacing: -0.03em; background: linear-gradient(135deg, var(--text-main), var(--accent)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; margin-top: 0.5rem;">
            Launch New Ad Campaign
        </h1>
        <p style="color: var(--text-muted); font-size: 0.95rem; margin-top: 0.25rem;">Deploy a mock campaign directly to your sandbox account to simulate audience delivery.</p>
    </div>

    @if ($errors->any())
        <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); color: #ef4444; padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem;">
            <ul style="margin: 0; padding-left: 1.25rem;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('ads.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 2rem; align-items: start;">
            <!-- Left Side: Campaign Configuration -->
            <div class="premium-card" style="padding: 2.5rem; display: flex; flex-direction: column; gap: 1.5rem;">
                <h3 style="font-size: 1.1rem; font-weight: 800; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 8px;">
                    <i data-lucide="settings" style="color: var(--accent);"></i> Campaign Settings
                </h3>

                <!-- Campaign Name -->
                <div>
                    <label style="display: block; font-size: 0.85rem; font-weight: 700; color: var(--text-muted); margin-bottom: 6px;">Campaign Name</label>
                    <input type="text" name="name" required placeholder="e.g. Summer Special Promotion" style="width: 100%; padding: 0.75rem 1rem; background: var(--nav-active); border: 1px solid var(--glass-border); border-radius: 10px; color: var(--text-main); font-weight: 600; outline: none;" />
                </div>

                <!-- Facebook Page Selection -->
                <div>
                    <label style="display: block; font-size: 0.85rem; font-weight: 700; color: var(--text-muted); margin-bottom: 6px;">Target Facebook Page</label>
                    <select name="facebook_page_id" required style="width: 100%; padding: 0.75rem 1rem; background: var(--nav-active); border: 1px solid var(--glass-border); border-radius: 10px; color: var(--text-main); font-weight: 600; outline: none; cursor: pointer;">
                        <option value="">-- Choose target page --</option>
                        @foreach($pages as $page)
                            <option value="{{ $page->id }}">{{ $page->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Campaign Objective & Daily Budget -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 700; color: var(--text-muted); margin-bottom: 6px;">Objective</label>
                        <select name="objective" required style="width: 100%; padding: 0.75rem 1rem; background: var(--nav-active); border: 1px solid var(--glass-border); border-radius: 10px; color: var(--text-main); font-weight: 600; outline: none; cursor: pointer;">
                            <option value="LINK_CLICKS">Link Clicks</option>
                            <option value="PAGE_LIKES">Page Engagement / Likes</option>
                            <option value="BRAND_AWARENESS">Brand Awareness</option>
                        </select>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 700; color: var(--text-muted); margin-bottom: 6px;">Daily Budget ($)</label>
                        <input type="number" name="daily_budget" required min="10" placeholder="Min. $10.00" style="width: 100%; padding: 0.75rem 1rem; background: var(--nav-active); border: 1px solid var(--glass-border); border-radius: 10px; color: var(--text-main); font-weight: 600; outline: none;" />
                    </div>
                </div>

                <!-- Ad Creative (Text & Media) -->
                <div>
                    <label style="display: block; font-size: 0.85rem; font-weight: 700; color: var(--text-muted); margin-bottom: 6px;">Ad Caption / Text</label>
                    <textarea name="ad_text" required rows="4" placeholder="Write your ad copy here..." style="width: 100%; padding: 0.75rem 1rem; background: var(--nav-active); border: 1px solid var(--glass-border); border-radius: 10px; color: var(--text-main); font-weight: 600; outline: none; font-family: inherit; resize: vertical;"></textarea>
                </div>

                <div>
                    <label style="display: block; font-size: 0.85rem; font-weight: 700; color: var(--text-muted); margin-bottom: 6px;">Ad Media Creative (Image)</label>
                    <input type="file" name="ad_image" accept="image/*" style="width: 100%; padding: 0.75rem; background: var(--nav-active); border: 1px solid var(--glass-border); border-radius: 10px; color: var(--text-main); font-weight: 600; outline: none; cursor: pointer;" />
                </div>
            </div>

            <!-- Right Side: Audience Targeting & Preview -->
            <div style="display: flex; flex-direction: column; gap: 2rem;">
                <!-- Targeting Card -->
                <div class="premium-card" style="padding: 2.5rem; display: flex; flex-direction: column; gap: 1.5rem;">
                    <h3 style="font-size: 1.1rem; font-weight: 800; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 8px;">
                        <i data-lucide="users" style="color: var(--accent);"></i> Audience Targeting
                    </h3>

                    <!-- Target Location -->
                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 700; color: var(--text-muted); margin-bottom: 6px;">Target Location</label>
                        <input type="text" name="target_location" required value="India" placeholder="e.g. United States, India" style="width: 100%; padding: 0.75rem 1rem; background: var(--nav-active); border: 1px solid var(--glass-border); border-radius: 10px; color: var(--text-main); font-weight: 600; outline: none;" />
                    </div>

                    <!-- Target Age -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div>
                            <label style="display: block; font-size: 0.85rem; font-weight: 700; color: var(--text-muted); margin-bottom: 6px;">Min Age</label>
                            <input type="number" name="target_age_min" required min="13" max="65" value="18" style="width: 100%; padding: 0.75rem 1rem; background: var(--nav-active); border: 1px solid var(--glass-border); border-radius: 10px; color: var(--text-main); font-weight: 600; outline: none;" />
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.85rem; font-weight: 700; color: var(--text-muted); margin-bottom: 6px;">Max Age</label>
                            <input type="number" name="target_age_max" required min="13" max="65" value="65" style="width: 100%; padding: 0.75rem 1rem; background: var(--nav-active); border: 1px solid var(--glass-border); border-radius: 10px; color: var(--text-main); font-weight: 600; outline: none;" />
                        </div>
                    </div>
                </div>

                <!-- Submit / Launch Button -->
                <button type="submit" class="btn-primary" style="padding: 1.25rem; font-size: 1.1rem; width: 100%; font-weight: 800; justify-content: center; text-transform: uppercase; letter-spacing: 0.03em;">
                    <i data-lucide="zap"></i> Launch Campaign Demo
                </button>
            </div>
        </div>
    </form>
@endsection
