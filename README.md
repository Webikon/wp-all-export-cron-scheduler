# wp-all-export-cron-scheduler
WordPress plugin for setting up and running recurring export cron jobs for WP All Export.

See https://www.wpallimport.com/documentation/recurring/cron/.

Asana brief: https://app.asana.com/0/341576714325118/1158502185544681

## Usage

1. Activate plugin.
1. Set-up your exports in Admin > Settings > WPAE Cron Scheduler.
1. Use function `wpae_crsch_get_exports_list` to retrieve a list of exports from another plugin or theme.

## Data format

```
array(
    array(
        'id' => 56,
        'is_wc_products' = 1    //  1 or 0, optional
    ),
    array(
        'id' => 27,
    ),
)
```

## Logging

Any errors that occur during HTTP calls are logged using Wonolog library.