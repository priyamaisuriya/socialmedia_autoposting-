
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
                const icon = card.querySelector('i');
                if (!card || !icon) return;
                
                if (t === type) {
                    card.style.border = '2px solid var(--accent)';
                    card.style.boxShadow = '0 5px 15px rgba(0,0,0,0.05)';
                    icon.style.color = 'var(--accent)';
                } else {
                    card.style.border = '2px solid var(--glass-border)';
                    card.style.boxShadow = 'none';
                    icon.style.color = 'var(--text-muted)';
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
                igCard.style.cursor = 'pointer';
                igCheckbox.disabled = false;
                igCheckbox.checked = true;
                
                if (igUsernameBadge) {
                    igUsernameBadge.textContent = '@' + igUsername;
                    igUsernameBadge.style.display = 'inline-block';
                }
                if (igStatusText) igStatusText.textContent = 'Publish to linked Instagram account';
                
                togglePlatformCard('ig-card', igCheckbox);
            } else {
                igCard.style.opacity = '0.5';
                igCard.style.cursor = 'not-allowed';
                igCheckbox.disabled = true;
                igCheckbox.checked = false;
                
                if (igUsernameBadge) igUsernameBadge.style.display = 'none';
                if (igUsername && igUsername.trim() !== '') {
                    if (igStatusText) igStatusText.textContent = 'Instagram inactive. Activate on Accounts page.';
                } else {
                    if (igStatusText) igStatusText.textContent = 'No connected Instagram account';
                }
                
                togglePlatformCard('ig-card', igCheckbox);
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
    