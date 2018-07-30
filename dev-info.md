# Feedback during Dev

Place to write up next steps or what's left to do

## Discussion
- Autoloader
- Bottom-right banner doesn't save
- Adding links to confirmation pages in emails
- Implementing discount code stats in core and using that graph
- Dismiss banner Xs in upper right?
- Remove Dev Info (class and button)

=== v1.3.5 ===
- Move banners to methods in a class
- Make meta fields for pre-sale, sale, and post-sale content

=== v1.3.4 ===
- <s>Finalize settings/cpt page layouts</s>
<s>If we have the page for Sitewide sale under Memberships specifying the Active sale, what is the checkbox for on each sale cpt?</s>
<s>Not sure that the checkbox is saving at the moment.</s>
<s>Down to one php file, checkout.php, that is not a class.</s>

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
