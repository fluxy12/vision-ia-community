/**
 * Vision IA Community - JavaScript
 */

(function($) {
    'use strict';

    // DOM Ready
    $(document).ready(function() {
        initCreatePostForm();
        initLikes();
        initFilters();
        initLoadMore();
        initPinning();
    });

    /**
     * Create Post Form Toggle
     */
    function initCreatePostForm() {
        const $trigger = $('#vic-open-form');
        const $form = $('#vic-post-form');
        const $cancelBtn = $('#vic-cancel-form');
        const $submitForm = $('#vic-new-post-form');
        const $submitBtn = $submitForm.find('.vic-btn-primary');
        const $fileInput = $('#vic-file-input');
        const $preview = $('#vic-attachments-preview');
        const $urlField = $('#vic-url-field');
        
        // Track selected files
        let selectedFiles = [];

        // Open form
        $trigger.on('click', function() {
            $form.slideDown(200);
            $(this).hide();
            $form.find('input[name="post_title"]').focus();
        });

        // Cancel form
        $cancelBtn.on('click', function() {
            $form.slideUp(200);
            $trigger.show();
            $submitForm[0].reset();
            selectedFiles = [];
            $preview.empty();
            $urlField.hide();
            $('.vic-upload-btn').removeClass('has-files');
        });

        // Enable submit button when form has content
        $submitForm.on('input', 'input, textarea', function() {
            updateSubmitButton();
        });
        
        function updateSubmitButton() {
            const title = $submitForm.find('input[name="post_title"]').val().trim();
            const content = $submitForm.find('textarea[name="post_content"]').val().trim();
            
            if (title && content) {
                $submitBtn.addClass('active');
            } else {
                $submitBtn.removeClass('active');
            }
        }
        
        // File input change
        $fileInput.on('change', function(e) {
            const files = Array.from(e.target.files);
            
            files.forEach(function(file) {
                // Check file size (10MB max)
                if (file.size > 10 * 1024 * 1024) {
                    alert('Le fichier "' + file.name + '" est trop volumineux (max 10MB)');
                    return;
                }
                
                // Check file type
                const allowedTypes = [
                    'image/jpeg', 'image/png', 'image/gif', 'image/webp',
                    'video/mp4', 'video/webm', 'video/ogg', 'video/quicktime',
                    'audio/mpeg', 'audio/wav', 'audio/ogg', 'audio/mp3',
                    'application/pdf'
                ];
                
                if (!allowedTypes.includes(file.type)) {
                    alert('Type de fichier non autorisé: ' + file.type);
                    return;
                }
                
                selectedFiles.push(file);
                addFilePreview(file);
            });
            
            if (selectedFiles.length > 0) {
                $('.vic-upload-btn').addClass('has-files');
            }
        });
        
        // Add file preview
        function addFilePreview(file) {
            const index = selectedFiles.indexOf(file);
            const $item = $('<div class="vic-preview-item" data-index="' + index + '"></div>');
            
            // Icon or thumbnail
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $item.prepend('<img src="' + e.target.result + '" alt="">');
                };
                reader.readAsDataURL(file);
            } else if (file.type.startsWith('video/')) {
                $item.prepend('<span>🎬</span>');
            } else if (file.type.startsWith('audio/')) {
                $item.prepend('<span>🎵</span>');
            } else if (file.type === 'application/pdf') {
                $item.prepend('<span>📄</span>');
            }
            
            $item.append('<span class="vic-filename">' + truncateFilename(file.name, 20) + '</span>');
            $item.append('<button type="button" class="vic-remove-file" data-index="' + index + '">✕</button>');
            
            $preview.append($item);
        }
        
        // Remove file
        $preview.on('click', '.vic-remove-file', function() {
            const index = parseInt($(this).data('index'));
            selectedFiles.splice(index, 1);
            $(this).closest('.vic-preview-item').remove();
            
            // Update indices
            $preview.find('.vic-preview-item').each(function(i) {
                $(this).attr('data-index', i);
                $(this).find('.vic-remove-file').attr('data-index', i);
            });
            
            if (selectedFiles.length === 0) {
                $('.vic-upload-btn').removeClass('has-files');
            }
        });
        
        // Truncate filename
        function truncateFilename(name, maxLength) {
            if (name.length <= maxLength) return name;
            const ext = name.split('.').pop();
            const base = name.substring(0, maxLength - ext.length - 4);
            return base + '...' + ext;
        }
        
        // URL field toggle
        $('#vic-add-url').on('click', function() {
            $urlField.slideToggle(200);
            if ($urlField.is(':visible')) {
                $urlField.find('input').focus();
            }
        });
        
        // Remove URL field
        $urlField.find('.vic-btn-remove-field').on('click', function() {
            $urlField.slideUp(200);
            $urlField.find('input').val('');
        });
        
        // YouTube prompt
        $('#vic-add-youtube').on('click', function() {
            const url = prompt('Collez l\'URL de la vidéo YouTube :');
            if (url) {
                const $textarea = $submitForm.find('textarea[name="post_content"]');
                $textarea.val($textarea.val() + '\n' + url);
                updateSubmitButton();
            }
        });

        // Submit form
        $submitForm.on('submit', function(e) {
            e.preventDefault();
            
            const $btn = $submitForm.find('.vic-btn-primary');
            const originalText = $btn.text();
            
            $btn.text('Publication...').prop('disabled', true);
            
            // Create FormData for file upload
            const formData = new FormData();
            formData.append('action', 'vic_create_post');
            formData.append('nonce', vicAjax.nonce);
            formData.append('post_title', $submitForm.find('input[name="post_title"]').val());
            formData.append('post_content', $submitForm.find('textarea[name="post_content"]').val());
            formData.append('post_category', $submitForm.find('select[name="post_category"]').val());
            formData.append('post_url', $submitForm.find('input[name="post_url"]').val());
            
            // Add files
            selectedFiles.forEach(function(file) {
                formData.append('post_attachments[]', file);
            });
            
            $.ajax({
                url: vicAjax.ajaxurl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        // Add new post to top of feed
                        $('#vic-feed').prepend(response.data.post_html);
                        
                        // Reset and close form
                        $submitForm[0].reset();
                        selectedFiles = [];
                        $preview.empty();
                        $urlField.hide();
                        $('.vic-upload-btn').removeClass('has-files');
                        $form.slideUp(200);
                        $trigger.show();
                        
                        // Highlight new post briefly
                        $('#vic-feed .vic-post-card').first().css('background', '#f0f9ff').animate({
                            backgroundColor: '#ffffff'
                        }, 2000);
                    } else {
                        alert(response.data.message || 'Erreur lors de la publication');
                    }
                },
                error: function() {
                    alert('Erreur de connexion. Veuillez réessayer.');
                },
                complete: function() {
                    $btn.text(originalText).prop('disabled', false);
                }
            });
        });
    }

    /**
     * Like System
     */
    function initLikes() {
        $(document).on('click', '.vic-like-btn', function(e) {
            e.preventDefault();
            
            const $btn = $(this);
            const postId = $btn.data('post-id');
            const $count = $btn.find('.vic-like-count');
            
            $btn.prop('disabled', true);
            
            $.ajax({
                url: vicAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'vic_like_post',
                    nonce: vicAjax.nonce,
                    post_id: postId
                },
                success: function(response) {
                    if (response.success) {
                        $count.text(response.data.likes);
                        
                        if (response.data.action === 'liked') {
                            $btn.addClass('liked');
                            $btn.find('svg').attr('fill', 'currentColor');
                        } else {
                            $btn.removeClass('liked');
                            $btn.find('svg').attr('fill', 'none');
                        }
                    }
                },
                complete: function() {
                    $btn.prop('disabled', false);
                }
            });
        });
    }

    /**
     * Category Filters
     */
    function initFilters() {
        $('.vic-filter-btn').on('click', function() {
            const $btn = $(this);
            const category = $btn.data('category');
            
            // Update active state
            $('.vic-filter-btn').removeClass('active');
            $btn.addClass('active');
            
            // Reset page counter
            $('#vic-load-more').data('page', 1);
            
            // Load filtered posts
            loadPosts(category, 1, true);
        });
    }

    /**
     * Load More
     */
    function initLoadMore() {
        $('#vic-load-more').on('click', function() {
            const $btn = $(this);
            const page = parseInt($btn.data('page')) + 1;
            const category = $('.vic-filter-btn.active').data('category');
            
            $btn.data('page', page);
            loadPosts(category, page, false);
        });
    }

    /**
     * Load Posts via AJAX
     */
    function loadPosts(category, page, replace) {
        const $feed = $('#vic-feed');
        const $loadMore = $('#vic-load-more');
        
        $feed.addClass('vic-loading');
        $loadMore.text('Chargement...').prop('disabled', true);
        
        $.ajax({
            url: vicAjax.ajaxurl,
            type: 'POST',
            data: {
                action: 'vic_load_posts',
                nonce: vicAjax.nonce,
                category: category,
                page: page
            },
            success: function(response) {
                if (response.success) {
                    if (replace) {
                        $feed.html(response.data.html);
                    } else {
                        $feed.append(response.data.html);
                    }
                    
                    // Hide load more if no more posts
                    if (response.data.has_more) {
                        $loadMore.show();
                    } else {
                        $loadMore.hide();
                    }
                }
            },
            complete: function() {
                $feed.removeClass('vic-loading');
                $loadMore.text('Charger plus').prop('disabled', false);
            }
        });
    }

    /**
     * Pin/Unpin Posts (Admin only)
     */
    function initPinning() {
        $(document).on('click', '.vic-pin-btn', function(e) {
            e.preventDefault();
            
            const $btn = $(this);
            const postId = $btn.data('post-id');
            
            $btn.prop('disabled', true);
            
            $.ajax({
                url: vicAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'vic_pin_post',
                    nonce: vicAjax.nonce,
                    post_id: postId
                },
                success: function(response) {
                    if (response.success) {
                        if (response.data.pinned) {
                            $btn.addClass('pinned');
                            $btn.closest('.vic-post-card').prepend('<div class="vic-pinned-badge">📌 Épinglé</div>');
                        } else {
                            $btn.removeClass('pinned');
                            $btn.closest('.vic-post-card').find('.vic-pinned-badge').remove();
                        }
                    }
                },
                complete: function() {
                    $btn.prop('disabled', false);
                }
            });
        });
    }

})(jQuery);
