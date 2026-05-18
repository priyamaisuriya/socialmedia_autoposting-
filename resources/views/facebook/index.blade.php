@extends('layouts.premium')

@section('content')
    <div style="margin-bottom: 2.5rem;">
        <h1 style="font-size: 2rem; font-weight: 800; letter-spacing: -0.02em;">Social Accounts</h1>
        <p style="color: var(--text-muted);">Connect and manage your Facebook profiles here.</p>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 350px; gap: 2rem; align-items: start;">
        <!-- Connected Accounts List -->
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            @forelse($accounts as $account)
                <div class="premium-card">
                    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1.5rem;">
                        <div style="display: flex; align-items: center; gap: 1.5rem;">
                            <div style="width: 64px; height: 64px; background: #1877f2; border-radius: 20px; display: flex; align-items: center; justify-content: center; color: white; box-shadow: 0 10px 20px rgba(24, 119, 242, 0.2);">
                                <i data-lucide="facebook" size="32"></i>
                            </div>
                            <div>
                                <h3 style="margin: 0; font-size: 1.25rem; font-weight: 800;">{{ $account->name }}</h3>
                                <div style="display: flex; align-items: center; gap: 0.5rem; color: #10b981; font-size: 0.875rem; font-weight: 700; margin-top: 4px;">
                                    <span style="width: 8px; height: 8px; background: #10b981; border-radius: 50%;"></span>
                                    CONNECTED
                                </div>
                            </div>
                        </div>

                        <div style="display: flex; align-items: center; gap: 2rem;">
                            <div style="text-align: right;">
                                <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase;">Pages Sync</div>
                                <div style="font-weight: 800; font-size: 1.25rem;">{{ auth()->user()->facebookPages()->count() }}</div>
                            </div>
                            
                            <form action="{{ route('facebook.destroy', $account->id) }}" method="POST" style="margin: 0; position: relative; z-index: 50;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-danger" style="padding: 1rem 1.5rem; font-size: 0.875rem; cursor: pointer; border: none; border-radius: 12px; display: flex; align-items: center; gap: 0.5rem;">
                                    <i data-lucide="trash-2" size="18"></i> Disconnect
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="premium-card" style="text-align: center; padding: 5rem 2rem;">
                    <div style="width: 80px; height: 80px; background: var(--nav-active); color: var(--text-muted); border-radius: 24px; display: flex; align-items: center; justify-content: center; margin: 0 auto 2rem;">
                        <i data-lucide="user-plus" size="40"></i>
                    </div>
                    <h2 style="font-weight: 800; margin-bottom: 1rem;">No accounts linked</h2>
                    <p style="color: var(--text-muted); max-width: 400px; margin: 0 auto;">Link your Facebook account to start managing your pages.</p>
                </div>
            @endforelse
        </div>

        <!-- Connection Sidebar -->
        <div class="premium-card" style="position: sticky; top: 130px;">
            <div style="width: 48px; height: 48px; background: var(--accent-glow); color: var(--accent); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 1.5rem;">
                <i data-lucide="plus"></i>
            </div>
            <h3 style="font-weight: 800; margin-bottom: 1rem;">Connect New</h3>
            <p style="color: var(--text-muted); font-size: 0.9375rem; line-height: 1.6; margin-bottom: 2rem;">
                Manage multiple Facebook profiles from one single dashboard.
            </p>
            <a href="{{ url('/auth/facebook') }}" 
               onclick="alert('Clicking to connect...');"
               class="btn-primary" 
               style="width: 100%; justify-content: center; padding: 1.25rem; position: relative; z-index: 9999;">
                <i data-lucide="facebook"></i> Link New Account
            </a>
            
            <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid var(--glass-border);">
                <div style="display: flex; gap: 0.75rem; margin-bottom: 1rem;">
                    <i data-lucide="check" size="18" style="color: #10b981;"></i>
                    <span style="font-size: 0.8125rem; color: var(--text-muted);">Auto-import Business Pages</span>
                </div>
                <div style="display: flex; gap: 0.75rem;">
                    <i data-lucide="check" size="18" style="color: #10b981;"></i>
                    <span style="font-size: 0.8125rem; color: var(--text-muted);">Real-time Comment Sync</span>
                </div>
            </div>
        </div>
    </div>
@endsection
