@extends('layouts.premium')

@section('content')
    <!-- CDN for Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div style="margin-bottom: 2.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="font-size: 2.25rem; font-weight: 800; letter-spacing: -0.03em; background: linear-gradient(135deg, var(--text-main), var(--accent)); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                Meta Ads Manager
            </h1>
            <p style="color: var(--text-muted); font-size: 0.95rem; margin-top: 0.25rem;">
                <span class="badge" style="background: rgba(59, 130, 246, 0.15); color: var(--accent); padding: 4px 8px; border-radius: 6px; font-weight: 700; font-size: 0.75rem;">Sandbox Demo Mode</span>
                Create mock campaigns, simulate budgets, and preview performance metrics.
            </p>
        </div>
        <a href="{{ route('ads.create') }}" class="btn-primary">
            <i data-lucide="plus-circle" style="width: 18px; height: 18px;"></i> Create New Ad
        </a>
    </div>

    <!-- Analytics Dashboard Overview Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
        <!-- Card 1: Total Spend -->
        <div class="premium-card" style="padding: 1.5rem; display: flex; align-items: center; gap: 1.25rem; position: relative; overflow: hidden;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(59, 130, 246, 0.1); color: var(--accent); display: flex; align-items: center; justify-content: center;">
                <i data-lucide="dollar-sign" style="width: 24px; height: 24px;"></i>
            </div>
            <div>
                <span style="font-size: 0.8rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase;">Total Spend</span>
                <h3 style="font-size: 1.5rem; font-weight: 800; margin-top: 2px;">${{ number_format($totalSpend, 2) }}</h3>
            </div>
        </div>

        <!-- Card 2: Impressions -->
        <div class="premium-card" style="padding: 1.5rem; display: flex; align-items: center; gap: 1.25rem; position: relative; overflow: hidden;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(16, 185, 129, 0.1); color: #10b981; display: flex; align-items: center; justify-content: center;">
                <i data-lucide="eye" style="width: 24px; height: 24px;"></i>
            </div>
            <div>
                <span style="font-size: 0.8rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase;">Impressions</span>
                <h3 style="font-size: 1.5rem; font-weight: 800; margin-top: 2px;">{{ number_format($totalImpressions) }}</h3>
            </div>
        </div>

        <!-- Card 3: Clicks -->
        <div class="premium-card" style="padding: 1.5rem; display: flex; align-items: center; gap: 1.25rem; position: relative; overflow: hidden;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(245, 158, 11, 0.1); color: #f59e0b; display: flex; align-items: center; justify-content: center;">
                <i data-lucide="mouse-pointer-click" style="width: 24px; height: 24px;"></i>
            </div>
            <div>
                <span style="font-size: 0.8rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase;">Ad Clicks</span>
                <h3 style="font-size: 1.5rem; font-weight: 800; margin-top: 2px;">{{ number_format($totalClicks) }}</h3>
            </div>
        </div>

        <!-- Card 4: Average CTR -->
        <div class="premium-card" style="padding: 1.5rem; display: flex; align-items: center; gap: 1.25rem; position: relative; overflow: hidden;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(236, 72, 153, 0.1); color: #ec4899; display: flex; align-items: center; justify-content: center;">
                <i data-lucide="percent" style="width: 24px; height: 24px;"></i>
            </div>
            <div>
                <span style="font-size: 0.8rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase;">Average CTR</span>
                <h3 style="font-size: 1.5rem; font-weight: 800; margin-top: 2px;">{{ number_format($averageCtr, 2) }}%</h3>
            </div>
        </div>
    </div>

    <!-- Chart Panel -->
    <div class="premium-card" style="padding: 2rem; margin-bottom: 2rem;">
        <h3 style="font-size: 1.1rem; font-weight: 800; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 8px;">
            <i data-lucide="trending-up" style="color: var(--accent);"></i> Performance Trends (Weekly Insights)
        </h3>
        <div style="height: 300px; position: relative; width: 100%;">
            <canvas id="performanceChart"></canvas>
        </div>
    </div>

    <!-- Campaigns List -->
    <div class="premium-card" style="padding: 2rem;">
        <h3 style="font-size: 1.1rem; font-weight: 800; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 8px;">
            <i data-lucide="layers" style="color: var(--accent);"></i> Active Ad Campaigns
        </h3>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead>
                    <tr style="border-bottom: 2px solid var(--glass-border);">
                        <th style="padding: 1rem; color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase; font-weight: 800;">Ad Campaign</th>
                        <th style="padding: 1rem; color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase; font-weight: 800;">Objective</th>
                        <th style="padding: 1rem; color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase; font-weight: 800;">Daily Budget</th>
                        <th style="padding: 1rem; color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase; font-weight: 800; text-align: center;">Spend / Clicks</th>
                        <th style="padding: 1rem; color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase; font-weight: 800;">Status</th>
                        <th style="padding: 1rem; color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase; font-weight: 800; text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($campaigns as $campaign)
                        <tr style="border-bottom: 1px solid var(--glass-border); transition: 0.2s; vertical-align: middle;" onmouseover="this.style.background='rgba(255,255,255,0.01)'" onmouseout="this.style.background='transparent'">
                            <td style="padding: 1.25rem 1rem;">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    @if($campaign->ad_image)
                                        <img src="{{ asset('storage/' . $campaign->ad_image) }}" style="width: 48px; height: 48px; border-radius: 10px; object-fit: cover; border: 1px solid var(--glass-border);" />
                                    @else
                                        <div style="width: 48px; height: 48px; border-radius: 10px; background: var(--nav-active); display: flex; align-items: center; justify-content: center; border: 1px solid var(--glass-border); color: var(--text-muted);">
                                            <i data-lucide="image" style="width: 20px; height: 20px;"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <div style="font-weight: 700; color: var(--text-main);">{{ $campaign->name }}</div>
                                        <span style="font-size: 0.7rem; color: var(--text-muted); display: block; margin-top: 2px;">
                                            Target: {{ $campaign->target_location }} (Age {{ $campaign->target_age_min }}-{{ $campaign->target_age_max }})
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td style="padding: 1.25rem 1rem;">
                                <span style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase; background: rgba(59, 130, 246, 0.1); color: var(--accent); padding: 4px 8px; border-radius: 6px;">
                                    {{ str_replace('_', ' ', $campaign->objective) }}
                                </span>
                            </td>
                            <td style="padding: 1.25rem 1rem; font-weight: 700;">
                                ${{ number_format($campaign->daily_budget, 2) }}/day
                            </td>
                            <td style="padding: 1.25rem 1rem; text-align: center;">
                                <div style="font-weight: 800; color: var(--text-main);">${{ number_format($campaign->spend, 2) }}</div>
                                <span style="font-size: 0.7rem; color: var(--text-muted);">{{ number_format($campaign->clicks) }} Clicks ({{ number_format($campaign->ctr, 1) }}% CTR)</span>
                            </td>
                            <td style="padding: 1.25rem 1rem;">
                                @if($campaign->status === 'ACTIVE')
                                    <span style="background: rgba(16, 185, 129, 0.1); color: #10b981; font-size: 0.65rem; font-weight: 800; padding: 3px 8px; border-radius: 20px; display: inline-flex; align-items: center; gap: 4px; border: 1px solid rgba(16, 185, 129, 0.2);">
                                        <span style="width: 5px; height: 5px; border-radius: 50%; background: #10b981;"></span> Running
                                    </span>
                                @else
                                    <span style="background: rgba(245, 158, 11, 0.1); color: #f59e0b; font-size: 0.65rem; font-weight: 800; padding: 3px 8px; border-radius: 20px; display: inline-flex; align-items: center; gap: 4px; border: 1px solid rgba(245, 158, 11, 0.2);">
                                        <span style="width: 5px; height: 5px; border-radius: 50%; background: #f59e0b;"></span> Paused
                                    </span>
                                @endif
                            </td>
                            <td style="padding: 1.25rem 1rem; text-align: right;">
                                <div style="display: inline-flex; gap: 8px; align-items: center;">
                                    <!-- Toggle Status -->
                                    <form action="{{ route('ads.toggle', $campaign->id) }}" method="POST" style="margin: 0;">
                                        @csrf
                                        <button type="submit" class="action-icon-btn edit" style="background: var(--nav-active); color: var(--text-main);" title="{{ $campaign->status === 'ACTIVE' ? 'Pause Campaign' : 'Resume Campaign' }}">
                                            <i data-lucide="{{ $campaign->status === 'ACTIVE' ? 'pause' : 'play' }}" style="width: 16px; height: 16px;"></i>
                                        </button>
                                    </form>

                                    <!-- Delete Campaign -->
                                    <form action="{{ route('ads.destroy', $campaign->id) }}" method="POST" style="margin: 0;" onsubmit="return confirm('Are you sure you want to delete this campaign?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-icon-btn delete" title="Delete Campaign">
                                            <i data-lucide="trash-2" style="width: 16px; height: 16px;"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 4rem 1rem; color: var(--text-muted);">
                                <i data-lucide="layers" style="width: 48px; height: 48px; opacity: 0.3; margin-bottom: 1rem; display: block; margin-left: auto; margin-right: auto;"></i>
                                <div style="font-weight: 800; font-size: 1.1rem; color: var(--text-main); margin-bottom: 4px;">No Ad Campaigns Found!</div>
                                <p style="font-size: 0.8rem; max-width: 320px; margin: 0 auto;">Build a sandbox ad to see dynamic budget allocation and metrics in real-time.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Chart.js configuration -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const ctx = document.getElementById('performanceChart').getContext('2d');
            
            // Premium gradients for the chart lines
            const gradientSpend = ctx.createLinearGradient(0, 0, 0, 300);
            gradientSpend.addColorStop(0, 'rgba(59, 130, 246, 0.4)');
            gradientSpend.addColorStop(1, 'rgba(59, 130, 246, 0.0)');

            const gradientClicks = ctx.createLinearGradient(0, 0, 0, 300);
            gradientClicks.addColorStop(0, 'rgba(236, 72, 153, 0.4)');
            gradientClicks.addColorStop(1, 'rgba(236, 72, 153, 0.0)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json($chartData['labels']),
                    datasets: [
                        {
                            label: 'Spend ($)',
                            data: @json($chartData['spend']),
                            borderColor: '#3b82f6',
                            borderWidth: 3,
                            backgroundColor: gradientSpend,
                            fill: true,
                            tension: 0.4,
                            yAxisID: 'y'
                        },
                        {
                            label: 'Clicks',
                            data: @json($chartData['clicks']),
                            borderColor: '#ec4899',
                            borderWidth: 3,
                            backgroundColor: gradientClicks,
                            fill: true,
                            tension: 0.4,
                            yAxisID: 'y1'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            labels: {
                                color: '#94a3b8',
                                font: { family: 'Inter', weight: 600 }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { color: 'rgba(255, 255, 255, 0.03)' },
                            ticks: { color: '#94a3b8', font: { family: 'Inter' } }
                        },
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            grid: { color: 'rgba(255, 255, 255, 0.03)' },
                            ticks: { color: '#3b82f6', font: { family: 'Inter' } },
                            title: { display: true, text: 'Spend ($)', color: '#3b82f6' }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            grid: { drawOnChartArea: false }, // only want the grid lines for one axis
                            ticks: { color: '#ec4899', font: { family: 'Inter' } },
                            title: { display: true, text: 'Clicks', color: '#ec4899' }
                        }
                    }
                }
            });
            
            // Initialize lucide icons in dynamically loaded elements
            lucide.createIcons();
        });
    </script>
@endsection
