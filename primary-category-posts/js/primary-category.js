/* eslint-enable no-console */
const post_wrapper = document.querySelector(".posts-wrapper");

function get_primary_category_ajax( categoryId ) {
    var data = {
        'action': 'my_action_name', 
        'query': primary_category_params.posts,
        'post_id': primary_category_params.post_id,
        'category_id': categoryId
    };

    jQuery.ajax({
        url: primary_category_params.ajaxurl,
        data: data,
        type: 'POST',

        beforeSend: function() {
            post_wrapper.innerHTML = '';
        },

        success: function(data) {
            if (data) {
                post_wrapper.insertAdjacentHTML('beforeend', data);

            }

        }
    });
}

function click_actions() {
    const categories_buttons = document.querySelectorAll('.cat-list_item');
    categories_buttons.forEach(button => {
        button.addEventListener('click', function(event) {
            //console.log('test ajax!!');
            event.preventDefault();
            let categoryId = jQuery(this).data('category-id'); // Extract the category ID
            //console.log(categoryId);
            get_primary_category_ajax(categoryId);
        });
        
    });

    
}

window.addEventListener('load', function () {
    click_actions();
} );
