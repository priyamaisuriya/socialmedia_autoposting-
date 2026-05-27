@extends('layouts.premium')

@section('content')
    <!-- Premium Show Page Custom CSS and Styling -->
    <style>
        .details-grid {
            display: grid;
            grid-template-columns: 1.4fr 1fr;
            gap: 2.5rem;
            align-items: start;
            max-width: 1400px;
            margin: 0 auto;
            width: 100%;
        }
        
        @media (max-width: 1024px) {
            .details-grid {
                grid-template-columns: 1fr;
                gap: 2rem;
            }
        }

        .comments-feed::-webkit-scrollbar {
            width: 6px;
        }
        .comments-feed::-webkit-scrollbar-track {
            background: transparent;
        }
        .comments-feed::-webkit-scrollbar-thumb {
            background: var(--glass-border);
            border-radius: 20px;
        }
        .comments-feed::-webkit-scrollbar-thumb:hover {
            background: var(--accent);
        }

        .back-btn {
            background: rgba(255, 255, 255, 0.03); 
            color: var(--text-main); 
            border: 1px solid var(--glass-border); 
            text-decoration: none; 
            padding: 0.75rem 1.5rem; 
            border-radius: 16px; 
            font-weight: 800; 
            display: inline-flex; 
            align-items: center; 
            gap: 8px; 
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); 
            box-shadow: none;
            backdrop-filter: blur(10px);
        }
        .back-btn:hover {
            background: rgba(59, 130, 246, 0.08); 
            border-color: var(--accent);
            transform: translateX(-4px);
            box-shadow: 0 4px 20px rgba(59, 130, 246, 0.15);
        }

        .metric-card {
            border-radius: 24px;
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 6px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        .metric-card.reactions {
            background: linear-gradient(135deg, rgba(244, 63, 94, 0.03), rgba(244, 63, 94, 0.08));
            border: 1px solid rgba(244, 63, 94, 0.15);
        }
        .metric-card.reactions:hover {
            border-color: #f43f5e;
            box-shadow: 0 10px 25px rgba(244, 63, 94, 0.15);
            transform: translateY(-2px);
        }
        .metric-card.comments {
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.03), rgba(37, 99, 235, 0.08));
            border: 1px solid rgba(37, 99, 235, 0.15);
        }
        .metric-card.comments:hover {
            border-color: var(--accent);
            box-shadow: 0 10px 25px rgba(37, 99, 235, 0.15);
            transform: translateY(-2px);
        }
        .metric-card.score {
            background: linear-gradient(135deg, rgba(24, 119, 242, 0.03), rgba(24, 119, 242, 0.08));
            border: 1px solid rgba(24, 119, 242, 0.15);
        }
        .metric-card.score:hover {
            border-color: #1877f2;
            box-shadow: 0 10px 25px rgba(24, 119, 242, 0.15);
            transform: translateY(-2px);
        }

        .comment-bubble {
            display: flex; 
            gap: 14px; 
            align-items: flex-start; 
            background: rgba(255, 255, 255, 0.015); 
            border: 1px solid var(--glass-border); 
            padding: 1.25rem; 
            border-radius: 24px; 
            transition: all 0.3s ease;
        }
        .comment-bubble:hover {
            background: rgba(255, 255, 255, 0.03);
            border-color: var(--accent);
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.08);
        }

        .meta-status-badge {
            background: rgba(16, 185, 129, 0.1); 
            color: #10b981; 
            font-size: 0.75rem; 
            font-weight: 800; 
            padding: 6px 12px; 
            border-radius: 20px; 
            border: 1px solid rgba(16, 185, 129, 0.25);
            box-shadow: 0 0 15px rgba(16, 185, 129, 0.1);
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-transform: uppercase; 
            letter-spacing: 0.05em;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>

    <!-- Top Header Navigation -->
    <div style="margin-bottom: 2.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1.5rem;">
        <div>
            <!-- Breadcrumb Navigation -->
            <div style="display: flex; align-items: center; gap: 8px; font-size: 0.8rem; color: var(--text-muted); font-weight: 700; margin-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 0.05em;">
                <a href="{{ route('posts.index') }}" style="color: var(--text-muted); text-decoration: none; transition: 0.2s;" onmouseover="this.style.color='var(--accent)'" onmouseout="this.style.color='var(--text-muted)'">Posts</a>
                <i data-lucide="chevron-right" style="width: 12px; height: 12px;"></i>
                <span style="color: var(--accent);">Post Details</span>
            </div>
            
            <h1 style="font-size: 2.25rem; font-weight: 800; letter-spacing: -0.03em; background: linear-gradient(135deg, var(--text-main), var(--accent)); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Live Analytics Overview</h1>
        </div>

        <!-- Header Actions: Styled Back and Delete Button -->
        <div style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
            <a href="{{ route('posts.index') }}" class="back-btn">
                <i data-lucide="arrow-left" style="width: 18px; height: 18px; color: var(--accent);"></i> Back to My Posts Hub
            </a>

            @if($post->facebook_post_id)
                <form action="{{ route('posts.archive', $post->id) }}" method="POST" style="display: inline-block; margin: 0; padding: 0;">
                    @csrf
                    <button type="submit" class="btn-primary" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.2); padding: 0.75rem 1.75rem; border-radius: 16px; font-weight: 800; display: inline-flex; align-items: center; gap: 8px; cursor: pointer; transition: 0.2s;" onmouseover="this.style.background='rgba(245, 158, 11, 0.2)';" onmouseout="this.style.background='rgba(245, 158, 11, 0.1)';">
                        <i data-lucide="{{ $post->is_fb_archived ? 'folder-up' : 'folder-down' }}" style="width: 18px; height: 18px;"></i> {{ $post->is_fb_archived ? 'Unarchive Facebook' : 'Archive Facebook' }}
                    </button>
                </form>
            @endif

            <form action="{{ route('posts.destroy', $post->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this post? This will delete it on Facebook as well!')" style="display: inline-block; margin: 0; padding: 0;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-danger" style="padding: 0.75rem 1.75rem; border-radius: 16px; font-weight: 800; display: inline-flex; align-items: center; gap: 8px; cursor: pointer; border: none; box-shadow: 0 4px 15px rgba(239, 68, 68, 0.25); transition: 0.2s;" onmouseover="this.style.transform='scale(1.02)';" onmouseout="this.style.transform='none';">
                    <i data-lucide="trash-2" style="width: 18px; height: 18px;"></i> Delete Post
                </button>
            </form>
        </div>
    </div>

    <!-- Main Responsive Grid Layout -->
    <div class="details-grid">
        
        <!-- Left Panel: Content, Media Preview, Comments Thread -->
        <div style="display: flex; flex-direction: column; gap: 2rem;">
            
            <!-- Message Card with Structural Categorisation -->
            <div class="premium-card" style="padding: 2.5rem; display: flex; flex-direction: column; gap: 1.75rem;">
                
                <!-- Section 1: Clean Description -->
                <div>
                    <div>
                        <h3 style="font-size: 0.8rem; font-weight: 800; text-transform: uppercase; color: var(--text-muted); letter-spacing: 0.05em; margin-bottom: 0.75rem; display: flex; align-items: center; gap: 6px;">
                            <i data-lucide="file-text" style="width: 14px; height: 14px; color: var(--accent);"></i> Post Description
                        </h3>
                        <div style="font-size: 1.125rem; line-height: 1.6; color: var(--text-main); font-weight: 500; word-break: break-word; white-space: pre-wrap;">
                            @php
                                // Get clean description by stripping hashtags and mentions
                                $cleanText = preg_replace('/#\w+/u', '', $post->message);
                                $cleanText = preg_replace('/@\[[^\]]+\]|@\w+/u', '', $cleanText);
                                $cleanText = trim($cleanText);
                            @endphp
                            {{ $cleanText ?: $post->message }}
                        </div>
                    </div>
                </div>

                <!-- Horizontal Glass Line -->
                <div style="height: 1px; background: var(--glass-border); width: 100%;"></div>

                <!-- 2-Column Split: Mentions vs Hashtags -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 2rem;">
                    
                    <!-- Section 2: Mentions -->
                    <div>
                        <h4 style="font-size: 0.75rem; font-weight: 800; text-transform: uppercase; color: var(--text-muted); letter-spacing: 0.05em; margin-bottom: 0.75rem; display: flex; align-items: center; gap: 6px;">
                            <i data-lucide="user" style="width: 13px; height: 13px; color: #10b981;"></i> Mentions
                        </h4>
                        
                        @php
                            $mentions = [];
                            
                            // Match bracketed mentions @[username] or standard @username
                            preg_match_all('/@\[([^\]]+)\]|@(\w+)/u', $post->message, $mentionMatches);
                            if (!empty($mentionMatches[0])) {
                                foreach ($mentionMatches[0] as $m) {
                                    $mentions[] = str_replace(['[', ']'], '', $m);
                                }
                            }
                            
                            $mentions = array_unique($mentions);
                        @endphp
                        
                        @if(!empty($mentions))
                            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                @foreach($mentions as $men)
                                    <span style="color: #10b981; font-weight: 700; background: rgba(16, 185, 129, 0.06); border: 1px solid rgba(16, 185, 129, 0.15); padding: 4px 10px; border-radius: 10px; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 4px;">
                                        <i data-lucide="at-sign" style="width: 12px; height: 12px;"></i>{{ $men }}
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <span style="font-size: 0.8rem; color: var(--text-muted); font-weight: 600; font-style: italic;">No mentions in this post</span>
                        @endif
                    </div>

                    <!-- Section 3: Hashtags -->
                    <div>
                        <h4 style="font-size: 0.75rem; font-weight: 800; text-transform: uppercase; color: var(--text-muted); letter-spacing: 0.05em; margin-bottom: 0.75rem; display: flex; align-items: center; gap: 6px;">
                            <i data-lucide="hash" style="width: 13px; height: 13px; color: var(--accent);"></i> Hashtags
                        </h4>

                        @php
                            preg_match_all('/#(\w+)/u', $post->message, $hashtagMatches);
                            $hashtags = array_unique($hashtagMatches[0] ?? []);
                        @endphp

                        @if(!empty($hashtags))
                            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                @foreach($hashtags as $hash)
                                    <span style="color: var(--accent); font-weight: 700; background: rgba(59, 130, 246, 0.06); border: 1px solid rgba(59, 130, 246, 0.15); padding: 4px 10px; border-radius: 10px; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 4px;">
                                        {{ $hash }}
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <span style="font-size: 0.8rem; color: var(--text-muted); font-weight: 600; font-style: italic;">No hashtags in this post</span>
                        @endif
                    </div>

                </div>

            </div>

            <!-- Attached Media Card -->
            @php $firstMedia = $post->media->first(); @endphp
            @if($firstMedia)
                <div class="premium-card" style="padding: 2rem; overflow: hidden;">
                    <h3 style="font-size: 0.8rem; font-weight: 800; text-transform: uppercase; color: var(--text-muted); letter-spacing: 0.05em; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 6px;">
                        <i data-lucide="image" style="width: 14px; height: 14px; color: var(--accent);"></i> Attached Media Preview
                    </h3>
                    
                    <div style="border-radius: 20px; overflow: hidden; border: 1px solid var(--glass-border); background: #000; display: flex; justify-content: center; align-items: center; box-shadow: 0 10px 30px rgba(0,0,0,0.3);">
                        @if($firstMedia->media_type === 'video')
                            <video src="{{ asset('storage/' . $firstMedia->file_path) }}" controls style="width: 100%; height: auto; max-height: 500px; display: block;"></video>
                        @else
                            <img src="{{ asset('storage/' . $firstMedia->file_path) }}" style="width: 100%; height: auto; max-height: 500px; object-fit: contain; display: block;" />
                        @endif
                    </div>
                </div>
            @else
                <!-- Elegant Text-only Post Alert -->
                <div style="background: rgba(255, 255, 255, 0.02); border: 1px dashed var(--glass-border); padding: 2rem; border-radius: 24px; text-align: center; color: var(--text-muted);">
                    <i data-lucide="align-left" style="width: 32px; height: 32px; opacity: 0.3; margin-bottom: 0.5rem; display: block; margin-left: auto; margin-right: auto;"></i>
                    <span style="font-weight: 700; font-size: 0.85rem; display: block; color: var(--text-main);">Text-only Post Format</span>
                    <span style="font-size: 0.75rem; display: block; margin-top: 2px;">Optimized perfectly for pure textual and organic reach campaigns.</span>
                </div>
            @endif

            <!-- Comments Stream for this specific post -->
            <div class="premium-card" style="padding: 2.5rem; display: flex; flex-direction: column; gap: 1.5rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--glass-border); padding-bottom: 1rem; margin-bottom: 0.5rem; flex-wrap: wrap; gap: 0.75rem;">
                    <h3 style="font-size: 0.8rem; font-weight: 800; text-transform: uppercase; color: var(--text-muted); letter-spacing: 0.05em; display: flex; align-items: center; gap: 8px; margin: 0;">
                        <i data-lucide="messages-square" style="width: 16px; height: 16px; color: var(--accent);"></i> Post Comments
                    </h3>
                    
                    <div style="display: inline-flex; gap: 12px; align-items: center;">
                        <!-- Direct Sync Button -->
                        @if($post->status === 'success')
                            <a href="{{ route('comments.sync', $post->id) }}" class="btn-primary" style="background: rgba(59, 130, 246, 0.06); color: var(--accent); padding: 6px 14px; border-radius: 12px; font-size: 0.75rem; font-weight: 800; text-decoration: none; border: 1px solid rgba(59, 130, 246, 0.2); display: inline-flex; align-items: center; gap: 4px; transition: all 0.2s;" onmouseover="this.style.background='var(--accent)'; this.style.color='white';" onmouseout="this.style.background='rgba(59, 130, 246, 0.06)'; this.style.color='var(--accent)';">
                                <i data-lucide="refresh-cw" style="width: 12px; height: 12px;"></i> Sync Comments
                            </a>
                            
                            <a href="{{ route('comments.index', ['post_id' => $post->id]) }}" style="font-size: 0.75rem; color: var(--text-muted); font-weight: 800; text-decoration: none; display: flex; align-items: center; gap: 4px;" onmouseover="this.style.color='var(--accent)'" onmouseout="this.style.color='var(--text-muted)'">
                                Full Feed <i data-lucide="arrow-right" style="width: 12px; height: 12px;"></i>
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Chat bubble style Comments feed -->
                @if($post->hide_comments)
                    <div style="text-align: center; padding: 3rem 0; color: var(--text-muted); background: var(--bg-main); border: 1px dashed var(--glass-border); border-radius: 20px;">
                        <i data-lucide="eye-off" style="width: 44px; height: 44px; opacity: 0.3; margin-bottom: 0.75rem; display: block; margin-left: auto; margin-right: auto;"></i>
                        <span style="font-weight: 800; font-size: 0.95rem; display: block; color: var(--text-main);">Comments Hidden</span>
                        <span style="font-size: 0.75rem; display: block; margin-top: 4px;">You have chosen to hide the comments section for this post in the dashboard.</span>
                    </div>
                @else
                    <div class="comments-feed" style="max-height: 480px; overflow-y: auto; display: flex; flex-direction: column; gap: 1.25rem; padding-right: 6px;">
                        @forelse($post->comments as $comment)
                        @php
                            // Generate static gradient avatars based on user's name
                            $char = strtoupper(substr($comment->user_name, 0, 1));
                            $colors = [
                                'A' => 'linear-gradient(135deg, #f59e0b, #e11d48)',
                                'B' => 'linear-gradient(135deg, #10b981, #059669)',
                                'C' => 'linear-gradient(135deg, #3b82f6, #1d4ed8)',
                                'D' => 'linear-gradient(135deg, #8b5cf6, #6d28d9)',
                                'E' => 'linear-gradient(135deg, #ec4899, #be185d)'
                            ];
                            $avatarBg = $colors[$char] ?? 'linear-gradient(135deg, #6b7280, #374151)';
                        @endphp
                        
                        <!-- Single Comment Container -->
                        <div class="comment-bubble">
                            <!-- Avatar circle -->
                            <div style="width: 36px; height: 36px; border-radius: 50%; background: {{ $avatarBg }}; display: flex; align-items: center; justify-content: center; color: white; font-weight: 800; font-size: 0.95rem; flex-shrink: 0; box-shadow: 0 4px 10px rgba(0,0,0,0.15);">
                                {{ $char }}
                            </div>
                            
                            <!-- Comment Body -->
                            <div style="flex: 1; display: flex; flex-direction: column; gap: 6px;">
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <span style="font-weight: 800; font-size: 0.85rem; color: var(--text-main);">{{ $comment->user_name }}</span>
                                    <div style="display: inline-flex; align-items: center; gap: 8px;">
                                        <span style="font-size: 0.65rem; color: var(--text-muted); font-weight: 600;">{{ $comment->created_at->diffForHumans() }}</span>
                                        
                                        <!-- Inline Delete Comment Icon Button -->
                                        <form action="{{ route('comments.destroy', $comment->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this comment?')" style="margin: 0; padding: 0;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" style="background: none; border: none; cursor: pointer; color: #ef4444; padding: 2px; transition: 0.2s;" onmouseover="this.style.transform='scale(1.15)';" onmouseout="this.style.transform='none';" title="Delete Comment">
                                                <i data-lucide="trash-2" style="width: 12px; height: 12px;"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                <p style="font-size: 0.85rem; color: var(--text-main); line-height: 1.5; margin: 0; font-weight: 500;">
                                    {{ $comment->message }}
                                </p>

                                <!-- Direct Nesting Replies displays -->
                                @if($comment->replies && $comment->replies->count() > 0)
                                    <div style="display: flex; flex-direction: column; gap: 10px; margin-top: 10px; border-left: 2px solid var(--glass-border); padding-left: 12px;">
                                        @foreach($comment->replies as $rep)
                                            <div style="display: flex; gap: 8px; align-items: flex-start; font-size: 0.8rem;">
                                                <div style="width: 22px; height: 22px; border-radius: 50%; background: var(--accent); display: flex; align-items: center; justify-content: center; color: white; font-weight: 800; font-size: 0.65rem; flex-shrink: 0;">
                                                    {{ strtoupper(substr($rep->user_name, 0, 1)) }}
                                                </div>
                                                <div style="flex: 1;">
                                                    <span style="font-weight: 800; color: var(--text-main); font-size: 0.75rem;">{{ $rep->user_name }}</span>
                                                    <span style="font-size: 0.6rem; color: var(--text-muted); margin-left: 4px;">{{ $rep->created_at->diffForHumans() }}</span>
                                                    <p style="margin: 2px 0 0; color: var(--text-main); font-weight: 500; font-size: 0.75rem; line-height: 1.4;">{{ $rep->message }}</p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                <!-- Direct Expandable Reply Input Box -->
                                <div style="margin-top: 8px;">
                                    <button onclick="toggleReplyInput({{ $comment->id }})" style="background: none; border: none; cursor: pointer; color: var(--accent); font-size: 0.72rem; font-weight: 800; padding: 0; display: inline-flex; align-items: center; gap: 4px; transition: 0.2s;" onmouseover="this.style.textDecoration='underline';" onmouseout="this.style.textDecoration='none';">
                                        <i data-lucide="reply" style="width: 10px; height: 10px;"></i> Reply as Page
                                    </button>
                                    
                                    <div id="reply-form-{{ $comment->id }}" style="display: none; margin-top: 8px; intent: slideDown; animation: slideDown 0.25s ease;">
                                        <form action="{{ route('comments.reply', $comment->id) }}" method="POST" style="display: flex; gap: 8px; margin: 0; width: 100%;">
                                            @csrf
                                            <input name="message" required placeholder="Type reply as Page..." style="flex: 1; padding: 6px 12px; border-radius: 10px; border: 1px solid var(--glass-border); background: var(--bg-main); color: var(--text-main); outline: none; font-size: 0.75rem; font-weight: 500; transition: 0.2s;" onfocus="this.style.borderColor='var(--accent)';" onblur="this.style.borderColor='var(--glass-border)';" />
                                            <button type="submit" class="btn-primary" style="padding: 6px 14px; border-radius: 10px; font-size: 0.75rem; font-weight: 800; border: none; cursor: pointer; background: linear-gradient(135deg, var(--accent), #1d4ed8); display: inline-flex; align-items: center; gap: 2px;">
                                                Send
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div style="text-align: center; padding: 3rem 0; color: var(--text-muted);">
                            <i data-lucide="message-square" style="width: 44px; height: 44px; opacity: 0.3; margin-bottom: 0.75rem; display: block; margin-left: auto; margin-right: auto;"></i>
                            <span style="font-weight: 800; font-size: 0.95rem; display: block; color: var(--text-main);">No comments on this post yet</span>
                            <span style="font-size: 0.75rem; display: block; margin-top: 4px;">When users respond on Facebook, click "Sync Comments" to fetch them here!</span>
                        </div>
                    @endforelse
                </div>
                @endif
            </div>
        </div>

        <!-- Right Panel: Meta Details & Live Stats -->
        <div style="display: flex; flex-direction: column; gap: 2rem;">
            
            <!-- Metadata & Meta Analytics Card -->
            <div class="premium-card" style="padding: 2.5rem; display: flex; flex-direction: column; gap: 1.75rem;">
                <h3 style="font-size: 0.8rem; font-weight: 800; text-transform: uppercase; color: var(--text-muted); letter-spacing: 0.05em; display: flex; align-items: center; gap: 6px; margin: 0;">
                    <i data-lucide="activity" style="width: 14px; height: 14px; color: var(--accent);"></i> Live Performance
                </h3>

                <!-- Page info with verify badge -->
                <div style="display: flex; align-items: center; gap: 12px; padding-bottom: 1.25rem; border-bottom: 1px solid var(--glass-border);">
                    <div style="width: 44px; height: 44px; border-radius: 50%; background: linear-gradient(135deg, #1877f2, #00c6ff); display: flex; align-items: center; justify-content: center; color: white; font-weight: 800; font-size: 1.1rem; box-shadow: 0 4px 10px rgba(24, 119, 242, 0.2);">
                        {{ substr($post->facebookPage->name ?? 'F', 0, 1) }}
                    </div>
                    <div>
                        <div style="font-weight: 800; font-size: 0.95rem; color: var(--text-main); display: flex; align-items: center; gap: 4px;">
                            {{ $post->facebookPage->name ?? 'Unknown Page' }}
                            <span style="display: inline-flex; align-items: center; justify-content: center; width: 13px; height: 13px; border-radius: 50%; background: #1877f2; color: white; font-size: 7px; font-weight: 800;" title="Verified Meta Business Page">✓</span>
                        </div>
                        <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600; display: inline-flex; align-items: center; gap: 4px;">
                            Connected Business Page
                        </span>
                    </div>
                </div>

                <!-- Engagement metrics: Glowing reactions and Comments -->
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    @php
                        $totalComments = max($post->dynamic_comments ?? 0, \App\Models\Comment::where('post_id', $post->id)->count());
                        $totalLikes = max($post->dynamic_likes ?? 0, $post->likes_count ?? 0);
                        $engagement = ($totalLikes + $totalComments) * 1.5;
                    @endphp
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <!-- Reactions -->
                        <div class="metric-card reactions">
                            <span style="font-size: 0.7rem; color: var(--text-muted); font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em;">Likes</span>
                            <div style="font-size: 1.75rem; font-weight: 900; color: #f43f5e; display: flex; align-items: center; gap: 6px;">
                                <i data-lucide="{{ $post->hide_likes ? 'eye-off' : 'heart' }}" style="width: 20px; height: 20px; fill: {{ $post->hide_likes ? 'none' : '#f43f5e' }}; color: {{ $post->hide_likes ? 'var(--text-muted)' : '#f43f5e' }};"></i>
                                @if($post->hide_likes)
                                    <span style="font-size: 1rem; color: var(--text-muted);">Hidden</span>
                                @else
                                    {{ number_format($totalLikes) }}
                                @endif
                            </div>
                        </div>
                        
                        <!-- Comments -->
                        <div class="metric-card comments">
                            <span style="font-size: 0.7rem; color: var(--text-muted); font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em;">Comments</span>
                            <div style="font-size: 1.75rem; font-weight: 900; color: var(--accent); display: flex; align-items: center; gap: 6px;">
                                <i data-lucide="{{ $post->hide_comments ? 'eye-off' : 'message-circle' }}" style="width: 20px; height: 20px; fill: {{ $post->hide_comments ? 'none' : 'var(--accent)' }}; color: {{ $post->hide_comments ? 'var(--text-muted)' : 'var(--accent)' }};"></i>
                                @if($post->hide_comments)
                                    <span style="font-size: 1rem; color: var(--text-muted);">Hidden</span>
                                @else
                                    {{ number_format($totalComments) }}
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions Card (Open Post) -->
                    @if($post->facebook_post_id)
                        <div class="metric-card score">
                            <span style="font-size: 0.7rem; color: var(--text-muted); font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; display: inline-flex; align-items: center; gap: 4px;">
                                <i data-lucide="share-2" style="width: 12px; height: 12px; color: #1877f2;"></i> Quick Actions
                            </span>
                            
                            <div style="display: flex; flex-direction: column; gap: 10px; margin-top: 8px;">
                                <!-- Open Live on Facebook -->
                                <a href="https://facebook.com/{{ $post->facebook_post_id }}" target="_blank" style="background: linear-gradient(135deg, #1877f2, #1d4ed8); color: white; text-decoration: none; padding: 8px 12px; border-radius: 12px; font-weight: 800; font-size: 0.75rem; display: inline-flex; align-items: center; justify-content: center; gap: 6px; transition: all 0.2s; box-shadow: 0 4px 12px rgba(24, 119, 242, 0.2);" onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 6px 16px rgba(24, 119, 242, 0.3)';" onmouseout="this.style.transform='none'; this.style.boxShadow='0 4px 12px rgba(24, 119, 242, 0.2)';">
                                    <i data-lucide="external-link" style="width: 12px; height: 12px;"></i> View Live Post
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="metric-card score">
                            <span style="font-size: 0.7rem; color: var(--text-muted); font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; display: inline-flex; align-items: center; gap: 4px;">
                                <i data-lucide="alert-circle" style="width: 12px; height: 12px; color: #e11d48;"></i> Actions Unavailable
                            </span>
                            <p style="font-size: 0.75rem; color: var(--text-muted); margin: 6px 0 0; line-height: 1.4; font-weight: 600;">
                                Post has not been successfully published to Facebook yet.
                            </p>
                        </div>
                    @endif

                </div>

                </div>

                <!-- Technical Details -->
                <div style="display: flex; flex-direction: column; gap: 12px; font-size: 0.8125rem; border-top: 1px solid var(--glass-border); padding-top: 1.25rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: var(--text-muted); font-weight: 600;">Publishing Status</span>
                        @if($post->status === 'success')
                            <span class="meta-status-badge">
                                <span style="width: 6px; height: 6px; border-radius: 50%; background: #10b981; display: inline-block;"></span> Live on Meta
                            </span>
                        @else
                            <span style="background: rgba(239, 68, 68, 0.1); color: #ef4444; font-size: 0.7rem; font-weight: 800; padding: 4px 10px; border-radius: 20px; border: 1px solid rgba(239, 68, 68, 0.2); text-transform: uppercase; letter-spacing: 0.05em;">
                                {{ $post->status }}
                            </span>
                        @endif
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: var(--text-muted); font-weight: 600;">Published On</span>
                        <span style="color: var(--text-main); font-weight: 700;">{{ $post->created_at->format('M d, Y h:i A') }}</span>
                    </div>

                    @if($post->facebook_post_id)
                        <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--glass-border); padding-top: 12px; margin-top: 4px;">
                            <span style="color: var(--text-muted); font-weight: 600;">Meta Post ID</span>
                            <div style="display: inline-flex; align-items: center; gap: 6px;">
                                <span id="post-id-text" style="color: var(--text-main); font-weight: 800; font-family: monospace;">{{ Str::limit($post->facebook_post_id, 16) }}</span>
                                <button onclick="copyPostId('{{ $post->facebook_post_id }}')" style="background: none; border: none; cursor: pointer; color: var(--text-muted); transition: 0.2s;" onmouseover="this.style.color='var(--accent)';" onmouseout="this.style.color='var(--text-muted)';" title="Copy to clipboard">
                                    <i data-lucide="copy" style="width: 14px; height: 14px;"></i>
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Toggle Reply Javascript and CSS animations -->
    <script>
        function toggleReplyInput(commentId) {
            const form = document.getElementById('reply-form-' + commentId);
            if (form.style.display === 'none') {
                form.style.display = 'block';
            } else {
                form.style.display = 'none';
            }
        }

        function copyPostId(id) {
            navigator.clipboard.writeText(id).then(() => {
                alert('Meta Post ID copied to clipboard!');
            });
        }

    </script>
@endsection
