@extends('layouts.premium')

@section('content')
    <!-- Creative Planner Hero Banner -->
    <div style="margin-bottom: 3rem; background: linear-gradient(145deg, #0f172a, #1e1b4b, #312e81); border-radius: 32px; padding: 2.5rem 3rem; border: 1px solid rgba(99, 102, 241, 0.3); box-shadow: 0 20px 40px rgba(0,0,0,0.25), inset 0 1px 0 rgba(255,255,255,0.1); position: relative; overflow: hidden; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 2rem;">
        <!-- Background aesthetic glowing orbs -->
        <div style="position: absolute; top: -100px; left: -100px; width: 300px; height: 300px; background: radial-gradient(circle, rgba(99,102,241,0.25) 0%, transparent 70%); border-radius: 50%; animation: pulse 4s infinite alternate;"></div>
        <div style="position: absolute; bottom: -100px; right: -50px; width: 300px; height: 300px; background: radial-gradient(circle, rgba(236,72,153,0.15) 0%, transparent 70%); border-radius: 50%; animation: pulse 5s infinite alternate-reverse;"></div>
        
        <div style="position: relative; z-index: 10;">
            <h1 style="font-size: 2.8rem; font-weight: 900; letter-spacing: -0.03em; color: #fff; margin: 0; display: flex; align-items: center; gap: 1rem;">
                <i data-lucide="calendar-days" style="color: #a855f7; width: 42px; height: 42px;"></i> Content Universe
            </h1>
            <p style="color: #cbd5e1; font-size: 1.05rem; margin-top: 0.5rem; font-weight: 500; max-width: 400px; line-height: 1.5;">
                Orchestrate, schedule, and dominate your social media campaigns from one powerful command center.
            </p>
        </div>

        <!-- Right Header controls: Jump Selector + Navigation Arrows -->
        <div style="position: relative; z-index: 10; display: flex; align-items: center; gap: 1.25rem; flex-wrap: wrap; background: rgba(255,255,255,0.05); padding: 1rem; border-radius: 20px; border: 1px solid rgba(255,255,255,0.1); backdrop-filter: blur(10px);">
            
            <!-- Month & Year Jump Selector Form -->
            <form action="{{ route('analytics.index') }}" method="GET" style="display: flex; gap: 8px; align-items: center;">
                <input type="hidden" name="facebook_page_id" value="{{ $pageIdFilter }}">
                <select name="month" style="background: rgba(0,0,0,0.2) !important; color: white !important; border: 1px solid rgba(255,255,255,0.1) !important; padding: 8px 12px; border-radius: 12px; font-weight: 700; outline: none; cursor: pointer; appearance: none;" onchange="this.form.submit()">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" style="color: black;" {{ $month == $m ? 'selected' : '' }}>
                            {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                        </option>
                    @endfor
                </select>
                <select name="year" style="background: rgba(0,0,0,0.2) !important; color: white !important; border: 1px solid rgba(255,255,255,0.1) !important; padding: 8px 12px; border-radius: 12px; font-weight: 700; outline: none; cursor: pointer; appearance: none;" onchange="this.form.submit()">
                    @for($y = 2025; $y <= 2030; $y++)
                        <option value="{{ $y }}" style="color: black;" {{ $year == $y ? 'selected' : '' }}>
                            {{ $y }}
                        </option>
                    @endfor
                </select>
            </form>

            <!-- Navigation Arrows -->
            <div style="display: inline-flex; align-items: center; gap: 4px;">
                <a href="{{ route('analytics.index', ['month' => $prevMonth->month, 'year' => $prevMonth->year, 'facebook_page_id' => $pageIdFilter]) }}" 
                   style="background: rgba(99,102,241,0.2); border: 1px solid rgba(99,102,241,0.4); color: white; width: 36px; height: 36px; border-radius: 12px; display: flex; align-items: center; justify-content: center; text-decoration: none; transition: 0.2s;" onmouseover="this.style.background='#4f46e5'" onmouseout="this.style.background='rgba(99,102,241,0.2)'" title="Previous Month">
                    <i data-lucide="chevron-left" style="width: 18px; height: 18px;"></i>
                </a>
                
                <span style="font-weight: 900; font-size: 1.1rem; color: #fff; padding: 0 1rem; min-width: 140px; text-align: center; letter-spacing: -0.01em;">
                    {{ $monthName }}
                </span>
                
                <a href="{{ route('analytics.index', ['month' => $nextMonth->month, 'year' => $nextMonth->year, 'facebook_page_id' => $pageIdFilter]) }}" 
                   style="background: rgba(99,102,241,0.2); border: 1px solid rgba(99,102,241,0.4); color: white; width: 36px; height: 36px; border-radius: 12px; display: flex; align-items: center; justify-content: center; text-decoration: none; transition: 0.2s;" onmouseover="this.style.background='#4f46e5'" onmouseout="this.style.background='rgba(99,102,241,0.2)'" title="Next Month">
                    <i data-lucide="chevron-right" style="width: 18px; height: 18px;"></i>
                </a>
            </div>

        </div>
    </div>

    <!-- Main Grid Layout: Controls Sidebar + Calendar Grid -->
    <div style="display: grid; grid-template-columns: 280px 1fr; gap: 2rem; align-items: start; width: 100%;">
        
        <!-- Left Sidebar: Controls & Stats Insights -->
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            
            <!-- Page Filter panel -->
            <div class="premium-card" style="padding: 1.5rem;">
                <h3 style="font-size: 0.9rem; font-weight: 800; color: var(--text-main); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 1rem; display: flex; align-items: center; gap: 8px;">
                    <i data-lucide="filter" style="width: 14px; height: 14px; color: var(--accent);"></i> Filter by Page
                </h3>
                
                <form action="{{ route('analytics.index') }}" method="GET" id="filter-form">
                    <input type="hidden" name="month" value="{{ $month }}">
                    <input type="hidden" name="year" value="{{ $year }}">
                    
                    <select name="facebook_page_id" onchange="this.form.submit()" class="planner-page-selector">
                        <option value="">All Connected Pages</option>
                        @foreach($connectedPages as $p)
                            <option value="{{ $p->id }}" {{ $pageIdFilter == $p->id ? 'selected' : '' }}>
                                {{ $p->name }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>

            <!-- Month statistics panel -->
            <div class="premium-card" style="padding: 1.5rem;">
                <h3 style="font-size: 0.9rem; font-weight: 800; color: var(--text-main); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 8px;">
                    <i data-lucide="pie-chart" style="width: 14px; height: 14px; color: var(--accent);"></i> Monthly Insights
                </h3>

                <!-- Progress Ring Success Rate -->
                <div style="display: flex; justify-content: center; align-items: center; margin-bottom: 1.5rem; position: relative;">
                    <svg width="120" height="120" viewBox="0 0 120 120">
                        <circle cx="60" cy="60" r="50" fill="none" stroke="rgba(255,255,255,0.03)" stroke-width="8"></circle>
                        <circle cx="60" cy="60" r="50" fill="none" stroke="var(--accent)" stroke-width="8"
                                stroke-dasharray="314" stroke-dashoffset="{{ 314 - (314 * ($plannerStats['success_rate'] / 100)) }}"
                                stroke-linecap="round" style="transition: stroke-dashoffset 0.8s ease-in-out; transform: rotate(-90deg); transform-origin: 50% 50%;"></circle>
                    </svg>
                    <div style="position: absolute; text-align: center;">
                        <span style="font-size: 1.5rem; font-weight: 900; color: var(--text-main); display: block; line-height: 1;">
                            {{ $plannerStats['success_rate'] }}%
                        </span>
                        <span style="font-size: 0.65rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; display: block; margin-top: 4px;">
                            Success Rate
                        </span>
                    </div>
                </div>

                <!-- Status Distribution list -->
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <div class="stat-insight-item">
                        <span style="display: flex; align-items: center; gap: 8px; font-size: 0.8rem; font-weight: 700; color: var(--text-muted);">
                            <span style="width: 8px; height: 8px; border-radius: 50%; background: var(--accent);"></span>
                            Total Scheduled
                        </span>
                        <span style="font-weight: 800; font-size: 0.9rem; color: var(--text-main);">{{ $plannerStats['total'] }}</span>
                    </div>
                    <div class="stat-insight-item">
                        <span style="display: flex; align-items: center; gap: 8px; font-size: 0.8rem; font-weight: 700; color: var(--text-muted);">
                            <span style="width: 8px; height: 8px; border-radius: 50%; background: #10b981;"></span>
                            Success
                        </span>
                        <span style="font-weight: 800; font-size: 0.9rem; color: #10b981;">{{ $plannerStats['success'] }}</span>
                    </div>
                    <div class="stat-insight-item">
                        <span style="display: flex; align-items: center; gap: 8px; font-size: 0.8rem; font-weight: 700; color: var(--text-muted);">
                            <span style="width: 8px; height: 8px; border-radius: 50%; background: #f59e0b;"></span>
                            Pending
                        </span>
                        <span style="font-weight: 800; font-size: 0.9rem; color: #f59e0b;">{{ $plannerStats['pending'] }}</span>
                    </div>
                    <div class="stat-insight-item">
                        <span style="display: flex; align-items: center; gap: 8px; font-size: 0.8rem; font-weight: 700; color: var(--text-muted);">
                            <span style="width: 8px; height: 8px; border-radius: 50%; background: #ef4444;"></span>
                            Failed
                        </span>
                        <span style="font-weight: 800; font-size: 0.9rem; color: #ef4444;">{{ $plannerStats['failed'] }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side: Monthly Calendar Grid -->
        <div style="display: flex; flex-direction: column; gap: 1rem; width: 100%;">
            
            <!-- Calendar Card Wrapper -->
            <div class="premium-card" style="padding: 1.75rem; width: 100%;">
                
                <!-- Weekdays Header Grid -->
                <div style="display: grid; grid-template-columns: repeat(7, 1fr); text-align: center; border-bottom: 1px solid var(--glass-border); padding-bottom: 0.75rem; margin-bottom: 0.75rem; gap: 8px;">
                    @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $dayName)
                        <div style="font-weight: 800; font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em;">
                            {{ $dayName }}
                        </div>
                    @endforeach
                </div>

                <!-- Monthly grid cells -->
                <div style="display: grid; grid-template-columns: repeat(7, 1fr); grid-auto-rows: minmax(115px, auto); gap: 8px; width: 100%;">
                    
                    <!-- Empty Offsets -->
                    @for($i = 0; $i < $startOfWeek; $i++)
                        <div style="background: rgba(255, 255, 255, 0.005); border: 1px dashed rgba(255, 255, 255, 0.02); border-radius: 12px; opacity: 0.25;"></div>
                    @endfor

                    <!-- Active Days in Month -->
                    @for($day = 1; $day <= $daysInMonth; $day++)
                        @php
                            $isToday = ($day == date('j') && $month == date('n') && $year == date('Y'));
                            $dayPosts = $postsByDay[$day] ?? [];
                        @endphp
                        
                        <!-- Day Cell Container -->
                        <div class="planner-day-cell {{ $isToday ? 'today' : '' }}">
                            
                            <!-- Header within Cell -->
                            <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                                <span class="planner-day-num {{ $isToday ? 'active' : '' }}">
                                    {{ $day }}
                                </span>
                                
                                <!-- Add/Schedule Quick Plan button opens Modal -->
                                <button type="button" class="btn-quick-plan" onclick="openQuickPlanner('{{ $day }}', '{{ $monthName }}')" title="Quick Plan Post">
                                    <i data-lucide="plus" style="width: 10px; height: 10px; stroke-width: 3;"></i>
                                </button>
                            </div>

                            <!-- Post List inside cell -->
                            <div class="planner-posts-container">
                                @foreach($dayPosts as $post)
                                    @php
                                        $firstMedia = $post->media->first();
                                        $mediaUrl = $firstMedia ? asset('storage/' . $firstMedia->file_path) : null;
                                        if ($firstMedia && (filter_var($firstMedia->file_path, FILTER_VALIDATE_URL) !== false)) {
                                            $mediaUrl = $firstMedia->file_path;
                                        }
                                    @endphp

                                    {{-- 1. Facebook Item --}}
                                    @if($post->post_to_facebook)
                                        @php
                                            $fbStatus = $post->status ?: 'pending';
                                            $statusClass = 'success';
                                            if($fbStatus === 'failed') $statusClass = 'failed';
                                            elseif($fbStatus === 'pending') $statusClass = 'pending';

                                            $fbJsonData = [
                                                'id' => $post->id,
                                                'platform' => 'Facebook',
                                                'message' => $post->message,
                                                'status' => ucfirst($fbStatus),
                                                'page_name' => $post->facebookPage->name ?? 'None connected',
                                                'media_url' => $mediaUrl,
                                                'media_type' => $firstMedia ? $firstMedia->media_type : null,
                                                'likes' => $post->dynamic_likes ?? 0,
                                                'comments' => $post->dynamic_comments ?? 0,
                                                'date' => $post->created_at->format('M d, Y \a\t h:i A'),
                                                'error' => null
                                            ];
                                        @endphp
                                        <button type="button" class="planner-post-bubble fb-bubble {{ $statusClass }}" 
                                                onclick="openPostPreview({{ json_encode($fbJsonData) }})"
                                                title="Facebook: {{ $post->message }}">
                                            
                                            <span class="platform-badge fb">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="8" height="8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
                                            </span>

                                            @if($firstMedia && $firstMedia->media_type === 'image')
                                                <img src="{{ $mediaUrl }}" style="width: 14px; height: 14px; border-radius: 3px; object-fit: cover; flex-shrink: 0;" />
                                            @elseif($firstMedia && $firstMedia->media_type === 'video')
                                                <div style="width: 14px; height: 14px; border-radius: 3px; background: black; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                                    <i data-lucide="play" style="width: 6px; height: 6px; color: white; fill: white;"></i>
                                                </div>
                                            @endif
                                            
                                            <span class="bubble-text">
                                                {{ Str::limit($post->message, 10) }}
                                            </span>
                                        </button>
                                    @endif

                                    {{-- 2. Instagram Item --}}
                                    @if($post->post_to_instagram)
                                        @php
                                            $igStatus = $post->instagram_status ?: 'pending';
                                            $statusClass = 'success';
                                            if($igStatus === 'failed') $statusClass = 'failed';
                                            elseif($igStatus === 'pending') $statusClass = 'pending';

                                            $igJsonData = [
                                                'id' => $post->id,
                                                'platform' => 'Instagram',
                                                'message' => $post->message,
                                                'status' => ucfirst($igStatus),
                                                'page_name' => $post->facebookPage->name ?? 'None connected',
                                                'instagram_username' => $post->facebookPage->instagram_username ?? null,
                                                'media_url' => $mediaUrl,
                                                'media_type' => $firstMedia ? $firstMedia->media_type : null,
                                                'likes' => $post->dynamic_likes ?? 0,
                                                'comments' => $post->dynamic_comments ?? 0,
                                                'date' => $post->created_at->format('M d, Y \a\t h:i A'),
                                                'error' => $post->instagram_error
                                            ];
                                        @endphp
                                        <button type="button" class="planner-post-bubble ig-bubble {{ $statusClass }}" 
                                                onclick="openPostPreview({{ json_encode($igJsonData) }})"
                                                title="Instagram: {{ $post->message }}">
                                            
                                            <span class="platform-badge ig">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="8" height="8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
                                            </span>

                                            @if($firstMedia && $firstMedia->media_type === 'image')
                                                <img src="{{ $mediaUrl }}" style="width: 14px; height: 14px; border-radius: 3px; object-fit: cover; flex-shrink: 0;" />
                                            @elseif($firstMedia && $firstMedia->media_type === 'video')
                                                <div style="width: 14px; height: 14px; border-radius: 3px; background: black; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                                    <i data-lucide="play" style="width: 6px; height: 6px; color: white; fill: white;"></i>
                                                </div>
                                            @endif
                                            
                                            <span class="bubble-text">
                                                {{ Str::limit($post->message, 10) }}
                                            </span>
                                        </button>
                                    @endif
                                @endforeach
                            </div>

                        </div>
                    @endfor

                    <!-- Padding cells for trailing week -->
                    @php
                        $totalCellsSoFar = $startOfWeek + $daysInMonth;
                        $remainingCells = (7 - ($totalCellsSoFar % 7)) % 7;
                    @endphp
                    @for($i = 0; $i < $remainingCells; $i++)
                        <div style="background: rgba(255, 255, 255, 0.005); border: 1px dashed rgba(255, 255, 255, 0.02); border-radius: 12px; opacity: 0.25;"></div>
                    @endfor

                </div>
            </div>

            <!-- Beautiful Horizontal Legend Bar -->
            <div class="premium-card" style="padding: 1rem 1.5rem; display: flex; justify-content: center; gap: 2rem; align-items: center; flex-wrap: wrap;">
                <span style="font-size: 0.75rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; margin-right: 0.5rem;">
                    Status Legend:
                </span>
                <span class="legend-badge success">
                    <span class="dot"></span> Published / Success
                </span>
                <span class="legend-badge pending">
                    <span class="dot"></span> Pending / Scheduled
                </span>
                <span class="legend-badge failed">
                    <span class="dot"></span> Failed / Action Needed
                </span>
            </div>

        </div>
    </div>

    <!-- MODAL 1: Quick Post Creator Modal -->
    <div id="quickPlannerModal" class="planner-modal-overlay">
        <div class="planner-modal-content">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid var(--glass-border); padding-bottom: 1rem;">
                <div>
                    <h3 style="font-size: 1.25rem; font-weight: 900; letter-spacing: -0.02em;" id="plannerModalTitle">Quick Post Creator</h3>
                    <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 2px;" id="plannerModalDateLabel"></p>
                </div>
                <button type="button" class="btn-close-modal" onclick="closeQuickPlanner()">
                    <i data-lucide="x" style="width: 20px; height: 20px;"></i>
                </button>
            </div>

            <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 1.25rem;">
                @csrf
                
                <!-- Target Page select -->
                <div>
                    <label class="modal-form-label">Target Page</label>
                    <select name="facebook_page_id" required class="modal-form-select">
                        @foreach($connectedPages as $p)
                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Post Message Content textarea -->
                <div>
                    <label class="modal-form-label">Post Message</label>
                    <textarea name="message" required class="modal-form-textarea" placeholder="Write something amazing here..."></textarea>
                </div>

                <!-- Emojis panel -->
                <div style="display: flex; gap: 6px; flex-wrap: wrap; margin-top: -6px;">
                    @foreach(['🚀', '🔥', '✨', '✅', '❤️', '🌟', '💬'] as $em)
                        <button type="button" class="btn-modal-emoji" onclick="modalInsertEmoji('{{ $em }}')">{{ $em }}</button>
                    @endforeach
                </div>

                <!-- Media select files -->
                <div>
                    <label class="modal-form-label">Attach Media</label>
                    <input type="file" name="media[]" multiple class="modal-form-file" accept="image/*,video/*" />
                </div>

                <!-- Submit / Cancel Actions -->
                <div style="display: flex; gap: 1rem; justify-content: flex-end; border-top: 1px solid var(--glass-border); padding-top: 1.25rem; margin-top: 0.5rem;">
                    <button type="button" class="btn-modal-action cancel" onclick="closeQuickPlanner()">Cancel</button>
                    <button type="submit" class="btn-modal-action submit">Publish Content</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 2: Post Detail Preview Modal -->
    <div id="postPreviewModal" class="planner-modal-overlay">
        <div class="planner-modal-content detail-preview">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid var(--glass-border); padding-bottom: 1rem;">
                <div>
                    <span id="previewPageBadge" style="font-size: 0.75rem; background: var(--accent); color: white; padding: 2px 8px; border-radius: 8px; font-weight: 800;">Facebook Page</span>
                    <h3 id="previewPostTitle" style="font-size: 1.25rem; font-weight: 900; margin-top: 6px;">Post Detail</h3>
                </div>
                <button type="button" class="btn-close-modal" onclick="closePostPreview()">
                    <i data-lucide="x" style="width: 20px; height: 20px;"></i>
                </button>
            </div>

            <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                
                <!-- Message Details box -->
                <div style="padding: 1.25rem; background: rgba(255,255,255,0.02); border: 1px solid var(--glass-border); border-radius: 16px;">
                    <p id="previewMessageText" style="font-size: 0.95rem; color: var(--text-main); line-height: 1.6; white-space: pre-wrap; font-weight: 500;"></p>
                </div>

                <!-- Media Preview container -->
                <div id="previewMediaBox" style="display: none; border-radius: 16px; overflow: hidden; border: 1px solid var(--glass-border); background: black;">
                    <!-- Dynamically populated media -->
                </div>

                <!-- Error Message block if failed -->
                <div id="previewErrorBlock" style="display: none; padding: 1rem; background: rgba(239,68,68,0.08); border: 1px solid rgba(239,68,68,0.2); border-radius: 16px; color: #ef4444; font-size: 0.85rem;">
                    <strong style="font-weight: 800; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em; display: block; margin-bottom: 4px;">Publishing Error:</strong>
                    <span id="previewErrorText"></span>
                </div>

                <!-- Status & Stats Row -->
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem;">
                    
                    <div class="preview-stat-card">
                        <span style="font-size: 0.65rem; color: var(--text-muted); text-transform: uppercase; font-weight: 800; letter-spacing: 0.05em; display: block; margin-bottom: 4px;">Status</span>
                        <span id="previewStatusBadge" style="font-weight: 900; font-size: 0.85rem;">Pending</span>
                    </div>
                    
                    <div class="preview-stat-card">
                        <span style="font-size: 0.65rem; color: var(--text-muted); text-transform: uppercase; font-weight: 800; letter-spacing: 0.05em; display: block; margin-bottom: 4px;">Reactions</span>
                        <span style="font-weight: 900; font-size: 0.85rem; color: #f43f5e; display: flex; align-items: center; gap: 4px;">
                            <i data-lucide="heart" style="width: 14px; height: 14px; fill: #f43f5e;"></i> <span id="previewLikesCount">0</span>
                        </span>
                    </div>

                    <div class="preview-stat-card">
                        <span style="font-size: 0.65rem; color: var(--text-muted); text-transform: uppercase; font-weight: 800; letter-spacing: 0.05em; display: block; margin-bottom: 4px;">Comments</span>
                        <span style="font-weight: 900; font-size: 0.85rem; color: var(--accent); display: flex; align-items: center; gap: 4px;">
                            <i data-lucide="message-square" style="width: 14px; height: 14px; fill: var(--accent);"></i> <span id="previewCommentsCount">0</span>
                        </span>
                    </div>

                </div>

                <!-- Post date info -->
                <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 700; text-align: center; margin-top: 0.5rem;" id="previewDateLabel"></div>

                <!-- Actions -->
                <div style="display: flex; gap: 1rem; border-top: 1px solid var(--glass-border); padding-top: 1.25rem; margin-top: 0.5rem;">
                    <a id="previewFullBtn" href="#" class="btn-modal-action submit" style="text-align: center; text-decoration: none; flex: 1;">Full Performance Page</a>
                    <button type="button" class="btn-modal-action cancel" onclick="closePostPreview()">Dismiss</button>
                </div>

            </div>
        </div>
    </div>

    <!-- Stunning Visual Styles for Content Calendar -->
    <style>
        /* Header Selector Jump styles */
        .header-jump-selector {
            background: transparent !important;
            border: none !important;
            color: var(--text-main) !important;
            padding: 8px 24px 8px 12px !important;
            border-radius: 8px !important;
            font-weight: 800 !important;
            font-size: 0.85rem !important;
            outline: none !important;
            cursor: pointer !important;
            appearance: none !important;
            -webkit-appearance: none !important;
            background-image: url("data:image/svg+xml;charset=utf-8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23a0aec0' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E") !important;
            background-repeat: no-repeat !important;
            background-position: right 8px center !important;
            background-size: 10px !important;
        }

        .header-jump-selector:hover {
            color: var(--accent) !important;
        }

        /* Month Nav control hover scaling */
        .btn-nav-cal {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 34px;
            height: 34px;
            border-radius: 10px;
            color: var(--text-muted);
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
        }
        
        .btn-nav-cal:hover {
            background: var(--accent) !important;
            color: white !important;
            transform: scale(1.05);
            box-shadow: 0 4px 12px var(--accent-glow);
        }

        /* Filter Select list style */
        .planner-page-selector {
            width: 100% !important;
            background: var(--nav-active) !important;
            border: 1px solid var(--glass-border) !important;
            color: var(--text-main) !important;
            padding: 10px 14px !important;
            border-radius: 12px !important;
            font-weight: 700 !important;
            outline: none !important;
            cursor: pointer !important;
            transition: all 0.3s !important;
            appearance: none !important;
            -webkit-appearance: none !important;
            background-image: url("data:image/svg+xml;charset=utf-8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23a0aec0' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E") !important;
            background-repeat: no-repeat !important;
            background-position: right 14px center !important;
            background-size: 14px !important;
        }

        .planner-page-selector:focus {
            border-color: var(--accent) !important;
            box-shadow: 0 0 15px var(--accent-glow) !important;
        }

        /* Stats insights list items styles */
        .stat-insight-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 12px;
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            transition: background 0.2s;
        }
        
        .stat-insight-item:hover {
            background: rgba(255, 255, 255, 0.04);
        }

        /* Individual day cells styling */
        .planner-day-cell {
            background: var(--nav-active);
            border: 1px solid var(--glass-border);
            border-radius: 14px;
            padding: 0.6rem;
            display: flex;
            flex-direction: column;
            gap: 6px;
            position: relative;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            min-height: 110px;
        }

        .planner-day-cell.today {
            background: rgba(99, 102, 241, 0.04);
            border-color: rgba(99, 102, 241, 0.4);
            box-shadow: inset 0 0 15px rgba(99, 102, 241, 0.05);
        }

        .planner-day-cell:hover {
            transform: translateY(-2px);
            border-color: var(--accent);
            box-shadow: 0 8px 24px rgba(99, 102, 241, 0.1);
        }

        /* Date text inside cells */
        .planner-day-num {
            font-weight: 800;
            font-size: 0.85rem;
            color: var(--text-muted);
            width: 22px;
            height: 22px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.2s;
        }

        .planner-day-num.active {
            background: var(--accent);
            color: white !important;
            box-shadow: 0 4px 10px var(--accent-glow);
        }

        /* Quick plan button inside cell (invisible by default, visible on cell hover) */
        .btn-quick-plan {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 18px;
            height: 18px;
            border-radius: 6px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--glass-border);
            color: var(--text-muted);
            opacity: 0;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            cursor: pointer;
        }

        .planner-day-cell:hover .btn-quick-plan {
            opacity: 1;
        }

        .btn-quick-plan:hover {
            background: var(--accent) !important;
            color: white !important;
            border-color: var(--accent) !important;
            transform: scale(1.1);
        }

        /* Scrollable container for bubbles inside day cell */
        .planner-posts-container {
            display: flex;
            flex-direction: column;
            gap: 4px;
            overflow-y: auto;
            max-height: 80px;
            padding-right: 2px;
        }

        /* Customized scrollbars for scrollable planners */
        .planner-posts-container::-webkit-scrollbar {
            width: 2px;
        }
        .planner-posts-container::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.06);
            border-radius: 2px;
        }

        /* Bubbles inside calendar cells representing posts */
        .planner-post-bubble {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 4px 6px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 0.7rem;
            color: var(--text-main);
            transition: all 0.2s;
            border: 1px solid rgba(255, 255, 255, 0.03);
            background: transparent;
            cursor: pointer;
            width: 100%;
            text-align: left;
        }

        .platform-badge {
            width: 14px;
            height: 14px;
            border-radius: 4px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            flex-shrink: 0;
        }
        .platform-badge.fb {
            background: #1877f2;
        }
        .platform-badge.ig {
            background: linear-gradient(45deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888);
        }
        .platform-badge svg {
            width: 8px;
            height: 8px;
        }

        .bubble-text {
            font-weight: 700;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 55px;
        }

        .fb-bubble:hover {
            box-shadow: 0 4px 10px rgba(24, 119, 242, 0.15);
        }

        .ig-bubble:hover {
            box-shadow: 0 4px 10px rgba(225, 48, 108, 0.15);
        }

        .planner-post-bubble.success {
            background: rgba(16, 185, 129, 0.06);
            color: #10b981;
            border-color: rgba(16, 185, 129, 0.1);
        }
        .planner-post-bubble.success:hover {
            background: rgba(16, 185, 129, 0.15);
            border-color: rgba(16, 185, 129, 0.3);
        }

        .planner-post-bubble.pending {
            background: rgba(245, 158, 11, 0.06);
            color: #f59e0b;
        }
        .planner-post-bubble.pending:hover {
            background: rgba(245, 158, 11, 0.15);
            border-color: rgba(245, 158, 11, 0.3);
        }

        .planner-post-bubble.failed {
            background: rgba(239, 68, 68, 0.06);
            color: #ef4444;
        }
        .planner-post-bubble.failed:hover {
            background: rgba(239, 68, 68, 0.15);
            border-color: rgba(239, 68, 68, 0.3);
        }

        /* Status legend badges */
        .legend-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.75rem;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 8px;
            border: 1px solid rgba(255,255,255,0.03);
        }
        
        .legend-badge .dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            display: inline-block;
        }

        .legend-badge.success { background: rgba(16, 185, 129, 0.06); color: #10b981; }
        .legend-badge.success .dot { background: #10b981; }

        .legend-badge.pending { background: rgba(245, 158, 11, 0.06); color: #f59e0b; }
        .legend-badge.pending .dot { background: #f59e0b; }

        .legend-badge.failed { background: rgba(239, 68, 68, 0.06); color: #ef4444; }
        .legend-badge.failed .dot { background: #ef4444; }

        /* MODALS OVERLAYS STYLES */
        .planner-modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(8px);
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            pointer-events: none;
            transition: all 0.3s ease-out;
        }

        .planner-modal-overlay.active {
            opacity: 1;
            pointer-events: all;
        }

        .planner-modal-content {
            background: rgba(26, 26, 46, 0.95);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            padding: 2rem;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
            transform: translateY(30px);
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .planner-modal-content.detail-preview {
            max-width: 550px;
        }

        .planner-modal-overlay.active .planner-modal-content {
            transform: translateY(0);
        }

        .btn-close-modal {
            background: transparent;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            transition: color 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-close-modal:hover {
            color: var(--text-main);
        }

        /* Form elements inside modal */
        .modal-form-label {
            display: block;
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-muted);
            margin-bottom: 0.5rem;
        }

        .modal-form-select,
        .modal-form-textarea,
        .modal-form-file {
            width: 100%;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            color: var(--text-main);
            padding: 10px 12px;
            font-weight: 600;
            font-size: 0.9rem;
            outline: none;
            transition: all 0.2s;
        }

        .modal-form-select:focus,
        .modal-form-textarea:focus {
            border-color: var(--accent);
            background: rgba(255, 255, 255, 0.05);
        }

        .modal-form-textarea {
            height: 120px;
            resize: none;
            line-height: 1.5;
        }

        .btn-modal-emoji {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid var(--glass-border);
            border-radius: 8px;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .btn-modal-emoji:hover {
            transform: scale(1.15);
            background: rgba(255, 255, 255, 0.08);
        }

        .btn-modal-action {
            padding: 10px 20px;
            border-radius: 12px;
            font-weight: 800;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
        }

        .btn-modal-action.cancel {
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-muted);
        }

        .btn-modal-action.cancel:hover {
            background: rgba(255, 255, 255, 0.08);
            color: var(--text-main);
        }

        .btn-modal-action.submit {
            background: var(--accent);
            color: white;
            box-shadow: 0 4px 12px var(--accent-glow);
        }

        .btn-modal-action.submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 16px var(--accent-glow);
        }

        /* Preview Stat cards styles */
        .preview-stat-card {
            padding: 10px 14px;
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            text-align: center;
        }
    </style>

    <!-- Modals Script Logics -->
    <script>
        // Opening / Closing Quick Post Planner Modal
        function openQuickPlanner(dayNum, monthYearString) {
            const overlay = document.getElementById('quickPlannerModal');
            const dateLabel = document.getElementById('plannerModalDateLabel');
            
            // Format target date nicely
            dateLabel.innerText = "Schedule post for: " + monthYearString + " " + dayNum + ", 2026";
            
            overlay.classList.add('active');
        }

        function closeQuickPlanner() {
            document.getElementById('quickPlannerModal').classList.remove('active');
        }

        // Insert emoji inside modal text area
        function modalInsertEmoji(emoji) {
            const textarea = document.querySelector('.modal-form-textarea');
            textarea.value += emoji;
            textarea.focus();
        }

        // Opening / Closing Post Preview detail modal
        function openPostPreview(post) {
            const overlay = document.getElementById('postPreviewModal');
            
            // Set fields values
            const pageBadge = document.getElementById('previewPageBadge');
            if (post.platform === 'Instagram') {
                pageBadge.innerText = 'Instagram: @' + (post.instagram_username || post.page_name);
                pageBadge.style.background = 'linear-gradient(45deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888)';
            } else {
                pageBadge.innerText = 'Facebook: ' + post.page_name;
                pageBadge.style.background = '#1877f2';
            }
            
            // Escape HTML and highlight hashtags/mentions beautifully
            let escapedMsg = post.message
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;")
                .replace(/\n/g, '<br>')
                .replace(/#(\w+)/g, '<span style="color: var(--accent); font-weight: 700; background: rgba(59, 130, 246, 0.1); padding: 2px 6px; border-radius: 6px;">#$1</span>')
                .replace(/@(\w+)/g, '<span style="color: #10b981; font-weight: 700; background: rgba(16, 185, 129, 0.1); padding: 2px 6px; border-radius: 6px;">@$1</span>');
                
            document.getElementById('previewMessageText').innerHTML = escapedMsg;
            document.getElementById('previewLikesCount').innerText = Number(post.likes).toLocaleString();
            document.getElementById('previewCommentsCount').innerText = Number(post.comments).toLocaleString();
            document.getElementById('previewDateLabel').innerText = "Created on: " + post.date;
            
            // Status styling
            const statusBadge = document.getElementById('previewStatusBadge');
            statusBadge.innerText = post.status;
            
            if (post.status.toLowerCase() === 'success') {
                statusBadge.style.color = '#10b981';
            } else if (post.status.toLowerCase() === 'pending') {
                statusBadge.style.color = '#f59e0b';
            } else {
                statusBadge.style.color = '#ef4444';
            }

            // Error display logic
            const errorBlock = document.getElementById('previewErrorBlock');
            if (post.error) {
                errorBlock.style.display = 'block';
                document.getElementById('previewErrorText').innerText = post.error;
            } else {
                errorBlock.style.display = 'none';
            }

            // Media Rendering logic
            const mediaBox = document.getElementById('previewMediaBox');
            mediaBox.innerHTML = ''; // Clear previous contents
            
            if (post.media_url) {
                mediaBox.style.display = 'block';
                if (post.media_type === 'image') {
                    mediaBox.innerHTML = `<img src="${post.media_url}" style="width: 100%; max-height: 250px; object-fit: contain; display: block;" />`;
                } else if (post.media_type === 'video') {
                    mediaBox.innerHTML = `<video controls style="width: 100%; max-height: 250px; display: block;"><source src="${post.media_url}" type="video/mp4"></video>`;
                }
            } else {
                mediaBox.style.display = 'none';
            }

            // Full Performance Redirect Link
            document.getElementById('previewFullBtn').href = "/posts/" + post.id;

            overlay.classList.add('active');
        }

        function closePostPreview() {
            document.getElementById('postPreviewModal').classList.remove('active');
        }

        // Close on overlay backing click
        window.addEventListener('click', function(e) {
            const overlay1 = document.getElementById('quickPlannerModal');
            const overlay2 = document.getElementById('postPreviewModal');
            if (e.target === overlay1) {
                closeQuickPlanner();
            }
            if (e.target === overlay2) {
                closePostPreview();
            }
        });
    </script>
@endsection
