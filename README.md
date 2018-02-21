# WordPress-Yet-Easy-Settings

WordPress Yet Easy Settings class is PHP class for easy to build advanced admin page for WordPress.

## Built-in setting field types

* Text
* URL
* Email
* Password
* Number
* Decimal
* Textarea
* Checkbox
* Multiple Checkbox
* Select
* Multiple Select
* Radio Button
* Color Picker
* File Upload
* WYSIWYG

## Advanced features

* Add as many as admin pages, placed it any where as top level admin menu or submenu.
* Add custom callback to render custom setting field type.
* Add custom callback to render custom tab content.
* Add custom callback to render custom page content.
* Built-in data sanitazion and validation.
* Easy to add help tabs for admin page.
* Easy to add custom action button for admin page.

## How to Use

Include the Wpyes class file in your plugin main file:

`require_once "includes/class-wpyes.php";`

After you include the Wpyes class file, all you have to do is to initialize the Wpyes class, then add the settings object propertis in sequence add tabs, add sections, add fields.

### Simple admin page setting

This is the simplest way to initialize the setting page without defining the tabs and sections.

```php
if ( ! function_exists( 'wpyes_simple' ) ) {
    function wpyes_simple() {

        $settings = new Wpyes( 'wpyes_simple' ); // Initialize the Wpyes class.

        $settings->add_field(
            array(
                'id' => 'wpyes_simple_field_1',
            )
        );

        $settings->add_field(
            array(
                'id' => 'wpyes_simple_field_2',
            )
        );

        $settings->init(); // Run the Wpyes class.
    }

    wpyes_simple();
}// End if().
```

### Multiple tabs admin page setting

If you want to add another tabs, just call the **Wpyes::add_tab** method after the last **Wpyes::add_field** on the first tab, then following in sequence calling **Wpyes::add_section** and **Wpyes::add_field** method. If there is only 1 tab registered, the tab links will not displayed.

```php
if ( ! function_exists( 'wpyes_multi_tabs' ) ) {
    function wpyes_multi_tabs() {
        $settings = new Wpyes( 'wpyes_multi_tabs' ); // Initialize the Wpyes class.

        $settings->add_tab(
            array(
                'id' => 'tab_1',
            )
        );

        $settings->add_section(
            array(
                'id' => 'section_1',
            )
        );

        $settings->add_field(
            array(
                'id'       => 'wpyes_multi_tabs_field_1',
                'required' => true,
                'type'     => 'text',
            )
        );

        $settings->add_tab( // <-- Add tab 2.
            array(
                'id' => 'tab_2',
            )
        );

        $settings->add_section(
            array(
                'id' => 'section_1',
            )
        );

        $settings->add_field(
            array(
                'id'       => 'wpyes_multi_tabs_field_3',
                'required' => true,
                'type'     => 'file',
            )
        );

        $settings->init(); // Run the Wpyes class.
    }

    wpyes_multi_tabs();
}// End if().
```

A note you must keep in hand here is that you neeed to have a unique value for the **menu_slug** parameter that passed in the Wpyes class constructor and field **id** key in the **Wpyes::add_field** method parameter. You can have same tab id in different page manu, also can has same sections id in different tabs.

### Admin page setting with custom action button and help tabs

To add help tabs and custom actin button to the admin page, you need to call **Wpyes::add_help_tab** and **Wpyes::add_button** method anywhere before calling the **Wpyes::init** method.

```php
if ( ! function_exists( 'wpyes_button_and_help_tabs' ) ) {
    function wpyes_button_and_help_tabs() {

        $settings = new Wpyes( 'wpyes_button_and_help_tabs' ); // Initialize the Wpyes class.

        $settings->add_help_tab(  // <-- Add help tab 1.
            array(
                'id'      => 'my_help_tab',
                'title'   => __( 'My Help Tab' ),
                'content' => '<p>' . __('Descriptive content that will show in My Help Tab-body goes here.') . '</p>',
            )
        );

        $settings->add_help_tab(  // <-- Add help tab 2.
            array(
                'id'      => 'my_help_tab2',
                'title'   => __( 'My Help Tab2' ),
                'content' => '<p>' . __( 'Descriptive content that will show in My Help Tab-body goes here 2. ') . '</p>',
            )
        );

        $settings->add_tab(
            array(
                'id' => 'tab_1',
            )
        );

        $settings->add_section(
            array(
                'id' => 'section_1',
            )
        );

        $settings->add_field(
            array(
                'id' => 'wpyes_button_and_help_tabs_field_1',
            )
        );

        $settings->add_button( 'Custom Action Button', 'index.php' ); // <-- Add custom action button.

        $settings->init(); // Run the Wpyes class.
    }

    wpyes_button_and_help_tabs();
}// End if().
```

### Getting the stored option value

To get the option value is by call built-in WordPress **get_option** function with filed id as the first argument.

```php
get_option( 'wpyes_simple_field_1' );
```

If you set the $setting_prefix value at third arguments in Wpyes constructor, then you need to prepend that prefix when calling  *get_option** function.

```php
if ( ! function_exists( 'wpyes_with_prefix' ) ) {
    function wpyes_with_prefix() {

        $settings = new Wpyes( 'wpyes_with_prefix', array(), 'my_setting_prefix' ); // Initialize the Wpyes class.

        $settings->add_tab(
            array(
                'id' => 'tab_1',
            )
        );

        $settings->add_section(
            array(
                'id' => 'section_1',
            )
        );

        $settings->add_field(
            array(
                'id' => 'wpyes_with_prefix_field_1',
            )
        );

        $settings->init(); // Run the Wpyes class.
    }

    wpyes_with_prefix();
}// End if().

// To get stored option value for setting field wpyes_with_prefix_field_1
get_option( 'my_setting_prefix_wpyes_with_prefix_field_1' );
```

Please take a look at the example in [wpyes-example.php](https://github.com/sofyansitorus/WordPress-Yet-Easy-Settings/blob/master/wpyes-example.php) for more advanced example such as adding custom tab content, adding custom page content, etc.

## Screenshots

### Simple Setting Form

![Simple Setting Form](https://content.screencast.com/users/SofyanSitorus/folders/Snagit/media/f85423e6-c28e-4429-8978-0dfc8796b052/2018-02-01_01-39-12.png)

### All Fields Types

![All Fields Types](https://content.screencast.com/users/SofyanSitorus/folders/Snagit/media/a12e5a97-dce1-49e0-8780-463bfad75b62/2018-02-01_01-42-40.png)

### Setting Form with Tabs

![Setting Form with Tabs](https://content.screencast.com/users/SofyanSitorus/folders/Snagit/media/cdd44ab3-591d-498c-a441-31ad1f6bbcc9/2018-02-01_01-47-28.png)

### Admin Page with Action Button

![Admin Page with Action Button](https://content.screencast.com/users/SofyanSitorus/folders/Snagit/media/a1803569-8264-4db9-b8bd-49b597186dc3/2018-02-01_01-49-06.png)

### Admin Page with Help Tabs

![Admin Page with Help Tabs](https://content.screencast.com/users/SofyanSitorus/folders/Snagit/media/6919b9ce-399b-42b7-bbcc-58cc7f9a8120/2018-02-01_01-49-58.png)

### Submenu Admin Page

![Submenu Admin Page](https://content.screencast.com/users/SofyanSitorus/folders/Snagit/media/a1803569-8264-4db9-b8bd-49b597186dc3/2018-02-01_01-49-06.png)

### Custom Tab Content

![Custom Tab Content](https://content.screencast.com/users/SofyanSitorus/folders/Snagit/media/2ab79e35-0e7d-4555-80a4-5fb72e57414d/2018-02-01_01-43-32.png)

### Custom Page Content

![Custom Page Content](https://content.screencast.com/users/SofyanSitorus/folders/Snagit/media/2ab79e35-0e7d-4555-80a4-5fb72e57414d/2018-02-01_01-43-32.png)
