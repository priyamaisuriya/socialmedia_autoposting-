@extends('layouts.premium')

@section('content')
    <!-- CSS styles package for the premium Social Inbox -->
    <style>
        :root {
            --inbox-sidebar-width: 360px;
        }

        .social-inbox-container {
            display: grid;
            grid-template-columns: var(--inbox-sidebar-width) 1fr;
            gap: 2rem;
            align-items: start;
            margin-top: 1rem;
        }

        @media (max-width: 1024px) {
            .social-inbox-container {
                grid-template-columns: 1fr;
            }
        }

        /* Glassmorphic Inbox Cards */
        .glass-inbox-card {
            background: var(--card-bg);
            border-radius: 20px;
            border: 1px solid var(--glass-border);
            padding: 1.5rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: visible;
        }

        .glass-inbox-card:hover {
            border-color: var(--accent);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.05);
        }

        /* Active Post Card Left Panel */
        .post-context-sidebar {
            position: sticky;
            top: 110px;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        /* Dropdown selector stylings */
        .elegant-select {
            width: 100%;
            padding: 0.85rem 1.25rem;
            background: var(--bg-main);
            border: 1px solid var(--glass-border);
            border-radius: 14px;
            color: var(--text-main);
            font-weight: 700;
            font-size: 0.875rem;
            cursor: pointer;
            outline: none;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%238892b0' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1.25rem center;
            background-size: 1rem;
            padding-right: 3rem;
            transition: all 0.2s;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);
        }

        .elegant-select:hover {
            border-color: var(--accent);
            background-color: var(--nav-active);
        }

        /* Scrollbar styles for post list */
        .scrollable-posts-list {
            max-height: 400px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            padding-right: 0.25rem;
        }

        .scrollable-posts-list::-webkit-scrollbar {
            width: 6px;
        }

        .scrollable-posts-list::-webkit-scrollbar-track {
            background: transparent;
        }

        .scrollable-posts-list::-webkit-scrollbar-thumb {
            background: var(--glass-border);
            border-radius: 10px;
        }

        /* Individual Post item in sidebar */
        .sidebar-post-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            border-radius: 12px;
            text-decoration: none;
            color: var(--text-main);
            font-size: 0.875rem;
            transition: all 0.2s ease;
            border: 1px solid transparent;
            background: transparent;
        }

        .sidebar-post-item:hover {
            background: var(--nav-active);
            transform: translateX(3px);
        }

        .sidebar-post-item.active {
            background: var(--accent-glow);
            border-color: var(--accent);
            color: var(--text-main);
        }

        /* Root comment structure */
        .comment-thread-wrapper {
            margin-bottom: 2rem;
            animation: fadeInUp 0.4s ease-out;
        }

        .root-comment-container {
            display: flex;
            gap: 1rem;
            align-items: start;
        }

        /* Colorful Avatar Bubble */
        .avatar-circle {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 1rem;
            color: white;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
            border: 2px solid var(--card-bg);
            flex-shrink: 0;
            user-select: none;
        }

        .comment-bubble-box {
            flex: 1;
            background: var(--bg-main);
            border: 1px solid var(--glass-border);
            border-radius: 18px;
            padding: 1.25rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.01);
            position: relative;
            transition: 0.2s;
        }

        .comment-bubble-box:hover {
            border-color: rgba(59, 130, 246, 0.2);
            box-shadow: 0 4px 12px rgba(0,0,0,0.02);
        }

        .comment-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
        }

        .commenter-name {
            font-size: 0.9375rem;
            font-weight: 800;
            color: var(--text-main);
        }

        .comment-timestamp {
            font-size: 0.75rem;
            color: var(--text-muted);
            font-weight: 600;
        }

        .comment-text {
            font-size: 0.9375rem;
            color: var(--text-main);
            line-height: 1.6;
            margin: 0;
        }

        /* Threaded replies section */
        .replies-timeline-branch {
            margin-left: 22px;
            border-left: 2px dashed var(--glass-border);
            padding-left: 2.25rem;
            margin-top: 0.75rem;
            margin-bottom: 0.75rem;
            display: flex;
            flex-direction: column;
            gap: 1rem;
            position: relative;
        }

        .reply-node {
            display: flex;
            gap: 0.75rem;
            align-items: start;
            position: relative;
        }

        /* Connecting horizontal guide line */
        .reply-node::before {
            content: "";
            position: absolute;
            left: calc(-2.25rem - 2px);
            top: 20px;
            width: 2.25rem;
            height: 2px;
            border-top: 2px dashed var(--glass-border);
        }

        .reply-bubble-box {
            flex: 1;
            background: var(--card-bg);
            border: 1px solid var(--glass-border);
            border-radius: 16px;
            padding: 1rem 1.25rem;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.01);
            position: relative;
        }

        /* Page admin official reply */
        .reply-node.is-official .reply-bubble-box {
            background: var(--nav-active);
            border-color: rgba(59, 130, 246, 0.25);
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.03);
        }

        .reply-node.is-official .avatar-circle {
            border-color: var(--accent);
            box-shadow: 0 0 10px rgba(59, 130, 246, 0.2);
        }

        /* Official verification badge */
        .official-reply-badge {
            background: var(--accent-glow);
            color: var(--accent);
            font-size: 0.65rem;
            font-weight: 800;
            padding: 2px 8px;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            border: 1px solid rgba(59, 130, 246, 0.2);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        /* Chat reply forms */
        .reply-composer-container {
            margin-left: 22px;
            padding-left: 2.25rem;
            position: relative;
            margin-top: 1rem;
        }

        .reply-composer-container::before {
            content: "";
            position: absolute;
            left: calc(-2.25rem - 2px);
            top: 22px;
            width: 2.25rem;
            height: 2px;
            border-top: 2px dashed var(--glass-border);
        }

        .reply-composer-form {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            background: var(--bg-main);
            border: 1px solid var(--glass-border);
            border-radius: 16px;
            padding: 0.5rem 0.75rem;
            transition: all 0.3s ease;
            position: relative;
            z-index: 10;
        }

        .reply-composer-form:focus-within {
            border-color: var(--accent);
            box-shadow: 0 0 0 4px var(--accent-glow);
            background: var(--card-bg);
        }

        .reply-composer-input {
            flex: 1;
            background: transparent;
            border: none;
            outline: none;
            padding: 0.5rem 0.75rem;
            color: var(--text-main);
            font-size: 0.875rem;
            font-family: inherit;
            position: relative;
            z-index: 11;
            cursor: text;
        }

        .reply-submit-btn {
            background: var(--accent);
            color: white;
            border: none;
            padding: 0.6rem 1.25rem;
            border-radius: 12px;
            font-weight: 700;
            font-size: 0.8125rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 4px 12px var(--accent-glow);
            transition: all 0.2s ease;
            position: relative;
            z-index: 11;
        }

        .reply-submit-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 15px var(--accent-glow);
            filter: brightness(1.05);
        }

        .reply-submit-btn:active {
            transform: translateY(0);
        }

        /* Post quick context card on right pane */
        .post-context-banner {
            background: var(--accent-glow);
            border: 1px solid rgba(59, 130, 246, 0.15);
            border-radius: 20px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        /* Emoji bubble helper */
        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 0.75rem;
            color: var(--text-muted);
            background: var(--bg-main);
            padding: 4px 10px;
            border-radius: 8px;
            border: 1px solid var(--glass-border);
            font-weight: 600;
        }

        /* Platform Comments Tabs Styles */
        .platform-tab-btn {
            background: transparent;
            color: var(--text-muted);
            border: 1px solid transparent;
        }
        
        .platform-tab-btn:hover {
            color: var(--text-main);
            background: var(--nav-active);
        }

        .platform-tab-btn.active-facebook {
            background: rgba(24, 119, 242, 0.08) !important;
            color: #1877f2 !important;
            border: 1px solid rgba(24, 119, 242, 0.25) !important;
            box-shadow: 0 4px 15px rgba(24, 119, 242, 0.05);
        }

        .platform-tab-btn.active-instagram {
            background: rgba(225, 48, 108, 0.08) !important;
            color: #e1306c !important;
            border: 1px solid rgba(225, 48, 108, 0.25) !important;
            box-shadow: 0 4px 15px rgba(225, 48, 108, 0.05);
        }
    </style>

    <!-- Header Panel -->
    <div style="background: var(--sidebar-bg); border-radius: 24px; border: 1px solid var(--glass-border); padding: 1.5rem 2rem; margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; gap: 2rem; flex-wrap: wrap; box-shadow: 0 10px 30px rgba(0,0,0,0.02); position: relative; z-index: 1000;">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <div class="section-icon">
                <i data-lucide="message-square"></i>
            </div>
            <div>
                <h2 style="margin: 0; font-size: 1.5rem; font-weight: 800; letter-spacing: -0.02em;">Social Comment Center</h2>
                <p style="color: var(--text-muted); font-size: 0.8125rem; margin-top: 2px;">Track feedback, view threads, and reply to posts on Facebook and Instagram instantly.</p>
            </div>
        </div>

        <div style="display: flex; align-items: center; gap: 1rem;">
            <!-- Back to Posts Hub Button -->
            <a href="{{ route('posts.index') }}" style="display: flex; align-items: center; gap: 8px; font-weight: 800; font-size: 0.875rem; color: var(--text-main); text-decoration: none; padding: 0.65rem 1.25rem; border-radius: 12px; background: var(--bg-main); border: 1px solid var(--glass-border); transition: all 0.2s; box-shadow: 0 4px 10px rgba(0,0,0,0.02);" onmouseover="this.style.background='var(--nav-active)'; this.style.borderColor='var(--accent)';" onmouseout="this.style.background='var(--bg-main)'; this.style.borderColor='var(--glass-border)';">
                <i data-lucide="arrow-left" style="width: 16px; height: 16px;"></i> Back to My Posts
            </a>

            @if($post)
                <a href="{{ route('comments.sync', $post->id) }}" class="btn-primary" style="padding: 0.65rem 1.25rem; border-radius: 12px; font-size: 0.875rem; display: flex; align-items: center; gap: 6px;">
                    <i data-lucide="refresh-cw" style="width: 15px; height: 15px;"></i> Synchronize Comments
                </a>
            @endif
        </div>
    </div>

    <!-- Master-Detail Social Inbox Layout -->
    <div class="social-inbox-container">
        
        <!-- Left Panel: Sidebar Post Filter & Selected Post Meta -->
        <div class="post-context-sidebar">
            
            <!-- Post selector card -->
            <div class="glass-inbox-card">
                <h3 style="font-size: 0.95rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; color: var(--accent); margin-bottom: 1.25rem; display: flex; align-items: center; gap: 8px;">
                    <i data-lucide="filter" style="width: 16px; height: 16px;"></i> Active Post Selector
                </h3>
                
                <!-- Custom Glassmorphic Select Dropdown with Images -->
                <div style="position: relative;" id="custom-dropdown-container">
                    <button onclick="toggleDropdown(event)" class="elegant-select" style="display: flex; align-items: center; justify-content: space-between; text-align: left; padding-right: 2.5rem;">
                        <span style="display: flex; align-items: center; gap: 8px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" id="selected-post-label">
                            @if($post)
                                @php $firstMedia = $post->media->first(); @endphp
                                @if($firstMedia && $firstMedia->media_type === 'image')
                                    <img src="{{ asset('storage/' . $firstMedia->file_path) }}" style="width: 22px; height: 22px; border-radius: 4px; object-fit: cover; border: 1px solid var(--glass-border);" />
                                @elseif($firstMedia && $firstMedia->media_type === 'video')
                                    <div style="width: 22px; height: 22px; border-radius: 4px; background: #000; display: flex; align-items: center; justify-content: center;">
                                        <i data-lucide="play" style="width: 8px; height: 8px; color: white; fill: white;"></i>
                                    </div>
                                @else
                                    <div style="width: 22px; height: 22px; border-radius: 4px; background: var(--accent-glow); display: flex; align-items: center; justify-content: center;">
                                        <i data-lucide="file-text" style="width: 10px; height: 10px; color: var(--accent);"></i>
                                    </div>
                                @endif
                                <span>{{ Str::limit($post->message, 24) }}</span>
                            @else
                                <i data-lucide="layers" style="width: 16px; height: 16px; color: var(--text-muted);"></i>
                                <span>Show All Comments</span>
                            @endif
                        </span>
                    </button>
                    
                    <!-- Dropdown options list with post images -->
                    <div id="dropdown-options" style="display: none; position: absolute; top: calc(100% + 6px); left: 0; right: 0; background: var(--sidebar-bg); border: 1px solid var(--glass-border); border-radius: 14px; box-shadow: 0 10px 25px rgba(0,0,0,0.15); padding: 0.5rem; flex-direction: column; gap: 4px; backdrop-filter: blur(15px); z-index: 9999;">
                        <a href="{{ route('comments.index') }}" style="display: flex; align-items: center; gap: 0.75rem; padding: 0.65rem 0.85rem; border-radius: 10px; text-decoration: none; color: var(--text-main); font-size: 0.8125rem; font-weight: 700; background: {{ !$post ? 'var(--nav-active)' : 'transparent' }}; transition: 0.2s;" onmouseover="this.style.background='var(--nav-active)'" onmouseout="this.style.background='{{ !$post ? 'var(--nav-active)' : 'transparent' }}'">
                            <div style="width: 24px; height: 24px; border-radius: 4px; background: var(--accent-glow); display: flex; align-items: center; justify-content: center;">
                                <i data-lucide="layers" style="width: 12px; height: 12px; color: var(--accent);"></i>
                            </div>
                            <span>Show All Comments</span>
                        </a>
                        
                        <div style="border-top: 1px solid var(--glass-border); margin: 4px 0;"></div>
                        
                        @foreach($allPosts as $p)
                            @php
                                $pMedia = $p->media->first();
                                $isActive = $post && $post->id == $p->id;
                            @endphp
                            <a href="{{ route('comments.index') }}?post_id={{ $p->id }}" style="display: flex; align-items: center; gap: 0.75rem; padding: 0.65rem 0.85rem; border-radius: 10px; text-decoration: none; color: var(--text-main); font-size: 0.8125rem; background: {{ $isActive ? 'var(--nav-active)' : 'transparent' }}; transition: 0.2s;" onmouseover="this.style.background='var(--nav-active)'" onmouseout="this.style.background='{{ $isActive ? 'var(--nav-active)' : 'transparent' }}'">
                                @if($pMedia && $pMedia->media_type === 'image')
                                    <img src="{{ asset('storage/' . $pMedia->file_path) }}" style="width: 24px; height: 24px; border-radius: 4px; object-fit: cover; border: 1px solid var(--glass-border);" />
                                @elseif($pMedia && $pMedia->media_type === 'video')
                                    <div style="width: 24px; height: 24px; border-radius: 4px; background: #000; display: flex; align-items: center; justify-content: center;">
                                        <i data-lucide="play" style="width: 10px; height: 10px; color: white; fill: white;"></i>
                                    </div>
                                @else
                                    <div style="width: 24px; height: 24px; border-radius: 4px; background: var(--nav-active); display: flex; align-items: center; justify-content: center;">
                                        <i data-lucide="file-text" style="width: 12px; height: 12px; color: var(--text-muted);"></i>
                                    </div>
                                @endif
                                <div style="display: flex; flex-direction: column; overflow: hidden; flex: 1;">
                                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 6px;">
                                        <span style="font-weight: 800; font-size: 0.75rem; color: var(--accent); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $p->facebookPage->name }}</span>
                                        <div style="display: flex; gap: 4px; align-items: center;">
                                            @if($p->facebook_post_id)
                                                <span style="width: 6px; height: 6px; border-radius: 50%; background: #1877f2; display: inline-block;" title="Facebook"></span>
                                            @endif
                                            @if($p->instagram_post_id)
                                                <span style="width: 6px; height: 6px; border-radius: 50%; background: #e1306c; display: inline-block;" title="Instagram"></span>
                                            @endif
                                        </div>
                                    </div>
                                    <span style="font-size: 0.7rem; color: var(--text-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $p->message }}</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Detailed Selected Post Info Card -->
            @if($post)
                <div class="glass-inbox-card">
                    <h3 style="font-size: 0.95rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; color: var(--accent); margin-bottom: 1rem; display: flex; align-items: center; gap: 8px;">
                        <i data-lucide="layers" style="width: 16px; height: 16px;"></i> Post Context
                    </h3>
                    
                    <div style="display: flex; flex-direction: column; gap: 0.75rem; margin-bottom: 1rem;">
                        @if($post->facebook_post_id)
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <div style="width: 28px; height: 28px; border-radius: 6px; background: #1877f2; color: white; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.7rem;">F</div>
                                <div>
                                    <div style="font-weight: 800; font-size: 0.8125rem;">{{ $post->facebookPage->name }}</div>
                                    <div style="font-size: 0.6rem; color: var(--text-muted); font-weight: 600;">Facebook Page</div>
                                </div>
                            </div>
                        @endif
                        @if($post->instagram_post_id)
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <div style="width: 28px; height: 28px; border-radius: 6px; background: linear-gradient(45deg, #f09433, #dc2743, #bc1888); color: white; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.7rem;">
                                    <i data-lucide="instagram" style="width: 12px; height: 12px; color: white;"></i>
                                </div>
                                <div>
                                    <div style="font-weight: 800; font-size: 0.8125rem;">@{{ $post->facebookPage->instagram_username ?? 'Instagram Account' }}</div>
                                    <div style="font-size: 0.6rem; color: var(--text-muted); font-weight: 600;">Instagram Profile</div>
                                </div>
                            </div>
                        @endif
                    </div>
                    
                    <p style="font-size: 0.9375rem; line-height: 1.5; color: var(--text-main); font-weight: 500; font-style: italic; margin-bottom: 1.25rem; background: var(--bg-main); padding: 0.75rem 1rem; border-radius: 12px; border: 1px solid var(--glass-border);">
                        "{{ $post->message }}"
                    </p>

                    @php
                        $hasImage = $post->media->where('media_type', 'image')->isNotEmpty();
                        $hasVideo = $post->media->where('media_type', 'video')->isNotEmpty();
                    @endphp

                    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; margin-bottom: 1.25rem;">
                        @if($hasImage)
                            <span class="status-pill"><i data-lucide="image" style="width: 12px; height: 12px; color: var(--accent);"></i> Photo</span>
                        @elseif($hasVideo)
                            <span class="status-pill"><i data-lucide="video" style="width: 12px; height: 12px; color: var(--accent);"></i> Video</span>
                        @else
                            <span class="status-pill"><i data-lucide="align-left" style="width: 12px; height: 12px; color: var(--accent);"></i> Status</span>
                        @endif
                        
                        <span class="status-pill"><i data-lucide="message-square" style="width: 12px; height: 12px; color: var(--accent);"></i> {{ $comments->count() }} Comments</span>
                    </div>

                    @if($post->media->isNotEmpty())
                        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                            @foreach($post->media as $media)
                                @if($media->media_type === 'image')
                                    <img src="{{ asset('storage/' . $media->file_path) }}" style="width: 100%; max-height: 200px; border-radius: 12px; object-fit: cover; border: 1px solid var(--glass-border);" />
                                @elseif($media->media_type === 'video')
                                    <video src="{{ asset('storage/' . $media->file_path) }}" controls style="width: 100%; max-height: 200px; border-radius: 12px; border: 1px solid var(--glass-border);"></video>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>
            @else
                <!-- Show lists of recent successful posts with quick filter links -->
                <div class="glass-inbox-card">
                    <h3 style="font-size: 0.95rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; color: var(--accent); margin-bottom: 1rem;">
                        Successful Posts List
                    </h3>
                    <p style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 1rem;">Click on any post below to filter comments specifically to that post.</p>
                    
                    <div class="scrollable-posts-list">
                        @forelse($allPosts as $p)
                            @php $pMedia = $p->media->first(); @endphp
                            <a href="{{ route('comments.index', ['post_id' => $p->id]) }}" class="sidebar-post-item">
                                @if($pMedia && $pMedia->media_type === 'image')
                                    <img src="{{ asset('storage/' . $pMedia->file_path) }}" style="width: 28px; height: 28px; border-radius: 6px; object-fit: cover; border: 1px solid var(--glass-border);" />
                                @elseif($pMedia && $pMedia->media_type === 'video')
                                    <div style="width: 28px; height: 28px; border-radius: 6px; background: #000; display: flex; align-items: center; justify-content: center;">
                                        <i data-lucide="play" style="width: 10px; height: 10px; color: white; fill: white;"></i>
                                    </div>
                                @else
                                    <div style="width: 28px; height: 28px; border-radius: 6px; background: var(--nav-active); display: flex; align-items: center; justify-content: center; border: 1px solid var(--glass-border);">
                                        <i data-lucide="file-text" style="width: 12px; height: 12px; color: var(--text-muted);"></i>
                                    </div>
                                @endif
                                <div style="display: flex; flex-direction: column; overflow: hidden; flex: 1;">
                                    <span style="font-weight: 800; font-size: 0.75rem; color: var(--accent);">{{ $p->facebookPage->name }}</span>
                                    <span style="font-size: 0.7rem; color: var(--text-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $p->message }}</span>
                                </div>
                            </a>
                        @empty
                            <p style="color: var(--text-muted); font-size: 0.8rem; text-align: center; padding: 1rem 0;">No active posts found.</p>
                        @endforelse
                    </div>
                </div>
            @endif
        </div>
        
        <!-- Right Panel: Conversation Feeds & Timeline -->
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            
            <!-- Alert/Context banner about what is loaded -->
            @if(!$post)
                <div class="post-context-banner">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="display: flex; align-items: center; gap: 8px; font-weight: 700; font-size: 0.9375rem; color: var(--accent);">
                            <i data-lucide="layers" style="width: 18px; height: 18px;"></i> Showing All Recent Comments
                        </span>
                        <span style="font-size: 0.75rem; color: var(--accent); background: rgba(59, 130, 246, 0.1); padding: 4px 10px; border-radius: 10px; font-weight: 800;">
                            Global Inbox
                        </span>
                    </div>
                    <p style="margin: 0; font-size: 0.875rem; color: var(--text-main); line-height: 1.4;">
                        You are currently viewing a unified feed of all comments across all of your published Facebook posts. To respond, sync new replies, or isolate threads, select a specific post from the sidebar filter on the left.
                    </p>
                </div>
            @endif

            @php
                $fbComments = $comments->where('platform', 'facebook');
                $igComments = $comments->where('platform', 'instagram');
            @endphp

            <!-- Platform Tab Switcher -->
            <div class="glass-inbox-card" style="padding: 0.5rem; margin-bottom: 1.5rem; display: flex; gap: 10px; border-radius: 16px; background: var(--sidebar-bg); border: 1px solid var(--glass-border);">
                <button type="button" onclick="switchPlatformTab('facebook')" id="tab-btn-facebook" class="platform-tab-btn active-facebook" style="flex: 1; display: flex; align-items: center; justify-content: center; gap: 10px; padding: 1rem; border-radius: 12px; font-weight: 800; font-size: 0.95rem; border: none; cursor: pointer; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); outline: none;">
                    <i data-lucide="facebook" style="width: 18px; height: 18px;"></i>
                    Facebook Comments
                    <span class="tab-count-badge" style="background: #1877f2; color: white; padding: 2px 8px; border-radius: 20px; font-size: 0.75rem; font-weight: 800;">{{ $fbComments->count() }}</span>
                </button>
                <button type="button" onclick="switchPlatformTab('instagram')" id="tab-btn-instagram" class="platform-tab-btn" style="flex: 1; display: flex; align-items: center; justify-content: center; gap: 10px; padding: 1rem; border-radius: 12px; font-weight: 800; font-size: 0.95rem; border: none; cursor: pointer; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); outline: none;">
                    <i data-lucide="instagram" style="width: 18px; height: 18px;"></i>
                    Instagram Comments
                    <span class="tab-count-badge" style="background: linear-gradient(45deg, #f09433, #dc2743, #bc1888); color: white; padding: 2px 8px; border-radius: 20px; font-size: 0.75rem; font-weight: 800;">{{ $igComments->count() }}</span>
                </button>
            </div>

            <!-- Comments Timeline Stream -->
            <div>
                <!-- Facebook Comments Section -->
                <div id="facebook-comments-feed" class="platform-comments-feed">
                    @forelse($fbComments as $comment)
                        @php
                            // Choose a colorful avatar gradient dynamically based on user's name
                            $char = strtoupper(substr($comment->user_name, 0, 1));
                            if (preg_match('/[A-E]/', $char)) {
                                $grad = 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)';
                            } elseif (preg_match('/[F-J]/', $char)) {
                                $grad = 'linear-gradient(135deg, #5ee7df 0%, #b490ca 100%)';
                            } elseif (preg_match('/[K-O]/', $char)) {
                                $grad = 'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)';
                            } elseif (preg_match('/[P-T]/', $char)) {
                                $grad = 'linear-gradient(135deg, #fa709a 0%, #fee140 100%)';
                            } else {
                                $grad = 'linear-gradient(135deg, #2575fc 0%, #6a11cb 100%)';
                            }
                        @endphp

                        <div class="comment-thread-wrapper glass-inbox-card">
                            
                            <!-- Root comment card block -->
                            <div class="root-comment-container">
                                <div class="avatar-circle" style="background: {{ $grad }};">
                                    {{ substr($comment->user_name, 0, 1) }}
                                </div>
                                
                                <div class="comment-bubble-box">
                                    <div class="comment-header">
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <span class="commenter-name">{{ $comment->user_name }}</span>
                                            <span style="background: rgba(24, 119, 242, 0.1); color: #1877f2; font-size: 0.65rem; font-weight: 800; padding: 2px 8px; border-radius: 6px; display: inline-flex; align-items: center; gap: 4px; border: 1px solid rgba(24, 119, 242, 0.2);">
                                                <i data-lucide="facebook" style="width: 10px; height: 10px;"></i> FB
                                            </span>
                                        </div>
                                        <div style="display: flex; align-items: center; gap: 12px;">
                                            <span class="comment-timestamp">{{ $comment->created_at->diffForHumans() }}</span>
                                            <form action="{{ route('comments.destroy', $comment->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this comment? This will delete it on Facebook as well!')" style="display: inline-block; margin: 0; padding: 0; line-height: 1;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" style="background: none; border: none; padding: 4px; cursor: pointer; color: var(--text-muted); transition: 0.2s;" onmouseover="this.style.color='#ef4444'" onmouseout="this.style.color='var(--text-muted)'" title="Delete Comment">
                                                    <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    <p class="comment-text">{{ $comment->message }}</p>
                                    
                                    <!-- In View All Mode, display context box showing which post this comment belongs to -->
                                    @if(!$post)
                                        <div style="margin-top: 1rem; padding: 0.75rem 1rem; background: var(--nav-active); border-left: 3px solid var(--accent); border-radius: 10px; display: flex; flex-direction: column; gap: 4px;">
                                            <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.7rem; color: var(--text-muted); font-weight: 700;">
                                                <span>POST CONTEXT</span>
                                                <span style="color: var(--accent); font-weight: 800;">{{ $comment->post->facebookPage->name }}</span>
                                            </div>
                                            <div style="font-size: 0.8rem; color: var(--text-main); font-style: italic; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                "{{ $comment->post->message }}"
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Nested threaded replies tree -->
                            @if($comment->replies->isNotEmpty())
                                <div class="replies-timeline-branch">
                                    @foreach($comment->replies->sortBy('created_at') as $reply)
                                        @php
                                            $isOfficial = ($reply->user_name === $comment->post->facebookPage->name);
                                            
                                            // Pick gradient for reply author avatar
                                            $charR = strtoupper(substr($reply->user_name, 0, 1));
                                            if ($isOfficial) {
                                                $gradR = 'linear-gradient(135deg, #2563eb, #3b82f6)';
                                            } elseif (preg_match('/[A-E]/', $charR)) {
                                                $gradR = 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)';
                                            } else {
                                                $gradR = 'linear-gradient(135deg, #2575fc 0%, #6a11cb 100%)';
                                            }
                                        @endphp
                                        
                                        <div class="reply-node {{ $isOfficial ? 'is-official' : '' }}">
                                            <div class="avatar-circle" style="background: {{ $gradR }}; width: 34px; height: 34px; font-size: 0.8rem;">
                                                {{ substr($reply->user_name, 0, 1) }}
                                            </div>
                                            
                                            <div class="reply-bubble-box">
                                                <div class="comment-header" style="margin-bottom: 0.25rem;">
                                                    <div style="display: flex; align-items: center; gap: 8px;">
                                                        <span class="commenter-name" style="font-size: 0.875rem;">{{ $reply->user_name }}</span>
                                                        @if($isOfficial)
                                                            <span class="official-reply-badge">
                                                                <i data-lucide="check-check" style="width: 10px; height: 10px;"></i> PAGE AUTHOR
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <div style="display: flex; align-items: center; gap: 12px;">
                                                        <span class="comment-timestamp" style="font-size: 0.7rem;">{{ $reply->created_at->diffForHumans() }}</span>
                                                        <form action="{{ route('comments.destroy', $reply->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this reply? This will delete it on Facebook as well!')" style="display: inline-block; margin: 0; padding: 0; line-height: 1;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" style="background: none; border: none; padding: 4px; cursor: pointer; color: var(--text-muted); transition: 0.2s;" onmouseover="this.style.color='#ef4444'" onmouseout="this.style.color='var(--text-muted)'" title="Delete Reply">
                                                                <i data-lucide="trash-2" style="width: 12px; height: 12px;"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                                <p class="comment-text" style="font-size: 0.875rem;">{{ $reply->message }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <!-- Inline Glass Reply Composer Form -->
                            <div class="reply-composer-container">
                                <form action="{{ route('comments.reply', $comment->id) }}" method="POST" class="reply-composer-form">
                                    @csrf
                                    <input type="text" name="message" id="reply-input-{{ $comment->id }}" placeholder="Write a reply to {{ $comment->user_name }}'s comment on Facebook..." required class="reply-composer-input" />
                                    <button type="button" class="reply-submit-btn ai-reply-btn" data-comment-id="{{ $comment->id }}" data-comment-text="{{ htmlspecialchars($comment->message) }}" style="background: rgba(99, 102, 241, 0.1); color: var(--accent); margin-right: 4px; border: 1px solid rgba(99, 102, 241, 0.2);" onclick="generateAiReply(this)">
                                        <i data-lucide="sparkles" style="width: 12px; height: 12px;"></i> AI Reply
                                    </button>
                                    <button type="submit" class="reply-submit-btn" style="background: var(--accent); box-shadow: 0 4px 12px var(--accent-glow);">
                                        <i data-lucide="send" style="width: 12px; height: 12px;"></i> Post Reply
                                    </button>
                                </form>
                            </div>

                        </div>
                    @empty
                        <div class="glass-inbox-card" style="text-align: center; padding: 4rem 2rem;">
                            <i data-lucide="facebook" style="width: 48px; height: 48px; color: var(--text-muted); margin-bottom: 1rem; opacity: 0.5;"></i>
                            <h2 style="font-weight: 800; font-size: 1.5rem; margin-bottom: 0.5rem;">No Facebook Comments</h2>
                            <p style="color: var(--text-muted); font-size: 0.9rem; max-width: 400px; margin: 0 auto;">No comments recorded on Facebook under this selection. Tap sync or select another post to refresh.</p>
                        </div>
                    @endforelse
                </div>

                <!-- Instagram Comments Section -->
                <div id="instagram-comments-feed" class="platform-comments-feed" style="display: none;">
                    @forelse($igComments as $comment)
                        @php
                            // Choose a colorful avatar gradient dynamically based on user's name
                            $char = strtoupper(substr($comment->user_name, 0, 1));
                            if (preg_match('/[A-E]/', $char)) {
                                $grad = 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)';
                            } elseif (preg_match('/[F-J]/', $char)) {
                                $grad = 'linear-gradient(135deg, #5ee7df 0%, #b490ca 100%)';
                            } elseif (preg_match('/[K-O]/', $char)) {
                                $grad = 'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)';
                            } elseif (preg_match('/[P-T]/', $char)) {
                                $grad = 'linear-gradient(135deg, #fa709a 0%, #fee140 100%)';
                            } else {
                                $grad = 'linear-gradient(135deg, #2575fc 0%, #6a11cb 100%)';
                            }
                        @endphp

                        <div class="comment-thread-wrapper glass-inbox-card">
                            
                            <!-- Root comment card block -->
                            <div class="root-comment-container">
                                <div class="avatar-circle" style="background: {{ $grad }};">
                                    {{ substr($comment->user_name, 0, 1) }}
                                </div>
                                
                                <div class="comment-bubble-box">
                                    <div class="comment-header">
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <span class="commenter-name">{{ $comment->user_name }}</span>
                                            <span style="background: rgba(225, 48, 108, 0.1); color: #e1306c; font-size: 0.65rem; font-weight: 800; padding: 2px 8px; border-radius: 6px; display: inline-flex; align-items: center; gap: 4px; border: 1px solid rgba(225, 48, 108, 0.2);">
                                                <i data-lucide="instagram" style="width: 10px; height: 10px;"></i> IG
                                            </span>
                                        </div>
                                        <div style="display: flex; align-items: center; gap: 12px;">
                                            <span class="comment-timestamp">{{ $comment->created_at->diffForHumans() }}</span>
                                            <form action="{{ route('comments.destroy', $comment->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this comment? This will delete it on Instagram as well!')" style="display: inline-block; margin: 0; padding: 0; line-height: 1;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" style="background: none; border: none; padding: 4px; cursor: pointer; color: var(--text-muted); transition: 0.2s;" onmouseover="this.style.color='#ef4444'" onmouseout="this.style.color='var(--text-muted)'" title="Delete Comment">
                                                    <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    <p class="comment-text">{{ $comment->message }}</p>
                                    
                                    <!-- In View All Mode, display context box showing which post this comment belongs to -->
                                    @if(!$post)
                                        <div style="margin-top: 1rem; padding: 0.75rem 1rem; background: var(--nav-active); border-left: 3px solid var(--accent); border-radius: 10px; display: flex; flex-direction: column; gap: 4px;">
                                            <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.7rem; color: var(--text-muted); font-weight: 700;">
                                                <span>POST CONTEXT</span>
                                                <span style="color: var(--accent); font-weight: 800;">{{ $comment->post->facebookPage->name }}</span>
                                            </div>
                                            <div style="font-size: 0.8rem; color: var(--text-main); font-style: italic; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                "{{ $comment->post->message }}"
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Nested threaded replies tree -->
                            @if($comment->replies->isNotEmpty())
                                <div class="replies-timeline-branch">
                                    @foreach($comment->replies->sortBy('created_at') as $reply)
                                        @php
                                            $isOfficial = ($reply->user_name === $comment->post->facebookPage->instagram_username || $reply->user_name === 'Instagram Admin');
                                            
                                            // Pick gradient for reply author avatar
                                            $charR = strtoupper(substr($reply->user_name, 0, 1));
                                            if ($isOfficial) {
                                                $gradR = 'linear-gradient(135deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888)';
                                            } elseif (preg_match('/[A-E]/', $charR)) {
                                                $gradR = 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)';
                                            } else {
                                                $gradR = 'linear-gradient(135deg, #2575fc 0%, #6a11cb 100%)';
                                            }
                                        @endphp
                                        
                                        <div class="reply-node {{ $isOfficial ? 'is-official' : '' }}">
                                            <div class="avatar-circle" style="background: {{ $gradR }}; width: 34px; height: 34px; font-size: 0.8rem;">
                                                {{ substr($reply->user_name, 0, 1) }}
                                            </div>
                                            
                                            <div class="reply-bubble-box">
                                                <div class="comment-header" style="margin-bottom: 0.25rem;">
                                                    <div style="display: flex; align-items: center; gap: 8px;">
                                                        <span class="commenter-name" style="font-size: 0.875rem;">{{ $reply->user_name }}</span>
                                                        @if($isOfficial)
                                                            <span class="official-reply-badge" style="background: rgba(225, 48, 108, 0.1); color: #e1306c; border-color: rgba(225, 48, 108, 0.2);">
                                                                <i data-lucide="check-check" style="width: 10px; height: 10px;"></i> IG ADMIN
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <div style="display: flex; align-items: center; gap: 12px;">
                                                        <span class="comment-timestamp" style="font-size: 0.7rem;">{{ $reply->created_at->diffForHumans() }}</span>
                                                        <form action="{{ route('comments.destroy', $reply->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this reply? This will delete it on Instagram as well!')" style="display: inline-block; margin: 0; padding: 0; line-height: 1;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" style="background: none; border: none; padding: 4px; cursor: pointer; color: var(--text-muted); transition: 0.2s;" onmouseover="this.style.color='#ef4444'" onmouseout="this.style.color='var(--text-muted)'" title="Delete Reply">
                                                                <i data-lucide="trash-2" style="width: 12px; height: 12px;"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                                <p class="comment-text" style="font-size: 0.875rem;">{{ $reply->message }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <!-- Inline Glass Reply Composer Form -->
                            <div class="reply-composer-container">
                                <form action="{{ route('comments.reply', $comment->id) }}" method="POST" class="reply-composer-form">
                                    @csrf
                                    <input type="text" name="message" id="reply-input-{{ $comment->id }}" placeholder="Write a reply to {{ $comment->user_name }}'s comment on Instagram..." required class="reply-composer-input" />
                                    <button type="button" class="reply-submit-btn ai-reply-btn" data-comment-id="{{ $comment->id }}" data-comment-text="{{ htmlspecialchars($comment->message) }}" style="background: rgba(225, 48, 108, 0.1); color: #e1306c; margin-right: 4px; border: 1px solid rgba(225, 48, 108, 0.2);" onclick="generateAiReply(this)">
                                        <i data-lucide="sparkles" style="width: 12px; height: 12px;"></i> AI Reply
                                    </button>
                                    <button type="submit" class="reply-submit-btn" style="background: linear-gradient(45deg, #f09433, #dc2743, #bc1888); box-shadow: 0 4px 12px rgba(225, 48, 108, 0.2);">
                                        <i data-lucide="send" style="width: 12px; height: 12px;"></i> Post Reply
                                    </button>
                                </form>
                            </div>

                        </div>
                    @empty
                        <div class="glass-inbox-card" style="text-align: center; padding: 4rem 2rem;">
                            <i data-lucide="instagram" style="width: 48px; height: 48px; color: var(--text-muted); margin-bottom: 1rem; opacity: 0.5;"></i>
                            <h2 style="font-weight: 800; font-size: 1.5rem; margin-bottom: 0.5rem;">No Instagram Comments</h2>
                            <p style="color: var(--text-muted); font-size: 0.9rem; max-width: 400px; margin: 0 auto;">No comments recorded on Instagram under this selection. Tap sync or select another post to refresh.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>

    <!-- Dropdown Script -->
    <script>
        function toggleDropdown(event) {
            event.stopPropagation();
            const dropdown = document.getElementById('dropdown-options');
            if (dropdown.style.display === 'none' || dropdown.style.display === '') {
                dropdown.style.display = 'flex';
            } else {
                dropdown.style.display = 'none';
            }
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('dropdown-options');
            const container = document.getElementById('custom-dropdown-container');
            if (dropdown && container && !container.contains(event.target)) {
                dropdown.style.display = 'none';
            }
        });

        // Switch Platform Tabs function
        function switchPlatformTab(platform) {
            // Hide all feeds
            document.querySelectorAll('.platform-comments-feed').forEach(feed => {
                feed.style.display = 'none';
            });

            // Remove active classes
            const fbBtn = document.getElementById('tab-btn-facebook');
            const igBtn = document.getElementById('tab-btn-instagram');
            if (fbBtn) fbBtn.classList.remove('active-facebook');
            if (igBtn) igBtn.classList.remove('active-instagram');

            // Show selected feed and add active class
            if (platform === 'facebook') {
                const fbFeed = document.getElementById('facebook-comments-feed');
                if (fbFeed) fbFeed.style.display = 'block';
                if (fbBtn) fbBtn.classList.add('active-facebook');
            } else {
                const igFeed = document.getElementById('instagram-comments-feed');
                if (igFeed) igFeed.style.display = 'block';
                if (igBtn) igBtn.classList.add('active-instagram');
            }
            
            // Save state
            localStorage.setItem('active_comments_tab', platform);
        }

        document.addEventListener('DOMContentLoaded', function() {
            const savedTab = localStorage.getItem('active_comments_tab') || 'facebook';
            switchPlatformTab(savedTab);
        });

        // Generate AI Reply Function
        async function generateAiReply(btn) {
            const commentId = btn.getAttribute('data-comment-id');
            const commentText = btn.getAttribute('data-comment-text');
            const inputField = document.getElementById('reply-input-' + commentId);
            const originalHtml = btn.innerHTML;
            
            if(!inputField) return;
            
            // Set Loading State
            btn.innerHTML = '<i data-lucide="loader-2" style="width: 12px; height: 12px;" class="lucide-spin"></i> Generating...';
            btn.disabled = true;
            if(window.lucide) window.lucide.createIcons();

            try {
                const response = await fetch("{{ route('ai.reply') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Accept": "application/json"
                    },
                    body: JSON.stringify({
                        comment_text: commentText,
                        tone: 'casual',
                        language: 'auto'
                    })
                });

                const data = await response.json();
                
                if (data.reply) {
                    inputField.value = data.reply;
                    inputField.focus();
                } else if (data.error) {
                    alert('AI Error: ' + data.error);
                }
            } catch (error) {
                console.error("AI Generation Error", error);
                alert("Failed to generate AI reply.");
            } finally {
                // Restore Button State
                btn.innerHTML = originalHtml;
                btn.disabled = false;
                if(window.lucide) window.lucide.createIcons();
            }
        }
    </script>
@endsection
