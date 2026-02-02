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

    // ====== COMPRESSION D'IMAGE CÔTÉ CLIENT ======
    // Redimensionne et compresse les images avant upload pour accélérer le traitement
    function compressImage(file, maxWidth, maxHeight, quality) {
        return new Promise(function(resolve) {
            // Si ce n'est pas une image, retourner le fichier original
            if (!file.type.startsWith('image/') || file.type === 'image/gif') {
                resolve(file);
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                const img = new Image();
                img.onload = function() {
                    // Calculer les nouvelles dimensions en gardant le ratio
                    let width = img.width;
                    let height = img.height;

                    if (width > maxWidth) {
                        height = Math.round((height * maxWidth) / width);
                        width = maxWidth;
                    }
                    if (height > maxHeight) {
                        width = Math.round((width * maxHeight) / height);
                        height = maxHeight;
                    }

                    // Si l'image est déjà plus petite et < 1MB, ne pas compresser
                    if (img.width <= maxWidth && img.height <= maxHeight && file.size < 1024 * 1024) {
                        resolve(file);
                        return;
                    }

                    // Créer un canvas pour redimensionner
                    const canvas = document.createElement('canvas');
                    canvas.width = width;
                    canvas.height = height;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0, width, height);

                    // Convertir en blob
                    canvas.toBlob(function(blob) {
                        if (blob) {
                            // Créer un nouveau fichier avec le même nom
                            const compressedFile = new File([blob], file.name, {
                                type: 'image/jpeg',
                                lastModified: Date.now()
                            });
                            console.log('Image compressée: ' + (file.size / 1024 / 1024).toFixed(2) + 'MB -> ' + (compressedFile.size / 1024 / 1024).toFixed(2) + 'MB');
                            resolve(compressedFile);
                        } else {
                            resolve(file);
                        }
                    }, 'image/jpeg', quality);
                };
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
        });
    }

    // Compresser plusieurs fichiers
    function compressFiles(files, maxWidth, maxHeight, quality) {
        return Promise.all(files.map(function(file) {
            return compressImage(file, maxWidth, maxHeight, quality);
        }));
    }

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
            $('#vic-post-gif-input').val('');
            $('#vic-post-youtube-input').val('');
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

        // YouTube prompt avec preview
        $(document).on('click', '#vic-add-youtube', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const url = prompt('Collez l\'URL de la vidéo YouTube :');
            if (url && url.trim()) {
                // Extraire l'ID de la vidéo YouTube
                const youtubeRegex = /(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([a-zA-Z0-9_-]+)/;
                const match = url.trim().match(youtubeRegex);

                if (match && match[1]) {
                    const videoId = match[1];
                    const thumbnailUrl = 'https://img.youtube.com/vi/' + videoId + '/mqdefault.jpg';

                    // Stocker l'URL dans le champ caché
                    $('#vic-post-youtube-input').val(url.trim());

                    // Supprimer les previews YouTube existants (une seule vidéo par post)
                    $('.vic-youtube-preview-item').remove();

                    // Ajouter une preview
                    const $preview = $('#vic-attachments-preview');
                    $preview.append(`
                        <div class="vic-attachment-item vic-youtube-preview-item">
                            <img src="${thumbnailUrl}" alt="YouTube">
                            <span class="vic-youtube-play-icon">▶</span>
                            <button type="button" class="vic-remove-attachment vic-remove-youtube-preview">✕</button>
                        </div>
                    `);

                    updateSubmitButton();
                } else {
                    alert('URL YouTube invalide. Formats acceptés:\n- https://www.youtube.com/watch?v=VIDEO_ID\n- https://youtu.be/VIDEO_ID');
                }
            }
        });

        // Supprimer la preview YouTube du post
        $(document).on('click', '.vic-remove-youtube-preview', function(e) {
            e.preventDefault();
            e.stopPropagation();

            // Vider le champ caché
            $('#vic-post-youtube-input').val('');

            $(this).closest('.vic-youtube-preview-item').remove();
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
                    // Only use specific class to avoid conflicts with general handler
                    $grid.append('<span class="vic-post-emoji-item">' + emoji + '</span>');
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
                    // Only use specific class to avoid conflicts
                    $grid.append('<span class="vic-post-emoji-item">' + emoji + '</span>');
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
                        // Only use specific class to avoid conflicts
                        $grid.append('<span class="vic-post-emoji-item">' + emoji + '</span>');
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
                    // Only use specific class to avoid conflicts
                    $grid.append('<span class="vic-post-emoji-item">' + emoji + '</span>');
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

            // Stocker le GIF dans le champ caché (pas dans le textarea)
            const $gifInput = $('#vic-post-gif-input');
            if ($gifInput.length && gifUrl) {
                $gifInput.val(gifUrl);
                updateSubmitButton();

                // Supprimer les previews GIF existants (un seul GIF par post)
                $('.vic-gif-preview-item').remove();

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

            // Vider le champ caché
            $('#vic-post-gif-input').val('');

            $(this).closest('.vic-gif-preview-item').remove();
        });

        // Submit form
        $submitForm.on('submit', function(e) {
            e.preventDefault();

            const $btn = $submitForm.find('.vic-btn-primary');
            const $cancelBtn = $submitForm.find('.vic-btn-cancel');
            const originalText = $btn.text();

            // Désactiver les boutons
            $btn.text('Compression...').prop('disabled', true);
            $cancelBtn.prop('disabled', true);

            // Créer/afficher la barre de progression
            let $progressBar = $submitForm.find('.vic-upload-progress');
            if (!$progressBar.length) {
                $progressBar = $('<div class="vic-upload-progress"><div class="vic-upload-progress-track"><div class="vic-upload-progress-bar"></div></div><span class="vic-upload-progress-text">0%</span></div>');
                $preview.before($progressBar);
            }
            $progressBar.show().removeClass('processing');
            const $progressFill = $progressBar.find('.vic-upload-progress-bar');
            const $progressText = $progressBar.find('.vic-upload-progress-text');
            $progressFill.css('width', '0%');
            $progressText.text('Compression...');

            // Compresser les images avant envoi (max 1920x1080, qualité 85%)
            compressFiles(selectedFiles, 1920, 1080, 0.85).then(function(compressedFiles) {
                $btn.text('Envoi...');
                $progressText.text('0%');

                // Create FormData for file upload
                const formData = new FormData();
                formData.append('action', 'vic_create_post');
                formData.append('nonce', vicAjax.nonce);
                formData.append('post_title', $submitForm.find('input[name="post_title"]').val());
                formData.append('post_content', $submitForm.find('textarea[name="post_content"]').val());
                formData.append('post_category', $submitForm.find('select[name="post_category"]').val());
                formData.append('post_url', $submitForm.find('input[name="post_url"]').val());
                formData.append('post_gif', $('#vic-post-gif-input').val());
                formData.append('post_youtube', $('#vic-post-youtube-input').val());

                // Add compressed files
                compressedFiles.forEach(function(file) {
                    formData.append('post_attachments[]', file);
                });

                $.ajax({
                url: vicAjax.ajaxurl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                xhr: function() {
                    const xhr = new window.XMLHttpRequest();
                    // Upload progress (0-90%, les 10% restants pour le traitement serveur)
                    xhr.upload.addEventListener('progress', function(e) {
                        if (e.lengthComputable) {
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
                        $progressText.text('Publié !');

                        setTimeout(function() {
                            // Add new post to top of feed
                            $('#vic-feed').prepend(response.data.post_html);

                            // Reset and close form
                            $submitForm[0].reset();
                            selectedFiles = [];
                            $preview.empty();
                            $urlField.hide();
                            $('#vic-post-gif-input').val('');
                            $('#vic-post-youtube-input').val('');
                            $('.vic-upload-btn').removeClass('has-files');
                            $progressBar.hide();
                            $progressFill.css('width', '0%');
                            $form.slideUp(200);
                            $trigger.show();

                            // Highlight new post briefly
                            $('#vic-feed .vic-post-card').first().css('background', '#f0f9ff').animate({
                                backgroundColor: '#ffffff'
                            }, 2000);
                        }, 500);
                    } else {
                        alert(response.data.message || 'Erreur lors de la publication');
                        $progressBar.hide();
                    }
                },
                error: function() {
                    alert('Erreur de connexion. Veuillez réessayer.');
                    $progressBar.hide();
                },
                complete: function() {
                    $btn.text(originalText).prop('disabled', false);
                    $cancelBtn.prop('disabled', false);
                }
                });
            }); // fin de compressFiles.then()
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

        // Changer de catégorie (general handler - skip for specific modals)
        $(document).on('click', '.vic-emoji-categories button', function(e) {
            const $picker = $(this).closest('.vic-emoji-picker-full');

            // Skip if this is for edit comment modal (has its own handler)
            if ($picker.hasClass('vic-edit-comment-emoji-picker')) {
                console.log('SKIP general category handler - edit comment modal');
                return;
            }

            // Skip if this is for edit post modal (has its own handler)
            if ($picker.hasClass('vic-edit-emoji-picker')) {
                console.log('SKIP general category handler - edit post modal');
                return;
            }

            e.preventDefault();
            e.stopPropagation();
            const category = $(this).data('category');

            $picker.find('.vic-emoji-categories button').removeClass('active');
            $(this).addClass('active');

            renderEmojiCategory(category, $picker.find('.vic-emoji-grid'));
        });

        // Recherche emoji (general handler - skip for specific modals)
        $(document).on('input', '.vic-emoji-search-input', function() {
            const $picker = $(this).closest('.vic-emoji-picker-full');

            // Skip if this is for edit comment modal
            if ($picker.hasClass('vic-edit-comment-emoji-picker')) {
                return;
            }

            // Skip if this is for edit post modal
            if ($picker.hasClass('vic-edit-emoji-picker')) {
                return;
            }

            const query = $(this).val().toLowerCase();
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
            console.log('=== GENERAL EMOJI HANDLER (.vic-emoji-item) TRIGGERED ===');
            console.log('Element classes:', $(this).attr('class'));

            const $emojiItem = $(this);

            // Skip if this is for edit comment modal (handled by specific handler)
            if ($emojiItem.hasClass('vic-edit-comment-emoji-item')) {
                console.log('SKIPPING - has vic-edit-comment-emoji-item class');
                return;
            }

            // Skip if this is for edit post modal (handled by specific handler)
            if ($emojiItem.hasClass('vic-edit-emoji-item')) {
                console.log('SKIPPING - has vic-edit-emoji-item class');
                return;
            }

            console.log('PROCESSING in general handler');
            e.preventDefault();
            e.stopPropagation();

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

            // Méthode 3: Chercher globalement pour commentaires (fallback)
            if (!$input || !$input.length) {
                $input = $('.vic-comment-input-skool:visible').first();
            }

            // Méthode 4: Chercher dans le formulaire de création de post
            if (!$input || !$input.length) {
                const $postForm = $('#vic-new-post-form');
                if ($postForm.is(':visible')) {
                    $input = $postForm.find('textarea[name="post_content"]');
                    console.log('Found post form textarea:', $input.length);
                }
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
            // Skip if handled by specific handlers (let them deal with it)
            if ($(this).hasClass('vic-edit-comment-gif-item')) return;
            if ($(this).hasClass('vic-edit-gif-item')) return;
            if ($(this).hasClass('vic-post-gif-item')) return;

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

    // ====== POST MENU (Edit/Delete) ======
    initPostMenu();

    function initPostMenu() {
        // Toggle menu dropdown
        $(document).on('click', '.vic-post-menu-trigger', function(e) {
            e.stopPropagation();
            const $menu = $(this).closest('.vic-post-menu');
            const $dropdown = $menu.find('.vic-post-menu-dropdown');

            // Close all other menus
            $('.vic-post-menu-dropdown').not($dropdown).removeClass('show');

            $dropdown.toggleClass('show');
        });

        // Close menu when clicking outside
        $(document).on('click', function() {
            $('.vic-post-menu-dropdown').removeClass('show');
        });

        // Delete post
        $(document).on('click', '.vic-delete-post', function(e) {
            e.stopPropagation();
            const postId = $(this).data('post-id');

            if (!confirm('Êtes-vous sûr de vouloir supprimer ce post ? Cette action est irréversible.')) {
                return;
            }

            $.ajax({
                url: vicAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'vic_delete_post',
                    nonce: vicAjax.nonce,
                    post_id: postId
                },
                success: function(response) {
                    if (response.success) {
                        // Remove post card from feed
                        $('.vic-post-card[data-post-id="' + postId + '"]').fadeOut(300, function() {
                            $(this).remove();
                        });

                        // Close modal if open
                        $('.vic-modal-overlay').fadeOut(300, function() {
                            $(this).remove();
                        });
                        $('body').removeClass('vic-modal-open');
                    } else {
                        alert(response.data.message || 'Erreur lors de la suppression');
                    }
                },
                error: function() {
                    alert('Erreur de connexion');
                }
            });
        });

        // Edit post - open full edit modal (like creation form)
        $(document).on('click', '.vic-edit-post', function(e) {
            e.stopPropagation();
            const postId = $(this).data('post-id');
            const $postCard = $('.vic-post-card[data-post-id="' + postId + '"]');

            // Get current post data
            const currentTitle = $postCard.find('.vic-post-title').text().trim();

            // Create full edit modal (similar to creation form)
            const editModal = `
                <div class="vic-edit-modal-overlay">
                    <div class="vic-edit-modal vic-edit-modal-full">
                        <div class="vic-edit-modal-header">
                            <h3>Modifier le post</h3>
                            <button class="vic-edit-modal-close">✕</button>
                        </div>
                        <form class="vic-edit-post-form" data-post-id="${postId}">
                            <input type="text" name="edit_post_title" class="vic-input vic-input-title" value="${escapeHtml(currentTitle)}" placeholder="Titre" required>
                            <textarea name="edit_post_content" class="vic-textarea" placeholder="Contenu..." required></textarea>

                            <!-- Hidden fields for GIF and YouTube -->
                            <input type="hidden" name="edit_post_gif" id="vic-edit-post-gif-input" value="">
                            <input type="hidden" name="edit_post_youtube" id="vic-edit-post-youtube-input" value="">
                            <input type="hidden" name="edit_attachments_to_remove" id="vic-edit-attachments-to-remove" value="">

                            <!-- URL field -->
                            <div class="vic-url-field vic-edit-url-field" id="vic-edit-url-field" style="display: none;">
                                <input type="url" name="edit_post_url" placeholder="https://example.com" class="vic-input">
                                <button type="button" class="vic-btn-remove-field" data-target="vic-edit-url-field">✕</button>
                            </div>

                            <!-- Current attachments preview -->
                            <div class="vic-edit-current-attachments" id="vic-edit-current-attachments">
                                <div class="vic-edit-loading">Chargement des médias...</div>
                            </div>

                            <!-- New attachments preview -->
                            <div class="vic-attachments-preview vic-edit-attachments-preview" id="vic-edit-attachments-preview"></div>

                            <div class="vic-form-footer vic-edit-form-footer">
                                <div class="vic-form-actions vic-edit-form-actions">
                                    <!-- Attachment button -->
                                    <label class="vic-btn-icon vic-upload-btn vic-edit-upload-btn" title="Ajouter une pièce jointe">
                                        <input type="file" name="edit_post_attachments[]" multiple accept="image/*,video/*,audio/*,.pdf" style="display:none;" id="vic-edit-file-input">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"/>
                                        </svg>
                                    </label>

                                    <!-- Link button -->
                                    <button type="button" class="vic-btn-icon vic-edit-add-url" title="Ajouter un lien">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
                                            <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
                                        </svg>
                                    </button>

                                    <!-- YouTube button -->
                                    <button type="button" class="vic-btn-icon vic-edit-add-youtube" title="Ajouter une vidéo YouTube">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <rect x="2" y="4" width="20" height="16" rx="2"/>
                                            <polygon points="10,8 16,12 10,16"/>
                                        </svg>
                                    </button>

                                    <!-- Emoji button -->
                                    <button type="button" class="vic-btn-icon vic-edit-add-emoji" title="Ajouter un emoji">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <circle cx="12" cy="12" r="10"/>
                                            <path d="M8 14s1.5 2 4 2 4-2 4-2"/>
                                            <line x1="9" y1="9" x2="9.01" y2="9"/>
                                            <line x1="15" y1="9" x2="15.01" y2="9"/>
                                        </svg>
                                    </button>

                                    <!-- GIF button -->
                                    <button type="button" class="vic-btn-icon vic-edit-add-gif" title="Ajouter un GIF">
                                        <span style="font-weight: 600; font-size: 11px;">GIF</span>
                                    </button>
                                </div>

                                <div class="vic-form-submit">
                                    <button type="button" class="vic-btn vic-btn-cancel vic-edit-modal-close">ANNULER</button>
                                    <button type="submit" class="vic-btn vic-btn-primary">ENREGISTRER</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            `;

            $('body').append(editModal);

            // Initialize edit modal state
            window.vicEditSelectedFiles = [];
            window.vicEditAttachmentsToRemove = [];

            // Load current post data via AJAX
            $.ajax({
                url: vicAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'vic_get_post_data_for_edit',
                    nonce: vicAjax.nonce,
                    post_id: postId
                },
                success: function(response) {
                    if (response.success) {
                        const data = response.data;

                        // Fill content
                        $('.vic-edit-post-form textarea[name="edit_post_content"]').val(data.content);

                        // Fill URL if exists
                        if (data.url) {
                            $('#vic-edit-url-field').show().find('input').val(data.url);
                        }

                        // Fill GIF if exists
                        if (data.gif) {
                            $('#vic-edit-post-gif-input').val(data.gif);
                        }

                        // Fill YouTube if exists
                        if (data.youtube) {
                            $('#vic-edit-post-youtube-input').val(data.youtube);
                        }

                        // Display current attachments
                        const $attachmentsContainer = $('#vic-edit-current-attachments');
                        $attachmentsContainer.empty();

                        if (data.attachments && data.attachments.length > 0) {
                            $attachmentsContainer.append('<div class="vic-edit-attachments-label">Médias actuels :</div>');
                            data.attachments.forEach(function(att) {
                                let preview = '';
                                if (att.type === 'image') {
                                    preview = `<img src="${att.url}" alt="">`;
                                } else if (att.type === 'video') {
                                    preview = `<span class="vic-file-icon">🎬</span>`;
                                } else if (att.type === 'audio') {
                                    preview = `<span class="vic-file-icon">🎵</span>`;
                                } else {
                                    preview = `<span class="vic-file-icon">📄</span>`;
                                }

                                $attachmentsContainer.append(`
                                    <div class="vic-edit-attachment-item" data-attachment-id="${att.id}">
                                        ${preview}
                                        <span class="vic-edit-attachment-name">${att.filename || 'Fichier'}</span>
                                        <button type="button" class="vic-remove-existing-attachment" data-attachment-id="${att.id}">✕</button>
                                    </div>
                                `);
                            });
                        }

                        // Display GIF preview if exists
                        if (data.gif) {
                            $attachmentsContainer.append(`
                                <div class="vic-edit-attachment-item vic-edit-gif-preview" data-type="gif">
                                    <img src="${data.gif}" alt="GIF">
                                    <span class="vic-edit-attachment-name">GIF</span>
                                    <button type="button" class="vic-remove-edit-gif">✕</button>
                                </div>
                            `);
                        }

                        // Display YouTube preview if exists
                        if (data.youtube) {
                            const youtubeRegex = /(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([a-zA-Z0-9_-]+)/;
                            const match = data.youtube.match(youtubeRegex);
                            if (match && match[1]) {
                                const thumbnailUrl = 'https://img.youtube.com/vi/' + match[1] + '/mqdefault.jpg';
                                $attachmentsContainer.append(`
                                    <div class="vic-edit-attachment-item vic-edit-youtube-preview" data-type="youtube">
                                        <img src="${thumbnailUrl}" alt="YouTube">
                                        <span class="vic-youtube-play-icon">▶</span>
                                        <span class="vic-edit-attachment-name">YouTube</span>
                                        <button type="button" class="vic-remove-edit-youtube">✕</button>
                                    </div>
                                `);
                            }
                        }

                    } else {
                        // Fallback: extract content from post card
                        const $temp = $('<div>').html(response.data.html || '');
                        const content = $temp.find('.vic-modal-post-content').text().trim();
                        $('.vic-edit-post-form textarea[name="edit_post_content"]').val(content);
                        $('#vic-edit-current-attachments').empty();
                    }
                },
                error: function() {
                    $('#vic-edit-current-attachments').html('<div class="vic-edit-error">Erreur de chargement</div>');
                }
            });

            // Close any open post menu dropdown
            $('.vic-post-menu-dropdown').removeClass('show');
        });

        // ====== EDIT MODAL: File input handling ======
        $(document).on('change', '#vic-edit-file-input', function(e) {
            const files = Array.from(e.target.files);
            const $preview = $('#vic-edit-attachments-preview');

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

                window.vicEditSelectedFiles.push(file);

                // Add preview
                const index = window.vicEditSelectedFiles.length - 1;
                const $item = $('<div class="vic-preview-item vic-edit-new-file" data-index="' + index + '"></div>');

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

                const truncatedName = file.name.length > 20 ? file.name.substring(0, 17) + '...' + file.name.split('.').pop() : file.name;
                $item.append('<span class="vic-filename">' + truncatedName + '</span>');
                $item.append('<button type="button" class="vic-remove-edit-new-file" data-index="' + index + '">✕</button>');

                $preview.append($item);
            });

            if (window.vicEditSelectedFiles.length > 0) {
                $('.vic-edit-upload-btn').addClass('has-files');
            }
        });

        // Remove new file from edit form
        $(document).on('click', '.vic-remove-edit-new-file', function() {
            const index = parseInt($(this).data('index'));
            window.vicEditSelectedFiles.splice(index, 1);
            $(this).closest('.vic-preview-item').remove();

            // Update indices
            $('#vic-edit-attachments-preview .vic-preview-item').each(function(i) {
                $(this).attr('data-index', i);
                $(this).find('.vic-remove-edit-new-file').attr('data-index', i);
            });

            if (window.vicEditSelectedFiles.length === 0) {
                $('.vic-edit-upload-btn').removeClass('has-files');
            }
        });

        // Remove existing attachment
        $(document).on('click', '.vic-remove-existing-attachment', function() {
            const attachmentId = $(this).data('attachment-id');
            window.vicEditAttachmentsToRemove.push(attachmentId);
            $('#vic-edit-attachments-to-remove').val(window.vicEditAttachmentsToRemove.join(','));
            $(this).closest('.vic-edit-attachment-item').fadeOut(200, function() {
                $(this).remove();
            });
        });

        // Remove GIF from edit
        $(document).on('click', '.vic-remove-edit-gif', function() {
            $('#vic-edit-post-gif-input').val('');
            $(this).closest('.vic-edit-gif-preview').fadeOut(200, function() {
                $(this).remove();
            });
        });

        // Remove YouTube from edit
        $(document).on('click', '.vic-remove-edit-youtube', function() {
            $('#vic-edit-post-youtube-input').val('');
            $(this).closest('.vic-edit-youtube-preview').fadeOut(200, function() {
                $(this).remove();
            });
        });

        // ====== EDIT MODAL: URL field toggle ======
        $(document).on('click', '.vic-edit-add-url', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const $field = $('#vic-edit-url-field');
            $field.slideToggle(200);
            setTimeout(function() {
                if ($field.is(':visible')) {
                    $field.find('input').focus();
                }
            }, 210);
        });

        // Remove URL field in edit modal
        $(document).on('click', '#vic-edit-url-field .vic-btn-remove-field', function(e) {
            e.preventDefault();
            const $field = $('#vic-edit-url-field');
            $field.slideUp(200);
            $field.find('input').val('');
        });

        // ====== EDIT MODAL: YouTube ======
        $(document).on('click', '.vic-edit-add-youtube', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const url = prompt('Collez l\'URL de la vidéo YouTube :');
            if (url && url.trim()) {
                const youtubeRegex = /(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([a-zA-Z0-9_-]+)/;
                const match = url.trim().match(youtubeRegex);

                if (match && match[1]) {
                    const videoId = match[1];
                    const thumbnailUrl = 'https://img.youtube.com/vi/' + videoId + '/mqdefault.jpg';

                    $('#vic-edit-post-youtube-input').val(url.trim());

                    // Remove existing YouTube preview
                    $('.vic-edit-youtube-preview').remove();

                    // Add preview
                    $('#vic-edit-current-attachments').append(`
                        <div class="vic-edit-attachment-item vic-edit-youtube-preview" data-type="youtube">
                            <img src="${thumbnailUrl}" alt="YouTube">
                            <span class="vic-youtube-play-icon">▶</span>
                            <span class="vic-edit-attachment-name">YouTube</span>
                            <button type="button" class="vic-remove-edit-youtube">✕</button>
                        </div>
                    `);
                } else {
                    alert('URL YouTube invalide.');
                }
            }
        });

        // ====== EDIT MODAL: Emoji picker ======
        $(document).on('click', '.vic-edit-add-emoji', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const $btn = $(this);
            const $formActions = $btn.closest('.vic-edit-form-actions');

            // Close other pickers
            $('.vic-emoji-picker-full, .vic-emoji-picker, .vic-gif-picker').remove();

            // Create emoji picker
            const $picker = $('<div class="vic-emoji-picker-full vic-edit-emoji-picker"></div>');
            $picker.append('<div class="vic-emoji-search"><input type="text" placeholder="Rechercher un emoji..." class="vic-emoji-search-input"></div>');

            const editEmojiCategories = [
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
            editEmojiCategories.forEach(function(cat, idx) {
                $categories.append('<button data-category="' + cat.id + '" ' + (idx === 0 ? 'class="active"' : '') + ' title="' + cat.name + '">' + cat.icon + '</button>');
            });
            $picker.append($categories);

            const $grid = $('<div class="vic-emoji-grid"></div>');
            if (typeof emojiData !== 'undefined') {
                const emojis = emojiData['smileys'] || [];
                emojis.forEach(function(emoji) {
                    $grid.append('<span class="vic-emoji-item vic-edit-emoji-item">' + emoji + '</span>');
                });
            }
            $picker.append($grid);

            $formActions.append($picker);

            setTimeout(function() {
                $picker.find('.vic-emoji-search-input').focus();
            }, 10);
        });

        // Insert emoji in edit textarea
        $(document).on('click', '.vic-edit-emoji-item', function(e) {
            e.preventDefault();
            e.stopPropagation();

            let emoji = '';
            const $img = $(this).find('img.emoji');
            if ($img.length) {
                emoji = $img.attr('alt') || '';
            } else {
                emoji = $(this).text().trim();
            }

            if (!emoji) return;

            const $textarea = $('.vic-edit-post-form textarea[name="edit_post_content"]');
            if ($textarea.length) {
                const currentVal = $textarea.val() || '';
                $textarea.val(currentVal + emoji);
                $textarea.focus();
            }

            $('.vic-emoji-picker-full, .vic-emoji-picker').remove();
        });

        // Change category in edit emoji picker
        $(document).on('click', '.vic-edit-emoji-picker .vic-emoji-categories button', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const category = $(this).data('category');
            const $picker = $(this).closest('.vic-emoji-picker-full');

            $picker.find('.vic-emoji-categories button').removeClass('active');
            $(this).addClass('active');

            const $grid = $picker.find('.vic-emoji-grid');
            $grid.empty();
            if (typeof emojiData !== 'undefined' && emojiData[category]) {
                emojiData[category].forEach(function(emoji) {
                    $grid.append('<span class="vic-emoji-item vic-edit-emoji-item">' + emoji + '</span>');
                });
            }
        });

        // ====== EDIT MODAL: GIF picker ======
        $(document).on('click', '.vic-edit-add-gif', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const $btn = $(this);
            const $formActions = $btn.closest('.vic-edit-form-actions');

            // Close other pickers
            $('.vic-gif-picker, .vic-emoji-picker-full, .vic-emoji-picker').remove();

            // Create GIF picker
            const $picker = $(`
                <div class="vic-gif-picker vic-edit-gif-picker">
                    <div class="vic-gif-header">
                        <input type="text" placeholder="Rechercher des GIFs..." class="vic-gif-search-input">
                    </div>
                    <div class="vic-gif-grid">
                        <div class="vic-gif-loading">Recherchez un GIF...</div>
                    </div>
                </div>
            `);

            $formActions.append($picker);

            setTimeout(function() {
                $picker.find('.vic-gif-search-input').focus();
            }, 10);

            // Load trending GIFs
            loadEditTrendingGifs($picker.find('.vic-gif-grid'));
        });

        // Load trending GIFs for edit
        function loadEditTrendingGifs($grid) {
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
                                $grid.append(`<img src="${previewUrl}" data-full="${gifUrl}" class="vic-gif-item vic-edit-gif-item" alt="${gif.content_description || 'GIF'}">`);
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

        // Search GIFs in edit modal
        let editGifSearchTimeout;
        $(document).on('input', '.vic-edit-gif-picker .vic-gif-search-input', function() {
            const query = $(this).val().trim();
            const $grid = $(this).closest('.vic-gif-picker').find('.vic-gif-grid');

            clearTimeout(editGifSearchTimeout);

            if (query.length < 2) {
                loadEditTrendingGifs($grid);
                return;
            }

            editGifSearchTimeout = setTimeout(function() {
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
                                    $grid.append(`<img src="${previewUrl}" data-full="${gifUrl}" class="vic-gif-item vic-edit-gif-item" alt="${gif.content_description || 'GIF'}">`);
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

        // Select GIF in edit modal
        $(document).on('click', '.vic-edit-gif-item', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const gifUrl = $(this).data('full') || $(this).attr('src');

            $('#vic-edit-post-gif-input').val(gifUrl);

            // Remove existing GIF preview
            $('.vic-edit-gif-preview').remove();

            // Add preview
            $('#vic-edit-current-attachments').append(`
                <div class="vic-edit-attachment-item vic-edit-gif-preview" data-type="gif">
                    <img src="${gifUrl}" alt="GIF">
                    <span class="vic-edit-attachment-name">GIF</span>
                    <button type="button" class="vic-remove-edit-gif">✕</button>
                </div>
            `);

            // Close picker
            $('.vic-gif-picker').remove();
        });

        // Close edit modal
        $(document).on('click', '.vic-edit-modal-close, .vic-edit-modal-overlay', function(e) {
            if (e.target === this || $(this).hasClass('vic-edit-modal-close')) {
                $('.vic-edit-modal-overlay').fadeOut(200, function() {
                    $(this).remove();
                });
            }
        });

        // Prevent modal close when clicking inside modal
        $(document).on('click', '.vic-edit-modal', function(e) {
            e.stopPropagation();
        });

        // Submit edit form (full version with attachments)
        $(document).on('submit', '.vic-edit-post-form', function(e) {
            e.preventDefault();

            const $form = $(this);
            const postId = $form.data('post-id');
            const title = $form.find('input[name="edit_post_title"]').val().trim();
            const content = $form.find('textarea[name="edit_post_content"]').val().trim();

            if (!title || !content) {
                alert('Le titre et le contenu sont requis');
                return;
            }

            const $submitBtn = $form.find('button[type="submit"]');
            const $cancelBtn = $form.find('.vic-btn-cancel');
            const originalText = $submitBtn.text();

            $submitBtn.text('Envoi...').prop('disabled', true);
            $cancelBtn.prop('disabled', true);

            // Compress new files if any
            const filesToCompress = window.vicEditSelectedFiles || [];

            compressFiles(filesToCompress, 1920, 1080, 0.85).then(function(compressedFiles) {
                // Create FormData
                const formData = new FormData();
                formData.append('action', 'vic_edit_post');
                formData.append('nonce', vicAjax.nonce);
                formData.append('post_id', postId);
                formData.append('post_title', title);
                formData.append('post_content', content);
                formData.append('post_url', $form.find('input[name="edit_post_url"]').val() || '');
                formData.append('post_gif', $('#vic-edit-post-gif-input').val() || '');
                formData.append('post_youtube', $('#vic-edit-post-youtube-input').val() || '');
                formData.append('attachments_to_remove', $('#vic-edit-attachments-to-remove').val() || '');

                // Add new compressed files
                compressedFiles.forEach(function(file) {
                    formData.append('new_attachments[]', file);
                });

                $.ajax({
                    url: vicAjax.ajaxurl,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            // Replace post card in feed
                            const $oldCard = $('.vic-post-card[data-post-id="' + postId + '"]');
                            if ($oldCard.length) {
                                $oldCard.replaceWith(response.data.post_html);
                            }

                            // Close edit modal
                            $('.vic-edit-modal-overlay').fadeOut(200, function() {
                                $(this).remove();
                            });

                            // Close post modal if open
                            $('.vic-modal-overlay').fadeOut(200, function() {
                                $(this).remove();
                            });
                            $('body').removeClass('vic-modal-open');

                            // Clean up
                            window.vicEditSelectedFiles = [];
                            window.vicEditAttachmentsToRemove = [];
                        } else {
                            alert(response.data.message || 'Erreur lors de la modification');
                        }
                    },
                    error: function() {
                        alert('Erreur de connexion');
                    },
                    complete: function() {
                        $submitBtn.text(originalText).prop('disabled', false);
                        $cancelBtn.prop('disabled', false);
                    }
                });
            });
        });
    }

    // Helper function to escape HTML
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // ====== POST ACTIONS (Copy link, Report) ======
    initPostActions();

    function initPostActions() {
        // Copy post link
        $(document).on('click', '.vic-copy-post-link', function(e) {
            e.stopPropagation();
            const postId = $(this).data('post-id');
            const url = window.location.origin + window.location.pathname + '?post_id=' + postId;

            navigator.clipboard.writeText(url).then(function() {
                alert('Lien copié !');
            }).catch(function() {
                // Fallback for older browsers
                const textarea = document.createElement('textarea');
                textarea.value = url;
                document.body.appendChild(textarea);
                textarea.select();
                document.execCommand('copy');
                document.body.removeChild(textarea);
                alert('Lien copié !');
            });

            // Close menu
            $('.vic-post-menu-dropdown').removeClass('show');
        });

        // Report post
        $(document).on('click', '.vic-report-post', function(e) {
            e.stopPropagation();
            const postId = $(this).data('post-id');

            if (!confirm('Voulez-vous signaler ce post aux administrateurs ?')) {
                return;
            }

            $.ajax({
                url: vicAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'vic_report_post',
                    nonce: vicAjax.nonce,
                    post_id: postId
                },
                success: function(response) {
                    if (response.success) {
                        alert('Post signalé aux administrateurs');
                    } else {
                        alert(response.data.message || 'Erreur lors du signalement');
                    }
                },
                error: function() {
                    alert('Erreur de connexion');
                }
            });

            // Close menu
            $('.vic-post-menu-dropdown').removeClass('show');
        });
    }

    // ====== COMMENT ACTIONS (Edit, Delete) ======
    initCommentActions();

    function initCommentActions() {
        // Delete comment
        $(document).on('click', '.vic-delete-comment', function(e) {
            e.stopPropagation();
            const commentId = $(this).data('comment-id');
            const $comment = $(this).closest('.vic-comment');

            if (!confirm('Êtes-vous sûr de vouloir supprimer ce commentaire ? Cette action est irréversible.')) {
                return;
            }

            $.ajax({
                url: vicAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'vic_delete_comment',
                    nonce: vicAjax.nonce,
                    comment_id: commentId
                },
                success: function(response) {
                    if (response.success) {
                        // Remove comment from DOM
                        $comment.fadeOut(300, function() {
                            $(this).remove();

                            // Update comment count if visible
                            const $modalCount = $('.vic-comment-count');
                            if ($modalCount.length) {
                                const currentText = $modalCount.text();
                                const match = currentText.match(/(\d+)/);
                                if (match) {
                                    const newCount = Math.max(0, parseInt(match[1]) - 1);
                                    $modalCount.html($modalCount.html().replace(/\d+/, newCount));
                                }
                            }
                        });
                    } else {
                        alert(response.data.message || 'Erreur lors de la suppression');
                    }
                },
                error: function() {
                    alert('Erreur de connexion');
                }
            });

            // Close menu
            $('.vic-comment-menu-dropdown').removeClass('show');
        });

        // Edit comment - open full modal (like edit post)
        $(document).on('click', '.vic-edit-comment', function(e) {
            e.preventDefault();
            e.stopPropagation();

            // Close menu immediately
            $('.vic-comment-menu-dropdown').removeClass('show');

            const commentId = $(this).data('comment-id');

            // Create edit comment modal (similar to edit post modal)
            const editCommentModal = `
                <div class="vic-edit-comment-modal-overlay">
                    <div class="vic-edit-comment-modal">
                        <div class="vic-edit-modal-header">
                            <h3>Modifier le commentaire</h3>
                            <button class="vic-edit-modal-close vic-edit-comment-modal-close">✕</button>
                        </div>
                        <div class="vic-edit-comment-modal-body">
                            <div class="vic-edit-loading">Chargement...</div>
                        </div>
                    </div>
                </div>
            `;

            $('body').append(editCommentModal);

            // Initialize edit state
            window.vicEditCommentFiles = [];
            window.vicEditCommentAttachmentsToRemove = [];
            window.vicEditCommentGif = '';

            // Load comment data via AJAX
            $.ajax({
                url: vicAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'vic_get_comment_data_for_edit',
                    nonce: vicAjax.nonce,
                    comment_id: commentId
                },
                success: function(response) {
                    if (response.success) {
                        const data = response.data;

                        // Extract GIF from content if present
                        let content = data.content;
                        let gifUrl = '';
                        const gifMatch = content.match(/\[gif\](.*?)\[\/gif\]/i);
                        if (gifMatch) {
                            gifUrl = gifMatch[1];
                            content = content.replace(/\[gif\].*?\[\/gif\]/gi, '').trim();
                        }
                        window.vicEditCommentGif = gifUrl;

                        // Build modal content
                        let attachmentsHtml = '';
                        if (data.attachments && data.attachments.length > 0) {
                            data.attachments.forEach(function(att) {
                                attachmentsHtml += `
                                    <div class="vic-edit-attachment-item vic-edit-comment-attachment-item" data-attachment-id="${att.id}">
                                        <img src="${att.url}" alt="">
                                        <button type="button" class="vic-remove-comment-attachment" data-attachment-id="${att.id}">✕</button>
                                    </div>
                                `;
                            });
                        }

                        // Add GIF preview if exists
                        let gifPreviewHtml = '';
                        if (gifUrl) {
                            gifPreviewHtml = `
                                <div class="vic-edit-attachment-item vic-edit-comment-gif-preview" data-type="gif">
                                    <img src="${gifUrl}" alt="GIF">
                                    <span class="vic-edit-attachment-name">GIF</span>
                                    <button type="button" class="vic-remove-edit-comment-gif">✕</button>
                                </div>
                            `;
                        }

                        $('.vic-edit-comment-modal-body').html(`
                            <form class="vic-edit-comment-form" data-comment-id="${commentId}">
                                <textarea class="vic-textarea vic-edit-comment-input" placeholder="Votre commentaire...">${escapeHtml(content)}</textarea>

                                <input type="hidden" id="vic-edit-comment-gif-input" value="${gifUrl}">
                                <input type="hidden" class="vic-edit-comment-attachments-to-remove" value="">

                                <!-- Current attachments -->
                                <div class="vic-edit-current-attachments" id="vic-edit-comment-current-attachments">
                                    ${attachmentsHtml}
                                    ${gifPreviewHtml}
                                </div>

                                <!-- New attachments preview -->
                                <div class="vic-attachments-preview vic-edit-comment-new-attachments"></div>

                                <div class="vic-form-footer vic-edit-form-footer">
                                    <div class="vic-form-actions vic-edit-comment-form-actions">
                                        <!-- Image upload -->
                                        <label class="vic-btn-icon vic-edit-comment-upload-btn" title="Ajouter une image">
                                            <input type="file" class="vic-edit-comment-file-input" accept="image/*" multiple style="display:none;">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                                <circle cx="8.5" cy="8.5" r="1.5"/>
                                                <polyline points="21 15 16 10 5 21"/>
                                            </svg>
                                        </label>

                                        <!-- Emoji button -->
                                        <button type="button" class="vic-btn-icon vic-edit-comment-add-emoji" title="Ajouter un emoji">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <circle cx="12" cy="12" r="10"/>
                                                <path d="M8 14s1.5 2 4 2 4-2 4-2"/>
                                                <line x1="9" y1="9" x2="9.01" y2="9"/>
                                                <line x1="15" y1="9" x2="15.01" y2="9"/>
                                            </svg>
                                        </button>

                                        <!-- GIF button -->
                                        <button type="button" class="vic-btn-icon vic-edit-comment-add-gif" title="Ajouter un GIF">
                                            <span style="font-weight: 600; font-size: 11px;">GIF</span>
                                        </button>
                                    </div>

                                    <div class="vic-form-submit">
                                        <button type="button" class="vic-btn vic-btn-cancel vic-edit-comment-modal-close">ANNULER</button>
                                        <button type="submit" class="vic-btn vic-btn-primary">ENREGISTRER</button>
                                    </div>
                                </div>
                            </form>
                        `);

                        // Focus textarea
                        $('.vic-edit-comment-modal .vic-edit-comment-input').focus();
                    } else {
                        $('.vic-edit-comment-modal-body').html('<div class="vic-edit-error">Erreur de chargement</div>');
                    }
                },
                error: function() {
                    $('.vic-edit-comment-modal-body').html('<div class="vic-edit-error">Erreur de connexion</div>');
                }
            });
        });

        // Close edit comment modal
        $(document).on('click', '.vic-edit-comment-modal-close, .vic-edit-comment-modal-overlay', function(e) {
            if (e.target === this || $(this).hasClass('vic-edit-comment-modal-close')) {
                $('.vic-edit-comment-modal-overlay').fadeOut(200, function() {
                    $(this).remove();
                });
                // Clean up
                window.vicEditCommentFiles = [];
                window.vicEditCommentAttachmentsToRemove = [];
                window.vicEditCommentGif = '';
            }
        });

        // Prevent modal close when clicking inside
        $(document).on('click', '.vic-edit-comment-modal', function(e) {
            e.stopPropagation();
        });

        // Handle file input for edit comment modal
        $(document).on('change', '.vic-edit-comment-modal .vic-edit-comment-file-input', function(e) {
            const files = Array.from(e.target.files);
            const $preview = $('.vic-edit-comment-new-attachments');

            files.forEach(function(file) {
                if (file.size > 5 * 1024 * 1024) {
                    alert('Le fichier "' + file.name + '" est trop volumineux (max 5MB)');
                    return;
                }

                if (!file.type.startsWith('image/')) {
                    alert('Seules les images sont autorisées');
                    return;
                }

                window.vicEditCommentFiles.push(file);
                const index = window.vicEditCommentFiles.length - 1;

                const reader = new FileReader();
                reader.onload = function(e) {
                    $preview.append(`
                        <div class="vic-preview-item vic-edit-comment-new-file" data-index="${index}">
                            <img src="${e.target.result}" alt="">
                            <button type="button" class="vic-remove-edit-comment-new-file" data-index="${index}">✕</button>
                        </div>
                    `);
                };
                reader.readAsDataURL(file);
            });

            if (window.vicEditCommentFiles.length > 0) {
                $('.vic-edit-comment-upload-btn').addClass('has-files');
            }
        });

        // Remove new file from edit comment modal
        $(document).on('click', '.vic-remove-edit-comment-new-file', function(e) {
            e.stopPropagation();
            const index = parseInt($(this).data('index'));
            window.vicEditCommentFiles[index] = null;
            $(this).closest('.vic-edit-comment-new-file').fadeOut(200, function() {
                $(this).remove();
            });
        });

        // Remove existing attachment from comment modal
        $(document).on('click', '.vic-edit-comment-modal .vic-remove-comment-attachment', function(e) {
            e.stopPropagation();
            const attachmentId = $(this).data('attachment-id');
            window.vicEditCommentAttachmentsToRemove.push(attachmentId);
            $('.vic-edit-comment-attachments-to-remove').val(window.vicEditCommentAttachmentsToRemove.join(','));
            $(this).closest('.vic-edit-comment-attachment-item').fadeOut(200, function() {
                $(this).remove();
            });
        });

        // Remove GIF from edit comment modal
        $(document).on('click', '.vic-remove-edit-comment-gif', function(e) {
            e.stopPropagation();
            window.vicEditCommentGif = '';
            $('#vic-edit-comment-gif-input').val('');
            $(this).closest('.vic-edit-comment-gif-preview').fadeOut(200, function() {
                $(this).remove();
            });
        });

        // ====== EDIT COMMENT MODAL: Emoji picker ======
        $(document).on('click', '.vic-edit-comment-add-emoji', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const $btn = $(this);
            const $formActions = $btn.closest('.vic-edit-comment-form-actions');

            // Close other pickers
            $('.vic-emoji-picker-full, .vic-emoji-picker, .vic-gif-picker').remove();

            // Create emoji picker
            const $picker = $('<div class="vic-emoji-picker-full vic-edit-comment-emoji-picker"></div>');
            $picker.append('<div class="vic-emoji-search"><input type="text" placeholder="Rechercher un emoji..." class="vic-emoji-search-input"></div>');

            const categories = [
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
            categories.forEach(function(cat, idx) {
                $categories.append('<button data-category="' + cat.id + '" ' + (idx === 0 ? 'class="active"' : '') + ' title="' + cat.name + '">' + cat.icon + '</button>');
            });
            $picker.append($categories);

            const $grid = $('<div class="vic-emoji-grid"></div>');
            if (typeof emojiData !== 'undefined') {
                const emojis = emojiData['smileys'] || [];
                emojis.forEach(function(emoji) {
                    // Only use specific class, not the general one to avoid conflicts
                    $grid.append('<span class="vic-edit-comment-emoji-item">' + emoji + '</span>');
                });
            }
            $picker.append($grid);

            $formActions.append($picker);
        });

        // Insert emoji in edit comment textarea
        $(document).on('click', '.vic-edit-comment-emoji-item', function(e) {
            console.log('=== EDIT COMMENT EMOJI HANDLER TRIGGERED ===');

            e.preventDefault();
            e.stopPropagation();

            // WordPress/Twemoji converts emojis to <img> tags
            // We need to get the emoji from the alt attribute
            let emoji = '';
            const $img = $(this).find('img.emoji');
            if ($img.length) {
                emoji = $img.attr('alt') || '';
                console.log('Emoji from img alt:', emoji);
            } else {
                emoji = $(this).text().trim();
                console.log('Emoji from text:', emoji);
            }

            if (!emoji) {
                console.log('ERROR: No emoji found');
                return;
            }

            const $textarea = $('.vic-edit-comment-modal .vic-edit-comment-input');
            console.log('Textarea found:', $textarea.length);

            if ($textarea.length) {
                const currentVal = $textarea.val() || '';
                $textarea.val(currentVal + emoji);
                console.log('Emoji added! New value:', $textarea.val());
                $textarea.focus();
            } else {
                console.log('ERROR: No textarea found');
            }

            // Close the picker
            $('.vic-emoji-picker-full, .vic-emoji-picker').remove();
            console.log('Picker closed');
        });

        // Change category in edit comment emoji picker
        $(document).on('click', '.vic-edit-comment-emoji-picker .vic-emoji-categories button', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const category = $(this).data('category');
            const $picker = $(this).closest('.vic-emoji-picker-full');

            $picker.find('.vic-emoji-categories button').removeClass('active');
            $(this).addClass('active');

            const $grid = $picker.find('.vic-emoji-grid');
            $grid.empty();
            if (typeof emojiData !== 'undefined' && emojiData[category]) {
                emojiData[category].forEach(function(emoji) {
                    // Only use specific class, not the general one to avoid conflicts
                    $grid.append('<span class="vic-edit-comment-emoji-item">' + emoji + '</span>');
                });
            }
        });

        // ====== EDIT COMMENT MODAL: GIF picker ======
        $(document).on('click', '.vic-edit-comment-add-gif', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const $btn = $(this);
            const $formActions = $btn.closest('.vic-edit-comment-form-actions');

            // Close other pickers
            $('.vic-gif-picker, .vic-emoji-picker-full, .vic-emoji-picker').remove();

            // Create GIF picker
            const $picker = $(`
                <div class="vic-gif-picker vic-edit-comment-gif-picker">
                    <div class="vic-gif-header">
                        <input type="text" placeholder="Rechercher des GIFs..." class="vic-gif-search-input">
                    </div>
                    <div class="vic-gif-grid">
                        <div class="vic-gif-loading">Chargement...</div>
                    </div>
                </div>
            `);

            $formActions.append($picker);

            // Load trending GIFs
            loadEditCommentTrendingGifs($picker.find('.vic-gif-grid'));
        });

        // Load trending GIFs for edit comment
        function loadEditCommentTrendingGifs($grid) {
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
                                $grid.append(`<img src="${previewUrl}" data-full="${gifUrl}" class="vic-gif-item vic-edit-comment-gif-item" alt="${gif.content_description || 'GIF'}">`);
                            }
                        });
                    }
                })
                .catch(() => {
                    $grid.html('<div class="vic-gif-loading">Erreur de chargement</div>');
                });
        }

        // Search GIFs in edit comment modal
        let editCommentGifSearchTimeout;
        $(document).on('input', '.vic-edit-comment-gif-picker .vic-gif-search-input', function() {
            const query = $(this).val().trim();
            const $grid = $(this).closest('.vic-gif-picker').find('.vic-gif-grid');

            clearTimeout(editCommentGifSearchTimeout);

            if (query.length < 2) {
                loadEditCommentTrendingGifs($grid);
                return;
            }

            editCommentGifSearchTimeout = setTimeout(function() {
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
                                    $grid.append(`<img src="${previewUrl}" data-full="${gifUrl}" class="vic-gif-item vic-edit-comment-gif-item" alt="${gif.content_description || 'GIF'}">`);
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

        // Select GIF in edit comment modal
        $(document).on('click', '.vic-edit-comment-gif-item', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const gifUrl = $(this).data('full') || $(this).attr('src');

            window.vicEditCommentGif = gifUrl;
            $('#vic-edit-comment-gif-input').val(gifUrl);

            // Remove existing GIF preview
            $('.vic-edit-comment-gif-preview').remove();

            // Add preview
            $('#vic-edit-comment-current-attachments').append(`
                <div class="vic-edit-attachment-item vic-edit-comment-gif-preview" data-type="gif">
                    <img src="${gifUrl}" alt="GIF">
                    <span class="vic-edit-attachment-name">GIF</span>
                    <button type="button" class="vic-remove-edit-comment-gif">✕</button>
                </div>
            `);

            // Close picker
            $('.vic-gif-picker').remove();
        });

        // Submit edit comment form
        $(document).on('submit', '.vic-edit-comment-modal .vic-edit-comment-form', function(e) {
            e.preventDefault();

            const $form = $(this);
            const commentId = $form.data('comment-id');
            let content = $form.find('.vic-edit-comment-input').val().trim();
            const attachmentsToRemove = $form.find('.vic-edit-comment-attachments-to-remove').val() || '';
            const gifUrl = window.vicEditCommentGif || '';

            // Add GIF tag to content if present
            if (gifUrl) {
                content = content + '\n[gif]' + gifUrl + '[/gif]';
            }

            // Check if there's content or images
            const hasNewFiles = window.vicEditCommentFiles && window.vicEditCommentFiles.filter(f => f !== null).length > 0;
            const hasRemainingImages = $form.find('.vic-edit-comment-attachment-item:not(.vic-edit-comment-gif-preview)').length > 0;

            if (!content.trim() && !hasNewFiles && !hasRemainingImages && !gifUrl) {
                alert('Le commentaire ne peut pas être vide');
                return;
            }

            const $submitBtn = $form.find('button[type="submit"]');
            $submitBtn.text('...').prop('disabled', true);

            // Prepare FormData
            const formData = new FormData();
            formData.append('action', 'vic_edit_comment');
            formData.append('nonce', vicAjax.nonce);
            formData.append('comment_id', commentId);
            formData.append('content', content);
            formData.append('attachments_to_remove', attachmentsToRemove);

            // Add new files
            if (window.vicEditCommentFiles) {
                window.vicEditCommentFiles.forEach(function(file) {
                    if (file !== null) {
                        formData.append('comment_attachments[]', file);
                    }
                });
            }

            $.ajax({
                url: vicAjax.ajaxurl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        // Replace comment in the page
                        const $oldComment = $('.vic-comment[data-comment-id="' + commentId + '"]');
                        if ($oldComment.length) {
                            $oldComment.replaceWith(response.data.comment_html);
                        }

                        // Close modal
                        $('.vic-edit-comment-modal-overlay').fadeOut(200, function() {
                            $(this).remove();
                        });

                        // Clean up
                        window.vicEditCommentFiles = [];
                        window.vicEditCommentAttachmentsToRemove = [];
                        window.vicEditCommentGif = '';
                    } else {
                        alert(response.data.message || 'Erreur lors de la modification');
                        $submitBtn.text('ENREGISTRER').prop('disabled', false);
                    }
                },
                error: function() {
                    alert('Erreur de connexion');
                    $submitBtn.text('ENREGISTRER').prop('disabled', false);
                }
            });
        });
    }

})(jQuery);
