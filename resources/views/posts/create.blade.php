@extends('layouts.premium')

@section('content')
    <div style="max-width: 1400px; margin: 0 auto; animation: fadeInUp 0.6s ease-out;">
        <div style="margin-bottom: 2.5rem; text-align: center;">
            <h1 style="font-size: 2.5rem; font-weight: 900; letter-spacing: -0.04em; margin-bottom: 0.5rem;">Create Your Story</h1>
            <p style="color: var(--text-muted); font-size: 1.1rem;">Everything you need to publish a perfect Facebook post in one place.</p>
        </div>

        <div style="display: grid; grid-template-columns: 1.2fr 0.8fr; gap: 2.5rem; align-items: start; margin-bottom: 3rem;">
            <!-- Left Side: Post Creator Form -->
            <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data" class="premium-card" style="padding: 3rem; border-width: 2px; margin-bottom: 0;">
                @csrf

            <!-- Global Errors -->
            @if($errors->any())
                <div style="background: rgba(239, 68, 68, 0.1); color: #ef4444; padding: 1.5rem; border-radius: 16px; margin-bottom: 2rem; border: 1px solid rgba(239, 68, 68, 0.2);">
                    <ul style="margin: 0; padding-left: 1.5rem;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(session('error'))
                <div style="background: rgba(239, 68, 68, 0.1); color: #ef4444; padding: 1.5rem; border-radius: 16px; margin-bottom: 2rem; border: 1px solid rgba(239, 68, 68, 0.2);">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Target Facebook Page -->
            <div style="margin-bottom: 3rem;">
                <label style="display: block; font-weight: 800; margin-bottom: 1rem; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.1em; color: var(--accent);">Target Facebook Page</label>
                <select name="facebook_page_id" required style="width: 100%; padding: 1.25rem; border: 2px solid var(--glass-border); border-radius: 20px; background: var(--bg-main); color: var(--text-main); font-size: 1.1rem; outline: none; cursor: pointer; transition: 0.3s; box-shadow: 0 5px 15px rgba(0,0,0,0.02);">
                    @foreach($pages as $page)
                        <option value="{{ $page->id }}" 
                                data-has-instagram="{{ $page->is_instagram_connected ? '1' : '0' }}"
                                data-instagram-username="{{ $page->instagram_username }}"
                                {{ request('page_id') == $page->id ? 'selected' : '' }}>
                            {{ $page->name }} 
                            @if($page->instagram_username)
                                @if($page->is_instagram_connected)
                                    (IG Active: @{{ $page->instagram_username }})
                                @else
                                    (IG Inactive: @{{ $page->instagram_username }})
                                @endif
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>


            <!-- Content Type Selection (Hidden but kept for backend compatibility) -->
            <input type="hidden" name="post_type" value="feed">

            <!-- Publish to Platforms Section -->
            <div style="margin-bottom: 3rem;">
                <label style="display: block; font-weight: 800; margin-bottom: 1rem; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.1em; color: var(--accent);">Publish To Platforms</label>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                    <!-- Facebook Platform Card -->
                    <label id="fb-card" style="position: relative; display: flex; align-items: center; gap: 1rem; padding: 1.5rem; background: var(--bg-main); border: 2px solid var(--accent); border-radius: 20px; cursor: pointer; transition: 0.3s; box-shadow: 0 10px 20px rgba(0,0,0,0.05);">
                        <input type="checkbox" name="post_to_facebook" id="post-to-facebook" checked value="1" style="display: none;" onchange="togglePlatformCard('fb-card', this)">
                        <div style="background: rgba(24, 119, 242, 0.1); width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #1877f2;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 24px; height: 24px; flex-shrink: 0;"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
                        </div>
                        <div style="flex: 1;">
                            <div style="font-weight: 700; color: var(--text-main); font-size: 1.1rem;">Facebook Page</div>
                            <div style="font-size: 0.85rem; color: var(--text-muted);">Publish post on page timeline</div>
                        </div>
                        <div id="fb-card-check" style="color: var(--accent);">
                            <i data-lucide="check-circle-2" style="width: 20px; height: 20px;"></i>
                        </div>
                    </label>

                    <!-- Instagram Platform Card -->
                    <label id="ig-card" style="position: relative; display: flex; align-items: center; gap: 1rem; padding: 1.5rem; background: var(--bg-main); border: 2px solid var(--glass-border); border-radius: 20px; cursor: pointer; transition: 0.3s;">
                        <input type="checkbox" name="post_to_instagram" id="post-to-instagram" value="1" style="display: none;" onchange="togglePlatformCard('ig-card', this)">
                        <div id="ig-icon-container" style="background: rgba(225, 48, 108, 0.1); width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #e1306c;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 24px; height: 24px; flex-shrink: 0;"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
                        </div>
                        <div style="flex: 1;">
                            <div style="font-weight: 700; color: var(--text-main); font-size: 1.1rem; display: flex; align-items: center; gap: 6px;">
                                Instagram
                                <span id="ig-username-badge" style="display: none; background: linear-gradient(45deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888); color: white; font-size: 0.75rem; padding: 2px 8px; border-radius: 10px; font-weight: 600;"></span>
                            </div>
                            <div id="ig-status-text" style="font-size: 0.85rem; color: var(--text-muted);">Publish post to Instagram</div>
                        </div>
                        <div id="ig-card-check" style="color: var(--accent); display: none;">
                            <i data-lucide="check-circle-2" style="width: 20px; height: 20px;"></i>
                        </div>
                    </label>
                </div>
                
                <!-- Also Share to Story Toggle -->
                <div style="margin-top: 2rem;">
                    <label class="story-toggle-label" style="display: flex; align-items: center; gap: 1rem; cursor: pointer; background: var(--bg-main); padding: 1.25rem 2rem; border-radius: 20px; border: 2px solid var(--glass-border); box-shadow: 0 10px 25px rgba(0,0,0,0.03); transition: 0.3s;">
                        <div style="display: flex; align-items: center; gap: 14px; flex: 1;">
                            <div style="background: rgba(168, 85, 247, 0.1); width: 48px; height: 48px; border-radius: 14px; display: flex; align-items: center; justify-content: center; color: #a855f7;">
                                <i data-lucide="instagram" style="width: 24px; height: 24px;"></i>
                            </div>
                            <div>
                                <div style="font-weight: 800; color: var(--text-main); font-size: 1.1rem; letter-spacing: -0.02em;">Also Share as Story</div>
                                <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 3px;">Automatically post this media to your Facebook & Instagram Stories</div>
                            </div>
                        </div>
                        <div style="position: relative; width: 54px; height: 30px; background: var(--nav-active); border-radius: 50px; transition: 0.4s; overflow: hidden; display: flex; align-items: center; padding: 3px;" id="story-switch-track">
                            <input type="checkbox" name="also_add_to_story" id="also_add_to_story" value="1" style="display: none;" onchange="toggleStorySwitch(this)">
                            <div id="story-switch-thumb" style="width: 24px; height: 24px; background: var(--text-muted); border-radius: 50%; transition: 0.4s cubic-bezier(0.4, 0, 0.2, 1); transform: translateX(0); box-shadow: 0 2px 6px rgba(0,0,0,0.2);"></div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Message Area -->
            <div style="margin-bottom: 3rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <label style="font-weight: 800; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.1em; color: var(--accent); margin: 0;">Your Message</label>
                    <button type="button" id="open-ai-btn" style="background: linear-gradient(135deg, rgba(99, 102, 241, 0.15), rgba(168, 85, 247, 0.15)); border: 1px solid rgba(168, 85, 247, 0.4); padding: 0.6rem 1.2rem; border-radius: 14px; color: #c084fc; font-weight: 700; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 0.5rem; cursor: pointer; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); box-shadow: 0 0 15px rgba(168, 85, 247, 0.15); animation: pulseAiBtn 2s infinite;">
                        <i data-lucide="sparkles" style="width: 16px; height: 16px;"></i> <span>AI Caption Assistant</span>
                    </button>
                </div>
                <div style="position: relative; background: var(--bg-main); border: 2px solid var(--glass-border); border-radius: 24px; padding: 1.5rem; transition: 0.3s;">
                    <textarea name="message" id="post-message" required placeholder="Write something amazing..." style="width: 100%; height: 200px; background: transparent; border: none; color: var(--text-main); font-size: 1.25rem; resize: none; outline: none; line-height: 1.6;"></textarea>
                    
                    <div style="display: flex; gap: 0.75rem; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid var(--glass-border); flex-wrap: wrap;">
                        @foreach(['🚀', '🔥', '✨', '✅', '📸', '🎥', '💬', '❤️', '🌟', '💎'] as $emoji)
                            <button type="button" onclick="insertEmoji('{{ $emoji }}')" style="background: var(--card-bg); border: 1px solid var(--glass-border); width: 45px; height: 45px; border-radius: 12px; cursor: pointer; font-size: 1.5rem; transition: 0.3s; display: flex; align-items: center; justify-content: center;">{{ $emoji }}</button>
                        @endforeach
                    </div>
                </div>
            </div>



            <!-- Meta Data (Hashtags & Tags) -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 3rem;">
                <div>
                    <label style="display: block; font-weight: 800; margin-bottom: 1rem; font-size: 0.8rem; text-transform: uppercase; color: var(--text-muted);">Hashtags</label>
                    <div style="position: relative;">
                        <span style="position: absolute; left: 1.25rem; top: 50%; transform: translateY(-50%); color: var(--accent); font-weight: 900; font-size: 1.2rem;">#</span>
                        <input type="text" name="hashtags" id="post-hashtags" placeholder="social marketing tech" style="width: 100%; padding: 1.25rem 1.25rem 1.25rem 2.5rem; border: 2px solid var(--glass-border); border-radius: 18px; background: var(--bg-main); color: var(--text-main); font-size: 1rem; outline: none; transition: 0.3s;">
                    </div>
                </div>
                <div>
                    <label style="display: block; font-weight: 800; margin-bottom: 1rem; font-size: 0.8rem; text-transform: uppercase; color: var(--text-muted);">Tag Page IDs</label>
                    <div style="position: relative;">
                        <span style="position: absolute; left: 1.25rem; top: 50%; transform: translateY(-50%); color: var(--accent); font-weight: 900; font-size: 1.2rem;">@</span>
                        <input type="text" name="tags" placeholder="Page IDs..." style="width: 100%; padding: 1.25rem 1.25rem 1.25rem 2.5rem; border: 2px solid var(--glass-border); border-radius: 18px; background: var(--bg-main); color: var(--text-main); font-size: 1rem; outline: none; transition: 0.3s;">
                    </div>
                </div>
            </div>

            <!-- Schedule Post (Optional) -->
            <div style="margin-bottom: 3rem; background: var(--bg-main); border: 1px solid var(--glass-border); border-radius: 20px; padding: 1.5rem;">
                <label style="display: block; font-weight: 800; margin-bottom: 1.5rem; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.1em; color: var(--accent);">
                    <i data-lucide="clock" style="width: 16px; height: 16px; margin-right: 8px;"></i> Schedule Post (Optional)
                </label>
                
                <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                    <div>
                        <span style="font-weight: 700; font-size: 1rem; color: var(--text-main);">Schedule Date & Time</span>
                        <p style="font-size: 0.8rem; color: var(--text-muted); margin: 0; margin-top: 4px; margin-bottom: 1rem;">Choose a future date and time to automatically publish this post. Leave empty to publish immediately.</p>
                        <input type="datetime-local" name="scheduled_at" style="width: 100%; padding: 1rem; border: 2px solid var(--glass-border); border-radius: 12px; background: var(--bg-main); color: var(--text-main); font-size: 1rem; outline: none; transition: 0.3s; box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);">
                    </div>
                </div>
            </div>

            <!-- Dashboard Visibility Settings -->
            <div style="margin-bottom: 3rem; background: var(--bg-main); border: 1px solid var(--glass-border); border-radius: 20px; padding: 1.5rem;">
                <label style="display: block; font-weight: 800; margin-bottom: 1.5rem; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.1em; color: var(--accent);">
                    <i data-lucide="eye-off" style="width: 16px; height: 16px; margin-right: 8px;"></i> Dashboard Visibility Settings
                </label>
                
                <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                    <!-- Hide Likes Toggle -->
                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <div>
                            <span style="font-weight: 700; font-size: 1rem; color: var(--text-main);">Hide Likes Counter</span>
                            <p style="font-size: 0.8rem; color: var(--text-muted); margin: 0; margin-top: 4px;">Hide the number of likes for this post on your dashboard.</p>
                        </div>
                        <label style="position: relative; display: inline-block; width: 50px; height: 26px;">
                            <input type="checkbox" name="hide_likes" style="opacity: 0; width: 0; height: 0;" onchange="this.nextElementSibling.style.background = this.checked ? 'var(--accent)' : 'rgba(136, 146, 176, 0.3)'; this.nextElementSibling.querySelector('span').style.transform = this.checked ? 'translateX(24px)' : 'translateX(0)';">
                            <div style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background: rgba(136, 146, 176, 0.3); transition: .4s; border-radius: 34px;">
                                <span style="position: absolute; content: ''; height: 18px; width: 18px; left: 4px; bottom: 4px; background-color: white; transition: .4s; border-radius: 50%;"></span>
                            </div>
                        </label>
                    </div>

                    <!-- Hide Comments Toggle -->
                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <div>
                            <span style="font-weight: 700; font-size: 1rem; color: var(--text-main);">Hide Comments</span>
                            <p style="font-size: 0.8rem; color: var(--text-muted); margin: 0; margin-top: 4px;">Hide comments section and count for this post on your dashboard.</p>
                        </div>
                        <label style="position: relative; display: inline-block; width: 50px; height: 26px;">
                            <input type="checkbox" name="hide_comments" style="opacity: 0; width: 0; height: 0;" onchange="this.nextElementSibling.style.background = this.checked ? 'var(--accent)' : 'rgba(136, 146, 176, 0.3)'; this.nextElementSibling.querySelector('span').style.transform = this.checked ? 'translateX(24px)' : 'translateX(0)';">
                            <div style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background: rgba(136, 146, 176, 0.3); transition: .4s; border-radius: 34px;">
                                <span style="position: absolute; content: ''; height: 18px; width: 18px; left: 4px; bottom: 4px; background-color: white; transition: .4s; border-radius: 50%;"></span>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Media Upload Area -->
            <div style="margin-bottom: 4rem;">
                <label style="display: block; font-weight: 800; margin-bottom: 1rem; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.1em; color: var(--accent);">Visual Media</label>
                <div style="position: relative; border: 3px dashed var(--glass-border); border-radius: 30px; padding: 4rem; text-align: center; background: var(--bg-main); transition: 0.4s; overflow: hidden;" id="drop-zone">
                    <div id="upload-placeholder">
                        <i data-lucide="image-plus" size="60" style="color: var(--accent); margin-bottom: 1.5rem; opacity: 0.8;"></i>
                        <h3 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 0.5rem;">Drop your visuals here</h3>
                        <p style="color: var(--text-muted);">Supports high-quality Images and MP4 Videos</p>
                    </div>
                    <input type="file" name="media[]" multiple id="media-input" style="position: absolute; inset: 0; opacity: 0; cursor: pointer; z-index: 10;">
                    <div id="preview-container" style="display: flex; flex-wrap: wrap; gap: 1.25rem; justify-content: center; position: relative; z-index: 5;"></div>
                </div>
            </div>

            <!-- Submit Button Area -->
            <div style="display: flex; gap: 1.5rem; align-items: center; justify-content: flex-end; padding-top: 3rem; border-top: 2px solid var(--glass-border);">
                <a href="{{ route('dashboard') }}" style="color: var(--text-muted); font-weight: 800; text-decoration: none; font-size: 1.1rem; padding: 1rem 2rem; border-radius: 16px; transition: 0.3s;">Cancel</a>
                
                <button type="submit" id="submit-btn" class="btn-primary" style="padding: 1.25rem 3.5rem; font-size: 1.1rem; border-radius: 20px; min-width: 240px; justify-content: center; position: relative; z-index: 20;">
                    <span id="btn-text" style="display: flex; align-items: center; gap: 0.75rem;">
                        <i data-lucide="send-horizontal" size="20"></i> Publish Post Now
                    </span>
                    <span id="btn-loader" style="display: none; align-items: center; gap: 0.75rem;">
                        <i data-lucide="loader-2" class="spin" size="20"></i> Sending...
                    </span>
                </button>
            </div>
        </form>

        <!-- Right Side: Live Mockup Simulator (Sticky) -->
        <div id="preview-simulator" class="premium-card" style="position: sticky; top: 130px; padding: 2.25rem; border-width: 2px; display: flex; flex-direction: column; gap: 1.75rem; transition: all 0.3s ease; box-shadow: 0 20px 40px rgba(0,0,0,0.15); margin-bottom: 0;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h2 style="font-size: 1.35rem; font-weight: 900; letter-spacing: -0.02em; display: flex; align-items: center; gap: 0.5rem; margin: 0;">
                    <i data-lucide="monitor-play" style="color: var(--accent); width: 22px; height: 22px;"></i> Feed Preview
                </h2>
                <span style="font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; background: rgba(59, 130, 246, 0.1); color: var(--accent); padding: 4px 10px; border-radius: 20px;">Live Simulator</span>
            </div>

            <!-- Platform Tab Toggles -->
            <div style="display: flex; gap: 0.5rem; background: var(--bg-main); padding: 0.4rem; border-radius: 16px; border: 1px solid var(--glass-border);">
                <button type="button" id="tab-fb" onclick="switchPreviewTab('facebook')" style="flex: 1; padding: 0.75rem; border: none; border-radius: 12px; background: var(--nav-active); color: var(--text-main); font-weight: 700; cursor: pointer; transition: all 0.3s; display: flex; align-items: center; justify-content: center; gap: 8px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #1877f2;"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
                    Facebook
                </button>
                <button type="button" id="tab-ig" onclick="switchPreviewTab('instagram')" style="flex: 1; padding: 0.75rem; border: none; border-radius: 12px; background: transparent; color: var(--text-muted); font-weight: 700; cursor: pointer; transition: all 0.3s; display: flex; align-items: center; justify-content: center; gap: 8px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #e1306c;"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
                    Instagram
                </button>
            </div>

            <!-- Facebook Feed Preview -->
            <div id="fb-preview-card" style="background: rgba(255, 255, 255, 0.03); border: 1px solid var(--glass-border); border-radius: 20px; padding: 1.5rem; display: flex; flex-direction: column; gap: 1rem; transition: opacity 0.3s ease;">
                <!-- FB Header -->
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <div id="mock-fb-avatar" style="background: linear-gradient(135deg, var(--accent), #00c6ff); width: 44px; height: 44px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 800; font-size: 1.1rem; text-shadow: 0 2px 4px rgba(0,0,0,0.15); border: 2px solid rgba(255,255,255,0.1); flex-shrink: 0;">
                        FB
                    </div>
                    <div style="flex: 1; min-width: 0;">
                        <div id="mock-fb-page-name" style="font-weight: 700; color: var(--text-main); font-size: 0.95rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; cursor: pointer;">Facebook Page</div>
                        <div style="font-size: 0.75rem; color: var(--text-muted); display: flex; align-items: center; gap: 4px;">
                            Just now · <i data-lucide="globe" style="width: 12px; height: 12px;"></i>
                        </div>
                    </div>
                    <button type="button" style="background: transparent; border: none; color: var(--text-muted); cursor: pointer; padding: 4px;">
                        <i data-lucide="more-horizontal" style="width: 20px; height: 20px;"></i>
                    </button>
                </div>

                <!-- FB Caption -->
                <div id="mock-fb-text" style="color: var(--text-main); font-size: 0.95rem; line-height: 1.5; white-space: pre-wrap; word-break: break-word; min-height: 20px; opacity: 0.5;">Type a caption in the editor to see your Facebook post content preview...</div>

                <!-- FB Media Display -->
                <div id="mock-fb-media" style="width: 100%; border-radius: 12px; overflow: hidden; margin-top: 4px; display: none;"></div>

                <!-- FB Engagement Info -->
                <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 0.5rem; border-bottom: 1px solid var(--glass-border); padding-bottom: 0.75rem; font-size: 0.8rem; color: var(--text-muted);">
                    <div style="display: flex; align-items: center; gap: 4px;">
                        <span style="background: #1877f2; border-radius: 50%; width: 18px; height: 18px; display: flex; align-items: center; justify-content: center; color: white;">
                            <i data-lucide="thumbs-up" style="width: 10px; height: 10px; stroke-width: 3px;"></i>
                        </span>
                        <span id="mock-fb-likes-count">24</span>
                    </div>
                    <div>
                        <span>12 Comments</span> · <span>3 Shares</span>
                    </div>
                </div>

                <!-- FB Interactive Action Buttons -->
                <div style="display: flex; justify-content: space-around; padding-top: 0.25rem; font-size: 0.85rem; font-weight: 700; color: var(--text-muted);">
                    <button type="button" id="mock-fb-like-btn" onclick="toggleMockLike('fb')" style="flex: 1; background: transparent; border: none; padding: 8px; border-radius: 8px; display: flex; align-items: center; justify-content: center; gap: 8px; color: var(--text-muted); cursor: pointer; font-weight: bold; transition: all 0.2s ease;">
                        <i data-lucide="thumbs-up" style="width: 18px; height: 18px;"></i> Like
                    </button>
                    <button type="button" style="flex: 1; background: transparent; border: none; padding: 8px; border-radius: 8px; display: flex; align-items: center; justify-content: center; gap: 8px; color: var(--text-muted); cursor: pointer; font-weight: bold; transition: all 0.2s ease;">
                        <i data-lucide="message-square" style="width: 18px; height: 18px;"></i> Comment
                    </button>
                    <button type="button" style="flex: 1; background: transparent; border: none; padding: 8px; border-radius: 8px; display: flex; align-items: center; justify-content: center; gap: 8px; color: var(--text-muted); cursor: pointer; font-weight: bold; transition: all 0.2s ease;">
                        <i data-lucide="share-2" style="width: 18px; height: 18px;"></i> Share
                    </button>
                </div>
            </div>

            <!-- Instagram Feed Preview -->
            <div id="ig-preview-card" style="background: rgba(255, 255, 255, 0.03); border: 1px solid var(--glass-border); border-radius: 20px; padding: 0; display: none; flex-direction: column; overflow: hidden; transition: opacity 0.3s ease; opacity: 0;">
                <!-- IG Header -->
                <div style="display: flex; align-items: center; gap: 0.75rem; padding: 1.25rem;">
                    <div style="position: relative; padding: 2px; border-radius: 50%; background: linear-gradient(45deg, #f09433, #dc2743, #cc2366, #bc1888); flex-shrink: 0;">
                        <div id="mock-ig-avatar" style="background: var(--bg-main); width: 38px; height: 38px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--text-main); font-weight: 800; font-size: 0.95rem; border: 2px solid var(--card-bg);">
                            IG
                        </div>
                    </div>
                    <div style="flex: 1; min-width: 0;">
                        <div id="mock-ig-page-name" style="font-weight: 700; color: var(--text-main); font-size: 0.9rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; cursor: pointer;">page_username</div>
                        <div style="font-size: 0.7rem; color: var(--text-muted);">Sponsored</div>
                    </div>
                    <button type="button" style="background: transparent; border: none; color: var(--text-main); cursor: pointer; padding: 4px;">
                        <i data-lucide="more-horizontal" style="width: 20px; height: 20px;"></i>
                    </button>
                </div>

                <!-- IG Media Display -->
                <div id="mock-ig-media" style="width: 100%; aspect-ratio: 1/1; background: rgba(0,0,0,0.1); border-top: 1px solid var(--glass-border); border-bottom: 1px solid var(--glass-border); display: flex; align-items: center; justify-content: center; position: relative;">
                    <!-- Instagram Media Placeholder -->
                    <div id="mock-ig-media-placeholder" style="display: flex; flex-direction: column; align-items: center; justify-content: center; color: var(--text-muted); gap: 0.75rem;">
                        <i data-lucide="instagram" style="width: 48px; height: 48px; opacity: 0.4;"></i>
                        <span style="font-size: 0.8rem; font-weight: 600;">Media Preview Panel</span>
                    </div>
                </div>

                <!-- IG Action Bar -->
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem 1.25rem 0.75rem;">
                    <div style="display: flex; gap: 1rem; align-items: center;">
                        <button type="button" id="mock-ig-like-btn" onclick="toggleMockLike('ig')" style="background: transparent; border: none; color: var(--text-main); cursor: pointer; padding: 0; display: flex; align-items: center; justify-content: center;">
                            <i data-lucide="heart" style="width: 24px; height: 24px; transition: transform 0.2s;"></i>
                        </button>
                        <button type="button" style="background: transparent; border: none; color: var(--text-main); cursor: pointer; padding: 0; display: flex; align-items: center; justify-content: center;">
                            <i data-lucide="message-circle" style="width: 24px; height: 24px;"></i>
                        </button>
                        <button type="button" style="background: transparent; border: none; color: var(--text-main); cursor: pointer; padding: 0; display: flex; align-items: center; justify-content: center;">
                            <i data-lucide="send" style="width: 24px; height: 24px;"></i>
                        </button>
                    </div>
                    <button type="button" style="background: transparent; border: none; color: var(--text-main); cursor: pointer; padding: 0; display: flex; align-items: center; justify-content: center;">
                        <i data-lucide="bookmark" style="width: 24px; height: 24px;"></i>
                    </button>
                </div>

                <!-- IG Likes Info -->
                <div style="font-size: 0.85rem; font-weight: 700; color: var(--text-main); padding: 0 1.25rem 0.5rem;">
                    Liked by <span id="mock-ig-likes-count">92</span> others
                </div>

                <!-- IG Caption Block -->
                <div style="padding: 0 1.25rem 1.25rem; font-size: 0.85rem; line-height: 1.5; color: var(--text-main); word-break: break-word;">
                    <span id="mock-ig-page-name-caption" style="font-weight: 700; margin-right: 0.5rem;">page_username</span>
                    <span id="mock-ig-text" style="opacity: 0.5;">Type a caption in the editor to see your Instagram post content preview...</span>
                </div>
            </div>
        </div>
    </div>

@push('modals')
    <!-- AI Assistant Modal -->
    <div id="ai-modal" style="display: none; position: fixed; inset: 0; background: rgba(5, 8, 15, 0.85); backdrop-filter: blur(20px); z-index: 9999; align-items: center; justify-content: center; padding: 2rem; box-sizing: border-box; opacity: 0; transition: opacity 0.3s ease-out;">
        <div style="background: var(--card-bg); border: 2px solid var(--glass-border); border-radius: 30px; width: 100%; max-width: 950px; max-height: 85vh; overflow: hidden; display: flex; flex-direction: column; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); animation: zoomIn 0.3s cubic-bezier(0.34, 1.56, 0.64, 1); position: relative; margin: auto;">
            
            <!-- Modal Header -->
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 2rem 2.5rem; border-bottom: 1px solid var(--glass-border);">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <div style="background: linear-gradient(135deg, #6366f1, #a855f7); width: 42px; height: 42px; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; box-shadow: 0 0 15px rgba(168, 85, 247, 0.4);">
                        <i data-lucide="sparkles" style="width: 22px; height: 22px;"></i>
                    </div>
                    <div>
                        <h2 style="font-size: 1.5rem; font-weight: 800; background: linear-gradient(to right, #a855f7, #6366f1); -webkit-background-clip: text; -webkit-text-fill-color: transparent; margin: 0;">AI Caption & Hashtag Generator</h2>
                        <p style="color: var(--text-muted); font-size: 0.85rem; margin: 0; margin-top: 2px;">Generate engaging social media captions with advanced parameters using Gemini AI</p>
                    </div>
                </div>
                <button type="button" id="close-ai-modal" style="background: var(--nav-active); border: 1px solid var(--glass-border); color: var(--text-muted); width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: 0.3s;">
                    <i data-lucide="x" style="width: 18px; height: 18px;"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div style="padding: 2.5rem; display: grid; grid-template-columns: 1.2fr 1fr; gap: 2.5rem; overflow-y: auto;">
                
                <!-- Left Side: Controls -->
                <div style="display: flex; flex-direction: column; gap: 1.75rem;">
                    <!-- Description / Prompt -->
                    <div>
                        <label style="display: block; font-weight: 700; margin-bottom: 0.75rem; font-size: 0.9rem; color: var(--text-main);">What is your post about?</label>
                        <textarea id="ai-prompt" placeholder="Write a short description, promotion details, or main idea (e.g., 'We are launching our new high-tech software development agency in Surat this Monday with a special 20% discount on first month projects!')" style="width: 100%; height: 120px; border: 2px solid var(--glass-border); border-radius: 18px; background: var(--bg-main); color: var(--text-main); font-size: 0.95rem; padding: 1rem; resize: none; outline: none; transition: 0.3s;" required></textarea>
                    </div>

                    <!-- Tone Selector -->
                    <div>
                        <label style="display: block; font-weight: 700; margin-bottom: 0.75rem; font-size: 0.9rem; color: var(--text-main);">Tone of Voice</label>
                        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.75rem;">
                            <label class="ai-tone-card">
                                <input type="radio" name="ai-tone" value="professional" checked style="display: none;">
                                <span style="display: block; width: 100%;">💼 Professional</span>
                            </label>
                            <label class="ai-tone-card">
                                <input type="radio" name="ai-tone" value="casual" style="display: none;">
                                <span style="display: block; width: 100%;">😊 Casual</span>
                            </label>
                            <label class="ai-tone-card">
                                <input type="radio" name="ai-tone" value="funny" style="display: none;">
                                <span style="display: block; width: 100%;">🤪 Funny</span>
                            </label>
                            <label class="ai-tone-card">
                                <input type="radio" name="ai-tone" value="exciting" style="display: none;">
                                <span style="display: block; width: 100%;">🔥 Exciting</span>
                            </label>
                            <label class="ai-tone-card">
                                <input type="radio" name="ai-tone" value="witty" style="display: none;">
                                <span style="display: block; width: 100%;">🧠 Witty</span>
                            </label>
                            <label class="ai-tone-card">
                                <input type="radio" name="ai-tone" value="informative" style="display: none;">
                                <span style="display: block; width: 100%;">📚 Informative</span>
                            </label>
                        </div>
                    </div>

                    <!-- Language Selection -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
                        <div>
                            <label style="display: block; font-weight: 700; margin-bottom: 0.75rem; font-size: 0.9rem; color: var(--text-main);">Language</label>
                            <select id="ai-language" style="width: 100%; padding: 0.85rem; border: 2px solid var(--glass-border); border-radius: 14px; background: var(--bg-main); color: var(--text-main); font-size: 0.95rem; outline: none; cursor: pointer;">
                                <option value="english">🇺🇸 English</option>
                                <option value="gujarati">🇮🇳 Gujarati (ગુજરાતી)</option>
                                <option value="hindi">🇮🇳 Hindi (हिन्दी)</option>
                                <option value="mixed">💫 Gujlish (Eng + Guj)</option>
                            </select>
                        </div>

                        <!-- Hashtag Density -->
                        <div>
                            <label style="display: block; font-weight: 700; margin-bottom: 0.75rem; font-size: 0.9rem; color: var(--text-main);">Hashtag Density</label>
                            <select id="ai-density" style="width: 100%; padding: 0.85rem; border: 2px solid var(--glass-border); border-radius: 14px; background: var(--bg-main); color: var(--text-main); font-size: 0.95rem; outline: none; cursor: pointer;">
                                <option value="none">❌ None</option>
                                <option value="low">🏷️ Low (1-5 tags)</option>
                                <option value="medium" selected>🏷️ Medium (5-10 tags)</option>
                                <option value="high">🏷️ High (10-15 tags)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Generate Button -->
                    <button type="button" id="generate-ai-btn" class="btn-primary" style="padding: 1.1rem; border-radius: 16px; width: 100%; justify-content: center; font-size: 1.05rem; margin-top: 0.5rem; background: linear-gradient(135deg, #6366f1, #a855f7); box-shadow: 0 10px 20px rgba(168, 85, 247, 0.2);">
                        <i data-lucide="sparkles" style="width: 18px; height: 18px;"></i> Generate AI Copy
                    </button>
                </div>

                <!-- Right Side: Output Preview -->
                <div style="background: var(--bg-main); border: 2px dashed var(--glass-border); border-radius: 24px; padding: 2rem; display: flex; flex-direction: column; position: relative; min-height: 380px;">
                    
                    <!-- Initial State Placeholder -->
                    <div id="ai-placeholder" style="display: flex; flex-direction: column; align-items: center; justify-content: center; flex: 1; text-align: center; color: var(--text-muted);">
                        <div style="background: rgba(168, 85, 247, 0.05); width: 70px; height: 70px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 1.25rem;">
                            <i data-lucide="brain-circuit" style="width: 32px; height: 32px; color: #a855f7;"></i>
                        </div>
                        <h4 style="color: var(--text-main); font-weight: 700; margin-bottom: 0.5rem; font-size: 1.1rem;">AI Creative Space</h4>
                        <p style="font-size: 0.85rem; max-width: 250px; line-height: 1.5;">Choose parameters and press generate to manifest beautiful copywriting.</p>
                    </div>

                    <!-- Loading State -->
                    <div id="ai-loading" style="display: none; flex-direction: column; align-items: center; justify-content: center; flex: 1; text-align: center;">
                        <div class="loader-ring">
                            <div></div><div></div><div></div><div></div>
                        </div>
                        <h4 style="color: var(--text-main); font-weight: 700; margin-bottom: 0.5rem; font-size: 1.1rem; margin-top: 1.5rem;">Engaging Brain Cells...</h4>
                        <p style="color: var(--text-muted); font-size: 0.85rem; max-width: 220px; line-height: 1.5;">Writing premium captions, choosing matching emojis, and parsing tags...</p>
                    </div>

                    <!-- Error State -->
                    <div id="ai-error" style="display: none; flex-direction: column; align-items: center; justify-content: center; flex: 1; text-align: center; color: #ef4444;">
                        <i data-lucide="alert-triangle" style="width: 48px; height: 48px; margin-bottom: 1rem;"></i>
                        <h4 style="font-weight: 700; margin-bottom: 0.5rem; font-size: 1.1rem;">Generation Failed</h4>
                        <p id="ai-error-msg" style="font-size: 0.85rem; max-width: 300px; line-height: 1.5; word-break: break-word;">An unexpected error occurred. Please check your configuration.</p>
                    </div>

                    <!-- Completed / Success State Preview -->
                    <div id="ai-success" style="display: none; flex-direction: column; height: 100%; justify-content: space-between; flex: 1;">
                        <div style="overflow-y: auto; flex: 1; margin-bottom: 1.5rem; padding-right: 4px;">
                            
                            <!-- Caption Preview -->
                            <div style="margin-bottom: 1.5rem;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                                    <span style="font-weight: 800; font-size: 0.75rem; text-transform: uppercase; color: var(--accent); letter-spacing: 0.05em;">Generated Caption</span>
                                    <button type="button" onclick="copyToClipboard('ai-caption-text', this)" style="background: transparent; border: none; color: var(--text-muted); cursor: pointer; display: flex; align-items: center; gap: 4px; font-size: 0.75rem; font-weight: 600;">
                                        <i data-lucide="copy" style="width: 12px; height: 12px;"></i> Copy
                                    </button>
                                </div>
                                <div id="ai-caption-text" style="background: var(--card-bg); border: 1px solid var(--glass-border); border-radius: 16px; padding: 1.25rem; font-size: 0.95rem; color: var(--text-main); white-space: pre-wrap; line-height: 1.6; max-height: 200px; overflow-y: auto; box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);"></div>
                            </div>

                            <!-- Hashtags Preview -->
                            <div id="ai-hashtags-section" style="margin-bottom: 1rem;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                                    <span style="font-weight: 800; font-size: 0.75rem; text-transform: uppercase; color: var(--accent); letter-spacing: 0.05em;">Suggested Hashtags</span>
                                    <button type="button" onclick="copyToClipboard('ai-hashtags-text', this)" style="background: transparent; border: none; color: var(--text-muted); cursor: pointer; display: flex; align-items: center; gap: 4px; font-size: 0.75rem; font-weight: 600;">
                                        <i data-lucide="copy" style="width: 12px; height: 12px;"></i> Copy
                                    </button>
                                </div>
                                <div id="ai-hashtags-text" style="background: var(--card-bg); border: 1px solid var(--glass-border); border-radius: 16px; padding: 1rem 1.25rem; font-size: 0.9rem; color: var(--text-main); font-weight: 600; letter-spacing: 0.02em;"></div>
                            </div>
                        </div>

                        <!-- Integration Actions -->
                        <div style="display: flex; flex-direction: column; gap: 0.75rem; border-top: 1px solid var(--glass-border); padding-top: 1.25rem;">
                            <button type="button" id="apply-all-btn" class="btn-primary" style="justify-content: center; border-radius: 14px; font-size: 0.95rem; padding: 0.85rem; background: linear-gradient(135deg, #10b981, #059669); box-shadow: 0 10px 15px rgba(16, 185, 129, 0.15);">
                                <i data-lucide="check" style="width: 18px; height: 18px;"></i> Apply to Post Editor
                            </button>
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </div>
@endpush

    <style>
        .spin { animation: spin 1s linear infinite; }
        @verbatim
        @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
        @endverbatim
        #submit-btn:active { transform: scale(0.98); }

        /* AI Assistant Premium Styles */
        @keyframes pulseAiBtn {
            0% {
                box-shadow: 0 0 0 0 rgba(168, 85, 247, 0.4);
                transform: scale(1);
            }
            70% {
                box-shadow: 0 0 0 10px rgba(168, 85, 247, 0);
                transform: scale(1.02);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(168, 85, 247, 0);
                transform: scale(1);
            }
        }

        .ai-tone-card {
            background: var(--bg-main);
            border: 2px solid var(--glass-border);
            border-radius: 14px;
            padding: 0.75rem 0.5rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .ai-tone-card:hover {
            border-color: rgba(168, 85, 247, 0.4);
            transform: translateY(-2px);
            color: var(--text-main);
        }

        .ai-tone-card.active {
            border-color: #a855f7;
            background: rgba(168, 85, 247, 0.08);
            box-shadow: 0 0 15px rgba(168, 85, 247, 0.15);
            color: var(--text-main);
        }

        .loader-ring {
            display: inline-block;
            position: relative;
            width: 80px;
            height: 80px;
        }
        .loader-ring div {
            box-sizing: border-box;
            display: block;
            position: absolute;
            width: 64px;
            height: 64px;
            margin: 8px;
            border: 6px solid #a855f7;
            border-radius: 50%;
            animation: loader-ring 1.2s cubic-bezier(0.5, 0, 0.5, 1) infinite;
            border-color: #a855f7 transparent transparent transparent;
        }
        .loader-ring div:nth-child(1) { animation-delay: -0.45s; }
        .loader-ring div:nth-child(2) { animation-delay: -0.3s; }
        .loader-ring div:nth-child(3) { animation-delay: -0.15s; }
        
        @keyframes loader-ring {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @keyframes zoomIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }
    </style>

    <script>
        // Form Loading State
        document.querySelector('form').addEventListener('submit', function() {
            const btn = document.getElementById('submit-btn');
            const text = document.getElementById('btn-text');
            const loader = document.getElementById('btn-loader');
            
            btn.style.opacity = '0.7';
            btn.style.pointerEvents = 'none';
            text.style.display = 'none';
            loader.style.display = 'flex';
        });

        const mediaInput = document.getElementById('media-input');
        const previewContainer = document.getElementById('preview-container');
        const postMessage = document.getElementById('post-message');
        const uploadPlaceholder = document.getElementById('upload-placeholder');
        const postHashtags = document.getElementById('post-hashtags');
        const pageSelect = document.querySelector('select[name="facebook_page_id"]');
        const igCard = document.getElementById('ig-card');
        const igCheckbox = document.getElementById('post-to-instagram');
        const igStatusText = document.getElementById('ig-status-text');
        const igUsernameBadge = document.getElementById('ig-username-badge');

        // Global functions for inline HTML events
        window.insertEmoji = function(emoji) {
            if (!postMessage) return;
            const start = postMessage.selectionStart;
            const text = postMessage.value;
            postMessage.value = text.substring(0, start) + emoji + text.substring(postMessage.selectionEnd);
            postMessage.focus();
            postMessage.setSelectionRange(start + emoji.length, start + emoji.length);
            
            // Sync immediately on emoji insert!
            syncMessageAndHashtags();
        };

        window.toggleStorySwitch = function(checkbox) {
            const track = document.getElementById('story-switch-track');
            const thumb = document.getElementById('story-switch-thumb');
            const label = checkbox.closest('.story-toggle-label');
            if(checkbox.checked) {
                track.style.background = '#a855f7'; // Purple to match the icon
                thumb.style.transform = 'translateX(24px)';
                thumb.style.background = '#fff';
                label.style.borderColor = '#a855f7';
                label.style.boxShadow = '0 10px 25px rgba(168, 85, 247, 0.15)';
            } else {
                track.style.background = 'var(--nav-active)';
                thumb.style.transform = 'translateX(0)';
                thumb.style.background = 'var(--text-muted)';
                label.style.borderColor = 'var(--glass-border)';
                label.style.boxShadow = '0 10px 25px rgba(0,0,0,0.03)';
            }
        };

        window.togglePlatformCard = function(cardId, checkbox) {
            const card = document.getElementById(cardId);
            const check = document.getElementById(cardId + '-check');
            if (!card) return;
            if (checkbox.checked) {
                card.style.border = '2px solid var(--accent)';
                card.style.boxShadow = '0 10px 20px rgba(0,0,0,0.05)';
                if (check) check.style.display = 'block';
            } else {
                card.style.border = '2px solid var(--glass-border)';
                card.style.boxShadow = 'none';
                if (check) check.style.display = 'none';
            }
        };


        window.switchPreviewTab = function(platform) {
            const fbTab = document.getElementById('tab-fb');
            const igTab = document.getElementById('tab-ig');
            const fbCard = document.getElementById('fb-preview-card');
            const igCard = document.getElementById('ig-preview-card');

            if (!fbTab || !igTab || !fbCard || !igCard) return;

            if (platform === 'facebook') {
                fbTab.style.background = 'var(--nav-active)';
                fbTab.style.color = 'var(--text-main)';
                igTab.style.background = 'transparent';
                igTab.style.color = 'var(--text-muted)';
                fbCard.style.display = 'flex';
                setTimeout(() => fbCard.style.opacity = '1', 10);
                igCard.style.opacity = '0';
                setTimeout(() => igCard.style.display = 'none', 300);
            } else {
                igTab.style.background = 'var(--nav-active)';
                igTab.style.color = 'var(--text-main)';
                fbTab.style.background = 'transparent';
                fbTab.style.color = 'var(--text-muted)';
                igCard.style.display = 'flex';
                setTimeout(() => igCard.style.opacity = '1', 10);
                fbCard.style.opacity = '0';
                setTimeout(() => fbCard.style.display = 'none', 300);
            }
        };

        window.updatePostTypeUI = function(type) {
            const types = ['feed', 'reel'];
            types.forEach(t => {
                const card = document.getElementById('type-card-' + t);
                const icon = card.querySelector('svg') || card.querySelector('i');
                if (!card) return;
                
                if (t === type) {
                    card.style.border = '2px solid var(--accent)';
                    card.style.boxShadow = '0 5px 15px rgba(0,0,0,0.05)';
                    if (icon) icon.style.color = 'var(--accent)';
                } else {
                    card.style.border = '2px solid var(--glass-border)';
                    card.style.boxShadow = 'none';
                    if (icon) icon.style.color = 'var(--text-muted)';
                }
            });
        };

        window.toggleMockLike = function(platform) {
            if (platform === 'fb') {
                const btn = document.getElementById('mock-fb-like-btn');
                const count = document.getElementById('mock-fb-likes-count');
                if (!btn || !count) return;
                const currentLikes = parseInt(count.textContent) || 0;
                const isActive = btn.style.color === 'rgb(37, 99, 235)' || btn.classList.contains('active-like');
                
                if (isActive) {
                    btn.style.color = 'var(--text-muted)';
                    btn.classList.remove('active-like');
                    count.textContent = Math.max(0, currentLikes - 1);
                    const icon = btn.querySelector('i');
                    if (icon) icon.style.fill = 'none';
                } else {
                    btn.style.color = 'var(--accent)';
                    btn.classList.add('active-like');
                    count.textContent = currentLikes + 1;
                    const icon = btn.querySelector('i');
                    if (icon) icon.style.fill = 'var(--accent)';
                    
                    btn.style.transform = 'scale(1.15)';
                    setTimeout(() => { btn.style.transform = 'scale(1)'; }, 150);
                }
            } else if (platform === 'ig') {
                const btn = document.getElementById('mock-ig-like-btn');
                const count = document.getElementById('mock-ig-likes-count');
                if (!btn || !count) return;
                const currentLikes = parseInt(count.textContent) || 0;
                const icon = btn.querySelector('i');
                if (!icon) return;
                const isActive = icon.getAttribute('stroke') === '#ef4444' || icon.style.color === 'rgb(239, 68, 68)' || btn.classList.contains('active-like');

                if (isActive) {
                    icon.setAttribute('stroke', 'currentColor');
                    icon.style.color = '';
                    icon.style.fill = 'none';
                    btn.classList.remove('active-like');
                    count.textContent = Math.max(0, currentLikes - 1);
                } else {
                    icon.setAttribute('stroke', '#ef4444');
                    icon.style.color = '#ef4444';
                    icon.style.fill = '#ef4444';
                    btn.classList.add('active-like');
                    count.textContent = currentLikes + 1;
                    
                    icon.style.transform = 'scale(1.35)';
                    setTimeout(() => { icon.style.transform = 'scale(1)'; }, 150);
                }
            }
        };

        window.syncMessageAndHashtags = function() {
            const fbText = document.getElementById('mock-fb-text');
            const igText = document.getElementById('mock-ig-text');

            if (!postMessage) return;

            let messageVal = postMessage.value;
            let hashtagVal = postHashtags ? postHashtags.value.trim() : '';

            if (hashtagVal) {
                const formattedHashtags = hashtagVal.split(/\s+/).map(tag => {
                    if (!tag) return '';
                    return tag.startsWith('#') ? tag : '#' + tag;
                }).filter(tag => tag).join(' ');
                
                messageVal = messageVal + '\n\n' + formattedHashtags;
            }

            if (fbText) {
                fbText.textContent = messageVal.trim() || 'Type a caption in the editor to see your Facebook post content preview...';
                fbText.style.opacity = messageVal.trim() ? '1' : '0.5';
            }
            if (igText) {
                igText.textContent = messageVal.trim() || 'Type a caption in the editor to see your Instagram post content preview...';
                igText.style.opacity = messageVal.trim() ? '1' : '0.5';
            }
        };

        window.syncPageInfo = function() {
            if (!pageSelect) return;
            const selectedOption = pageSelect.options[pageSelect.selectedIndex];
            if (!selectedOption) return;
            
            const optionText = selectedOption.text;
            const pageName = optionText.split('(')[0].trim();
            
            const mockFbName = document.getElementById('mock-fb-page-name');
            if (mockFbName) mockFbName.textContent = pageName;
            
            const mockIgName = document.getElementById('mock-ig-page-name');
            const mockIgNameCap = document.getElementById('mock-ig-page-name-caption');
            
            const handle = pageName.toLowerCase().replace(/[^a-z0-9_]/g, '_').replace(/_+/g, '_').replace(/^_+|_+$/g, '');
            const cleanHandle = handle || 'page_username';
            
            if (mockIgName) mockIgName.textContent = cleanHandle;
            if (mockIgNameCap) mockIgNameCap.textContent = cleanHandle;
            
            const initials = pageName.split(/\s+/).map(w => w[0]).join('').slice(0, 2).toUpperCase() || 'FB';
            
            const fbAvatar = document.getElementById('mock-fb-avatar');
            const igAvatar = document.getElementById('mock-ig-avatar');
            
            if (fbAvatar) fbAvatar.textContent = initials;
            if (igAvatar) igAvatar.textContent = initials;
        };

        window.updateInstagramOption = function() {
            if (!pageSelect || !igCard || !igCheckbox) return;
            const selectedOption = pageSelect.options[pageSelect.selectedIndex];
            if (!selectedOption) return;

            const hasIg = selectedOption.getAttribute('data-has-instagram') === '1';
            const igUsername = selectedOption.getAttribute('data-instagram-username');

            if (hasIg) {
                igCard.style.opacity = '1';
                igCheckbox.disabled = false;
                igCheckbox.checked = true;
                
                if (igUsernameBadge) {
                    igUsernameBadge.textContent = '@' + igUsername;
                    igUsernameBadge.style.display = 'inline-block';
                }
                if (igStatusText) igStatusText.textContent = 'Publish to linked Instagram account';
                
                togglePlatformCard('ig-card', igCheckbox);
            } else {
                igCard.style.opacity = '0.7';
                igCheckbox.disabled = false;
                
                if (igUsernameBadge) igUsernameBadge.style.display = 'none';
                if (igUsername && igUsername.trim() !== '') {
                    if (igStatusText) igStatusText.textContent = 'Instagram inactive (Click to force select)';
                } else {
                    if (igStatusText) igStatusText.textContent = 'No connected Instagram account (Click to force)';
                }
                
                // Don't uncheck it automatically, let the user decide
            }
        };

        window.updateMockupMedia = function(sources) {
            const fbMediaContainer = document.getElementById('mock-fb-media');
            const igMediaContainer = document.getElementById('mock-ig-media');
            
            if (!sources || sources.length === 0) {
                if (fbMediaContainer) {
                    fbMediaContainer.style.display = 'none';
                    fbMediaContainer.innerHTML = '';
                }
                if (igMediaContainer) {
                    igMediaContainer.innerHTML = `
                        <div id="mock-ig-media-placeholder" style="display: flex; flex-direction: column; align-items: center; justify-content: center; color: var(--text-muted); gap: 0.75rem;">
                            <i data-lucide="instagram" style="width: 48px; height: 48px; opacity: 0.4;"></i>
                            <span style="font-size: 0.8rem; font-weight: 600;">Media Preview Panel</span>
                        </div>
                    `;
                    lucide.createIcons();
                }
                return;
            }

            // Facebook Preview Grid
            if (fbMediaContainer) {
                fbMediaContainer.style.display = 'block';
                let fbHtml = '';
                const count = sources.length;

                if (count === 1) {
                    const item = sources[0];
                    if (item.type.startsWith('image/')) {
                        fbHtml = `<div style="width:100%; overflow:hidden; display:flex; align-items:center; justify-content:center;"><img src="${item.src}" style="width:100%; max-height:360px; object-fit:cover;"></div>`;
                    } else {
                        fbHtml = `<div style="width:100%; overflow:hidden; display:flex; align-items:center; justify-content:center;"><video src="${item.src}" autoplay loop muted playsinline style="width:100%; max-height:360px; object-fit:cover;"></video></div>`;
                    }
                } else if (count === 2) {
                    fbHtml = `<div style="display:grid; grid-template-columns: 1fr 1fr; gap:4px; height:220px; overflow:hidden;">`;
                    sources.slice(0, 2).forEach(item => {
                        if (item.type.startsWith('image/')) {
                            fbHtml += `<img src="${item.src}" style="width:100%; height:100%; object-fit:cover;">`;
                        } else {
                            fbHtml += `<video src="${item.src}" autoplay loop muted playsinline style="width:100%; height:100%; object-fit:cover;"></video>`;
                        }
                    });
                    fbHtml += `</div>`;
                } else if (count === 3) {
                    fbHtml = `<div style="display:grid; grid-template-columns: 1.5fr 1fr; gap:4px; height:240px; overflow:hidden;">`;
                    const item1 = sources[0];
                    if (item1.type.startsWith('image/')) {
                        fbHtml += `<img src="${item1.src}" style="width:100%; height:100%; object-fit:cover;">`;
                    } else {
                        fbHtml += `<video src="${item1.src}" autoplay loop muted playsinline style="width:100%; height:100%; object-fit:cover;"></video>`;
                    }
                    fbHtml += `<div style="display:grid; grid-template-rows: 1fr 1fr; gap:4px; height:100%; overflow:hidden;">`;
                    sources.slice(1, 3).forEach(item => {
                        if (item.type.startsWith('image/')) {
                            fbHtml += `<img src="${item.src}" style="width:100%; height:100%; object-fit:cover;">`;
                        } else {
                            fbHtml += `<video src="${item.src}" autoplay loop muted playsinline style="width:100%; height:100%; object-fit:cover;"></video>`;
                        }
                    });
                    fbHtml += `</div></div>`;
                } else {
                    fbHtml = `<div style="display:grid; grid-template-columns: 1.2fr 1fr; gap:4px; height:240px; overflow:hidden;">`;
                    const item1 = sources[0];
                    if (item1.type.startsWith('image/')) {
                        fbHtml += `<img src="${item1.src}" style="width:100%; height:100%; object-fit:cover;">`;
                    } else {
                        fbHtml += `<video src="${item1.src}" autoplay loop muted playsinline style="width:100%; height:100%; object-fit:cover;"></video>`;
                    }
                    fbHtml += `<div style="display:grid; grid-template-rows: 1fr 1fr 1fr; gap:4px; height:100%; overflow:hidden;">`;
                    fbHtml += sources[1].type.startsWith('image/') ? `<img src="${sources[1].src}" style="width:100%; height:100%; object-fit:cover;">` : `<video src="${sources[1].src}" autoplay loop muted playsinline style="width:100%; height:100%; object-fit:cover;"></video>`;
                    fbHtml += sources[2].type.startsWith('image/') ? `<img src="${sources[2].src}" style="width:100%; height:100%; object-fit:cover;">` : `<video src="${sources[2].src}" autoplay loop muted playsinline style="width:100%; height:100%; object-fit:cover;"></video>`;
                    
                    fbHtml += `<div style="position:relative; width:100%; height:100%;">`;
                    if (sources[3].type.startsWith('image/')) {
                        fbHtml += `<img src="${sources[3].src}" style="width:100%; height:100%; object-fit:cover; filter:brightness(0.55);">`;
                    } else {
                        fbHtml += `<video src="${sources[3].src}" autoplay loop muted playsinline style="width:100%; height:100%; object-fit:cover; filter:brightness(0.55);"></video>`;
                    }
                    const extraCount = count - 4;
                    if (extraCount > 0) {
                        fbHtml += `<div style="position:absolute; inset:0; display:flex; align-items:center; justify-content:center; color:white; font-size:1.4rem; font-weight:900; text-shadow: 0 2px 4px rgba(0,0,0,0.5);">+${extraCount}</div>`;
                    }
                    fbHtml += `</div></div></div>`;
                }

                fbMediaContainer.innerHTML = fbHtml;
            }

            // Instagram Preview Carousel
            if (igMediaContainer) {
                let igHtml = '';
                const count = sources.length;
                const mainItem = sources[0];

                igHtml += `<div style="width:100%; height:100%; position:relative; overflow:hidden; display:flex; align-items:center; justify-content:center;">`;
                
                if (mainItem.type.startsWith('image/')) {
                    igHtml += `<img src="${mainItem.src}" style="width:100%; height:100%; object-fit:cover;">`;
                } else {
                    igHtml += `<video src="${mainItem.src}" autoplay loop muted playsinline style="width:100%; height:100%; object-fit:cover;"></video>`;
                }

                if (count > 1) {
                    igHtml += `<div style="position:absolute; top:12px; right:12px; background:rgba(0,0,0,0.7); backdrop-filter:blur(4px); padding:4px 10px; border-radius:12px; color:white; font-size:0.75rem; font-weight:700; display:flex; align-items:center; gap:4px;">`;
                    igHtml += `1/${count} <i data-lucide="layers" style="width:12px; height:12px;"></i>`;
                    igHtml += `</div>`;
                }
                igHtml += `</div>`;

                if (count > 1) {
                    igHtml += `<div style="position:absolute; bottom:12px; left:50%; transform:translateX(-50%); display:flex; gap:5px; background:rgba(0,0,0,0.4); backdrop-filter:blur(4px); padding:4px 8px; border-radius:10px; z-index: 5;">`;
                    sources.forEach((_, i) => {
                        igHtml += `<span style="width:6px; height:6px; border-radius:50%; background:${i === 0 ? 'white' : 'rgba(255,255,255,0.4)'};"></span>`;
                    });
                    igHtml += `</div>`;
                }

                igMediaContainer.innerHTML = igHtml;
                lucide.createIcons();
            }
        };

        // Event Listeners initialization
        document.addEventListener('DOMContentLoaded', () => {
            if (postMessage) {
                postMessage.addEventListener('input', syncMessageAndHashtags);
            }
            if (postHashtags) {
                postHashtags.addEventListener('input', syncMessageAndHashtags);
            }
            
            if (pageSelect) {
                pageSelect.addEventListener('change', () => {
                    updateInstagramOption();
                    syncPageInfo();
                });
                updateInstagramOption();
                syncPageInfo();
            }

            if (mediaInput) {
                mediaInput.addEventListener('change', function(e) {
                    const files = [...e.target.files];
                    if (files.length > 0) {
                        if (uploadPlaceholder) uploadPlaceholder.style.display = 'none';
                    } else {
                        if (uploadPlaceholder) uploadPlaceholder.style.display = 'block';
                    }
                    
                    if (previewContainer) previewContainer.innerHTML = '';
                    
                    if (files.length === 0) {
                        updateMockupMedia([]);
                        return;
                    }

                    const mediaSources = new Array(files.length);
                    files.forEach((file, index) => {
                        const reader = new FileReader();
                        reader.onload = function(event) {
                            const div = document.createElement('div');
                            div.style.cssText = 'width: 140px; height: 140px; border-radius: 20px; overflow: hidden; border: 3px solid var(--accent); box-shadow: 0 10px 20px var(--accent-soft);';
                            if (file.type.startsWith('image/')) {
                                div.innerHTML = `<img src="${event.target.result}" style="width: 100%; height: 100%; object-fit: cover;">`;
                            } else {
                                div.innerHTML = `<div style="width: 100%; height: 100%; background: var(--nav-active); display: flex; align-items: center; justify-content: center;"><i data-lucide="video" color="var(--accent)" size="32"></i></div>`;
                                lucide.createIcons();
                            }
                            if (previewContainer) previewContainer.appendChild(div);

                            mediaSources[index] = {
                                src: event.target.result,
                                type: file.type
                            };
                            
                            updateMockupMedia(mediaSources.filter(Boolean));
                        };
                        reader.readAsDataURL(file);
                    });
                });
            }

            // Sync initial state values on load
            syncMessageAndHashtags();
        });

        // AI Assistant Modal Integration JS
        document.addEventListener('DOMContentLoaded', function() {
            const aiModal = document.getElementById('ai-modal');
            const openAiBtn = document.getElementById('open-ai-btn');
            const closeAiModal = document.getElementById('close-ai-modal');
            const generateAiBtn = document.getElementById('generate-ai-btn');
        
        const aiPrompt = document.getElementById('ai-prompt');
        const aiLanguage = document.getElementById('ai-language');
        const aiDensity = document.getElementById('ai-density');
        
        const aiPlaceholder = document.getElementById('ai-placeholder');
        const aiLoading = document.getElementById('ai-loading');
        const aiError = document.getElementById('ai-error');
        const aiErrorMsg = document.getElementById('ai-error-msg');
        const aiSuccess = document.getElementById('ai-success');
        const aiCaptionText = document.getElementById('ai-caption-text');
        const aiHashtagsText = document.getElementById('ai-hashtags-text');
        const aiHashtagsSection = document.getElementById('ai-hashtags-section');
        const applyAllBtn = document.getElementById('apply-all-btn');
        const postHashtags = document.getElementById('post-hashtags');

        // Store active values
        let generatedCaption = '';
        let generatedHashtags = [];

        // Open Modal
        openAiBtn.addEventListener('click', () => {
            aiModal.style.display = 'flex';
            setTimeout(() => {
                aiModal.style.opacity = '1';
            }, 10);
            aiPrompt.focus();
        });

        // Close Modal
        function hideAiModal() {
            aiModal.style.opacity = '0';
            setTimeout(() => {
                aiModal.style.display = 'none';
            }, 300);
        }
        closeAiModal.addEventListener('click', hideAiModal);
        
        // Close modal when clicking outside
        aiModal.addEventListener('click', (e) => {
            if (e.target === aiModal) {
                hideAiModal();
            }
        });

        // Tone Radio Selection Handler
        const toneCards = document.querySelectorAll('.ai-tone-card');
        toneCards.forEach(card => {
            // Set initial active state based on checked input
            const input = card.querySelector('input');
            if (input && input.checked) {
                card.classList.add('active');
            }

            card.addEventListener('click', () => {
                toneCards.forEach(c => c.classList.remove('active'));
                card.classList.add('active');
                if (input) input.checked = true;
            });
        });

        // Copy to clipboard helper
        window.copyToClipboard = function(elementId, button) {
            const text = document.getElementById(elementId).innerText;
            navigator.clipboard.writeText(text).then(() => {
                const originalContent = button.innerHTML;
                button.innerHTML = `<i data-lucide="check" style="width: 12px; height: 12px;"></i> Copied!`;
                button.style.color = '#10b981';
                lucide.createIcons();
                setTimeout(() => {
                    button.innerHTML = originalContent;
                    button.style.color = 'var(--text-muted)';
                    lucide.createIcons();
                }, 2000);
            }).catch(err => {
                console.error('Failed to copy text: ', err);
            });
        };

        // Generate AI Caption handler
        generateAiBtn.addEventListener('click', async () => {
            const promptVal = aiPrompt.value.trim();
            if (!promptVal) {
                aiPrompt.style.borderColor = '#ef4444';
                setTimeout(() => {
                    aiPrompt.style.borderColor = 'var(--glass-border)';
                }, 2000);
                aiPrompt.focus();
                return;
            }

            // Get selected tone
            let selectedTone = 'professional';
            toneCards.forEach(card => {
                const input = card.querySelector('input');
                if (input && input.checked) {
                    selectedTone = input.value;
                }
            });

            const langVal = aiLanguage.value;
            const densityVal = aiDensity.value;

            // Switch to loading state
            aiPlaceholder.style.display = 'none';
            aiSuccess.style.display = 'none';
            aiError.style.display = 'none';
            aiLoading.style.display = 'flex';
            generateAiBtn.disabled = true;
            generateAiBtn.style.opacity = '0.7';

            try {
                const response = await fetch("{{ route('ai.generate') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        prompt: promptVal,
                        tone: selectedTone,
                        language: langVal,
                        density: densityVal
                    })
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.error || 'Server responded with an error.');
                }

                // Success! Set global variables
                generatedCaption = data.caption || '';
                generatedHashtags = data.hashtags || [];

                // Update UI elements
                aiCaptionText.innerText = generatedCaption;
                
                if (densityVal === 'none' || generatedHashtags.length === 0) {
                    aiHashtagsSection.style.display = 'none';
                } else {
                    aiHashtagsSection.style.display = 'block';
                    aiHashtagsText.innerText = generatedHashtags.map(tag => tag.startsWith('#') ? tag : '#' + tag).join(' ');
                }

                aiLoading.style.display = 'none';
                aiSuccess.style.display = 'flex';
                lucide.createIcons();

            } catch (err) {
                console.error(err);
                aiErrorMsg.innerText = err.message || 'An unexpected error occurred. Please verify GEMINI_API_KEY is defined in .env.';
                aiLoading.style.display = 'none';
                aiError.style.display = 'flex';
                lucide.createIcons();
            } finally {
                generateAiBtn.disabled = false;
                generateAiBtn.style.opacity = '1';
            }
        });

        // Apply All Injections
        applyAllBtn.addEventListener('click', () => {
            if (generatedCaption) {
                // Set message field
                postMessage.value = generatedCaption;
            }

            if (generatedHashtags && generatedHashtags.length > 0) {
                // Clean and set hashtags
                const formattedTags = generatedHashtags.map(tag => tag.replace(/^#/, '')).join(' ');
                postHashtags.value = formattedTags;
            }

            // Sync message to the live mockup
            syncMessageAndHashtags();

            // Close modal
            hideAiModal();
            
            // Pulse the editor textarea briefly to draw attention
            const container = postMessage.parentElement;
            const originalBorder = container.style.borderColor;
            container.style.borderColor = '#10b981';
            container.style.boxShadow = '0 0 20px rgba(16, 185, 129, 0.2)';
            setTimeout(() => {
                container.style.borderColor = originalBorder;
                container.style.boxShadow = 'none';
            }, 1500);
        });
    });
    </script>
@endsection

