/**
 * Editor script for Horizontal Cards widget.
 */
(function ($) {
    'use strict';
    console.log('Feebas horizontal-cards-editor.js loaded'); // Renamed for clarity

    elementor.hooks.addAction('panel/open_editor/widget/feebas_horizontal_cards', function (panel, model, view) {
        console.log('Horizontal cards widget panel opened for model:', model.cid);

        var postTypeControlName = 'post_type';
        var postsControlName = 'selected_posts';

        // Target the post type control select element
        var $postTypeSelect = panel.$el.find('select[data-setting="' + postTypeControlName + '"]');
        // Target the select2's underlying select element
        var $postsSelect = panel.$el.find('select[data-setting="' + postsControlName + '"]');

        // --- Function to Update Posts ---
        function updatePostsForType(postType, currentSelectedValues) {
            // Ensure the target Select2 select element exists
            if (!$postsSelect.length) {
                console.warn('Target Select2 element for selected_posts not found.');
                return;
            }

            // If postType is empty, clear the posts select and return
            if (!postType) {
                console.log('Post type is empty, clearing selected_posts.');
                $postsSelect.empty().trigger('change'); // Clear options and notify Select2
                return;
            }

            console.log('Updating posts for type:', postType);

            // Disable the select while loading
            $postsSelect.prop('disabled', true);

            $.ajax({
                url: ajaxurl, // Assumes ajaxurl is available globally (WordPress default)
                type: 'POST',
                data: {
                    action: 'feebas_get_posts_by_type', // Your backend AJAX action
                    nonce: FeebasHorizontalCardsSettings.nonce, // Your localized nonce
                    post_type: postType
                },
                success: function (response) {
                    console.log('AJAX response:', response);

                    // Basic check for successful response and data object
                    if (!response || !response.success || typeof response.data !== 'object') {
                        console.warn('Error loading posts or invalid data format. Clearing select.', response);
                        $postsSelect.empty().trigger('change'); // Clear options on error
                        return;
                    }

                    var newOptionsData = response.data; // e.g., { "1": "Post A", "2": "Post B" }
                    var optionsHtml = '';

                    // Build new <option> elements
                    $.each(newOptionsData, function (postId, postTitle) {
                         // Basic escaping for title text
                        var escapedTitle = $('<textarea />').html(postTitle).text();
                        optionsHtml += '<option value="' + postId + '">' + escapedTitle + '</option>';
                    });

                    // Update the underlying select element's options
                    $postsSelect.html(optionsHtml);

                    // --- Restore Selection ---
                    var validValuesToRestore = [];
                    if (Array.isArray(currentSelectedValues)) {
                        currentSelectedValues.forEach(function (value) {
                            // Check if the previously selected value exists as a key in the new options
                            if (newOptionsData.hasOwnProperty(value)) {
                                validValuesToRestore.push(value);
                            }
                        });
                    }
                     console.log('Attempting to restore values:', validValuesToRestore);


                    // Set the value on the underlying select element.
                    // For select2 multiple, .val() accepts an array.
                    $postsSelect.val(validValuesToRestore);

                    // --- IMPORTANT: Trigger change for Select2 ---
                    // Notify Select2 that the underlying select element has changed
                    // so it can update its display.
                    $postsSelect.trigger('change');

                },
                error: function (xhr, status, error) {
                    console.error('AJAX error fetching posts:', status, error, xhr);
                    // Clear options on AJAX error as well
                     $postsSelect.empty().trigger('change');
                },
                complete: function () {
                    // Re-enable the select control regardless of success or error
                    $postsSelect.prop('disabled', false);
                }
            });
        }

        // --- Event Listener for Post Type Change ---
        // Using panel.$el.on for robust event binding within the panel
        panel.$el.on('change', 'select[data-setting="' + postTypeControlName + '"]', function () {
            var newPostType = $(this).val();
            // Get the current selection *from the model* right before updating
            var currentSelectedPosts = model.get(postsControlName);

             // Ensure it's an array for consistency (Select2 multiple returns array)
             if (currentSelectedPosts && !Array.isArray(currentSelectedPosts)) {
                currentSelectedPosts = [currentSelectedPosts];
             } else if (!currentSelectedPosts) {
                currentSelectedPosts = [];
             }


            console.log('Post type changed to:', newPostType);
             console.log('Current selection before update:', currentSelectedPosts);
            updatePostsForType(newPostType, currentSelectedPosts);
        });

        // --- Initial Load on Panel Open ---
        var initialPostType = model.get(postTypeControlName); // Get initial post type from model
        var initialSelectedPosts = model.get(postsControlName); // Get initial selection from model

         // Ensure it's an array for consistency
         if (initialSelectedPosts && !Array.isArray(initialSelectedPosts)) {
            initialSelectedPosts = [initialSelectedPosts];
         } else if (!initialSelectedPosts) {
            initialSelectedPosts = [];
         }

        console.log('Initial load - Post Type:', initialPostType, 'Selected:', initialSelectedPosts);
        // Perform the initial fetch for the posts select
        // updatePostsForType(initialPostType, initialSelectedPosts);

    });

})(jQuery);