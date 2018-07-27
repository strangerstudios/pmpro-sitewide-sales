# Feedback during Dev

Place to write up next steps or what's left to do


- Implementing discount code stats in core and using that graph
- Adding links to confirmation pages and emails
- Finalize settings/cpt page layouts
- Remove Dev Info (class and button)

## Discussion
If we have the page for Sitewide sale under Memberships specifying the Active sale, what is the checkbox for on each sale cpt?
Not sure that the checkbox is saving at the moment.
Down to one php file, checkout.php, that is not a class.

=== v1.3.3 ===
- Landing page CPT, or settings/meta for landing page
- Dropdown on Reports needs afixin
Column Title = Screen Options ![Labeling columns gives checkbox for Screen Options](https://monosnap.com/image/QXU4oOs8icHUjR8pFqpTGRurwtULhj.png)

=== v1.3.2 ===
- Give CSS Selectors for chosen banner (David has a branch with this)
- How many people who used the summer code were already members? (David has a branch with this)
- Settings clean up
- pmpro-sws-reports.php vs pmpro-sws-reports.js

=== v1.3.1 ===
jQuery moved to files and enqueued.
better reports generation, added filter, modular reports
Merge conflict cleaned up
http://take.ms/xdZA8
### Two banners is
![Two Banners show](https://monosnap.com/image/9vY49q80NYG8Z5et9nJs58wKRn1Tfs.png)
deleting speculative help menu and customizer
hooking top banner on wp_head for minimal css - does'nt need to be
wp_footer because no inline jQuery any longer

=== v1.3 ===
renaming classes
moving admin.php to classes

=== v1.2 ===
adding in classes


## Errors
[] Notice: Undefined index: active_sitewide_sale_id in /app/public/wp-content/plugins/pmpro-sitewide-sale/includes/banners.php on line 16

Set as Current Sitewide Sale:
[] Notice: Undefined index: active_sitewide_sale_id in /app/public/wp-content/plugins/pmpro-sitewide-sale/includes/classes/class-sws-meta-boxes.php on line 143

Error from php in js ![Error](https://monosnap.com/image/jWumN2JwT3Y3VIZEPcU8QZBwpsoXId.png)
