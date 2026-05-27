@extends('layouts.premium')

@section('content')
<!-- Simple-DataTables CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css">

<div class="page-container" style="animation: fadeInUp 0.5s ease-out;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h1 style="font-size: 2.2rem; font-weight: 800; letter-spacing: -0.03em; margin-bottom: 0.5rem;">My Stories</h1>
            <p style="color: var(--text-muted); font-size: 1rem;">View your past story uploads</p>
        </div>
        <a href="{{ route('stories.create') }}" class="btn-primary" style="padding: 0.8rem 1.5rem; display: flex; align-items: center; gap: 0.5rem; border-radius: 12px; font-weight: 600;">
            <i data-lucide="plus" style="width: 18px; height: 18px;"></i> Upload Story
        </a>
    </div>

    <div class="premium-card" style="padding: 2.5rem;">
        <h2 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 10px;">
            <i data-lucide="history" style="color: var(--accent); width: 20px; height: 20px;"></i> All Published Stories
        </h2>

        <div class="datatable-wrapper">
            <table id="stories-table">
                <thead>
                    <tr>
                        <th>Media</th>
                        <th>Page</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th style="text-align: right; min-width: 100px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stories as $story)
                        <tr>
                            <td>
                                @if($story->media_path)
                                    <div style="width: 60px; height: 60px; border-radius: 10px; overflow: hidden; background: var(--bg-hover); position: relative;">
                                        @if(str_contains(strtolower($story->media_path), '.mp4') || str_contains(strtolower($story->media_path), '.mov'))
                                            <video src="{{ asset('storage/' . $story->media_path) }}" style="width: 100%; height: 100%; object-fit: cover;" muted></video>
                                            <div style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,0.3);">
                                                <i data-lucide="play" style="color: white; width: 20px; height: 20px; fill: white;"></i>
                                            </div>
                                        @else
                                            <img src="{{ asset('storage/' . $story->media_path) }}" style="width: 100%; height: 100%; object-fit: cover;">
                                        @endif
                                    </div>
                                @else
                                    <div style="width: 60px; height: 60px; border-radius: 10px; background: var(--bg-hover); display: flex; align-items: center; justify-content: center;">
                                        <i data-lucide="image" style="color: var(--text-muted);"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div style="font-weight: 600; color: var(--text-main);">{{ $story->facebookPage->name ?? 'Unknown Page' }}</div>
                            </td>
                            <td>
                                <div style="display: flex; flex-direction: column; gap: 4px;">
                                    <!-- FB Status -->
                                    @if($story->status !== 'skipped' && $story->status !== 'pending')
                                        @if($story->status === 'success')
                                            <span style="background: rgba(16, 185, 129, 0.1); color: #10b981; font-size: 0.65rem; font-weight: 800; padding: 2px 6px; border-radius: 6px; display: inline-flex; align-items: center; gap: 4px; border: 1px solid rgba(16, 185, 129, 0.2); width: fit-content;">
                                                FB: Published
                                            </span>
                                        @elseif($story->status === 'failed')
                                            <span style="background: rgba(239, 68, 68, 0.1); color: #ef4444; font-size: 0.65rem; font-weight: 800; padding: 2px 6px; border-radius: 6px; display: inline-flex; align-items: center; gap: 4px; border: 1px solid rgba(239, 68, 68, 0.2); width: fit-content;">
                                                FB: Failed
                                            </span>
                                        @endif
                                    @elseif($story->status === 'pending')
                                        <span style="background: rgba(245, 158, 11, 0.1); color: #f59e0b; font-size: 0.65rem; font-weight: 800; padding: 2px 6px; border-radius: 6px; display: inline-flex; align-items: center; gap: 4px; border: 1px solid rgba(245, 158, 11, 0.2); width: fit-content;">
                                            FB: Attempted
                                        </span>
                                    @endif
                                    
                                    <!-- IG Status -->
                                    @if($story->instagram_status !== 'skipped' && $story->instagram_status !== 'pending')
                                        @if($story->instagram_status === 'success')
                                            <span style="background: rgba(16, 185, 129, 0.1); color: #10b981; font-size: 0.65rem; font-weight: 800; padding: 2px 6px; border-radius: 6px; display: inline-flex; align-items: center; gap: 4px; border: 1px solid rgba(16, 185, 129, 0.2); width: fit-content;">
                                                IG: Published
                                            </span>
                                        @elseif($story->instagram_status === 'failed')
                                            <span style="background: rgba(239, 68, 68, 0.1); color: #ef4444; font-size: 0.65rem; font-weight: 800; padding: 2px 6px; border-radius: 6px; display: inline-flex; align-items: center; gap: 4px; border: 1px solid rgba(239, 68, 68, 0.2); width: fit-content;">
                                                IG: Failed
                                            </span>
                                        @endif
                                    @elseif($story->instagram_status === 'pending')
                                        <span style="background: rgba(245, 158, 11, 0.1); color: #f59e0b; font-size: 0.65rem; font-weight: 800; padding: 2px 6px; border-radius: 6px; display: inline-flex; align-items: center; gap: 4px; border: 1px solid rgba(245, 158, 11, 0.2); width: fit-content;">
                                            IG: Attempted
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                {{ $story->created_at->format('M d, Y h:i A') }}
                            </td>
                            <td style="text-align: right;">
                                <form action="{{ route('stories.destroy', $story->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to completely delete this story from Facebook, Instagram, and your dashboard?');" style="display: inline-block; margin: 0; padding: 0;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-icon-btn delete" title="Delete Story">
                                        <i data-lucide="trash-2" style="width: 16px; height: 16px;"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 4rem 1rem !important; color: var(--text-muted); border-bottom: none !important;">
                                <i data-lucide="history" style="width: 48px; height: 48px; opacity: 0.3; margin-bottom: 1rem; display: block; margin-left: auto; margin-right: auto; color: var(--accent);"></i>
                                <div style="font-weight: 800; font-size: 1.25rem; color: var(--text-main); margin-bottom: 6px;">No Stories Yet</div>
                                <p style="font-size: 0.9rem; max-width: 320px; margin: 0 auto; margin-bottom: 1.5rem; line-height: 1.5;">You haven't uploaded any stories yet. Click the button below to get started.</p>
                                <a href="{{ route('stories.create') }}" class="btn-primary" style="padding: 0.75rem 2rem; border-radius: 12px; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                                    <i data-lucide="plus-circle" style="width: 18px; height: 18px;"></i> Create First Story
                                </a>
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
            const storiesTable = document.getElementById("stories-table");
            if (storiesTable) {
                const dt = new simpleDatatables.DataTable(storiesTable, {
                    searchable: true,
                    fixedHeight: false,
                    perPage: 10,
                    labels: {
                        placeholder: "Search stories...",
                        noRows: "No matching stories found",
                        info: "Showing {start} to {end} of {rows} stories",
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
