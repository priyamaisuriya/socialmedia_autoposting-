@extends('layouts.premium')

@section('content')
    <div style="margin-bottom: 2.5rem;">
        <h1 style="font-size: 2rem; font-weight: 800; letter-spacing: -0.02em;">My Facebook Pages</h1>
        <p style="color: var(--text-muted);">Manage and monitor all your connected business pages.</p>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 2rem;">
        @forelse($pages as $page)
            <div class="premium-card" style="display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 2rem;">
                        <div style="width: 56px; height: 56px; background: var(--accent); color: white; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1.5rem; box-shadow: 0 10px 20px var(--accent-soft);">
                            {{ substr($page->name, 0, 1) }}
                        </div>
                        <div>
                            <h3 style="font-weight: 800; margin-bottom: 2px;">{{ $page->name }}</h3>
                            <div style="font-size: 0.8125rem; color: var(--text-muted);">ID: {{ $page->page_id }}</div>
                        </div>
                    </div>

                    @php $latestPost = $page->posts()->latest()->first(); @endphp
                    <div style="padding: 1.25rem; background: var(--bg-main); border-radius: 16px; border: 1px solid var(--glass-border); margin-bottom: 2rem;">
                        <div style="font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); margin-bottom: 0.75rem;">Latest Post Content</div>
                        <p style="font-size: 0.9375rem; color: var(--text-main); line-height: 1.6; margin: 0;">
                            {{ $latestPost ? Str::limit($latestPost->message, 80) : 'No posts published yet.' }}
                        </p>
                    </div>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="padding: 6px 12px; background: rgba(16, 185, 129, 0.1); color: #10b981; border-radius: 8px; font-size: 0.75rem; font-weight: 800;">CONNECTED</span>
                    <a href="{{ route('posts.create', ['page_id' => $page->id]) }}" class="btn-primary" style="padding: 0.625rem 1.25rem; font-size: 0.875rem;">
                        <i data-lucide="plus" size="16"></i> Create Post
                    </a>
                </div>
            </div>
        @empty
            <div class="premium-card" style="grid-column: 1 / -1; text-align: center; padding: 5rem 2rem;">
                <div style="width: 80px; height: 80px; background: var(--nav-active); border-radius: 24px; display: flex; align-items: center; justify-content: center; margin: 0 auto 2rem; color: var(--text-muted);">
                    <i data-lucide="layers" size="40"></i>
                </div>
                <h2 style="font-weight: 800; margin-bottom: 1rem;">No pages found</h2>
                <p style="color: var(--text-muted); max-width: 400px; margin: 0 auto 2.5rem;">You need to connect your Facebook account to import your business pages.</p>
                <a href="{{ url('/auth/facebook') }}" class="btn-primary" style="padding: 1rem 2.5rem; position: relative; z-index: 50; display: inline-flex;">
                    <i data-lucide="facebook"></i> Connect Facebook Now
                </a>
            </div>
        @endforelse
    </div>
@endsection
