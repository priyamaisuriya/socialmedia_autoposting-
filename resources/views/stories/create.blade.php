@extends('layouts.premium')

@section('content')
    <div style="max-width: 1000px; margin: 0 auto; animation: fadeInUp 0.6s ease-out;">
        <div style="margin-bottom: 2.5rem; text-align: center;">
            <h1 style="font-size: 2.5rem; font-weight: 900; letter-spacing: -0.04em; margin-bottom: 0.5rem;">Create Your Story</h1>
            <p style="color: var(--text-muted); font-size: 1.1rem;">Publish short-form visual content (Stories) to Facebook and Instagram.</p>
        </div>

        <form action="{{ route('stories.store') }}" method="POST" enctype="multipart/form-data" class="premium-card" style="padding: 3rem; border-width: 2px;">
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
                                    (IG Active: {{ $page->instagram_username }})
                                @else
                                    (IG Inactive: {{ $page->instagram_username }})
                                @endif
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>

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
                            <div style="font-weight: 700; color: var(--text-main); font-size: 1.1rem;">Facebook Story</div>
                            <div style="font-size: 0.85rem; color: var(--text-muted);">Publish post as Facebook Story</div>
                        </div>
                        <div id="fb-card-check" style="color: var(--accent);">
                            <i data-lucide="check-circle-2" style="width: 20px; height: 20px;"></i>
                        </div>
                    </label>

                    <!-- Instagram Platform Card -->
                    <label id="ig-card" style="position: relative; display: flex; align-items: center; gap: 1rem; padding: 1.5rem; background: var(--bg-main); border: 2px solid var(--accent); border-radius: 20px; cursor: pointer; transition: 0.3s; box-shadow: 0 10px 20px rgba(0,0,0,0.05);">
                        <input type="checkbox" name="post_to_instagram" id="post-to-instagram" checked value="1" style="display: none;" onchange="togglePlatformCard('ig-card', this)">
                        <div id="ig-icon-container" style="background: rgba(225, 48, 108, 0.1); width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #e1306c;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 24px; height: 24px; flex-shrink: 0;"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
                        </div>
                        <div style="flex: 1;">
                            <div style="font-weight: 700; color: var(--text-main); font-size: 1.1rem; display: flex; align-items: center; gap: 6px;">
                                Instagram Story
                            </div>
                            <div id="ig-status-text" style="font-size: 0.85rem; color: var(--text-muted);">Publish post as Instagram Story</div>
                        </div>
                        <div id="ig-card-check" style="color: var(--accent);">
                            <i data-lucide="check-circle-2" style="width: 20px; height: 20px;"></i>
                        </div>
                    </label>
                </div>
            </div>

            <div style="margin-bottom: 2rem; background: rgba(59, 130, 246, 0.05); padding: 1.25rem; border-radius: 16px; border: 1px solid rgba(59, 130, 246, 0.15); display: flex; align-items: center; gap: 1rem;">
                <div style="background: rgba(59, 130, 246, 0.1); width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #3b82f6;">
                    <i data-lucide="info" style="width: 20px; height: 20px;"></i>
                </div>
                <div style="flex: 1;">
                    <h4 style="font-size: 0.95rem; font-weight: 800; color: var(--text-main); margin: 0 0 0.25rem 0;">Stories & Captions</h4>
                    <p style="font-size: 0.85rem; color: var(--text-muted); margin: 0;">Stories via API do not support text captions, hashtags, or scheduled posting. Only the visual media (Image/Video) will be published.</p>
                </div>
            </div>

            <!-- Media Upload Area -->
            <div style="margin-bottom: 4rem;">
                <label style="display: block; font-weight: 800; margin-bottom: 1rem; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.1em; color: var(--accent);">Story Media (Photo or Video)</label>
                <div style="position: relative; border: 3px dashed var(--glass-border); border-radius: 30px; padding: 4rem; text-align: center; background: var(--bg-main); transition: 0.4s; overflow: hidden;" id="drop-zone">
                    <div id="upload-placeholder">
                        <i data-lucide="image-plus" size="60" style="color: var(--accent); margin-bottom: 1.5rem; opacity: 0.8;"></i>
                        <h3 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 0.5rem;">Drop your Story media here</h3>
                        <p style="color: var(--text-muted);">Supports vertical (9:16) Images and MP4 Videos (Max 15-60s)</p>
                    </div>
                    <!-- Note: Only single media upload for stories per API call -->
                    <input type="file" name="media" required id="media-input" style="position: absolute; inset: 0; opacity: 0; cursor: pointer; z-index: 10;" accept="image/jpeg,image/png,video/mp4,video/quicktime">
                    <div id="preview-container" style="display: flex; flex-wrap: wrap; gap: 1.25rem; justify-content: center; position: relative; z-index: 5;"></div>
                </div>
            </div>

            <!-- Submit Button Area -->
            <div style="display: flex; gap: 1.5rem; align-items: center; justify-content: flex-end; padding-top: 3rem; border-top: 2px solid var(--glass-border);">
                <a href="{{ route('dashboard') }}" style="color: var(--text-muted); font-weight: 800; text-decoration: none; font-size: 1.1rem; padding: 1rem 2rem; border-radius: 16px; transition: 0.3s;">Cancel</a>
                
                <button type="submit" id="submit-btn" class="btn-primary" style="padding: 1.25rem 3.5rem; font-size: 1.1rem; border-radius: 20px; min-width: 240px; justify-content: center; position: relative; z-index: 20;">
                    <span id="btn-text" style="display: flex; align-items: center; gap: 0.75rem;">
                        <i data-lucide="send-horizontal" size="20"></i> Publish Story Now
                    </span>
                    <span id="btn-loader" style="display: none; align-items: center; gap: 0.75rem;">
                        <i data-lucide="loader-2" class="spin" size="20"></i> Publishing...
                    </span>
                </button>
            </div>
        </form>
    </div>

    <style>
        .spin { animation: spin 1s linear infinite; }
        @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
        #submit-btn:active { transform: scale(0.98); }

        #drop-zone:hover {
            border-color: var(--accent);
            background: rgba(99, 102, 241, 0.03);
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }
        
        .media-preview-wrapper {
            position: relative;
            width: 140px;
            height: 140px;
            border-radius: 16px;
            overflow: hidden;
            border: 2px solid var(--glass-border);
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            animation: zoomIn 0.3s ease;
        }

        .media-preview-wrapper img, .media-preview-wrapper video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        @keyframes zoomIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }
    </style>

    <script>
        document.querySelector('form').addEventListener('submit', function() {
            const btn = document.getElementById('submit-btn');
            const text = document.getElementById('btn-text');
            const loader = document.getElementById('btn-loader');
            
            btn.style.opacity = '0.7';
            btn.style.pointerEvents = 'none';
            text.style.display = 'none';
            loader.style.display = 'flex';
        });

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

        const mediaInput = document.getElementById('media-input');
        const previewContainer = document.getElementById('preview-container');
        const uploadPlaceholder = document.getElementById('upload-placeholder');
        const dropZone = document.getElementById('drop-zone');

        mediaInput.addEventListener('change', function(e) {
            previewContainer.innerHTML = ''; // Clear previous
            
            if (this.files && this.files.length > 0) {
                uploadPlaceholder.style.display = 'none';
                
                const file = this.files[0];
                const wrapper = document.createElement('div');
                wrapper.className = 'media-preview-wrapper';
                
                if (file.type.startsWith('image/')) {
                    const img = document.createElement('img');
                    img.src = URL.createObjectURL(file);
                    wrapper.appendChild(img);
                } else if (file.type.startsWith('video/')) {
                    const video = document.createElement('video');
                    video.src = URL.createObjectURL(file);
                    video.muted = true;
                    video.play();
                    wrapper.appendChild(video);
                }
                
                previewContainer.appendChild(wrapper);
            } else {
                uploadPlaceholder.style.display = 'block';
            }
        });
    </script>
@endsection
