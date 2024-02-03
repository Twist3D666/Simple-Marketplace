#Bitcoin Simple Marketplace Script
---------------------------------------

This Script will make you able to host a DarkNet Marketplace with simple Functions.
The Admin still has to change User-Status to Vendor.
It will be created an account for each user, containing receiving addresses for Payments.
You have to install BitcoinCore and get the Blockchain-Data for making the Marketplace working.
Then you Setup your bitcoin.conf with an RPC-User and RPC-Password, for making the Script work.
The Data will be configured in the index.php

The Vendor has to click Check Orders, so the Script will check if there is enough BTC sent to his RCV-Address, so it will be marked as Paid.

What needs to be done:
Configuring the Vote-Ability for Users and Vendors for Products
Designing the div for entries.
(Making the Users able to send Coins on the Market automatically.)
Making the Marketplace encrypt the Shipping Address automatically.

(required is bitcoin-core below 0.17.0, as the used "account"-feature has been removed.)

