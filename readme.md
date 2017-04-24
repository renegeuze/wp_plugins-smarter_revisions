# Smarter revisions

Delete revisions not based on absolute numbers but on incrementing time based
intervals.

Note that this is not like a backup rotation scheme.
Daily rotation means a minimum time of 1 day between saved revisions.

## Why this plugin

Usually not because your database is full.
Your database can handle a lot of data.
This plugin is mostly useful to give your users a shorter list of revisions
to go through so they might feel less overwhelmed.


## TODO list

### Must have

* Decent readme
* More code comments
* Something with WP timezone settings

### Should have

* Config using constants
* Option to delete revisions on other hooks/cron
* Admin warning if other filters mess with the revision count
* Correct stuff to push this repo to official WP site.

### Could have

* Admin interface (optionally disabled using constant)
* Some Debug output to check what would be deleted
* Different setting per post type
* Option to keep all changes made by specific users.
* Filters
* Lowered minimum required PHP version
* Customized intervals
