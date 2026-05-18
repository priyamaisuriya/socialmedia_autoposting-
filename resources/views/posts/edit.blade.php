@extends('layouts.premium')

@section('content')
    <div style="margin-bottom: 2.5rem;">
        <!-- Breadcrumb Navigation -->
        <div style="display: flex; align-items: center; gap: 8px; font-size: 0.8rem; color: var(--text-muted); font-weight: 700; margin-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 0.05em;">
            <a href="{{ route('posts.index') }}" style="color: var(--text-muted); text-decoration: none; transition: 0.2s;" onmouseover="this.style.color='var(--accent)'" onmouseout="this.style.color='var(--text-muted)'">Posts</a>
            <i data-lucide="chevron-right" style="width: 12px; height: 12px;"></i>
            <a href="{{ route('posts.show', $post->id) }}" style="color: var(--text-muted); text-decoration: none; transition: 0.2s;" onmouseover="this.style.color='var(--accent)'" onmouseout="this.style.color='var(--text-muted)'">Post Details</a>
            <i data-lucide="chevron-right" style="width: 12px; height: 12px;"></i>
            <span style="color: var(--accent);">Edit Post</span>
        </div>
        
        <h1 style="font-size: 2rem; font-weight: 800; letter-spacing: -0.03em;">Modify Post Content</h1>
    </div>

    <div style="max-width: 800px; margin: 0 auto;">
        
        <!-- Context Warning Info Banner -->
        <div style="background: rgba(245, 158, 11, 0.07); border: 1px solid rgba(245, 158, 11, 0.2); padding: 1.25rem 2rem; border-radius: 20px; margin-bottom: 2rem; display: flex; align-items: flex-start; gap: 12px;">
            <div style="width: 32px; height: 32px; border-radius: 10px; background: rgba(245, 158, 11, 0.15); display: flex; align-items: center; justify-content: center; color: #f59e0b; flex-shrink: 0; margin-top: 2px;">
                <i data-lucide="alert-triangle" style="width: 16px; height: 16px;"></i>
            </div>
            <div>
                <h4 style="font-size: 0.9rem; font-weight: 800; color: #f59e0b; margin-bottom: 4px;">Important Note on Meta Editing Constraints</h4>
                <p style="margin: 0; font-size: 0.8125rem; color: var(--text-muted); line-height: 1.4; font-weight: 500;">
                    Facebook allows updating the **text message** of page posts, which will be synchronized instantly. However, Meta **does not support changing, replacing, or removing media files (images/videos)** after a post has been published.
                </p>
            </div>
        </div>

        <!-- Edit Form Card -->
        <div class="premium-card" style="padding: 3rem;">
            <form action="{{ route('posts.update', $post->id) }}" method="POST" style="display: flex; flex-direction: column; gap: 2rem;">
                @csrf
                @method('PATCH')

                <!-- Post Target Page Info (Disabled) -->
                <div>
                    <label style="font-size: 0.75rem; font-weight: 800; text-transform: uppercase; color: var(--text-muted); margin-bottom: 0.5rem; display: block;">Target Facebook Page</label>
                    <div style="background: var(--nav-active); border: 1px solid var(--glass-border); padding: 1rem 1.25rem; border-radius: 16px; display: flex; align-items: center; gap: 10px; font-weight: 700; font-size: 0.9rem; color: var(--text-main); opacity: 0.85;">
                        <i data-lucide="facebook" style="width: 16px; height: 16px; fill: var(--accent); color: var(--accent); border: none;"></i>
                        {{ $post->facebookPage->name ?? 'Unknown Page' }}
                        <span style="font-size: 0.65rem; color: var(--text-muted); font-weight: 600; margin-left: auto; border: 1px solid var(--glass-border); padding: 2px 8px; border-radius: 6px; background: var(--bg-main);">READ ONLY</span>
                    </div>
                </div>

                <!-- Text Area Content Field -->
                <div>
                    <label for="post-message" style="font-size: 0.75rem; font-weight: 800; text-transform: uppercase; color: var(--text-muted); margin-bottom: 0.5rem; display: block; display: flex; justify-content: space-between;">
                        <span>Post Message</span>
                        <span style="font-weight: 500; font-size: 0.7rem; color: var(--accent);">Markdown & Hashtags Allowed</span>
                    </label>
                    <textarea id="post-message" name="message" rows="8" required placeholder="Type your post message content here..." style="width: 100%; padding: 1.25rem; border-radius: 18px; border: 1px solid var(--glass-border); background: var(--nav-active); color: var(--text-main); outline: none; font-size: 0.95rem; font-weight: 500; resize: vertical; transition: 0.3s;" onfocus="this.style.borderColor='var(--accent)'; this.style.boxShadow='0 0 15px var(--accent-glow)'" onblur="this.style.borderColor='var(--glass-border)'; this.style.boxShadow='none'">{{ $post->message }}</textarea>
                </div>

                <!-- Attached Media Preview (ReadOnly Display) -->
                @php $firstMedia = $post->media->first(); @endphp
                @if($firstMedia)
                    <div>
                        <label style="font-size: 0.75rem; font-weight: 800; text-transform: uppercase; color: var(--text-muted); margin-bottom: 0.5rem; display: block;">Attached Media Preview (Non-Editable)</label>
                        <div style="background: var(--nav-active); border: 1px solid var(--glass-border); padding: 1.25rem; border-radius: 18px; display: flex; gap: 1rem; align-items: center;">
                            @if($firstMedia->media_type === 'video')
                                <div style="width: 80px; height: 80px; border-radius: 12px; background: #000; display: flex; align-items: center; justify-content: center; border: 1px solid var(--glass-border); flex-shrink: 0; position: relative;">
                                    <i data-lucide="play" style="width: 20px; height: 20px; color: white; fill: white;"></i>
                                </div>
                            @else
                                <img src="{{ asset('storage/' . $firstMedia->file_path) }}" style="width: 80px; height: 80px; border-radius: 12px; object-fit: cover; border: 1px solid var(--glass-border); flex-shrink: 0;" />
                            @endif
                            <div>
                                <div style="font-weight: 700; font-size: 0.85rem; color: var(--text-main); text-transform: capitalize;">Attached {{ $firstMedia->media_type }}</div>
                                <span style="font-size: 0.7rem; color: var(--text-muted); display: block; margin-top: 2px;">This media is locked on Meta server.</span>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Form Action Buttons -->
                <div style="display: flex; justify-content: flex-end; gap: 16px; border-top: 1px solid var(--glass-border); padding-top: 2rem; margin-top: 1rem;">
                    <a href="{{ route('posts.show', $post->id) }}" class="btn-primary" style="background: var(--nav-active); color: var(--text-main); box-shadow: none; border: 1px solid var(--glass-border);" onmouseover="this.style.borderColor='var(--accent)'" onmouseout="this.style.borderColor='var(--glass-border)'">
                        Cancel & Return
                    </a>
                    
                    <button type="submit" class="btn-primary" style="background: linear-gradient(135deg, #10b981, #059669); box-shadow: 0 10px 20px rgba(16, 185, 129, 0.15);">
                        <i data-lucide="check-circle" style="width: 18px; height: 18px;"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
