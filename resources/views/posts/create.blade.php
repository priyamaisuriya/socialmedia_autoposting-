@extends('layouts.premium')

@section('content')
    <div style="max-width: 1000px; margin: 0 auto; animation: fadeInUp 0.6s ease-out;">
        <div style="margin-bottom: 2.5rem; text-align: center;">
            <h1 style="font-size: 2.5rem; font-weight: 900; letter-spacing: -0.04em; margin-bottom: 0.5rem;">Create Your Story</h1>
            <p style="color: var(--text-muted); font-size: 1.1rem;">Everything you need to publish a perfect Facebook post in one place.</p>
        </div>

        <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data" class="premium-card" style="padding: 4rem; border-width: 2px;">
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

            <!-- Target Page Select -->
            <div style="margin-bottom: 3rem;">
                <label style="display: block; font-weight: 800; margin-bottom: 1rem; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.1em; color: var(--accent);">Target Facebook Page</label>
                <select name="facebook_page_id" required style="width: 100%; padding: 1.25rem; border: 2px solid var(--glass-border); border-radius: 20px; background: var(--bg-main); color: var(--text-main); font-size: 1.1rem; outline: none; cursor: pointer; transition: 0.3s; box-shadow: 0 5px 15px rgba(0,0,0,0.02);">
                    @foreach($pages as $page)
                        <option value="{{ $page->id }}" {{ request('page_id') == $page->id ? 'selected' : '' }}>
                            {{ $page->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Message Area -->
            <div style="margin-bottom: 3rem;">
                <label style="display: block; font-weight: 800; margin-bottom: 1rem; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.1em; color: var(--accent);">Your Message</label>
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
                        <input type="text" name="hashtags" placeholder="social marketing tech" style="width: 100%; padding: 1.25rem 1.25rem 1.25rem 2.5rem; border: 2px solid var(--glass-border); border-radius: 18px; background: var(--bg-main); color: var(--text-main); font-size: 1rem; outline: none; transition: 0.3s;">
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
    </div>

    <style>
        .spin { animation: spin 1s linear infinite; }
        @verbatim
        @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
        @endverbatim
        #submit-btn:active { transform: scale(0.98); }
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

        function insertEmoji(emoji) {
            const start = postMessage.selectionStart;
            const text = postMessage.value;
            postMessage.value = text.substring(0, start) + emoji + text.substring(postMessage.selectionEnd);
            postMessage.focus();
            postMessage.setSelectionRange(start + emoji.length, start + emoji.length);
        }

        mediaInput.addEventListener('change', function(e) {
            if (e.target.files.length > 0) {
                uploadPlaceholder.style.display = 'none';
            } else {
                uploadPlaceholder.style.display = 'block';
            }
            
            previewContainer.innerHTML = '';
            [...e.target.files].forEach(file => {
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
                    previewContainer.appendChild(div);
                }
                reader.readAsDataURL(file);
            });
        });
    </script>
@endsection

