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
        initSearch();
        initModal();
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
        $(document).on('click', '#vic-add-url', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const $field = $('#vic-url-field');
            $field.slideToggle(200);
            setTimeout(function() {
                if ($field.is(':visible')) {
                    $field.find('input').focus();
                }
            }, 210);
        });

        // Remove URL field
        $(document).on('click', '#vic-url-field .vic-btn-remove-field', function(e) {
            e.preventDefault();
            const $field = $('#vic-url-field');
            $field.slideUp(200);
            $field.find('input').val('');
        });

        // YouTube prompt
        $(document).on('click', '#vic-add-youtube', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const url = prompt('Collez l\'URL de la vidéo YouTube :');
            if (url && url.trim()) {
                const $textarea = $('#vic-new-post-form textarea[name="post_content"]');
                const currentVal = $textarea.val();
                $textarea.val(currentVal + (currentVal ? '\n' : '') + url.trim());
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
            e.stopPropagation();

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

                        // Update modal like button if open
                        const $modalBtn = $('.vic-modal-post-actions .vic-like-btn[data-post-id="' + postId + '"]');
                        if ($modalBtn.length) {
                            $modalBtn.find('.vic-like-count').text(response.data.likes);
                            if (response.data.action === 'liked') {
                                $modalBtn.addClass('liked');
                                $modalBtn.find('svg').attr('fill', 'currentColor');
                            } else {
                                $modalBtn.removeClass('liked');
                                $modalBtn.find('svg').attr('fill', 'none');
                            }
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
            e.stopPropagation();

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

    /**
     * Search functionality
     */
    function initSearch() {
        let searchTimeout;
        const $searchInput = $('#vic-search-input');
        const $searchClear = $('#vic-search-clear');
        const $feed = $('#vic-feed');
        const $loadMore = $('#vic-load-more');

        // Search on input with debounce
        $searchInput.on('input', function() {
            const query = $(this).val().trim();

            // Show/hide clear button
            if (query.length > 0) {
                $searchClear.show();
            } else {
                $searchClear.hide();
            }

            // Debounce search
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                performSearch(query);
            }, 300);
        });

        // Clear search
        $searchClear.on('click', function() {
            $searchInput.val('');
            $(this).hide();
            performSearch('');
        });

        // Search on enter
        $searchInput.on('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                clearTimeout(searchTimeout);
                performSearch($(this).val().trim());
            }
        });

        function performSearch(query) {
            const category = $('.vic-filter-btn.active').data('category');

            $feed.addClass('vic-loading');

            $.ajax({
                url: vicAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'vic_search_posts',
                    nonce: vicAjax.nonce,
                    search: query,
                    category: category
                },
                success: function(response) {
                    if (response.success) {
                        $feed.html(response.data.html);

                        // Hide load more when searching
                        if (query.length > 0) {
                            $loadMore.hide();
                        } else if (response.data.has_more) {
                            $loadMore.show();
                        }

                        // Reset page counter
                        $loadMore.data('page', 1);
                    }
                },
                complete: function() {
                    $feed.removeClass('vic-loading');
                }
            });
        }
    }

    /**
     * Modal System
     */
    function initModal() {
        let commentFiles = [];

        // Open modal when clicking on post card (not on buttons)
        $(document).on('click', '.vic-post-card', function(e) {
            // Don't open modal if clicking on buttons or links
            if ($(e.target).closest('.vic-like-btn, .vic-comment-btn, .vic-pin-btn, a').length) {
                return;
            }

            const postId = $(this).data('post-id');
            openModal(postId);
        });

        // Open modal from comment button
        $(document).on('click', '.vic-comment-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const postId = $(this).closest('.vic-post-card').data('post-id');
            openModal(postId);
        });

        // Close modal
        $(document).on('click', '.vic-modal-overlay', function(e) {
            if ($(e.target).hasClass('vic-modal-overlay')) {
                closeModal();
            }
        });

        $(document).on('click', '.vic-modal-close', function() {
            closeModal();
        });

        // Close on escape
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape' && $('.vic-modal-overlay.active').length) {
                closeModal();
            }
        });

        // Comment form handling
        $(document).on('input', '.vic-comment-input', function() {
            const $wrapper = $(this).closest('.vic-comment-input-wrapper');
            const $submit = $wrapper.find('.vic-comment-submit');
            const hasContent = $(this).val().trim().length > 0 || commentFiles.length > 0;

            if (hasContent) {
                $submit.addClass('active');
            } else {
                $submit.removeClass('active');
            }

            // Auto-resize textarea
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 120) + 'px';
        });

        // Comment file upload
        $(document).on('change', '.vic-comment-file-input', function(e) {
            const files = Array.from(e.target.files);
            const $preview = $(this).closest('.vic-comment-input-wrapper').find('.vic-comment-attachments-preview');

            files.forEach(function(file) {
                if (file.size > 10 * 1024 * 1024) {
                    alert('Fichier trop volumineux (max 10MB)');
                    return;
                }

                commentFiles.push(file);
                addCommentFilePreview(file, $preview);
            });

            updateCommentSubmitState();
        });

        // Remove comment attachment
        $(document).on('click', '.vic-comment-attachment-remove', function() {
            const index = $(this).closest('.vic-comment-attachment-item').index();
            commentFiles.splice(index, 1);
            $(this).closest('.vic-comment-attachment-item').remove();
            updateCommentSubmitState();
        });

        // Submit comment
        $(document).on('click', '.vic-comment-submit.active', function() {
            const $wrapper = $(this).closest('.vic-comment-input-wrapper');
            const $input = $wrapper.find('.vic-comment-input');
            const content = $input.val().trim();
            const postId = $(this).data('post-id');

            if (!content && commentFiles.length === 0) return;

            const $btn = $(this);
            $btn.text('...').prop('disabled', true);

            const formData = new FormData();
            formData.append('action', 'vic_add_comment');
            formData.append('nonce', vicAjax.nonce);
            formData.append('post_id', postId);
            formData.append('content', content);

            commentFiles.forEach(function(file) {
                formData.append('comment_attachments[]', file);
            });

            $.ajax({
                url: vicAjax.ajaxurl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        // Add new comment to list
                        $('.vic-comments-list').append(response.data.comment_html);

                        // Reset form
                        $input.val('').css('height', 'auto');
                        $wrapper.find('.vic-comment-attachments-preview').empty();
                        commentFiles = [];
                        $btn.removeClass('active');

                        // Update comment count
                        const $countEl = $('.vic-post-card[data-post-id="' + postId + '"] .vic-comment-btn span');
                        const newCount = parseInt($countEl.text()) + 1;
                        $countEl.text(newCount);

                        // Scroll to new comment
                        const $newComment = $('.vic-comments-list .vic-comment').last();
                        $newComment[0].scrollIntoView({ behavior: 'smooth' });
                    } else {
                        alert(response.data.message || 'Erreur');
                    }
                },
                error: function() {
                    alert('Erreur de connexion');
                },
                complete: function() {
                    $btn.text('Envoyer').prop('disabled', false);
                }
            });
        });

        // Add link to comment (legacy)
        $(document).on('click', '.vic-comment-add-link', function() {
            const url = prompt('Entrez l\'URL :');
            if (url && url.trim()) {
                // Check for Skool-style input first
                let $input = $(this).closest('.vic-comment-input-skool-wrapper').find('.vic-comment-input-skool');
                if (!$input.length) {
                    $input = $(this).closest('.vic-comment-input-wrapper').find('.vic-comment-input');
                }
                const currentVal = $input.val();
                $input.val(currentVal + (currentVal ? ' ' : '') + url.trim());
                $input.trigger('input');
            }
        });

        // Skool-style comment submission (on Enter key)
        $(document).on('keypress', '.vic-comment-input-skool', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                submitSkoolComment($(this));
            }
        });

        // Submit Skool-style comment
        function submitSkoolComment($input) {
            const content = $input.val().trim();
            const $wrapper = $input.closest('.vic-comment-form-wrapper-skool');
            const postId = $('.vic-modal').find('.vic-like-btn[data-post-id]').data('post-id');

            if (!content || !postId) return;

            $input.prop('disabled', true);

            const formData = new FormData();
            formData.append('action', 'vic_add_comment');
            formData.append('nonce', vicAjax.nonce);
            formData.append('post_id', postId);
            formData.append('content', content);

            // Add files if any
            const $fileInput = $wrapper.find('.vic-comment-file-input');
            if ($fileInput.length && $fileInput[0].files.length > 0) {
                for (let i = 0; i < $fileInput[0].files.length; i++) {
                    formData.append('comment_attachments[]', $fileInput[0].files[i]);
                }
            }

            $.ajax({
                url: vicAjax.ajaxurl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        // Add new comment to list
                        const $noComments = $('.vic-comments-list .vic-no-comments');
                        if ($noComments.length) {
                            $noComments.remove();
                        }
                        $('.vic-comments-list').append(response.data.comment_html);

                        // Reset form
                        $input.val('');
                        $wrapper.find('.vic-comment-attachments-preview').empty();
                        $fileInput.val('');

                        // Update comment count
                        const $countEl = $('.vic-post-card[data-post-id="' + postId + '"] .vic-comment-btn span');
                        if ($countEl.length) {
                            const newCount = parseInt($countEl.text()) + 1;
                            $countEl.text(newCount);
                        }

                        // Scroll to new comment
                        const $newComment = $('.vic-comments-list .vic-comment').last();
                        if ($newComment.length) {
                            $newComment[0].scrollIntoView({ behavior: 'smooth' });
                        }
                    } else {
                        alert(response.data.message || 'Erreur');
                    }
                },
                error: function() {
                    alert('Erreur de connexion');
                },
                complete: function() {
                    $input.prop('disabled', false).focus();
                }
            });
        }

        function openModal(postId) {
            // Show loading
            $('body').append(`
                <div class="vic-modal-overlay active">
                    <div class="vic-modal">
                        <div class="vic-modal-content" style="padding: 40px; text-align: center;">
                            Chargement...
                        </div>
                    </div>
                </div>
            `);
            $('body').css('overflow', 'hidden');

            // Load post content
            $.ajax({
                url: vicAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'vic_get_post_modal',
                    nonce: vicAjax.nonce,
                    post_id: postId
                },
                success: function(response) {
                    if (response.success) {
                        $('.vic-modal-overlay .vic-modal').html(response.data.html);
                    } else {
                        closeModal();
                        alert('Erreur lors du chargement');
                    }
                },
                error: function() {
                    closeModal();
                    alert('Erreur de connexion');
                }
            });
        }

        function closeModal() {
            const $overlay = $('.vic-modal-overlay');
            $overlay.removeClass('active');
            setTimeout(function() {
                $overlay.remove();
            }, 300);
            $('body').css('overflow', '');
            commentFiles = [];
        }

        function addCommentFilePreview(file, $preview) {
            const $item = $('<div class="vic-comment-attachment-item"></div>');

            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $item.prepend('<img src="' + e.target.result + '" alt="">');
                };
                reader.readAsDataURL(file);
            } else {
                $item.prepend('<span>📎</span>');
            }

            $item.append('<span>' + file.name.substring(0, 15) + '</span>');
            $item.append('<button type="button" class="vic-comment-attachment-remove">✕</button>');
            $preview.append($item);
        }

        function updateCommentSubmitState() {
            const $input = $('.vic-comment-input');
            const $submit = $('.vic-comment-submit');
            const hasContent = $input.val().trim().length > 0 || commentFiles.length > 0;

            if (hasContent) {
                $submit.addClass('active');
            } else {
                $submit.removeClass('active');
            }
        }

        // ====== Comment Like System ======
        $(document).on('click', '.vic-comment-like-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const $btn = $(this);
            const commentId = $btn.data('comment-id');
            const $count = $btn.find('.vic-comment-like-count');

            $btn.prop('disabled', true);

            $.ajax({
                url: vicAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'vic_like_comment',
                    nonce: vicAjax.nonce,
                    comment_id: commentId
                },
                success: function(response) {
                    if (response.success) {
                        $count.text(response.data.likes);

                        if (response.data.action === 'liked') {
                            $btn.addClass('liked');
                        } else {
                            $btn.removeClass('liked');
                        }
                    }
                },
                complete: function() {
                    $btn.prop('disabled', false);
                }
            });
        });

        // ====== Comment Reply System ======
        // Toggle reply form
        $(document).on('click', '.vic-comment-reply-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const $comment = $(this).closest('.vic-comment');
            const $replyForm = $comment.find('> .vic-comment-body > .vic-reply-form-wrapper').first();

            // Hide other reply forms
            $('.vic-reply-form-wrapper').not($replyForm).slideUp(200);

            // Toggle this reply form
            $replyForm.slideToggle(200);

            // Focus on textarea
            setTimeout(function() {
                if ($replyForm.is(':visible')) {
                    $replyForm.find('.vic-reply-input').focus();
                }
            }, 210);
        });

        // Cancel reply
        $(document).on('click', '.vic-reply-cancel', function(e) {
            e.preventDefault();
            const $wrapper = $(this).closest('.vic-reply-form-wrapper');
            $wrapper.find('.vic-reply-input').val('');
            $wrapper.slideUp(200);
        });

        // Auto-resize reply textarea
        $(document).on('input', '.vic-reply-input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 100) + 'px';
        });

        // ====== Jump to Latest Comment ======
        $(document).on('click', '.vic-jump-latest', function(e) {
            e.preventDefault();
            const $lastComment = $('.vic-comments-list > .vic-comment:last-child');
            if ($lastComment.length) {
                $lastComment[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });

        // ====== Comment Menu 3 Points ======
        $(document).on('click', '.vic-comment-menu-trigger', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const $menu = $(this).closest('.vic-comment-menu');
            $('.vic-comment-menu').not($menu).removeClass('active');
            $menu.toggleClass('active');
        });

        // Close menu on click outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.vic-comment-menu').length) {
                $('.vic-comment-menu').removeClass('active');
            }
        });

        // Copy comment link
        $(document).on('click', '.vic-copy-link', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const postId = $(this).data('post-id');
            const commentId = $(this).data('comment-id');
            const url = window.location.origin + window.location.pathname + '?p=' + postId + '#comment-' + commentId;

            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(url).then(function() {
                    alert('Lien copié !');
                }).catch(function() {
                    prompt('Copiez ce lien:', url);
                });
            } else {
                prompt('Copiez ce lien:', url);
            }
            $('.vic-comment-menu').removeClass('active');
        });

        // Report comment
        $(document).on('click', '.vic-report-comment', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const commentId = $(this).data('comment-id');

            if (confirm('Voulez-vous signaler ce commentaire aux administrateurs ?')) {
                $.ajax({
                    url: vicAjax.ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'vic_report_comment',
                        nonce: vicAjax.nonce,
                        comment_id: commentId
                    },
                    success: function(response) {
                        if (response.success) {
                            alert('Commentaire signalé aux administrateurs.');
                        } else {
                            alert(response.data.message || 'Erreur lors du signalement');
                        }
                    },
                    error: function() {
                        alert('Erreur de connexion');
                    }
                });
            }
            $('.vic-comment-menu').removeClass('active');
        });

        // ====== YouTube Button in Comment Form ======
        $(document).on('click', '.vic-comment-add-youtube', function(e) {
            e.preventDefault();
            const url = prompt('Collez l\'URL de la vidéo YouTube :');
            if (url && url.trim()) {
                const $input = $('.vic-comment-input-skool');
                const currentVal = $input.val();
                $input.val(currentVal + (currentVal ? ' ' : '') + url.trim());
                $input.focus();
            }
        });

        // ====== Emoji Picker ======
        $(document).on('click', '.vic-comment-add-emoji', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const $btn = $(this);
            let $picker = $btn.siblings('.vic-emoji-picker');

            // Close other pickers
            $('.vic-emoji-picker').not($picker).remove();

            if ($picker.length) {
                $picker.remove();
            } else {
                const emojis = ['😀', '😂', '😍', '🥰', '😎', '🤔', '👍', '👏', '🎉', '🔥', '❤️', '💯', '✨', '🙌', '💪', '🚀'];
                const $newPicker = $('<div class="vic-emoji-picker"></div>');
                emojis.forEach(function(emoji) {
                    $newPicker.append('<span class="vic-emoji-item">' + emoji + '</span>');
                });
                $btn.after($newPicker);
            }
        });

        // Insert emoji
        $(document).on('click', '.vic-emoji-item', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const emoji = $(this).text();
            const $input = $('.vic-comment-input-skool');
            $input.val($input.val() + emoji);
            $input.focus();
            $(this).closest('.vic-emoji-picker').remove();
        });

        // Close emoji picker on click outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.vic-comment-add-emoji, .vic-emoji-picker').length) {
                $('.vic-emoji-picker').remove();
            }
        });

        // ====== GIF Button ======
        $(document).on('click', '.vic-comment-add-gif', function(e) {
            e.preventDefault();
            const url = prompt('Collez l\'URL du GIF :');
            if (url && url.trim()) {
                const $input = $('.vic-comment-input-skool');
                const currentVal = $input.val();
                $input.val(currentVal + (currentVal ? ' ' : '') + url.trim());
                $input.focus();
            }
        });

        // Submit reply
        $(document).on('click', '.vic-reply-submit', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const $btn = $(this);
            const $wrapper = $btn.closest('.vic-reply-form-wrapper');
            const $input = $wrapper.find('.vic-reply-input');
            const content = $input.val().trim();
            const commentId = $btn.data('comment-id');
            const postId = $btn.data('post-id');

            if (!content) {
                alert('Veuillez écrire une réponse');
                return;
            }

            $btn.text('...').prop('disabled', true);

            $.ajax({
                url: vicAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'vic_reply_comment',
                    nonce: vicAjax.nonce,
                    post_id: postId,
                    parent_comment_id: commentId,
                    content: content
                },
                success: function(response) {
                    if (response.success) {
                        const $comment = $wrapper.closest('.vic-comment');

                        // Check if replies container exists
                        let $repliesContainer = $comment.find('> .vic-comment-body > .vic-comment-replies').first();
                        if (!$repliesContainer.length) {
                            $repliesContainer = $('<div class="vic-comment-replies"></div>');
                            $comment.find('> .vic-comment-body').append($repliesContainer);
                        }

                        // Add reply
                        $repliesContainer.append(response.data.comment_html);

                        // Reset and hide form
                        $input.val('').css('height', 'auto');
                        $wrapper.slideUp(200);

                        // Update comment count in post card
                        const $countEl = $('.vic-post-card[data-post-id="' + postId + '"] .vic-comment-btn span');
                        if ($countEl.length) {
                            const newCount = parseInt($countEl.text()) + 1;
                            $countEl.text(newCount);
                        }

                        // Scroll to new reply
                        const $newReply = $repliesContainer.find('.vic-comment').last();
                        $newReply[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
                    } else {
                        alert(response.data.message || 'Erreur');
                    }
                },
                error: function() {
                    alert('Erreur de connexion');
                },
                complete: function() {
                    $btn.text('Répondre').prop('disabled', false);
                }
            });
        });
    }

})(jQuery);
