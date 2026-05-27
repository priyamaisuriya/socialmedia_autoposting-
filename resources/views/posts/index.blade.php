@extends('layouts.premium')

@section('content')
    <!-- Simple-DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css">


    <div style="margin-bottom: 2.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="font-size: 2.25rem; font-weight: 800; letter-spacing: -0.03em; background: linear-gradient(135deg, var(--text-main), var(--accent)); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                My Posts Hub
            </h1>
            <p style="color: var(--text-muted); font-size: 0.95rem; margin-top: 0.25rem;">Monitor live engagement metrics, reactions, and statuses for all your published content.</p>
        </div>
        <a href="{{ route('posts.create') }}" class="btn-primary">
            <i data-lucide="plus-circle" style="width: 18px; height: 18px;"></i> Create New Post
        </a>
    </div>


    <!-- Tabs for Active / Archived -->
    <div style="display: flex; gap: 1rem; margin-bottom: 1.5rem; border-bottom: 1px solid var(--glass-border); padding-bottom: 0.5rem;">
        <a href="{{ route('posts.index') }}" style="text-decoration: none; font-weight: 800; padding: 0.5rem 1rem; border-radius: 8px; color: {{ !request('archived') ? 'var(--accent)' : 'var(--text-muted)' }}; background: {{ !request('archived') ? 'rgba(59, 130, 246, 0.1)' : 'transparent' }}; transition: all 0.2s;">
            Active Posts
        </a>
        <a href="{{ route('posts.index', ['archived' => 1]) }}" style="text-decoration: none; font-weight: 800; padding: 0.5rem 1rem; border-radius: 8px; color: {{ request('archived') ? 'var(--accent)' : 'var(--text-muted)' }}; background: {{ request('archived') ? 'rgba(59, 130, 246, 0.1)' : 'transparent' }}; transition: all 0.2s;">
            Archived Posts
        </a>
    </div>

    <!-- Posts Index Content -->
    <div class="premium-card" style="padding: 2.5rem;">
        <h2 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 10px;">
            <i data-lucide="list" style="color: var(--accent); width: 20px; height: 20px;"></i> {{ request('archived') ? 'Archived Content' : 'All Published Content' }}
        </h2>

        <!-- DataTables Container wrapper (No scrollbar allowed) -->
        <div class="datatable-wrapper">
            <table id="posts-table">
                <thead>
                    <tr>
                        <th>Post Content</th>
                        <th>Page</th>
                        <th style="text-align: center;">Likes</th>
                        <th style="text-align: center;">Comments</th>
                        <th>Status</th>
                        <th class="hide-mobile">Date</th>
                        <th style="text-align: right; min-width: 160px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($posts as $post)
                        @php $firstMedia = $post->media->first(); @endphp
                        <tr>
                            <!-- Post Content with media thumbnail -->
                            <td>
                                <div style="display: flex; gap: 12px; align-items: center;">
                                    @if($firstMedia && $firstMedia->media_type === 'image')
                                        <img src="{{ asset('storage/' . $firstMedia->file_path) }}" style="width: 44px; height: 44px; border-radius: 10px; object-fit: cover; border: 1px solid var(--glass-border); flex-shrink: 0;" />
                                    @elseif($firstMedia && $firstMedia->media_type === 'video')
                                        <div style="width: 44px; height: 44px; border-radius: 10px; background: #000; display: flex; align-items: center; justify-content: center; border: 1px solid var(--glass-border); flex-shrink: 0; position: relative;">
                                            <i data-lucide="play" style="width: 12px; height: 12px; color: white; fill: white;"></i>
                                        </div>
                                    @else
                                        <div style="width: 44px; height: 44px; border-radius: 10px; background: var(--bg-main); display: flex; align-items: center; justify-content: center; border: 1px solid var(--glass-border); flex-shrink: 0; color: var(--text-muted);">
                                            <i data-lucide="file-text" style="width: 16px; height: 16px;"></i>
                                        </div>
                                    @endif
                                    <div style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 220px;">
                                        <a href="{{ route('posts.show', $post->id) }}" style="font-weight: 700; font-size: 0.85rem; color: var(--text-main); text-decoration: none; transition: 0.2s;" onmouseover="this.style.color='var(--accent)'" onmouseout="this.style.color='var(--text-main)'" title="{{ $post->message }}">
                                            {{ $post->message }}
                                        </a>
                                        <span style="font-size: 0.65rem; color: var(--text-muted); display: block; margin-top: 2px;">
                                            @if($post->facebook_post_id)
                                                ID: {{ Str::limit($post->facebook_post_id, 12) }}
                                            @else
                                                Not Published
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <!-- Facebook Page -->
                            <td>
                                <span style="display: inline-flex; align-items: center; gap: 6px; background: rgba(59, 130, 246, 0.08); color: var(--accent); padding: 4px 8px; border-radius: 8px; font-size: 0.75rem;">
                                    <i data-lucide="facebook" style="width: 10px; height: 10px; fill: var(--accent); border: none;"></i>
                                    {{ $post->facebookPage->name ?? 'Unknown Page' }}
                                </span>
                            </td>

                            <!-- Likes -->
                            <td style="text-align: center; font-weight: 800; font-size: 0.9rem; color: #f43f5e;">
                                @if($post->hide_likes)
                                    <span style="font-size: 0.7rem; color: var(--text-muted); background: var(--bg-main); padding: 2px 6px; border-radius: 6px; border: 1px solid var(--glass-border);"><i data-lucide="eye-off" style="width:10px;height:10px;"></i> Hidden</span>
                                @else
                                    {{ number_format(max($post->dynamic_likes ?? 0, $post->likes_count ?? 0)) }}
                                @endif
                            </td>

                            <!-- Comments -->
                            <td style="text-align: center; font-weight: 800; font-size: 0.9rem; color: var(--accent);">
                                @if($post->hide_comments)
                                    <span style="font-size: 0.7rem; color: var(--text-muted); background: var(--bg-main); padding: 2px 6px; border-radius: 6px; border: 1px solid var(--glass-border);"><i data-lucide="eye-off" style="width:10px;height:10px;"></i> Hidden</span>
                                @else
                                    {{ number_format(max($post->dynamic_comments ?? 0, $post->comments->count())) }}
                                @endif
                            </td>

                            <!-- Status Badge -->
                            <td>
                                <div style="display: flex; flex-direction: column; gap: 4px;">
                                    @if($post->post_to_facebook)
                                        @if($post->status === 'success')
                                            <span style="background: rgba(16, 185, 129, 0.1); color: #10b981; font-size: 0.65rem; font-weight: 800; padding: 2px 6px; border-radius: 6px; display: inline-flex; align-items: center; gap: 4px; border: 1px solid rgba(16, 185, 129, 0.2); width: fit-content;">
                                                FB: Published
                                            </span>
                                        @elseif($post->status === 'failed')
                                            <span style="background: rgba(239, 68, 68, 0.1); color: #ef4444; font-size: 0.65rem; font-weight: 800; padding: 2px 6px; border-radius: 6px; display: inline-flex; align-items: center; gap: 4px; border: 1px solid rgba(239, 68, 68, 0.2); width: fit-content;">
                                                FB: Failed
                                            </span>
                                        @else
                                            <span style="background: rgba(245, 158, 11, 0.1); color: #f59e0b; font-size: 0.65rem; font-weight: 800; padding: 2px 6px; border-radius: 6px; display: inline-flex; align-items: center; gap: 4px; border: 1px solid rgba(245, 158, 11, 0.2); width: fit-content;">
                                                FB: Pending
                                            </span>
                                        @endif
                                    @endif

                                    @if($post->post_to_instagram)
                                        @if($post->instagram_status === 'success')
                                            <span style="background: rgba(186, 104, 200, 0.1); color: #ba68c8; font-size: 0.65rem; font-weight: 800; padding: 2px 6px; border-radius: 6px; display: inline-flex; align-items: center; gap: 4px; border: 1px solid rgba(186, 104, 200, 0.2); width: fit-content;">
                                                IG: Published
                                            </span>
                                        @elseif($post->instagram_status === 'failed')
                                            <span style="background: rgba(239, 68, 68, 0.1); color: #ef4444; font-size: 0.65rem; font-weight: 800; padding: 2px 6px; border-radius: 6px; display: inline-flex; align-items: center; gap: 4px; border: 1px solid rgba(239, 68, 68, 0.2); width: fit-content;" title="{{ $post->instagram_error }}">
                                                IG: Failed
                                            </span>
                                        @else
                                            <span style="background: rgba(245, 158, 11, 0.1); color: #f59e0b; font-size: 0.65rem; font-weight: 800; padding: 2px 6px; border-radius: 6px; display: inline-flex; align-items: center; gap: 4px; border: 1px solid rgba(245, 158, 11, 0.2); width: fit-content;">
                                                IG: Pending
                                            </span>
                                        @endif
                                    @endif
                                </div>
                            </td>

                            <!-- Date Published -->
                            <td class="hide-mobile" style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600;">
                                {{ $post->created_at->diffForHumans() }}
                            </td>

                            <!-- Actions - 100% Active & Clickable -->
                            <td style="text-align: right;">
                                <div style="display: inline-flex; gap: 8px; align-items: center;">
                                    <!-- Dedicated Show Page Link -->
                                    <a href="{{ route('posts.show', $post->id) }}" class="action-icon-btn view" title="View Post Details & Live Analytics">
                                        <i data-lucide="eye" style="width: 16px; height: 16px;"></i>
                                    </a>

                                    <!-- Comment Thread Link -->
                                    @if($post->status === 'success' && !$post->hide_comments)
                                        <a href="{{ route('comments.index', ['post_id' => $post->id]) }}" class="action-icon-btn view" style="color: var(--accent);" title="Manage Comments">
                                            <i data-lucide="message-square" style="width: 16px; height: 16px;"></i>
                                        </a>
                                    @endif

                                    <!-- Archive/Unarchive Form (Facebook Only) -->
                                    @if($post->facebook_post_id)
                                        <form action="{{ route('posts.archive', $post->id) }}" method="POST" style="display: inline-block; margin: 0; padding: 0;">
                                            @csrf
                                            <button type="submit" class="action-icon-btn edit" title="{{ $post->is_fb_archived ? 'Unarchive Facebook Post' : 'Archive Facebook Post' }}">
                                                <i data-lucide="{{ $post->is_fb_archived ? 'folder-up' : 'folder-down' }}" style="width: 16px; height: 16px;"></i>
                                            </button>
                                        </form>
                                    @endif

                                    <!-- Delete Button Form -->
                                    <form action="{{ route('posts.destroy', $post->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this post? This will delete it on Facebook as well!')" style="display: inline-block; margin: 0; padding: 0;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-icon-btn delete" title="Delete Post">
                                            <i data-lucide="trash-2" style="width: 16px; height: 16px;"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 4rem 1rem; color: var(--text-muted);">
                                <i data-lucide="file-text" style="width: 48px; height: 48px; opacity: 0.3; margin-bottom: 1rem; display: block; margin-left: auto; margin-right: auto;"></i>
                                <div style="font-weight: 800; font-size: 1.1rem; color: var(--text-main); margin-bottom: 4px;">No posts published yet!</div>
                                <p style="font-size: 0.8rem; max-width: 320px; margin: 0 auto;">Create your first premium post using the publisher to view metrics here.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Simple-DataTables Core & Active Icon Restoration Script -->
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/umd/simple-datatables.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const postsTable = document.getElementById("posts-table");
            if (postsTable) {
                const dt = new simpleDatatables.DataTable(postsTable, {
                    searchable: true,
                    fixedHeight: false,
                    perPage: 10,
                    labels: {
                        placeholder: "Search posts...",
                        noRows: "No matching posts found",
                        info: "Showing {start} to {end} of {rows} posts",
                    }
                });

                // Ultimate Restoration function to make sure all icons render on every single table redraw
                const restoreIconsAndStyles = () => {
                    lucide.createIcons();
                };

                // Bind to all DataTable events
                dt.on("datatable.init", restoreIconsAndStyles);
                dt.on("datatable.page", restoreIconsAndStyles);
                dt.on("datatable.sort", restoreIconsAndStyles);
                dt.on("datatable.search", restoreIconsAndStyles);
                dt.on("datatable.update", restoreIconsAndStyles);

                // Initial load
                setTimeout(restoreIconsAndStyles, 100);
            }
        });

    </script>
@endsection

