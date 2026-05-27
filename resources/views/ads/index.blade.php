@extends('layouts.premium')

@section('content')
<div style="max-width: 1400px; margin: 0 auto; animation: fadeInUp 0.6s ease-out;">
    <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 2.5rem;">
        <div>
            <h1 style="font-size: 2.5rem; font-weight: 900; letter-spacing: -0.04em; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.75rem;">
                <i data-lucide="megaphone" style="color: var(--accent); width: 32px; height: 32px;"></i> Ads Manager
            </h1>
            <p style="color: var(--text-muted); font-size: 1.1rem; margin: 0;">Manage your Facebook and Instagram ad campaigns.</p>
        </div>
        <a href="{{ route('ads.create') }}" class="btn-primary" style="padding: 1rem 2rem; border-radius: 16px; font-weight: 700; text-decoration: none;">
            <i data-lucide="plus" style="width: 20px; height: 20px; margin-right: 8px;"></i> Create Campaign
        </a>
    </div>

    @if(session('error'))
        <div style="background: rgba(239, 68, 68, 0.1); color: #ef4444; padding: 1.5rem; border-radius: 16px; margin-bottom: 2rem; border: 1px solid rgba(239, 68, 68, 0.2);">
            <div style="font-weight: bold; margin-bottom: 4px;">Error</div>
            {{ session('error') }}
        </div>
    @endif

    @if(session('success'))
        <div style="background: rgba(16, 185, 129, 0.1); color: #10b981; padding: 1.5rem; border-radius: 16px; margin-bottom: 2rem; border: 1px solid rgba(16, 185, 129, 0.2);">
            {{ session('success') }}
        </div>
    @endif

    <!-- Ad Accounts Section -->
    <div class="premium-card" style="margin-bottom: 3rem; padding: 2.5rem; border-width: 2px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h2 style="font-size: 1.5rem; font-weight: 800; margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                <i data-lucide="credit-card" style="color: var(--accent); width: 24px; height: 24px;"></i> Connected Ad Accounts
            </h2>
            <form action="{{ route('ads.fetch') }}" method="POST">
                @csrf
                <button type="submit" style="background: rgba(99, 102, 241, 0.1); border: 1px solid rgba(99, 102, 241, 0.2); color: #818cf8; padding: 0.75rem 1.5rem; border-radius: 12px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: 0.3s;">
                    <i data-lucide="refresh-cw" style="width: 16px; height: 16px;"></i> Sync Accounts
                </button>
            </form>
        </div>

        @if($accounts->isEmpty())
            <div style="text-align: center; padding: 3rem; background: var(--bg-main); border-radius: 20px; border: 1px dashed var(--glass-border);">
                <i data-lucide="folder-search" style="width: 48px; height: 48px; color: var(--text-muted); margin-bottom: 1rem; opacity: 0.5;"></i>
                <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.5rem;">No Ad Accounts Found</h3>
                <p style="color: var(--text-muted); margin-bottom: 1.5rem;">Click 'Sync Accounts' to fetch ad accounts connected to your Facebook profile.</p>
            </div>
        @else
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem;">
                @foreach($accounts as $account)
                    <div style="background: var(--bg-main); border: 1px solid var(--glass-border); border-radius: 20px; padding: 1.5rem; transition: 0.3s; position: relative; overflow: hidden;">
                        <div style="position: absolute; top: 0; left: 0; width: 4px; height: 100%; background: var(--accent);"></div>
                        <div style="font-size: 0.8rem; font-weight: 800; text-transform: uppercase; color: var(--text-muted); letter-spacing: 0.1em; margin-bottom: 0.5rem;">Account ID: {{ $account->account_id }}</div>
                        <div style="font-size: 1.25rem; font-weight: 800; color: var(--text-main); margin-bottom: 1rem;">{{ $account->name }}</div>
                        <div style="display: inline-block; padding: 0.4rem 0.8rem; background: rgba(168, 85, 247, 0.1); border-radius: 8px; color: #c084fc; font-weight: 700; font-size: 0.85rem;">
                            Currency: {{ $account->currency }}
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Campaigns Section -->
    <div class="premium-card" style="padding: 2.5rem; border-width: 2px;">
        <h2 style="font-size: 1.5rem; font-weight: 800; margin-bottom: 2rem; display: flex; align-items: center; gap: 0.5rem;">
            <i data-lucide="target" style="color: var(--accent); width: 24px; height: 24px;"></i> Recent Campaigns
        </h2>

        @if($campaigns->isEmpty())
            <div style="text-align: center; padding: 3rem; background: var(--bg-main); border-radius: 20px; border: 1px dashed var(--glass-border);">
                <i data-lucide="bar-chart-2" style="width: 48px; height: 48px; color: var(--text-muted); margin-bottom: 1rem; opacity: 0.5;"></i>
                <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.5rem;">No Campaigns Yet</h3>
                <p style="color: var(--text-muted); margin-bottom: 1.5rem;">Create your first campaign to start driving traffic and engagement.</p>
                <a href="{{ route('ads.create') }}" class="btn-primary" style="display: inline-flex; padding: 0.75rem 1.5rem; border-radius: 12px; font-weight: 700; text-decoration: none;">Create Campaign</a>
            </div>
        @else
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; text-align: left;">
                    <thead>
                        <tr style="border-bottom: 2px solid var(--glass-border);">
                            <th style="padding: 1rem; color: var(--text-muted); font-weight: 800; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em;">Campaign Name</th>
                            <th style="padding: 1rem; color: var(--text-muted); font-weight: 800; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em;">Objective</th>
                            <th style="padding: 1rem; color: var(--text-muted); font-weight: 800; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em;">Daily Budget</th>
                            <th style="padding: 1rem; color: var(--text-muted); font-weight: 800; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em;">Status</th>
                            <th style="padding: 1rem; color: var(--text-muted); font-weight: 800; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em;">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($campaigns as $campaign)
                            <tr style="border-bottom: 1px solid var(--glass-border); transition: 0.3s; background: transparent;" onmouseover="this.style.background='rgba(255,255,255,0.02)'" onmouseout="this.style.background='transparent'">
                                <td style="padding: 1.25rem 1rem;">
                                    <div style="font-weight: 700; color: var(--text-main); font-size: 1rem;">{{ $campaign->name }}</div>
                                    <div style="font-size: 0.75rem; color: var(--text-muted);">ID: {{ $campaign->campaign_id ?? 'Pending' }}</div>
                                </td>
                                <td style="padding: 1.25rem 1rem;">
                                    <span style="background: rgba(59, 130, 246, 0.1); color: #60a5fa; padding: 4px 10px; border-radius: 8px; font-size: 0.8rem; font-weight: 700;">{{ str_replace('OUTCOME_', '', $campaign->objective) }}</span>
                                </td>
                                <td style="padding: 1.25rem 1rem; font-weight: 700; color: var(--text-main);">
                                    {{ $campaign->daily_budget ? $campaign->daily_budget . ' ' . $campaign->account->currency : 'Lifetime' }}
                                </td>
                                <td style="padding: 1.25rem 1rem;">
                                    <span style="background: rgba(245, 158, 11, 0.1); color: #fbbf24; padding: 4px 10px; border-radius: 8px; font-size: 0.8rem; font-weight: 700;">{{ $campaign->status }}</span>
                                </td>
                                <td style="padding: 1.25rem 1rem; color: var(--text-muted); font-size: 0.9rem;">
                                    {{ $campaign->created_at->format('M d, Y') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
