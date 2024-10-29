=== amr personalise ===
Contributors: anmari
Tags: personalise, personalize, emails, user, name, first name, sender email, sender name, sender
Requires: 2.6
Tested up to: 5.5
Version: 2.10
Stable tag: Trunk

This plugin allows one to personalise pages, posts or wordpress single user emails.

== Description ==
This plugin allows one to personalise content for a logged in user.  It uses shortcode type functionality in any email template or page or post. It extract stored details of the viewer or reader. A default text can be specified for situations where there are no stored details.

It also allows you to customise the email address that wordpress uses to send the emails, and the sender name.  This can allow you to offer a more customised service for example with a more helpful unsubscribe link.  Or you could create your own "profile" page for logged in users only.

Plugin homepage: [http://wpusersplugin.com/personalise/](http://wpusersplugin.com/personalise/)
Demo page: [http://test.icalevents.com/personalise/](http://test.icalevents.com/personalise/)


== Changelog ==
= 2.10 ==
*  Tested on wp 4.8.1.
*  Minor tweak to code that lists capabilities

= 2.9 ==
*  changed deprecated get_currentuserinfo for wp_get_current_user
*  Tested on wp 4.6.1. 

= 2.8 ==
*  Tested on 4.4.1. 
*  Tweaked a few things with error messages
*  added banner image, moved screenshots


= 2.7 ==
*  Tested on 4.0. 
*  fixed warning caused when array of email addresses in wp_mail instead of single email.  Removed unneccssary code.

= 2.6 ==
*  Tested on 3.4.1 and added some additional help and examples. 

= 2.5 =
* Fixed warning message on php 5.3
* Fixed html comment on no data 
* Fixed shortcode with multiple attributes problem
* Updated some help info

= 2.4 =
* cleaned up debug messages.  Admin user only can do ID=x on a personalised page to get a dump of user data available to check meta fields etc.
* Added some links to make it easer for folks to find info and support.

= 2.3 =
*  changed straight parameter eg: [user user_registered] to show the meta key if there is no data for it, so that it is clear when testing what data is "not showing".  If you would rather have no output at all, then use the message feature rather.  [user message="You registered on %s" user_registered]
*  message altered to still return no message if not all the variables passed to it are there.  (eg: check if user_registered is returned for the meta_key user_registered, and display nothing
*  check for blank or empty strings in meta key (ie: meta key exists, but it is "empty") and treat as though meta key does not exist.
*  You can now put the personalised details in a text widget.  I added a text widget filter as per this suggestion: http://hackadelic.com/the-right-way-to-shortcodize-wordpress-widgets.   Note that this will enable all shortcodes for the text widget. Please test thoroughly as there is some concern about the order of plugins loading.
*  Added ability to convert a user meta data that is an array of values to a comma separated string (EG: roles).   Since capabilties only have boolean values, and it is the key that is useful, if you ask for capabilties, it will return a string of capabilities keys for which the value='1'.

= 2.2 =
* added ability to have a personalised message using the logged in users data IF it is available.  If there is no data, then the message is NOT displayed.
* added rss feed updates to keep people up to date on developments

= 2.0 =

* Added full shortcode functionality to wordpress email message and subject.
* Added access to more user data

= 1.0 =

* Launch of the plugin


== Installation ==

1. In the wordpress admin plugin section of your site, click "Add New" or download and Unzip the file into your wordpress plugins folder.
2. Activate the plugin through the 'Plugins' menu in WordPress
3. In the plugin settings page, define text to use if no user details found, Optionally redefine the wordpress sender name and email.
4. Add one or more [user] to a page, post or email template such as in Subscribe2, Post Notification or [Your Responder](http://www.newmedias.co.uk/?a_aid=34bf6dcc "Affiliate Link to the providers of Your Responder").
5. Test by either: logging in as someone and browsing to the page or post; or using S2 or YR to send a broadcast email to test email addresses only (such as yourself!)

= How to use  =

Simplest: Put [user] in your page, post, or email template.  eg: Welcome [user]

Note: at this stage, only the simpler meta values should be used.  later more complex data structures stored in the meta values may be accessible as in the amr_users listing tool.  See also http://codex.wordpress.org/Function_Reference/get_userdata for info.

Examples:

*  [user]     will display display name
*  [user user_login]
*  [user user_email]
*  [user display_name]
*  [user user_nicename]
*  [user first_name last_name]
*  [user description]
*  [user roles]
*  [user yourtableprefix_capabilities]
*  [user message="Dear %s," display_name ]
*  [user message="You registered on %s as a %s " user_registered wp_capabilities ]
*  [user message="A url like %s is valuable. " user_url]
*  [user message="You said you came from %s " state] - a custom register plus field


== Screenshots ==

1. Edit Screen showing how to use the plugin's shortcode to display the usersname if logged in.
2. A post that has been personalised
3. The admin screen, where one can specify the name or word to use if the user is not logged in, or there is no name available.
4. The personalised post if the viewer is not logged in.
5. Setting up a subscribe2 email with the personalise shortcode
6. A received subscribe2 email that has been personalised.
7. Setting up a [Your Responder](http://www.newmedias.co.uk/?a_aid=34bf6dcc "Link to Your members who do Your Responder")
8. A received email sent by the YR plugin
