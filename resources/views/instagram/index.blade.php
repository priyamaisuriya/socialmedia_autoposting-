@extends('layouts.premium')

@section('content')
    <style>
        .ig-header {
            margin-bottom: 2.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1.5rem;
        }
        
        .ig-connect-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            background: linear-gradient(45deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888);
            border: none;
            box-shadow: 0 4px 15px rgba(220, 39, 67, 0.3);
            text-decoration: none;
            border-radius: 14px;
            padding: 0.85rem 1.6rem;
            font-weight: 700;
            color: white;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .ig-connect-btn:hover {
            opacity: 0.95;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(220, 39, 67, 0.45);
        }
        
        .ig-layout-grid {
            display: grid;
            grid-template-columns: 1fr 380px;
            gap: 2.5rem;
            align-items: start;
        }

        @media (max-width: 1024px) {
            .ig-layout-grid {
                grid-template-columns: 1fr;
                gap: 2rem;
            }
            .setup-sidebar {
                position: static !important;
            }
        }

        .ig-card {
            border-left: 4px solid #dc2743 !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .ig-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 30px rgba(220, 39, 67, 0.12);
            border-color: rgba(220, 39, 67, 0.4);
        }

        .setup-sidebar {
            position: sticky;
            top: 130px;
            padding: 2.5rem;
            background: var(--card-bg);
            border-radius: 30px;
            border: 1px solid var(--glass-border);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.03);
            transition: all 0.3s ease;
        }
        
        .setup-sidebar:hover {
            transform: translateY(-4px);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.06);
            border-color: var(--accent);
        }

        .step-list {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--glass-border);
            list-style: none;
            margin: 0;
            padding-left: 0;
        }

        .step-item {
            display: flex;
            gap: 1rem;
            align-items: flex-start;
        }

        .step-number {
            background: linear-gradient(135deg, #f09433, #dc2743);
            color: white;
            width: 26px;
            height: 26px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            font-weight: 800;
            flex-shrink: 0;
            margin-top: 2px;
            box-shadow: 0 4px 10px rgba(220, 39, 67, 0.3);
        }

        .step-content {
            flex-grow: 1;
        }

        .step-title {
            font-size: 0.9rem;
            font-weight: 700;
            color: var(--text-main);
            display: block;
            margin-bottom: 6px;
        }

        .step-sublist {
            margin: 0;
            padding-left: 1.1rem;
            font-size: 0.8rem;
            color: var(--text-muted);
            line-height: 1.5;
        }

        .step-sublist li {
            margin-bottom: 0.4rem;
        }
        
        .step-sublist li strong {
            color: var(--text-main);
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.8rem;
            font-weight: 800;
            padding: 6px 14px;
            border-radius: 12px;
            letter-spacing: 0.02em;
        }
        
        .status-active {
            color: #10b981;
            background: rgba(16, 185, 129, 0.08);
            border: 1px solid rgba(16, 185, 129, 0.15);
        }

        .status-inactive {
            color: var(--text-muted);
            background: rgba(136, 146, 176, 0.08);
            border: 1px solid rgba(136, 146, 176, 0.15);
        }
    </style>

    <div style="max-width: 1400px; margin: 0 auto; animation: fadeInUp 0.6s ease-out;">
        <div class="ig-header">
        <div>
            <h1 style="font-size: 2rem; font-weight: 800; letter-spacing: -0.02em;">Instagram Accounts</h1>
            <p style="color: var(--text-muted);">Manage and activate your Instagram Business profiles for content publishing.</p>
        </div>
        <div>
            <a href="/auth/instagram" class="ig-connect-btn">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 18px; height: 18px;"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg> Connect Instagram
            </a>
        </div>
    </div>

    <!-- Success & Error Messages -->
    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom: 2rem;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-error" style="margin-bottom: 2rem;">
            {{ session('error') }}
        </div>
    @endif

    <div class="ig-layout-grid">
        <!-- Connected Instagram Accounts List -->
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            @forelse($pages as $page)
                <div class="premium-card ig-card" style="padding: 2rem;">
                    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1.5rem;">
                        <div style="display: flex; align-items: center; gap: 1.5rem;">
                            <!-- Instagram Premium Icon Card -->
                            <div style="width: 64px; height: 64px; background: linear-gradient(45deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888); border-radius: 20px; display: flex; align-items: center; justify-content: center; color: white; box-shadow: 0 10px 20px rgba(220, 39, 67, 0.2);">
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 32px; height: 32px;"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
                            </div>
                            <div>
                                <h3 style="margin: 0; font-size: 1.35rem; font-weight: 800; display: flex; align-items: center; gap: 8px;">
                                    <span>{{ '@' . $page->instagram_username }}</span>
                                    <span style="background: rgba(37, 99, 235, 0.1); color: #2563eb; font-size: 0.7rem; font-weight: 800; padding: 2px 8px; border-radius: 8px; letter-spacing: 0.05em;">BUSINESS</span>
                                </h3>
                                <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 6px; display: flex; align-items: center; gap: 6px;">
                                    Linked Page: <strong style="color: var(--text-main);">{{ $page->name }}</strong>
                                </div>
                            </div>
                        </div>

                        <div style="display: flex; align-items: center; gap: 1.5rem;">
                            <!-- Status Indicator -->
                            <div style="text-align: right; min-width: 110px;">
                                @if($page->is_instagram_connected)
                                    <div class="status-badge status-active">
                                        <span style="width: 6px; height: 6px; background: #10b981; border-radius: 50%;"></span>
                                        ACTIVE
                                    </div>
                                @else
                                    <div class="status-badge status-inactive">
                                        <span style="width: 6px; height: 6px; background: var(--text-muted); border-radius: 50%;"></span>
                                        INACTIVE
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Toggle Connection Form -->
                            <form action="{{ route('instagram.toggle', $page->id) }}" method="POST" style="margin: 0; position: relative; z-index: 50;">
                                @csrf
                                @if($page->is_instagram_connected)
                                    <button type="submit" class="btn-danger" style="padding: 0.85rem 1.25rem; font-size: 0.85rem; cursor: pointer; border: none; border-radius: 12px; display: flex; align-items: center; gap: 0.5rem; background: rgba(239, 68, 68, 0.1); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.2);">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 16px; height: 16px;"><path d="M18.36 6.64a9 9 0 1 1-12.73 0"/><line x1="12" y1="2" x2="12" y2="12"/></svg> Disconnect
                                    </button>
                                @else
                                    <button type="submit" style="padding: 0.85rem 1.5rem; font-size: 0.85rem; cursor: pointer; border: none; border-radius: 12px; display: flex; align-items: center; gap: 0.5rem; background: linear-gradient(135deg, #10b981, #059669); color: white; box-shadow: 0 4px 10px rgba(16, 185, 129, 0.2); font-weight: 700;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 16px; height: 16px;"><path d="M18.36 6.64a9 9 0 1 1-12.73 0"/><line x1="12" y1="2" x2="12" y2="12"/></svg> Activate Profile
                                    </button>
                                @endif
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="premium-card" style="text-align: center; padding: 5rem 2rem;">
                    <div style="width: 80px; height: 80px; background: linear-gradient(45deg, rgba(240, 148, 51, 0.1), rgba(188, 24, 136, 0.1)); color: #dc2743; border-radius: 24px; display: flex; align-items: center; justify-content: center; margin: 0 auto 2rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 40px; height: 40px;"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
                    </div>
                    <h2 style="font-weight: 800; margin-bottom: 1rem;">No Instagram Business Accounts connected</h2>
                    <p style="color: var(--text-muted); max-width: 500px; margin: 0 auto 2rem; line-height: 1.6;">
                        Connect your Instagram Professional or Business account to enable direct autoposting and scheduling from this dashboard.
                    </p>
                    <a href="/auth/instagram" class="ig-connect-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 18px; height: 18px;"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg> Connect Instagram Business
                    </a>
                </div>
            @endforelse
        </div>

        <!-- Connection Information Sidebar -->
        <div class="setup-sidebar">
            <div style="width: 48px; height: 48px; background: rgba(225, 48, 108, 0.1); color: #e1306c; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 1.5rem;">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 24px; height: 24px;"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            </div>
            <h3 style="font-weight: 800; margin-bottom: 1rem;">Setup Instructions</h3>
            <p style="color: var(--text-muted); font-size: 0.9rem; line-height: 1.6; margin-bottom: 1.5rem;">
                Follow these exact steps to connect your Instagram account for auto-posting:
            </p>
            <ul class="step-list">
                <!-- Step 1 -->
                <li class="step-item">
                    <span class="step-number">1</span>
                    <div class="step-content">
                        <span class="step-title">Set Instagram to Professional</span>
                        <ul class="step-sublist">
                            <li>Open Instagram app, go to your <strong>Profile</strong>.</li>
                            <li>Tap Menu (3 lines) -> <strong>Account type & tools</strong>.</li>
                            <li>Switch to <strong>Business</strong> or <strong>Creator</strong> (Personal profiles are not supported by Meta API).</li>
                        </ul>
                    </div>
                </li>
                
                <!-- Step 2 -->
                <li class="step-item">
                    <span class="step-number">2</span>
                    <div class="step-content">
                        <span class="step-title">Link IG to your Facebook Page</span>
                        <ul class="step-sublist">
                            <li>Log in to <strong>Facebook.com</strong>.</li>
                            <li>Click your profile photo (top-right) -> <strong>See all profiles</strong> -> Select your <strong>Facebook Page</strong>.</li>
                            <li>Once switched, click <strong>Settings (⚙️)</strong> in the left menu.</li>
                            <li>Click <strong>Linked Accounts</strong> -> Select <strong>Instagram</strong> -> click <strong>Connect Account</strong>.</li>
                        </ul>
                    </div>
                </li>

                <!-- Step 3 -->
                <li class="step-item">
                    <span class="step-number">3</span>
                    <div class="step-content">
                        <span class="step-title">Connect on this Dashboard</span>
                        <ul class="step-sublist">
                            <li>Click the <strong>Connect Instagram</strong> button above.</li>
                            <li>When the Facebook popup opens, click <strong>Edit Settings / Choose Details</strong>.</li>
                            <li><strong>Select both</strong> your Instagram Business Account and its linked Facebook Page.</li>
                        </ul>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>
@endsection
