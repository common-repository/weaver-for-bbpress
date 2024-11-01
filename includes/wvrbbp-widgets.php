<?php
/*
 *  Weaver X Widgets and shortcodes - widgets
 */


//add_action("widgets_init", "wvrbbp_load_widgets");


function wvrbbp_load_widgets()
{
    register_widget("wvrbbp_Activity_Widget");
}
