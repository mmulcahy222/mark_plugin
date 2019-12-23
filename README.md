# Wordpress Shortcodes in Elementor

I did a project in WordPress for the site www.strumandspirits.com.

The task said:

> I have created a new custom post type with CPT UI and Advanced Custom Fields for my bands and their shows. But now I need to display it on my website(s) with a template built in PHP (I think). Looking for someone to help me with this fairly simple project. For the display, I need to have a detail view, a semi-detailed "listing" view, and a simple one-line list view.

I first made them as it's own pages, but then it turned out that these "views" & "templates" had to be placed anywhere the content creator/administrator wanted with the Elementor Plugin.

That raised the difficulty level, but I was up to the task.

I converted them as shortcodes, and used WordPress Hooks to integrate them with the Elementor plugin.

Advanced PHP Debugging & Code Walking had to be done to find the exact nested array location to do this alteration at the WordPress hook.

In addition to all of this, I made a separate Google Maps API function in the Wordpress Admin, which also does autocompletion for previous address values added.
