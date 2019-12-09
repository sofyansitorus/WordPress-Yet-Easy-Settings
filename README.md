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

* Add as many as admin pages, placed it any where as top level admin menu or sub-menu.
* Add custom callback to render custom setting field type.
* Add custom callback to render custom tab content.
* Add custom callback to render custom page content.
* Built-in data sanitation and validation.
* Easy to add help tabs for admin page.
* Easy to add custom action button for admin page.

## How to Use

Installation:

`composer require sofyansitorus/wp-yes";`

After you include the WP_Yes class file, all you have to do is to initialize the WP_Yes class, then add the settings object properties in sequence add tabs, add sections, add fields.

### Simple admin page setting

This is the simplest way to initialize the setting page without defining the tabs and sections.

```php
if ( ! function_exists( 'wp_yes_simple' ) ) {
    function wp_yes_simple() {

        $settings = new WP_Yes( 'wp_yes_simple' ); // Initialize the WP_Yes class.

        $settings->add_field(
            array(
                'id' => 'wp_yes_simple_field_1',
            )
        );

        $settings->add_field(
            array(
                'id' => 'wp_yes_simple_field_2',
            )
        );

        $settings->init(); // Run the WP_Yes class.
    }
}

add_action( 'init', 'wp_yes_simple' );
```

### Multiple tabs admin page setting

By default, the setting page will only has 1 tab. If you want to add more tabs, just simply call the **WP_Yes::add_tab** method after the last **WP_Yes::add_field** fora each tab, then following in sequence calling **WP_Yes::add_section** and **WP_Yes::add_field** method.

```php
if ( ! function_exists( 'wp_yes_multi_tabs' ) ) {
    function wp_yes_multi_tabs() {
        $settings = new WP_Yes( 'wp_yes_multi_tabs' ); // Initialize the WP_Yes class.

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
                'id'       => 'wp_yes_multi_tabs_field_1',
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
                'id'       => 'wp_yes_multi_tabs_field_3',
                'required' => true,
                'type'     => 'file',
            )
        );

        $settings->init(); // Run the WP_Yes class.
    }
}
add_action( 'init', 'wp_yes_multi_tabs' );
```

A note you must keep in hand here is that you need to have a unique value for the **menu_slug** parameter that passed in the WP_Yes class constructor and field **id** key in the **WP_Yes::add_field** method parameter. You can have same tab id in different page menu, also can has same sections id in different tabs.

### Admin page setting with custom action button and help tabs

To add help tabs and custom actin button to the admin page, you need to call **WP_Yes::add_help_tab** and **WP_Yes::add_button** method anywhere before calling the **WP_Yes::init** method.

```php
if ( ! function_exists( 'wp_yes_button_and_help_tabs' ) ) {
    function wp_yes_button_and_help_tabs() {

        $settings = new WP_Yes( 'wp_yes_button_and_help_tabs' ); // Initialize the WP_Yes class.

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
                'id' => 'wp_yes_button_and_help_tabs_field_1',
            )
        );

        $settings->add_button( 'Custom Action Button', 'index.php' ); // <-- Add custom action button.

        $settings->init(); // Run the WP_Yes class.
    }
}
add_action( 'init', 'wp_yes_button_and_help_tabs' );
```

### Getting the stored option value

To get the option value is by call built-in WordPress **get_option** function with filed id as the first argument.

```php
get_option( 'wp_yes_simple_field_1' );
```

If you set the $setting_prefix value at third arguments in WP_Yes constructor, then you need to pre-pend that prefix when calling  *get_option** function.

```php
if ( ! function_exists( 'wp_yes_with_prefix' ) ) {
    function wp_yes_with_prefix() {

        $settings = new WP_Yes( 'wp_yes_with_prefix', array(), 'my_setting_prefix' ); // Initialize the WP_Yes class.

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
                'id' => 'wp_yes_with_prefix_field_1',
            )
        );

        $settings->init(); // Run the WP_Yes class.
    }
}
add_action( 'init', 'wp_yes_with_prefix' );

// To get stored option value for setting field wp_yes_with_prefix_field_1
get_option( 'my_setting_prefix_wp_yes_with_prefix_field_1' );
```

Please take a look at the example in [wp-yes-example.php](https://github.com/sofyansitorus/WordPress-Yet-Easy-Settings/blob/master/wp-yes-example.php) for more advanced example such as adding custom tab content, adding custom page content, etc.

## Screenshots

### Simple Setting Form

![Simple Setting Form](https://content.screencast.com/users/SofyanSitorus/folders/Snagit/media/f85423e6-c28e-4429-8978-0dfc8796b052/2018-02-01_01-39-12.png)

### All Fields Types

![All Fields Types](https://content.screencast.com/users/SofyanSitorus/folders/Snagit/media/a12e5a97-dce1-49e0-8780-463bfad75b62/2018-02-01_01-42-40.png)
![All Fields Types](https://content.screencast.com/users/SofyanSitorus/folders/Snagit/media/efc1b981-b376-4c41-b243-2ac8926542ff/2018-02-21_16-52-01.png)

### Setting Form with Tabs

![Setting Form with Tabs](https://content.screencast.com/users/SofyanSitorus/folders/Snagit/media/cdd44ab3-591d-498c-a441-31ad1f6bbcc9/2018-02-01_01-47-28.png)

### Admin Page with Action Button

![Admin Page with Action Button](https://content.screencast.com/users/SofyanSitorus/folders/Snagit/media/a1803569-8264-4db9-b8bd-49b597186dc3/2018-02-01_01-49-06.png)

### Admin Page with Help Tabs

![Admin Page with Help Tabs](https://content.screencast.com/users/SofyanSitorus/folders/Snagit/media/6919b9ce-399b-42b7-bbcc-58cc7f9a8120/2018-02-01_01-49-58.png)

### Custom Tab Content

![Custom Tab Content](https://content.screencast.com/users/SofyanSitorus/folders/Snagit/media/2ab79e35-0e7d-4555-80a4-5fb72e57414d/2018-02-01_01-43-32.png)

### Custom Page Content

![Custom Page Content](https://content.screencast.com/users/SofyanSitorus/folders/Snagit/media/c6491599-ea81-48e1-b0fc-2783f25eda8e/2018-02-01_01-44-20.png)

### Sub-menu Admin Page

![Sub-menu Admin Page](https://content.screencast.com/users/SofyanSitorus/folders/Snagit/media/85c042bf-5516-4cbd-82cc-345247704a08/2018-02-21_16-31-46.png)
