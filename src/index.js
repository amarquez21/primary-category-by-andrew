const { registerPlugin } = wp.plugins
const { PluginDocumentSettingPanel } = wp.editPost
const { SelectControl } = wp.components;
const { useSelect, useDispatch } = wp.data;
//import { useState, useEffect } from '@wordpress/element';
const { useState, useEffect } = wp.element;


// Function to fetch categories and return an array of category names
async function getCategories() {
    try {
        const response = await fetch('/wp-json/wp/v2/categories');
        if (!response.ok) {
            throw new Error(`Error fetching categories. Status code: ${response.status}`);
        }
        const categories = await response.json();
        const categoryObject = categories.map(category => category);
        return categoryObject;

    } catch (error) {
        console.error(error.message);
        return []; // Return an empty array in case of an error
    }
}




// Define a functional component for the PluginDocumentSettingPanel
const CategorySelectPanel = () => {
	const [categories, setCategories] = useState([]);

    useEffect(() => {
		

        async function fetchCategories() {
            const allCategories = await getCategories();

			let defaultOption =  { name:'Select', id: 0}
			allCategories.unshift(defaultOption);

            setCategories(allCategories);
        }
        fetchCategories();

		

    }, []);

	

	const currentPrimaryCategory = wp.data.select('core/editor').getEditedPostAttribute('meta')['primary_category'];
	const [selectedCategory, setSelectedCategory] = useState(currentPrimaryCategory);


    // Handle category selection
    const handleCategoryChange = (primaryCategory) => {

        setSelectedCategory(primaryCategory);

        // Save the selected category to the database
        // Example: Update post meta data with the selected category
        // Replace 'post_id' with the actual post ID
        wp.data.dispatch('core/editor').editPost({
            meta: {
                'primary_category': primaryCategory,
            },
        });
    };

	

    return (
        <PluginDocumentSettingPanel
            name="primary-category-select-panel"
            title="Primary Category"
        >
            <SelectControl
                label="Select Primary Category"
                value={selectedCategory}
                options={categories.map((category) => ({ label: category.name, value: category.id }))}
                onChange={handleCategoryChange}
            />
        </PluginDocumentSettingPanel>
    );
};

// Register the CategorySelectPanel
registerPlugin('primary-category-select-panel', {
    render: CategorySelectPanel,
});
