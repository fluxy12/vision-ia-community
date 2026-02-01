/**
 * Vision IA Community - JavaScript
 */

(function($) {
    'use strict';

    // ====== EMOJI DATA (global pour tous les pickers) ======
    const emojiData = {
        smileys: ['😀','😃','😄','😁','😆','😅','🤣','😂','🙂','😊','😇','🥰','😍','🤩','😘','😗','😚','😋','😛','😜','🤪','😝','🤑','🤗','🤭','🤫','🤔','🤐','🤨','😐','😑','😶','😏','😒','🙄','😬','😌','😔','😪','🤤','😴','😷','🤒','🤕','🤢','🤮','🥵','🥶','🥴','😵','🤯','🤠','🥳','😎','🤓','🧐','😈','👿','💀','☠️','💩','🤡','👹','👺','👻','👽','👾','🤖'],
        people: ['👋','🤚','🖐','✋','🖖','👌','🤌','🤏','✌️','🤞','🤟','🤘','🤙','👈','👉','👆','🖕','👇','☝️','👍','👎','✊','👊','🤛','🤜','👏','🙌','👐','🤲','🤝','🙏','💪','🦾','🦿','🦵','🦶','👂','🦻','👃','🧠','🫀','🫁','🦷','🦴','👀','👁','👅','👄'],
        nature: ['🐶','🐱','🐭','🐹','🐰','🦊','🐻','🐼','🐨','🐯','🦁','🐮','🐷','🐸','🐵','🐔','🐧','🐦','🐤','🦆','🦅','🦉','🦇','🐺','🐗','🐴','🦄','🐝','🪱','🐛','🦋','🐌','🐞','🐜','🪰','🪲','🪳','🦟','🦗','🌸','💐','🌷','🌹','🥀','🌺','🌻','🌼','🌱','🌲','🌳','🌴','🌵','🌾','🌿','☘️','🍀','🍁','🍂','🍃'],
        food: ['🍎','🍐','🍊','🍋','🍌','🍉','🍇','🍓','🫐','🍈','🍒','🍑','🥭','🍍','🥥','🥝','🍅','🍆','🥑','🥦','🥬','🥒','🌶️','🫑','🌽','🥕','🧄','🧅','🥔','🍠','🥐','🥯','🍞','🥖','🥨','🧀','🥚','🍳','🧈','🥞','🧇','🥓','🥩','🍗','🍖','🌭','🍔','🍟','🍕','🥪','🥙','🌮','🌯','🥗','🍝','🍜','🍲','🍛','🍣','🍱','🥟','🍤','🍙','🍚','🍘','🍥','🥠','🍢','🍡','🍧','🍨','🍦','🥧','🧁','🍰','🎂','🍮','🍭','🍬','🍫','🍿','🍩','🍪','☕','🍵','🥤','🍺','🍻','🥂','🍷','🍸','🍹'],
        activities: ['⚽','🏀','🏈','⚾','🥎','🎾','🏐','🏉','🥏','🎱','🏓','🏸','🏒','🏑','🥍','🏏','⛳','🏹','🎣','🥊','🥋','🎽','🛹','🛷','⛸️','🥌','🎿','⛷️','🏂','🏋️','🤼','🤸','🤺','⛹️','🤾','🏌️','🏇','🧘','🏄','🏊','🤽','🚣','🧗','🚴','🚵','🎬','🎤','🎧','🎼','🎹','🥁','🎷','🎺','🎸','🎻','🎲','🎯','🎳','🎮','🎰','🧩'],
        travel: ['🚗','🚕','🚙','🚌','🚎','🏎️','🚓','🚑','🚒','🚐','🚚','🚛','🚜','🛵','🏍️','🚲','🛴','🚁','✈️','🛩️','🛫','🛬','💺','🚀','🛶','⛵','🚤','🛥️','🛳️','🚢','🗼','🗽','🗿','🏰','🏯','🏟️','🎡','🎢','🎠','⛲','🏖️','🏝️','🏜️','🌋','⛰️','🏔️','🗻','🏕️','🏠','🏡','🏢','🏬','🏥','🏦','🏨','🏪','🏫','💒','🏛️','⛪','🕌','🕍','⛩️'],
        objects: ['⌚','📱','💻','⌨️','🖥️','🖨️','🖱️','🕹️','💽','💾','💿','📀','📷','📸','📹','🎥','📽️','📞','📺','📻','🎙️','⏰','⌛','📡','🔋','🔌','💡','🔦','🧯','💸','💵','💰','💳','💎','⚖️','🔧','🔨','🛠️','⛏️','🔩','⚙️','🔫','💣','🔪','🛡️','🔮','📿','💈','⚗️','🔭','🔬','💊','💉','🧬','🦠'],
        symbols: ['❤️','🧡','💛','💚','💙','💜','🖤','🤍','🤎','💔','❣️','💕','💞','💓','💗','💖','💘','💝','☮️','✝️','☪️','🕉️','☸️','✡️','🔯','🕎','☯️','☦️','⛎','♈','♉','♊','♋','♌','♍','♎','♏','♐','♑','♒','♓','🆔','⚛️','☢️','☣️','📴','📳','🆚','💮','🉐','㊙️','㊗️','🈴','🈵','🈹','🈲','🅰️','🅱️','🆎','🆑','🅾️','🆘','❌','⭕','🛑','⛔','📛','🚫','💯','💢','🚷','🚯','🚳','🚱','🔞','📵','🚭','❗','❓','‼️','⁉️','⚠️','♻️','✅','❎','🔱','📛','🔰','⚜️']
    };

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

        // ====== EMOJI PICKER POUR POSTS ======
        $(document).on('click', '.vic-post-add-emoji', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const $btn = $(this);
            const $formActions = $btn.closest('.vic-form-actions');

            // Fermer autres pickers
            $('.vic-emoji-picker-full, .vic-emoji-picker, .vic-gif-picker').remove();

            // Créer le picker complet (réutiliser emojiData défini plus bas)
            const $picker = $('<div class="vic-emoji-picker-full vic-post-emoji-picker"></div>');

            // Barre de recherche
            $picker.append('<div class="vic-emoji-search"><input type="text" placeholder="Rechercher un emoji..." class="vic-emoji-search-input"></div>');

            // Catégories
            const postEmojiCategories = [
                { id: 'smileys', icon: '😀', name: 'Smileys' },
                { id: 'people', icon: '👋', name: 'Personnes' },
                { id: 'nature', icon: '🌿', name: 'Nature' },
                { id: 'food', icon: '🍕', name: 'Nourriture' },
                { id: 'activities', icon: '⚽', name: 'Activités' },
                { id: 'travel', icon: '✈️', name: 'Voyage' },
                { id: 'objects', icon: '💡', name: 'Objets' },
                { id: 'symbols', icon: '❤️', name: 'Symboles' }
            ];

            const $categories = $('<div class="vic-emoji-categories"></div>');
            postEmojiCategories.forEach(function(cat, idx) {
                $categories.append('<button data-category="' + cat.id + '" ' + (idx === 0 ? 'class="active"' : '') + ' title="' + cat.name + '">' + cat.icon + '</button>');
            });
            $picker.append($categories);

            // Grille d'emojis
            const $grid = $('<div class="vic-emoji-grid"></div>');
            // Utiliser la même fonction renderEmojiCategory que pour les commentaires
            if (typeof emojiData !== 'undefined') {
                const emojis = emojiData['smileys'] || [];
                emojis.forEach(function(emoji) {
                    $grid.append('<span class="vic-emoji-item vic-post-emoji-item">' + emoji + '</span>');
                });
            }
            $picker.append($grid);

            // Ajouter au parent .vic-form-actions pour un bon positionnement
            $formActions.append($picker);

            // Ne pas scroller, garder le focus
            setTimeout(function() {
                $picker.find('.vic-emoji-search-input').focus();
            }, 10);
        });

        // Insérer emoji dans le textarea du post
        $(document).on('click', '.vic-post-emoji-item', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const $emojiItem = $(this);

            // Récupérer l'emoji (support Twemoji)
            let emoji = '';
            const $img = $emojiItem.find('img.emoji');
            if ($img.length) {
                emoji = $img.attr('alt') || '';
            } else {
                emoji = $emojiItem.text().trim();
            }

            if (!emoji) return;

            // Insérer dans le textarea du post
            const $textarea = $('#vic-new-post-form textarea[name="post_content"]');
            if ($textarea.length) {
                const currentVal = $textarea.val() || '';
                $textarea.val(currentVal + emoji);
                $textarea.focus();
                updateSubmitButton();
            }

            // Fermer le picker
            $('.vic-emoji-picker-full, .vic-emoji-picker').remove();
        });

        // Changer de catégorie dans le picker post
        $(document).on('click', '.vic-post-emoji-picker .vic-emoji-categories button', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const category = $(this).data('category');
            const $picker = $(this).closest('.vic-emoji-picker-full');

            $picker.find('.vic-emoji-categories button').removeClass('active');
            $(this).addClass('active');

            // Remplir la grille avec la catégorie sélectionnée
            const $grid = $picker.find('.vic-emoji-grid');
            $grid.empty();
            if (typeof emojiData !== 'undefined' && emojiData[category]) {
                emojiData[category].forEach(function(emoji) {
                    $grid.append('<span class="vic-emoji-item vic-post-emoji-item">' + emoji + '</span>');
                });
            }
        });

        // Recherche emoji dans picker post
        $(document).on('input', '.vic-post-emoji-picker .vic-emoji-search-input', function() {
            const query = $(this).val().toLowerCase();
            const $picker = $(this).closest('.vic-emoji-picker-full');
            const $grid = $picker.find('.vic-emoji-grid');

            if (query.length < 1) {
                const activeCategory = $picker.find('.vic-emoji-categories button.active').data('category');
                $grid.empty();
                if (typeof emojiData !== 'undefined' && emojiData[activeCategory]) {
                    emojiData[activeCategory].forEach(function(emoji) {
                        $grid.append('<span class="vic-emoji-item vic-post-emoji-item">' + emoji + '</span>');
                    });
                }
                return;
            }

            $grid.empty();
            if (typeof emojiData !== 'undefined') {
                let found = [];
                Object.keys(emojiData).forEach(function(cat) {
                    emojiData[cat].forEach(function(emoji) {
                        if (found.length < 40) {
                            found.push(emoji);
                        }
                    });
                });
                found.slice(0, 40).forEach(function(emoji) {
                    $grid.append('<span class="vic-emoji-item vic-post-emoji-item">' + emoji + '</span>');
                });
            }
        });

        // ====== GIF PICKER POUR POSTS ======
        $(document).on('click', '.vic-post-add-gif', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const $btn = $(this);
            const $formActions = $btn.closest('.vic-form-actions');

            // Fermer autres pickers
            $('.vic-gif-picker, .vic-emoji-picker-full, .vic-emoji-picker').remove();

            // Créer le picker GIF
            const $picker = $(`
                <div class="vic-gif-picker vic-post-gif-picker">
                    <div class="vic-gif-header">
                        <input type="text" placeholder="Rechercher des GIFs..." class="vic-gif-search-input">
                    </div>
                    <div class="vic-gif-grid">
                        <div class="vic-gif-loading">Recherchez un GIF...</div>
                    </div>
                </div>
            `);

            // Ajouter au parent .vic-form-actions pour un bon positionnement
            $formActions.append($picker);

            // Ne pas scroller, garder le focus
            setTimeout(function() {
                $picker.find('.vic-gif-search-input').focus();
            }, 10);

            // Charger les GIFs tendance
            loadPostTrendingGifs($picker.find('.vic-gif-grid'));
        });

        // Fonction pour charger les GIFs tendance pour les posts
        function loadPostTrendingGifs($grid) {
            const TENOR_API_KEY = 'AIzaSyAyimkuYQYF_FXVALexPuGQctUWRURdCYQ';
            $grid.html('<div class="vic-gif-loading">Chargement...</div>');

            fetch(`https://tenor.googleapis.com/v2/featured?key=${TENOR_API_KEY}&limit=20&media_filter=gif,tinygif`)
                .then(response => response.json())
                .then(data => {
                    $grid.empty();
                    if (data.results && data.results.length > 0) {
                        data.results.forEach(gif => {
                            const gifUrl = gif.media_formats.gif?.url || gif.media_formats.tinygif?.url;
                            const previewUrl = gif.media_formats.tinygif?.url || gif.media_formats.nanogif?.url || gifUrl;
                            if (gifUrl) {
                                $grid.append(`<img src="${previewUrl}" data-full="${gifUrl}" class="vic-gif-item vic-post-gif-item" alt="${gif.content_description || 'GIF'}">`);
                            }
                        });
                    } else {
                        $grid.html('<div class="vic-gif-loading">Aucun GIF trouvé</div>');
                    }
                })
                .catch(() => {
                    $grid.html('<div class="vic-gif-loading">Erreur de chargement</div>');
                });
        }

        // Recherche de GIFs pour posts
        let postGifSearchTimeout;
        $(document).on('input', '.vic-post-gif-picker .vic-gif-search-input', function() {
            const query = $(this).val().trim();
            const $grid = $(this).closest('.vic-gif-picker').find('.vic-gif-grid');

            clearTimeout(postGifSearchTimeout);

            if (query.length < 2) {
                loadPostTrendingGifs($grid);
                return;
            }

            postGifSearchTimeout = setTimeout(function() {
                const TENOR_API_KEY = 'AIzaSyAyimkuYQYF_FXVALexPuGQctUWRURdCYQ';
                $grid.html('<div class="vic-gif-loading">Recherche...</div>');

                fetch(`https://tenor.googleapis.com/v2/search?q=${encodeURIComponent(query)}&key=${TENOR_API_KEY}&limit=20&media_filter=gif,tinygif`)
                    .then(response => response.json())
                    .then(data => {
                        $grid.empty();
                        if (data.results && data.results.length > 0) {
                            data.results.forEach(gif => {
                                const gifUrl = gif.media_formats.gif?.url || gif.media_formats.tinygif?.url;
                                const previewUrl = gif.media_formats.tinygif?.url || gif.media_formats.nanogif?.url || gifUrl;
                                if (gifUrl) {
                                    $grid.append(`<img src="${previewUrl}" data-full="${gifUrl}" class="vic-gif-item vic-post-gif-item" alt="${gif.content_description || 'GIF'}">`);
                                }
                            });
                        } else {
                            $grid.html('<div class="vic-gif-loading">Aucun GIF trouvé</div>');
                        }
                    })
                    .catch(() => {
                        $grid.html('<div class="vic-gif-loading">Erreur de recherche</div>');
                    });
            }, 300);
        });

        // Sélectionner un GIF pour le post
        $(document).on('click', '.vic-post-gif-item', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const gifUrl = $(this).data('full') || $(this).attr('src');

            // Ajouter le GIF dans le textarea avec le format [gif]url[/gif]
            const $textarea = $('#vic-new-post-form textarea[name="post_content"]');
            if ($textarea.length && gifUrl) {
                const currentVal = $textarea.val() || '';
                $textarea.val(currentVal + (currentVal ? '\n' : '') + '[gif]' + gifUrl + '[/gif]');
                $textarea.focus();
                updateSubmitButton();

                // Ajouter une preview du GIF
                const $preview = $('#vic-attachments-preview');
                $preview.append(`
                    <div class="vic-attachment-item vic-gif-preview-item">
                        <img src="${gifUrl}" alt="GIF">
                        <button type="button" class="vic-remove-attachment vic-remove-gif-preview" data-gif="${gifUrl}">✕</button>
                    </div>
                `);
            }

            // Fermer le picker
            $('.vic-gif-picker').remove();
        });

        // Supprimer la preview GIF du post
        $(document).on('click', '.vic-remove-gif-preview', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const gifUrl = $(this).data('gif');
            const $textarea = $('#vic-new-post-form textarea[name="post_content"]');

            // Retirer le GIF du textarea
            if ($textarea.length && gifUrl) {
                const gifTag = '[gif]' + gifUrl + '[/gif]';
                $textarea.val($textarea.val().replace(gifTag, '').trim());
            }

            $(this).closest('.vic-gif-preview-item').remove();
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
        const $searchBox = $('.vic-search-box');
        const $searchToggle = $('#vic-search-toggle');
        const $feed = $('#vic-feed');
        const $loadMore = $('#vic-load-more');

        // Toggle search box visibility
        $searchToggle.on('click', function() {
            $searchBox.toggleClass('vic-search-open');
            $(this).toggleClass('active');

            if ($searchBox.hasClass('vic-search-open')) {
                $searchInput.focus();
            } else {
                // Clear search when closing
                $searchInput.val('');
                $searchClear.hide();
                performSearch('');
            }
        });

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

        // Comment file upload (compatible Skool-style et ancien style)
        $(document).on('change', '.vic-comment-file-input', function(e) {
            const files = Array.from(e.target.files);
            const $this = $(this);

            // Chercher la zone de preview - d'abord style Skool, puis ancien style
            let $preview = $this.closest('.vic-comment-form-wrapper-skool').find('.vic-comment-attachments-preview');
            if (!$preview.length) {
                $preview = $this.closest('.vic-comment-input-wrapper').find('.vic-comment-attachments-preview');
            }
            // Fallback: chercher dans la modale visible
            if (!$preview.length) {
                $preview = $('.vic-modal:visible .vic-comment-attachments-preview');
            }

            console.log('Upload fichier - Preview zone trouvée:', $preview.length);

            files.forEach(function(file) {
                if (file.size > 10 * 1024 * 1024) {
                    alert('Fichier trop volumineux (max 10MB)');
                    return;
                }

                commentFiles.push(file);
                addCommentFilePreview(file, $preview);
            });

            updateCommentSubmitState();
            // Reset l'input pour pouvoir re-sélectionner le même fichier
            $this.val('');
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

        // Add link to comment with preview
        $(document).on('click', '.vic-comment-add-link', function() {
            const url = prompt('Entrez l\'URL :');
            if (url && url.trim()) {
                const $wrapper = $(this).closest('.vic-comment-form-wrapper-skool, .vic-comment-input-wrapper');
                let $input = $wrapper.find('.vic-comment-input-skool');
                if (!$input.length) {
                    $input = $wrapper.find('.vic-comment-input');
                }

                // Ajouter l'URL au texte
                const currentVal = $input.val();
                $input.val(currentVal + (currentVal ? ' ' : '') + url.trim());
                $input.trigger('input');

                // Ajouter un aperçu visuel du lien
                let $preview = $wrapper.find('.vic-comment-attachments-preview');
                if (!$preview.length) {
                    $preview = $wrapper.closest('.vic-comment-form-wrapper-skool').find('.vic-comment-attachments-preview');
                }

                if ($preview.length) {
                    // Extraire le domaine pour l'affichage
                    let domain = url.trim();
                    try {
                        domain = new URL(url.trim()).hostname;
                    } catch(e) {}

                    const $linkPreview = $(`
                        <div class="vic-comment-attachment-item vic-link-preview">
                            <span class="vic-file-icon">🔗</span>
                            <span class="vic-file-name" title="${url.trim()}">${domain}</span>
                            <button type="button" class="vic-comment-attachment-remove vic-link-remove">✕</button>
                        </div>
                    `);
                    $linkPreview.data('url', url.trim());
                    $preview.append($linkPreview);
                }

                $input.focus();
            }
        });

        // Remove link preview
        $(document).on('click', '.vic-link-remove', function() {
            const $item = $(this).closest('.vic-link-preview');
            const urlToRemove = $item.data('url');
            const $wrapper = $item.closest('.vic-comment-form-wrapper-skool, .vic-comment-input-wrapper');
            let $input = $wrapper.find('.vic-comment-input-skool');
            if (!$input.length) {
                $input = $wrapper.find('.vic-comment-input');
            }

            // Retirer l'URL du texte
            if ($input.length && urlToRemove) {
                $input.val($input.val().replace(urlToRemove, '').trim());
            }
            $item.remove();
        });

        // Skool-style comment submission (on Enter key)
        $(document).on('keypress', '.vic-comment-input-skool', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                submitSkoolComment($(this));
            }
        });

        // Skool-style comment submission (on button click)
        $(document).on('click', '.vic-comment-submit-skool', function(e) {
            e.preventDefault();
            const $wrapper = $(this).closest('.vic-comment-form-wrapper-skool');
            const $input = $wrapper.find('.vic-comment-input-skool');
            if ($input.length) {
                submitSkoolComment($input);
            }
        });

        // Submit Skool-style comment
        function submitSkoolComment($input) {
            let content = $input.val().trim();
            const $wrapper = $input.closest('.vic-comment-form-wrapper-skool');
            const postId = $('.vic-modal').find('.vic-like-btn[data-post-id]').data('post-id');

            // Récupérer le GIF sélectionné s'il y en a un
            const selectedGif = $wrapper.data('selected-gif') || '';

            // Ajouter le GIF au contenu s'il existe
            if (selectedGif) {
                // Format markdown pour l'image GIF
                content = content + (content ? '\n' : '') + '[gif]' + selectedGif + '[/gif]';
            }

            if ((!content && commentFiles.length === 0) || !postId) return;

            // Désactiver l'input et le bouton d'envoi
            $input.prop('disabled', true);
            const $submitBtn = $wrapper.find('.vic-comment-submit-skool');
            $submitBtn.prop('disabled', true);

            // Créer la barre de progression
            let $progressBar = $wrapper.find('.vic-upload-progress');
            if (!$progressBar.length) {
                $progressBar = $('<div class="vic-upload-progress"><div class="vic-upload-progress-track"><div class="vic-upload-progress-bar"></div></div><span class="vic-upload-progress-text">0%</span></div>');
                $wrapper.find('.vic-comment-attachments-preview').before($progressBar);
            }
            $progressBar.show().removeClass('processing');
            const $progressFill = $progressBar.find('.vic-upload-progress-bar');
            const $progressText = $progressBar.find('.vic-upload-progress-text');
            $progressFill.css('width', '0%');

            const formData = new FormData();
            formData.append('action', 'vic_add_comment');
            formData.append('nonce', vicAjax.nonce);
            formData.append('post_id', postId);
            formData.append('content', content);

            // Add files from commentFiles array (global)
            if (commentFiles && commentFiles.length > 0) {
                commentFiles.forEach(function(file) {
                    formData.append('comment_attachments[]', file);
                });
                console.log('Envoi de', commentFiles.length, 'fichier(s)');
            }

            $.ajax({
                url: vicAjax.ajaxurl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                xhr: function() {
                    const xhr = new window.XMLHttpRequest();
                    // Upload progress (seulement jusqu'à 90%, les 10% restants pour le traitement serveur)
                    xhr.upload.addEventListener('progress', function(e) {
                        if (e.lengthComputable) {
                            // Upload = 0-90%, traitement serveur = 90-100%
                            const percent = Math.round((e.loaded / e.total) * 90);
                            $progressFill.css('width', percent + '%');
                            $progressText.text(percent + '%');
                        }
                    }, false);
                    // Quand l'upload est terminé, passer en mode "traitement"
                    xhr.upload.addEventListener('load', function() {
                        $progressBar.addClass('processing');
                        $progressFill.css('width', '90%');
                        $progressText.text('Traitement...');
                    }, false);
                    return xhr;
                },
                success: function(response) {
                    if (response.success) {
                        // Animation de complétion
                        $progressBar.removeClass('processing');
                        $progressFill.css('width', '100%');
                        $progressText.text('Envoyé !');

                        setTimeout(function() {
                            // Add new comment to list
                            const $noComments = $('.vic-comments-list .vic-no-comments');
                            if ($noComments.length) {
                                $noComments.remove();
                            }
                            $('.vic-comments-list').append(response.data.comment_html);

                            // Reset form
                            $input.val('');
                            $wrapper.find('.vic-comment-attachments-preview').empty();
                            $wrapper.data('selected-gif', '');
                            $wrapper.find('.vic-comment-file-input').val('');
                            commentFiles = []; // Reset le tableau global
                            $progressBar.hide();
                            $progressFill.css('width', '0%');

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
                        }, 500);
                    } else {
                        alert(response.data.message || 'Erreur');
                        $progressBar.hide();
                    }
                },
                error: function() {
                    alert('Erreur de connexion');
                    $progressBar.hide();
                },
                complete: function() {
                    $input.prop('disabled', false).focus();
                    $submitBtn.prop('disabled', false);
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

                        // Attacher le listener scroll au modal content
                        setTimeout(function() {
                            const $modalContent = $('.vic-modal-content');
                            if ($modalContent.length) {
                                $modalContent.on('scroll', function() {
                                    const $jumpBtn = $('#vic-jump-latest');
                                    if (!$jumpBtn.length) return;

                                    const scrollTop = $(this).scrollTop();
                                    const scrollHeight = this.scrollHeight;
                                    const clientHeight = this.clientHeight;

                                    // Si on est proche du bas (< 120px), masquer le bouton
                                    if (scrollTop + clientHeight >= scrollHeight - 120) {
                                        $jumpBtn.addClass('hidden');
                                    } else {
                                        $jumpBtn.removeClass('hidden');
                                    }
                                });
                            }
                        }, 100);
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
                // Utiliser URL.createObjectURL - beaucoup plus rapide que FileReader
                const objectUrl = URL.createObjectURL(file);
                const $img = $('<img src="' + objectUrl + '" alt="">');
                $item.prepend($img);
                // Libérer la mémoire quand l'image est chargée
                $img.on('load', function() {
                    URL.revokeObjectURL(objectUrl);
                });
            } else if (file.type.startsWith('video/')) {
                $item.prepend('<span class="vic-file-icon">🎬</span>');
            } else if (file.type.startsWith('audio/')) {
                $item.prepend('<span class="vic-file-icon">🎵</span>');
            } else if (file.type === 'application/pdf') {
                $item.prepend('<span class="vic-file-icon">📄</span>');
            } else {
                $item.prepend('<span class="vic-file-icon">📎</span>');
            }

            $item.append('<span class="vic-file-name">' + file.name.substring(0, 20) + (file.name.length > 20 ? '...' : '') + '</span>');
            $item.append('<button type="button" class="vic-comment-attachment-remove">✕</button>');
            $preview.append($item);

            console.log('Preview ajoutée pour:', file.name);
        }

        function updateCommentSubmitState() {
            // Support both Skool-style and legacy input
            let $input = $('.vic-comment-input-skool:visible');
            if (!$input.length) {
                $input = $('.vic-comment-input');
            }
            const $submit = $('.vic-comment-submit-skool, .vic-comment-submit');

            // Vérifier si l'input existe avant d'appeler .val()
            const inputVal = $input.length ? $input.val() : '';
            const hasContent = (inputVal && inputVal.trim().length > 0) || commentFiles.length > 0;

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
            e.stopPropagation();

            // Scroller vers la zone de commentaire (input en bas)
            const $commentForm = $('.vic-comment-form-wrapper-skool');
            if ($commentForm.length) {
                $commentForm[0].scrollIntoView({ behavior: 'smooth', block: 'end' });
            } else {
                // Fallback: scroller vers le dernier commentaire
                const $lastComment = $('.vic-comments-list > .vic-comment:last-child');
                if ($lastComment.length) {
                    $lastComment[0].scrollIntoView({ behavior: 'smooth', block: 'end' });
                }
            }
        });

        // Détection scroll pour masquer le bouton jump quand en bas
        // Utilisation de l'event delegation sur le modal-content
        $(document).on('scroll', function(e) {
            const $modalContent = $('.vic-modal-content');
            if (!$modalContent.length) return;

            const $jumpBtn = $('#vic-jump-latest');
            if (!$jumpBtn.length) return;

            const modalContent = $modalContent[0];
            const scrollTop = $modalContent.scrollTop();
            const scrollHeight = modalContent.scrollHeight;
            const clientHeight = modalContent.clientHeight;

            // Si on est proche du bas (< 100px du bas), masquer le bouton
            if (scrollTop + clientHeight >= scrollHeight - 100) {
                $jumpBtn.addClass('hidden');
            } else {
                $jumpBtn.removeClass('hidden');
            }
        });

        // Observer pour le scroll dans la modale (car l'event delegation marche mal sur scroll)
        $(document).on('DOMNodeInserted', '.vic-modal-content', function() {
            const $modalContent = $(this);
            $modalContent.off('scroll.vicjump').on('scroll.vicjump', function() {
                const $jumpBtn = $('#vic-jump-latest');
                if (!$jumpBtn.length) return;

                const scrollTop = $(this).scrollTop();
                const scrollHeight = this.scrollHeight;
                const clientHeight = this.clientHeight;

                if (scrollTop + clientHeight >= scrollHeight - 100) {
                    $jumpBtn.addClass('hidden');
                } else {
                    $jumpBtn.removeClass('hidden');
                }
            });
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
                const $wrapper = $(this).closest('.vic-comment-form-wrapper-skool, .vic-comment-input-wrapper');
                let $input = $wrapper.find('.vic-comment-input-skool');
                if (!$input.length) {
                    $input = $wrapper.find('.vic-comment-input');
                }
                if (!$input.length) {
                    $input = $('.vic-comment-input-skool');
                }

                // Extraire l'ID de la vidéo YouTube
                const youtubeRegex = /(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([a-zA-Z0-9_-]+)/;
                const match = url.trim().match(youtubeRegex);

                if (match && match[1]) {
                    const videoId = match[1];
                    const thumbnailUrl = 'https://img.youtube.com/vi/' + videoId + '/mqdefault.jpg';

                    // Ajouter l'URL au texte
                    const currentVal = $input.val();
                    $input.val(currentVal + (currentVal ? ' ' : '') + url.trim());
                    $input.trigger('input');

                    // Ajouter un aperçu visuel avec miniature
                    let $preview = $wrapper.find('.vic-comment-attachments-preview');
                    if (!$preview.length) {
                        $preview = $wrapper.closest('.vic-comment-form-wrapper-skool').find('.vic-comment-attachments-preview');
                    }

                    if ($preview.length) {
                        const $ytPreview = $(`
                            <div class="vic-comment-attachment-item vic-youtube-preview">
                                <img src="${thumbnailUrl}" alt="YouTube" class="vic-youtube-thumb">
                                <span class="vic-youtube-play">▶</span>
                                <button type="button" class="vic-comment-attachment-remove vic-youtube-remove">✕</button>
                            </div>
                        `);
                        $ytPreview.data('url', url.trim());
                        $preview.append($ytPreview);
                    }

                    $input.focus();
                } else {
                    alert('URL YouTube invalide. Formats acceptés:\n- https://www.youtube.com/watch?v=VIDEO_ID\n- https://youtu.be/VIDEO_ID');
                }
            }
        });

        // Remove YouTube preview
        $(document).on('click', '.vic-youtube-remove', function() {
            const $item = $(this).closest('.vic-youtube-preview');
            const urlToRemove = $item.data('url');
            const $wrapper = $item.closest('.vic-comment-form-wrapper-skool, .vic-comment-input-wrapper');
            let $input = $wrapper.find('.vic-comment-input-skool');
            if (!$input.length) {
                $input = $wrapper.find('.vic-comment-input');
            }

            // Retirer l'URL du texte
            if ($input.length && urlToRemove) {
                $input.val($input.val().replace(urlToRemove, '').trim());
            }
            $item.remove();
        });

        // ====== EMOJI PICKER COMPLET ======
        // (emojiData est défini au début du fichier)

        const emojiCategories = [
            { id: 'smileys', icon: '😀', name: 'Smileys' },
            { id: 'people', icon: '👋', name: 'Personnes' },
            { id: 'nature', icon: '🌿', name: 'Nature' },
            { id: 'food', icon: '🍕', name: 'Nourriture' },
            { id: 'activities', icon: '⚽', name: 'Activités' },
            { id: 'travel', icon: '✈️', name: 'Voyage' },
            { id: 'objects', icon: '💡', name: 'Objets' },
            { id: 'symbols', icon: '❤️', name: 'Symboles' }
        ];

        $(document).on('click', '.vic-comment-add-emoji', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const $btn = $(this);

            // Fermer autres pickers
            $('.vic-emoji-picker-full, .vic-emoji-picker, .vic-gif-picker').remove();

            // Créer le picker complet
            const $picker = $('<div class="vic-emoji-picker-full"></div>');

            // Barre de recherche
            $picker.append('<div class="vic-emoji-search"><input type="text" placeholder="Rechercher un emoji..." class="vic-emoji-search-input"></div>');

            // Catégories
            const $categories = $('<div class="vic-emoji-categories"></div>');
            emojiCategories.forEach(function(cat, idx) {
                $categories.append('<button data-category="' + cat.id + '" ' + (idx === 0 ? 'class="active"' : '') + ' title="' + cat.name + '">' + cat.icon + '</button>');
            });
            $picker.append($categories);

            // Grille d'emojis
            const $grid = $('<div class="vic-emoji-grid"></div>');
            renderEmojiCategory('smileys', $grid);
            $picker.append($grid);

            $btn.after($picker);
            $picker.find('.vic-emoji-search-input').focus();
        });

        function renderEmojiCategory(category, $grid) {
            $grid.empty();
            const emojis = emojiData[category] || [];
            emojis.forEach(function(emoji) {
                $grid.append('<span class="vic-emoji-item">' + emoji + '</span>');
            });
        }

        // Changer de catégorie
        $(document).on('click', '.vic-emoji-categories button', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const category = $(this).data('category');
            const $picker = $(this).closest('.vic-emoji-picker-full');

            $picker.find('.vic-emoji-categories button').removeClass('active');
            $(this).addClass('active');

            renderEmojiCategory(category, $picker.find('.vic-emoji-grid'));
        });

        // Recherche emoji
        $(document).on('input', '.vic-emoji-search-input', function() {
            const query = $(this).val().toLowerCase();
            const $picker = $(this).closest('.vic-emoji-picker-full');
            const $grid = $picker.find('.vic-emoji-grid');

            if (query.length < 1) {
                // Afficher catégorie active
                const activeCategory = $picker.find('.vic-emoji-categories button.active').data('category');
                renderEmojiCategory(activeCategory, $grid);
                return;
            }

            // Rechercher dans toutes les catégories
            $grid.empty();
            let found = [];
            Object.keys(emojiData).forEach(function(cat) {
                emojiData[cat].forEach(function(emoji) {
                    if (found.length < 50) {
                        found.push(emoji);
                    }
                });
            });

            // Afficher les résultats (on prend les 40 premiers pour l'instant)
            found.slice(0, 40).forEach(function(emoji) {
                $grid.append('<span class="vic-emoji-item">' + emoji + '</span>');
            });
        });

        // Insérer emoji - FIX du bug
        $(document).on('click', '.vic-emoji-item', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const $emojiItem = $(this);

            // WordPress/Twemoji convertit les emojis en images <img>
            // On doit récupérer l'emoji depuis l'attribut alt de l'image
            let emoji = '';
            const $img = $emojiItem.find('img.emoji');
            if ($img.length) {
                // Emoji rendu comme image par Twemoji
                emoji = $img.attr('alt') || '';
            } else {
                // Emoji en texte brut (fallback)
                emoji = $emojiItem.text().trim();
            }

            console.log('Emoji inséré:', emoji);

            if (!emoji) {
                console.error('Aucun emoji trouvé');
                return;
            }

            // Trouver l'input - la modale est prioritaire car c'est là qu'on commente
            const $picker = $emojiItem.closest('.vic-emoji-picker-full, .vic-emoji-picker');
            let $input = null;

            // Méthode 1: Chercher dans le wrapper parent direct
            const $wrapper = $picker.closest('.vic-comment-input-skool-wrapper');
            if ($wrapper.length) {
                $input = $wrapper.find('.vic-comment-input-skool');
            }

            // Méthode 2: Chercher dans la modale visible
            if (!$input || !$input.length) {
                const $modal = $('.vic-modal:visible');
                if ($modal.length) {
                    $input = $modal.find('.vic-comment-input-skool');
                }
            }

            // Méthode 3: Chercher globalement (fallback)
            if (!$input || !$input.length) {
                $input = $('.vic-comment-input-skool:visible').first();
            }

            if ($input && $input.length) {
                const currentVal = $input.val() || '';
                const newVal = currentVal + emoji;

                // Définir la valeur
                $input.val(newVal);
                $input[0].value = newVal;

                // Trigger les événements pour React/Vue/autres frameworks
                $input.trigger('input').trigger('change');

                // Focus et curseur à la fin
                $input[0].focus();
                const len = newVal.length;
                $input[0].setSelectionRange(len, len);

                console.log('Emoji ajouté, nouvelle valeur:', newVal);
            } else {
                console.error('Aucun input trouvé pour insérer l\'emoji');
            }

            // Fermer le picker
            $('.vic-emoji-picker-full, .vic-emoji-picker').remove();
        });

        // Fermer emoji picker au clic ailleurs
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.vic-comment-add-emoji, .vic-emoji-picker-full, .vic-emoji-picker').length) {
                $('.vic-emoji-picker-full, .vic-emoji-picker').remove();
            }
        });

        // ====== GIF PICKER AVEC TENOR ======
        // Tenor API (plus fiable que Giphy beta key)
        const TENOR_API_KEY = 'AIzaSyAyimkuYQYF_FXVALexPuGQctUWRURdCYQ'; // Google Tenor API key publique

        $(document).on('click', '.vic-comment-add-gif', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const $btn = $(this);

            // Fermer autres pickers
            $('.vic-gif-picker, .vic-emoji-picker-full, .vic-emoji-picker').remove();

            // Créer le picker GIF
            const $picker = $(`
                <div class="vic-gif-picker">
                    <div class="vic-gif-header">
                        <input type="text" placeholder="Rechercher des GIFs..." class="vic-gif-search-input">
                        <span class="vic-gif-powered">Tenor</span>
                    </div>
                    <div class="vic-gif-grid">
                        <div class="vic-gif-loading">Chargement...</div>
                    </div>
                </div>
            `);

            $btn.after($picker);

            // Charger les GIFs tendance
            loadTrendingGifs($picker.find('.vic-gif-grid'));

            $picker.find('.vic-gif-search-input').focus();
        });

        // Charger GIFs tendance avec Tenor
        function loadTrendingGifs($container) {
            $container.html('<div class="vic-gif-loading">Chargement...</div>');

            fetch('https://tenor.googleapis.com/v2/featured?key=' + TENOR_API_KEY + '&limit=20&media_filter=gif,tinygif&contentfilter=medium')
                .then(response => {
                    if (!response.ok) throw new Error('Network error');
                    return response.json();
                })
                .then(data => {
                    console.log('Tenor response:', data);
                    if (data.results) {
                        renderGifsTenor(data.results, $container);
                    } else {
                        $container.html('<div class="vic-gif-loading">Aucun GIF disponible</div>');
                    }
                })
                .catch(err => {
                    console.error('Tenor error:', err);
                    // Fallback: afficher des GIFs populaires en dur
                    showFallbackGifs($container);
                });
        }

        // Rechercher GIFs avec Tenor
        let gifSearchTimeout;
        $(document).on('input', '.vic-gif-search-input', function() {
            const query = $(this).val().trim();
            const $picker = $(this).closest('.vic-gif-picker');
            const $grid = $picker.find('.vic-gif-grid');

            clearTimeout(gifSearchTimeout);

            if (query.length < 2) {
                loadTrendingGifs($grid);
                return;
            }

            $grid.html('<div class="vic-gif-loading">Recherche...</div>');

            gifSearchTimeout = setTimeout(function() {
                fetch('https://tenor.googleapis.com/v2/search?key=' + TENOR_API_KEY + '&q=' + encodeURIComponent(query) + '&limit=20&media_filter=gif,tinygif&contentfilter=medium')
                    .then(response => response.json())
                    .then(data => {
                        if (data.results) {
                            renderGifsTenor(data.results, $grid);
                        } else {
                            $grid.html('<div class="vic-gif-loading">Aucun GIF trouvé</div>');
                        }
                    })
                    .catch(() => {
                        $grid.html('<div class="vic-gif-loading">Erreur de recherche</div>');
                    });
            }, 300);
        });

        // Afficher les GIFs Tenor
        function renderGifsTenor(gifs, $container) {
            $container.empty();

            if (!gifs || gifs.length === 0) {
                $container.html('<div class="vic-gif-loading">Aucun GIF trouvé</div>');
                return;
            }

            gifs.forEach(function(gif) {
                // Tenor v2 API structure
                const previewUrl = gif.media_formats?.tinygif?.url || gif.media_formats?.gif?.url || '';
                const fullUrl = gif.media_formats?.gif?.url || previewUrl;

                if (previewUrl) {
                    const $img = $('<img src="' + previewUrl + '" alt="' + (gif.content_description || 'GIF') + '" data-full="' + fullUrl + '">');
                    $container.append($img);
                }
            });

            if ($container.children().length === 0) {
                $container.html('<div class="vic-gif-loading">Aucun GIF disponible</div>');
            }
        }

        // Fallback GIFs si l'API ne marche pas
        function showFallbackGifs($container) {
            const fallbackGifs = [
                'https://media.giphy.com/media/l0MYt5jPR6QX5pnqM/giphy.gif',
                'https://media.giphy.com/media/3o7abKhOpu0NwenH3O/giphy.gif',
                'https://media.giphy.com/media/111ebonMs90YLu/giphy.gif',
                'https://media.giphy.com/media/3o7TKSjRrfIPjeiVyM/giphy.gif',
                'https://media.giphy.com/media/QMHoU66sBXqqLqYvGO/giphy.gif',
                'https://media.giphy.com/media/5GoVLqeAOo6PK/giphy.gif',
                'https://media.giphy.com/media/3o6Zt6KHxJTbXCnSvu/giphy.gif',
                'https://media.giphy.com/media/xUPGGDNsLvqsBOhuU0/giphy.gif'
            ];

            $container.empty();
            fallbackGifs.forEach(function(url) {
                const $img = $('<img src="' + url + '" alt="GIF" data-full="' + url + '">');
                $container.append($img);
            });
        }

        // Insérer GIF sélectionné - Afficher comme image dans la preview
        $(document).on('click', '.vic-gif-grid img', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const gifUrl = $(this).data('full');
            if (!gifUrl) return;

            // Trouver le wrapper de preview des attachements
            const $picker = $(this).closest('.vic-gif-picker');
            let $previewContainer = null;
            let $input = null;

            // Trouver le form wrapper parent
            const $formWrapper = $('.vic-comment-form-wrapper-skool');
            if ($formWrapper.length) {
                $previewContainer = $formWrapper.find('.vic-comment-attachments-preview');
                $input = $formWrapper.find('.vic-comment-input-skool');
            }

            // Ajouter le GIF à la preview
            if ($previewContainer && $previewContainer.length) {
                // Supprimer les GIFs précédents (on n'en garde qu'un)
                $previewContainer.find('.vic-gif-preview-item').remove();

                // Créer l'élément de preview du GIF
                const $gifPreview = $(`
                    <div class="vic-gif-preview-item" data-gif-url="${gifUrl}">
                        <img src="${gifUrl}" alt="GIF sélectionné">
                        <button type="button" class="vic-gif-preview-remove">✕</button>
                    </div>
                `);
                $previewContainer.append($gifPreview);

                // Stocker l'URL du GIF dans un data attribute sur le form
                $formWrapper.data('selected-gif', gifUrl);
            }

            // Fermer le picker
            $('.vic-gif-picker').remove();

            // Focus sur l'input
            if ($input && $input.length) {
                $input.focus();
            }
        });

        // Supprimer le GIF de la preview
        $(document).on('click', '.vic-gif-preview-remove', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const $item = $(this).closest('.vic-gif-preview-item');
            const $formWrapper = $item.closest('.vic-comment-form-wrapper-skool');
            $formWrapper.data('selected-gif', '');
            $item.remove();
        });

        // Fermer GIF picker au clic ailleurs
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.vic-comment-add-gif, .vic-gif-picker').length) {
                $('.vic-gif-picker').remove();
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
