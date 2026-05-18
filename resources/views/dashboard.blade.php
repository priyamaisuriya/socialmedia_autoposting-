@extends('layouts.premium')

@section('content')
    <style>
        /* Premium Dashboard Custom Styles */
        .dash-header-gradient {
            background: linear-gradient(135deg, var(--text-main), var(--accent));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: 800;
            letter-spacing: -0.03em;
        }

        /* Stats Cards Styling */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }

        .stat-card {
            background: var(--card-bg);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            padding: 1.75rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            border-color: var(--accent);
            box-shadow: 0 12px 40px rgba(99, 102, 241, 0.15);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: var(--accent-glow);
            filter: blur(40px);
            opacity: 0.15;
            transition: 0.3s;
        }

        .stat-card:hover::before {
            opacity: 0.3;
        }

        .stat-icon-wrapper {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: 0.3s;
        }

        .stat-badge {
            font-size: 0.7rem;
            font-weight: 800;
            padding: 4px 8px;
            border-radius: 8px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        /* Activity Table CSS - Zero Scrollbar & 100% Clickable */
        .activity-table-container {
            overflow-x: hidden !important; /* Force block horizontal scrollbars */
            width: 100%;
            border-radius: 20px;
            border: 1px solid var(--glass-border);
            background: var(--card-bg);
        }

        .activity-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            table-layout: auto;
        }

        .activity-table th {
            background: var(--nav-active);
            padding: 1.25rem 1.5rem;
            color: var(--text-muted);
            font-weight: 800;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 1px solid var(--glass-border);
        }

        .activity-table td {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--glass-border);
            font-size: 0.85rem;
            vertical-align: middle;
        }

        .activity-table tbody tr {
            transition: background 0.2s ease;
        }

        .activity-table tbody tr:hover {
            background: rgba(255, 255, 255, 0.02);
        }

        .activity-table tbody tr:last-child td {
            border-bottom: none;
        }

        .action-icon-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: var(--nav-active);
            border: 1px solid var(--glass-border);
            color: var(--text-main);
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            position: relative;
            z-index: 10;
        }

        .action-icon-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .action-icon-btn.sync:hover {
            background: rgba(59, 130, 246, 0.15);
            color: var(--accent);
            border-color: var(--accent);
        }

        .action-icon-btn.view:hover {
            background: rgba(16, 185, 129, 0.15);
            color: #10b981;
            border-color: #10b981;
        }

        .action-icon-btn svg,
        .action-icon-btn i {
            pointer-events: none !important;
        }

        /* Widgets Styling */
        .widget-card {
            background: var(--card-bg);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            padding: 2rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
        }

        .pulse-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #10b981;
            box-shadow: 0 0 10px rgba(16, 185, 129, 0.6);
            display: inline-block;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 8px rgba(16, 185, 129, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }

        /* Mobile Column Hiding */
        @media (max-width: 768px) {
            .hide-mobile {
                display: none !important;
            }
        }
    </style>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 class="dash-header-gradient" style="font-size: 2.25rem; margin: 0;">Dashboard Overview</h1>
            <p style="color: var(--text-muted); font-size: 0.95rem; margin-top: 0.25rem;">Real-time overview of your Meta integration, active pages, and general performance.</p>
        </div>
        <a href="{{ route('posts.create') }}" class="btn-primary">
            <i data-lucide="plus-circle" style="width: 18px; height: 18px;"></i> Create New Post
        </a>
    </div>

    @if($facebookAccounts->isEmpty())
        <!-- Gorgeous Glassmorphic Onboarding Card -->
        <div class="premium-card" style="padding: 4rem 2rem; text-align: center; margin-bottom: 3rem; background: linear-gradient(135deg, rgba(99, 102, 241, 0.08), rgba(168, 85, 247, 0.03)); border: 1px solid rgba(99, 102, 241, 0.25); position: relative; overflow: hidden; border-radius: 32px;">
            <div style="position: absolute; top: -50px; left: -50px; width: 150px; height: 150px; border-radius: 50%; background: var(--accent); filter: blur(80px); opacity: 0.15;"></div>
            <div style="position: absolute; bottom: -50px; right: -50px; width: 150px; height: 150px; border-radius: 50%; background: #a855f7; filter: blur(80px); opacity: 0.15;"></div>
            
            <div style="width: 80px; height: 80px; background: linear-gradient(135deg, var(--accent), #a855f7); color: white; border-radius: 24px; display: flex; align-items: center; justify-content: center; margin: 0 auto 2rem; box-shadow: 0 15px 30px rgba(99, 102, 241, 0.3);">
                <i data-lucide="sparkles" style="width: 38px; height: 38px;"></i>
            </div>
            
            <h2 style="font-size: 2.25rem; font-weight: 800; margin-bottom: 1rem; letter-spacing: -0.02em;">Welcome to FB Manager PRO</h2>
            <p style="color: var(--text-muted); font-size: 1.05rem; max-width: 600px; margin: 0 auto 2.5rem; line-height: 1.6; font-weight: 500;">
                Connect your professional Facebook/Meta account to start managing pages, publishing media, orchestrating replies, and analyzing engagements inside your premium suite.
            </p>
            <a href="/auth/facebook" class="btn-primary" style="padding: 1.1rem 2.5rem; font-size: 1.05rem; border-radius: 16px; background: linear-gradient(135deg, #1877f2, #166fe5); box-shadow: 0 8px 24px rgba(24, 119, 242, 0.25);">
                <i data-lucide="facebook" style="width: 18px; height: 18px; fill: white; border: none;"></i> Connect Facebook Account
            </a>
        </div>
    @endif

    <!-- Quick Stats Grid -->
    <div class="stats-grid">
        <!-- Stats Card: Total Posts -->
        <div class="stat-card">
            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1.25rem;">
                <div class="stat-icon-wrapper" style="background: rgba(99, 102, 241, 0.1); color: var(--accent);">
                    <i data-lucide="send" style="width: 22px; height: 22px;"></i>
                </div>
                <span class="stat-badge" style="background: rgba(99, 102, 241, 0.08); color: var(--accent);">Active</span>
            </div>
            <div style="color: var(--text-muted); font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.35rem;">Total Posts</div>
            <div style="font-size: 2rem; font-weight: 800;">{{ number_format($stats['total_posts']) }}</div>
        </div>

        <!-- Stats Card: Total Likes -->
        <div class="stat-card" style="position: relative; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);" onmouseover="this.style.borderColor='#f43f5e'; this.style.boxShadow='0 12px 40px rgba(244, 63, 94, 0.2)';" onmouseout="this.style.borderColor='var(--glass-border)'; this.style.boxShadow='0 8px 32px rgba(0, 0, 0, 0.12)';">
            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1.25rem;">
                <div class="stat-icon-wrapper" style="background: rgba(244, 63, 94, 0.1); color: #f43f5e;">
                    <i data-lucide="heart" style="width: 22px; height: 22px; fill: #f43f5e; border: none;"></i>
                </div>
                <span class="stat-badge" style="background: rgba(244, 63, 94, 0.08); color: #f43f5e;">Page Fans</span>
            </div>
            <div style="color: var(--text-muted); font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.35rem;">Total Page Likes</div>
            <div style="font-size: 2rem; font-weight: 800; color: #f43f5e;">{{ number_format($stats['page_likes'] ?? 0) }}</div>
            <div style="font-size: 0.7rem; font-weight: 700; color: var(--text-muted); margin-top: 4px; display: flex; align-items: center; gap: 4px;">
                <i data-lucide="activity" style="width: 10px; height: 10px;"></i> +{{ number_format($stats['total_likes'] ?? 0) }} post reactions
            </div>
        </div>

        <!-- Stats Card: Comments -->
        <div class="stat-card">
            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1.25rem;">
                <div class="stat-icon-wrapper" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                    <i data-lucide="message-square" style="width: 22px; height: 22px;"></i>
                </div>
                <span class="stat-badge" style="background: rgba(245, 158, 11, 0.08); color: #f59e0b;">Monitored</span>
            </div>
            <div style="color: var(--text-muted); font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.35rem;">Total Comments</div>
            <div style="font-size: 2rem; font-weight: 800;">{{ number_format($stats['total_comments']) }}</div>
        </div>

        <!-- Stats Card: Total Media -->
        <div class="stat-card">
            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1.25rem;">
                <div class="stat-icon-wrapper" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                    <i data-lucide="image" style="width: 22px; height: 22px;"></i>
                </div>
                <span class="stat-badge" style="background: rgba(16, 185, 129, 0.08); color: #10b981;">Synced</span>
            </div>
            <div style="color: var(--text-muted); font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.35rem;">Total Media</div>
            <div style="font-size: 2rem; font-weight: 800;">{{ number_format($stats['total_images'] + $stats['total_videos']) }}</div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div style="display: grid; grid-template-columns: 2.2fr 1.1fr; gap: 2rem; align-items: start; width: 100%;">
        
        <!-- Left Column: Activity List -->
        <div class="premium-card" style="padding: 0; overflow: hidden; width: 100%;">
            <div style="padding: 2rem; border-bottom: 1px solid var(--glass-border); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
                <h3 style="font-weight: 800; font-size: 1.15rem; margin: 0; display: flex; align-items: center; gap: 8px;">
                    <i data-lucide="activity" style="color: var(--accent); width: 18px; height: 18px;"></i> Recent Activity Log
                </h3>
                <a href="{{ route('posts.index') }}" style="color: var(--accent); text-decoration: none; font-size: 0.8rem; font-weight: 800; display: inline-flex; align-items: center; gap: 4px; position: relative; z-index: 100; cursor: pointer;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">
                    View Posts Hub <i data-lucide="arrow-right" style="width: 14px; height: 14px; pointer-events: none;"></i>
                </a>
            </div>

            <!-- Custom Clean Responsive Table -->
            <div class="activity-table-container">
                <table class="activity-table">
                    <thead>
                        <tr>
                            <th style="width: 45%;">Connected Page & Content</th>
                            <th style="width: 15%;">Status</th>
                            <th class="hide-mobile" style="width: 15%;">Date</th>
                            <th style="width: 25%; text-align: right; min-width: 130px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentPosts as $post)
                            <tr>
                                <!-- Page and text content -->
                                <td>
                                    <div style="display: flex; gap: 10px; align-items: center;">
                                        <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(59, 130, 246, 0.08); color: var(--accent); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                            <i data-lucide="facebook" style="width: 14px; height: 14px; fill: var(--accent); border: none;"></i>
                                        </div>
                                        <div style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                            <div style="font-weight: 700; font-size: 0.85rem; color: var(--text-main);">{{ $post->facebookPage->name ?? 'Unknown Page' }}</div>
                                            <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 500;" title="{{ $post->message }}">{{ Str::limit($post->message, 32) }}</span>
                                        </div>
                                    </div>
                                </td>

                                <!-- Status Badge -->
                                <td>
                                    @if($post->status === 'success')
                                        <span style="background: rgba(16, 185, 129, 0.1); color: #10b981; font-size: 0.65rem; font-weight: 800; padding: 4px 8px; border-radius: 12px; border: 1px solid rgba(16, 185, 129, 0.2);">
                                            Success
                                        </span>
                                    @else
                                        <span style="background: rgba(239, 68, 68, 0.1); color: #ef4444; font-size: 0.65rem; font-weight: 800; padding: 4px 8px; border-radius: 12px; border: 1px solid rgba(239, 68, 68, 0.2);">
                                            Failed
                                        </span>
                                    @endif
                                </td>

                                <!-- Date -->
                                <td class="hide-mobile" style="color: var(--text-muted); font-size: 0.8rem; font-weight: 600;">
                                    {{ $post->created_at->diffForHumans() }}
                                </td>

                                <!-- Dedicated Actions -->
                                <td style="text-align: right;">
                                    <div style="display: inline-flex; gap: 8px; justify-content: flex-end; align-items: center;">
                                        @if($post->status === 'success')
                                            <!-- Sync Comments -->
                                            <a href="{{ route('comments.sync', $post->id) }}" class="action-icon-btn sync" title="Sync Live Comments">
                                                <i data-lucide="refresh-cw" style="width: 14px; height: 14px;"></i>
                                            </a>
                                            <!-- View Details -->
                                            <a href="{{ route('posts.show', $post->id) }}" class="action-icon-btn view" title="View Detailed Analytics">
                                                <i data-lucide="eye" style="width: 14px; height: 14px;"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="padding: 4rem 1rem; text-align: center; color: var(--text-muted);">
                                    <i data-lucide="clipboard-list" style="width: 40px; height: 40px; opacity: 0.3; margin-bottom: 0.75rem; display: block; margin-left: auto; margin-right: auto;"></i>
                                    <span style="font-weight: 700; font-size: 0.9rem; display: block; margin-bottom: 2px;">No activity logged yet</span>
                                    <span style="font-size: 0.8rem; display: block;">Create your first post to see logs here.</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right Column: Sidebar widgets -->
        <div style="display: flex; flex-direction: column; gap: 2rem;">
            
            <!-- Connected Accounts Widget -->
            <div class="widget-card">
                <h3 style="font-weight: 800; font-size: 1.05rem; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 8px; justify-content: space-between;">
                    <span>Connected Accounts</span>
                    <a href="/auth/facebook" style="background: var(--nav-active); border: 1px solid var(--glass-border); color: var(--text-main); width: 26px; height: 26px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; text-decoration: none;" title="Connect new account">
                        <i data-lucide="plus" style="width: 12px; height: 12px;"></i>
                    </a>
                </h3>

                <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                    @forelse($facebookAccounts as $account)
                        <div style="display: flex; align-items: center; gap: 12px; background: var(--nav-active); padding: 0.875rem 1.25rem; border-radius: 16px; border: 1px solid var(--glass-border);">
                            <div style="width: 36px; height: 36px; border-radius: 10px; background: #1877f2; color: white; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <i data-lucide="facebook" style="width: 18px; height: 18px; fill: white; border: none;"></i>
                            </div>
                            <div style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap; flex: 1;">
                                <div style="font-weight: 800; font-size: 0.85rem; color: var(--text-main); overflow: hidden; text-overflow: ellipsis;">{{ $account->name }}</div>
                                <span style="font-size: 0.7rem; color: #10b981; font-weight: 700; display: inline-flex; align-items: center; gap: 4px; margin-top: 1px;">
                                    <span class="pulse-dot"></span> Active
                                </span>
                            </div>
                        </div>
                    @empty
                        <div style="text-align: center; padding: 1.5rem 0; color: var(--text-muted);">
                            <i data-lucide="user-x" style="width: 28px; height: 28px; opacity: 0.3; margin-bottom: 0.5rem; display: block; margin-left: auto; margin-right: auto;"></i>
                            <span style="font-weight: 700; font-size: 0.8rem; display: block; margin-bottom: 4px;">No accounts connected</span>
                            <a href="/auth/facebook" class="btn-primary" style="padding: 0.5rem 1rem; font-size: 0.75rem; border-radius: 8px; margin-top: 8px; justify-content: center;">Connect Account</a>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Recent Comments Widget -->
            <div class="widget-card">
                <h3 style="font-weight: 800; font-size: 1.05rem; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 8px;">
                    <i data-lucide="message-square" style="color: var(--accent); width: 18px; height: 18px;"></i> Recent Comments Stream
                </h3>

                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    @forelse($recentComments as $comment)
                        <div style="padding: 1rem; background: var(--nav-active); border: 1px solid var(--glass-border); border-radius: 16px; position: relative; transition: 0.2s;" onmouseover="this.style.borderColor='var(--accent)';" onmouseout="this.style.borderColor='var(--glass-border)';">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                                <span style="font-weight: 800; font-size: 0.8rem; color: var(--text-main);">{{ $comment->user_name }}</span>
                                <span style="font-size: 0.65rem; color: var(--text-muted); font-weight: 600;">{{ $comment->created_at->diffForHumans() }}</span>
                            </div>
                            <p style="font-size: 0.8rem; color: var(--text-muted); line-height: 1.4; margin: 0; font-weight: 500;">
                                {{ Str::limit($comment->message, 45) }}
                            </p>
                        </div>
                    @empty
                        <div style="text-align: center; padding: 1.5rem 0; color: var(--text-muted);">
                            <i data-lucide="messages-square" style="width: 28px; height: 28px; opacity: 0.3; margin-bottom: 0.5rem; display: block; margin-left: auto; margin-right: auto;"></i>
                            <span style="font-weight: 700; font-size: 0.8rem; display: block;">No comments stream yet</span>
                            <span style="font-size: 0.7rem; display: block; margin-top: 1px;">Synchronized comments will stream here.</span>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Trigger Lucide rendering initially -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            lucide.createIcons();
        });
    </script>
@endsection