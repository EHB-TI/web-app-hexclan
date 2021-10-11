# Goal
*describe how this web app will (evantually) earn money or make the world a better place*

* Use case: a fun(draising) event organised by students of EhB.
* Student events tend to organize the financial part of the story with food and drink vouchers sold at the cash register at the entrance. At the bar, vouchers are exchanged for drinks or snacks.
* We want to get rid of the hassles of the cash registry at the entrance; we want to eliminate cash money and any potential fraud.
* *Let's digitize this burdensome cash registry process and focus on the fun during these events!*
* Value proposition: efficient cash register digitizing all payments.

# Acceptance criteria
*how do we know that the goals have been reached?*

*   We want a generic website in order to use it for several events 
*   Admin users can enter the name of the event, beneficiary bank account and products (simple catalogue with 3 or 4 options).
    Example: 
    *   Name event: Openingsfuif
    *   Bank account (beneficiary): BE15 0016 7783 1431
    *   Purchase: drankkaart van 10 bonnetjes
*   Admin user can change the product catalogue and the prices (admin should login with credentials, preferably with 2fa) - nice to have: log file of changes applied by the admin.
*   Guest users can choose their products (entrance fee, drink/food vouchers, ...)
*   The website calculates the amount to pay and generates a QR code for payment. The QR code should follow the specs of the [European Payments Council (EPC)](https://www.europeanpaymentscouncil.eu/sites/default/files/KB/files/EPC069-12%20v2) for them to be able to be read by bank apps (see also pdf in resources folder).
- Guest users can in turn scan the QR code with their bank app (ING, KBC, BNP Paribas Fortis, ...), and show proof of payment with their cell phone.
- Once proof of payment is confirmed, the guests get their vouchers.
- We want to avoid commercial initiatives such as Payconiq and iDeal. Those initiatives imply high barriers of entry: registry of the association (which often don't even exist), registry administration, a cost per transaction, etc...

# Threat model
*describe your threat model. One or more architectural diagram expected. Also a list of the principal threats and what you will do about them*
# Deployment
*minimally, this section contains a public URL of the app. A description of how your software is deployed is a bonus. Do you do this manually, or did you manage to automate? Have you taken into account the security of your deployment process?*
# *you may want further sections*
*especially if the use of your application is not self-evident*
