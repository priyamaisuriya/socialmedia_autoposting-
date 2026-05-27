@extends('layouts.premium')

@section('content')
    <style>
        .fb-header {
            margin-bottom: 2.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1.5rem;
        }
        
        .fb-connect-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            background: linear-gradient(135deg, #1877f2, #00c6ff);
            border: none;
            box-shadow: 0 4px 15px rgba(24, 119, 242, 0.3);
            text-decoration: none;
            border-radius: 14px;
            padding: 0.85rem 1.6rem;
            font-weight: 700;
            color: white;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .fb-connect-btn:hover {
            opacity: 0.95;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(24, 119, 242, 0.45);
        }
        
        .fb-layout-grid {
            display: grid;
            grid-template-columns: 1fr 380px;
            gap: 2.5rem;
            align-items: start;
        }

        @media (max-width: 1024px) {
            .fb-layout-grid {
                grid-template-columns: 1fr;
                gap: 2rem;
            }
            .setup-sidebar {
                position: static !important;
            }
        }

        .fb-card {
            border-left: 4px solid #1877f2 !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .fb-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 30px rgba(24, 119, 242, 0.12);
            border-color: rgba(24, 119, 242, 0.4);
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
            background: linear-gradient(135deg, #1877f2, #00c6ff);
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
            box-shadow: 0 4px 10px rgba(24, 119, 242, 0.3);
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
        <div class="fb-header">
        <div>
            <h1 style="font-size: 2rem; font-weight: 800; letter-spacing: -0.02em;">Facebook Accounts</h1>
            <p style="color: var(--text-muted);">Manage and connect your Facebook Profiles and Synced Business Pages.</p>
        </div>
        <div>
            <a href="{{ url('/auth/facebook') }}" class="fb-connect-btn">
                <i data-lucide="facebook" style="width: 18px; height: 18px;"></i> Connect Facebook
            </a>
        </div>
    </div>

    <!-- Success & Error Alerts -->
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

    <div class="fb-layout-grid">
        <!-- Connected Accounts Column -->
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            @forelse($accounts as $account)
                <div class="premium-card fb-card" style="padding: 2rem;">
                    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1.5rem;">
                        <div style="display: flex; align-items: center; gap: 1.5rem;">
                            <!-- Facebook Premium Blue Avatar -->
                            <div style="width: 64px; height: 64px; background: linear-gradient(135deg, #1877f2, #00c6ff); border-radius: 20px; display: flex; align-items: center; justify-content: center; color: white; box-shadow: 0 10px 20px rgba(24, 119, 242, 0.2);">
                                <i data-lucide="facebook" style="width: 32px; height: 32px;"></i>
                            </div>
                            <div>
                                <h3 style="margin: 0; font-size: 1.35rem; font-weight: 800; display: flex; align-items: center; gap: 8px;">
                                    <span>{{ $account->name }}</span>
                                    <span style="background: rgba(24, 119, 242, 0.1); color: #1877f2; font-size: 0.7rem; font-weight: 800; padding: 2px 8px; border-radius: 8px; letter-spacing: 0.05em;">PROFILE</span>
                                </h3>
                                <div style="display: flex; align-items: center; gap: 0.5rem; margin-top: 6px;">
                                    <div class="status-badge status-active">
                                        <span style="width: 6px; height: 6px; background: #10b981; border-radius: 50%;"></span>
                                        CONNECTED
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div style="display: flex; align-items: center; gap: 2rem;">
                            <!-- Page count -->
                            <div style="text-align: right; min-width: 100px;">
                                <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Pages Sync</div>
                                <div style="font-weight: 800; font-size: 1.35rem; color: var(--text-main); margin-top: 2px;">{{ auth()->user()->facebookPages()->count() }}</div>
                            </div>
                            
                            <!-- Disconnect Form -->
                            <form action="{{ route('facebook.destroy', $account->id) }}" method="POST" style="margin: 0; position: relative; z-index: 50;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-danger" style="padding: 0.85rem 1.25rem; font-size: 0.85rem; cursor: pointer; border: none; border-radius: 12px; display: flex; align-items: center; gap: 0.5rem; background: rgba(239, 68, 68, 0.1); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.2); font-weight: 700;">
                                    <i data-lucide="trash-2" style="width: 16px; height: 16px;"></i> Disconnect
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Synced Pages Sublist -->
                    @php $userPages = auth()->user()->facebookPages; @endphp
                    @if($userPages->count() > 0)
                        <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid var(--glass-border);">
                            <h4 style="font-weight: 800; font-size: 0.85rem; text-transform: uppercase; color: #1877f2; margin-bottom: 1.2rem; letter-spacing: 0.05em; display: flex; align-items: center; gap: 6px;">
                                <i data-lucide="check-circle-2" style="width: 16px; height: 16px;"></i> Synced Pages & Connected Instagram Profiles
                            </h4>
                            <div style="display: grid; grid-template-columns: 1fr; gap: 0.85rem;">
                                @foreach($userPages as $page)
                                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 1.1rem 1.3rem; background: var(--bg-main); border-radius: 16px; border: 1px solid var(--glass-border); transition: all 0.2s ease;">
                                        <div style="display: flex; align-items: center; gap: 10px;">
                                            <div style="width: 32px; height: 32px; background: rgba(24, 119, 242, 0.08); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #1877f2;">
                                                <i data-lucide="layout" style="width: 16px; height: 16px;"></i>
                                            </div>
                                            <span style="font-weight: 700; color: var(--text-main); font-size: 0.95rem;">{{ $page->name }}</span>
                                        </div>
                                        <div>
                                            @if($page->instagram_account_id)
                                                <span style="display: inline-flex; align-items: center; gap: 6px; background: linear-gradient(45deg, rgba(240, 148, 51, 0.1), rgba(188, 24, 136, 0.1)); border: 1px solid rgba(188, 24, 136, 0.2); color: #bc1888; font-weight: 800; font-size: 0.75rem; padding: 5px 12px; border-radius: 20px; box-shadow: 0 2px 8px rgba(188, 24, 136, 0.05);">
                                                    <i data-lucide="instagram" style="width: 12px; height: 12px;"></i>
                                                    <span>{{ '@' . $page->instagram_username }}</span>
                                                </span>
                                            @else
                                                <span style="color: var(--text-muted); background: rgba(136, 146, 176, 0.05); border: 1px solid var(--glass-border); font-size: 0.75rem; font-weight: 700; display: inline-flex; align-items: center; gap: 6px; padding: 5px 12px; border-radius: 20px;">
                                                    <i data-lucide="alert-circle" style="width: 12px; height: 12px;"></i>
                                                    No Instagram connected
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @empty
                <!-- Empty State -->
                <div class="premium-card" style="text-align: center; padding: 5rem 2rem;">
                    <div style="width: 80px; height: 80px; background: rgba(24, 119, 242, 0.1); color: #1877f2; border-radius: 24px; display: flex; align-items: center; justify-content: center; margin: 0 auto 2rem; box-shadow: 0 10px 25px rgba(24, 119, 242, 0.1);">
                        <i data-lucide="user-plus" style="width: 40px; height: 40px;"></i>
                    </div>
                    <h2 style="font-weight: 800; margin-bottom: 1rem;">No Facebook Accounts Connected</h2>
                    <p style="color: var(--text-muted); max-width: 500px; margin: 0 auto 2rem; line-height: 1.6;">
                        Link your Facebook profile to import your business pages, configure automation, and schedule campaigns seamlessly.
                    </p>
                    <a href="{{ url('/auth/facebook') }}" class="fb-connect-btn">
                        <i data-lucide="facebook" style="width: 18px; height: 18px;"></i> Connect Facebook Account
                    </a>
                </div>
            @endforelse
        </div>

        <!-- Connection Information Sidebar -->
        <div class="setup-sidebar">
            <div style="width: 48px; height: 48px; background: rgba(24, 119, 242, 0.1); color: #1877f2; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 1.5rem; box-shadow: 0 4px 12px rgba(24, 119, 242, 0.1);">
                <i data-lucide="help-circle" style="width: 24px; height: 24px;"></i>
            </div>
            <h3 style="font-weight: 800; margin-bottom: 1rem;">Setup Instructions</h3>
            <p style="color: var(--text-muted); font-size: 0.9rem; line-height: 1.6; margin-bottom: 1.5rem;">
                Follow these exact steps to connect your Facebook Profile and manage Business Pages:
            </p>
            <ul class="step-list">
                <!-- Step 1 -->
                <li class="step-item">
                    <span class="step-number">1</span>
                    <div class="step-content">
                        <span class="step-title">Ensure Page Admin Rights</span>
                        <ul class="step-sublist">
                            <li>Open <strong>Facebook.com</strong> and verify you are the <strong>Admin</strong> of the Business Page.</li>
                            <li>Go to Page settings -> <strong>New Pages Experience</strong> to check your access level.</li>
                        </ul>
                    </div>
                </li>
                
                <!-- Step 2 -->
                <li class="step-item">
                    <span class="step-number">2</span>
                    <div class="step-content">
                        <span class="step-title">Grant Full Permissions</span>
                        <ul class="step-sublist">
                            <li>Click the <strong>Connect Facebook</strong> button on the left.</li>
                            <li>Make sure to accept <strong>all checkboxes</strong> and requested permissions in the Meta dialogue window.</li>
                            <li>Do not deselect any Page, as this prevents syncing.</li>
                        </ul>
                    </div>
                </li>

                <!-- Step 3 -->
                <li class="step-item">
                    <span class="step-number">3</span>
                    <div class="step-content">
                        <span class="step-title">Verify Auto-import</span>
                        <ul class="step-sublist">
                            <li>Once authenticated, your pages will automatically load in this panel.</li>
                            <li>If they don't appear, disconnect and try again, ensuring all access ticks are enabled.</li>
                        </ul>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>
@endsection
