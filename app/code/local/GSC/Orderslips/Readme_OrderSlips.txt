/*********************************************

   README FOR: ORDER SLIPS MODULE

/*********************************************


/*** GETTING STARTED ***/

1. Install module files to base Magento installation. This is an "Admin only" module so it will only affect the Admin area (not the frontend).
2. Clear cache, log out & log back in to Magento admin panel.
3. Go to System > Configuration > Artizara Extensions > Additional Order Slips.
4. Set all required options (Enable & Show Button on Order View page). You may open OrderSlipsFormInfo.rtf which will have all Intro and Return/Exchange info for Artizara, Fashion Undercover & Wholesale stores. Place in required text area fields & Save.


/*** USING ***/

1. Head to the Sales > Orders page.
2. There, you may select multiple orders and click on the actions dropdown to choose either Return / Exchange form OR Gift Receipt form & press submit. This will bring up a pdf which you can then print.
3. If enabled ("Show Order View Button") in configuration, you can go into any order and press either button. A pdf will come up which you can then print.


/** LETTING CUSTOMERS KNOW ABOUT GIFT RECEIPT OPTION **/

/* Firecheckout (Templates Master - https://templates-master.com/) */

1. Go to app/design/frontend/pro/default/template/firecheckout/checkout/review.phtml
2. Place the following code:

	<p style="padding:6px 12px; border:1px dashed #e0e0e0; background:#f5f5f5;">Need a <strong>Gift Receipt</strong>? Let us know in the order comments box below!</p>

just below the <div id="checkout-review-submit"> tag.