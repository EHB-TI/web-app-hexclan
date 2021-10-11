# Goal
*describe how this web app will (evantually) earn money or make the world a better place*

- Idea1
Use case: a fun(draising) event organised by students of EhB.
Student events tend to organize the financial part of the story with food and drink vouchers sold at the cash register at the entrance. At the bar, vouchers are exchanged for drinks or snacks.
We want to get rid of the hassles of the cash registry at the entrance; we want to eliminate cash money and any potential fraud.
Let's digitize this burdensome cash registry process and focus on the fun during these events!
Value proposition: efficient cash register digitizing all payments.

- Idea2
The use case is that of a ticketing system used to improve the quality of the course material provided in the bachelor toegepaste informatica organised by the EhB. The objective is to enable users (students) to raise issues linked with the course material, such as typos in syllabi, outdated software components, dead links,...

# Acceptance criteria
*how do we know that the goals have been reached?*

Generic website 
- Guest users can choose the products (entrance fee, drink/food vouchers, ...)
- Admin user can change the product catalogue and the prices (admin should login with credentials, preferably with double authentication)
- The website calculates the amount to pay and generates a QR code. The QR code should follow the specs of the European Payments Council (EPC) - see attached.
students scan the QR code, pay with the bank app and show proof of payment with their cell phone.

We don't necessarily need Payconiq. A QR code generator might be sufficient. See http://phpqrcode.sourceforge.net/ and official documentation of the European Payments Council to generate a QR code: 
https://www.europeanpaymentscouncil.eu/sites/default/files/KB/files/EPC069-12%20v2.1%20Quick%20Response%20Code%20-%20Guidelines%20to%20Enable%20the%20Data%20Capture%20for%20the%20Initiation%20of%20a%20SCT.pdf

- Idea2
The administrator can enter the name of the event, bank account and what the customer wishes to purchase (simple catalogue with 3 or 4 options).
Example: 
  Name event: Chiro fuif
  Bank account (beneficiary): BE15 0016 7783 1431
  Purchase: drankkaart van 10 bonnetjes
That generates a QR code that can be read by Payconiq.
Feedback message to confirm payment is complete

- Idea3
  - Regular users are able to generate tickets linked to a particular course and assign a priority level. Fields should be present to upload and share screenshots and source code. Users have access to an overview of the tickets they have generated.
  - Privileged users (professors) should get access to an admin panel containing an overview of the ticket queues paired with the courses they are teaching. This implies that tickets should be routed to the correct privileged account. Professors should be able to communicate with students wrt a particular ticket in a forum-like interface. Professors should be able to update the status of a given ticket (open, in treatment, closed).
  - From a security perspective, only students and professors of the relevant domain (ehb.be) should be granted access to the tool. 
    - Optional: the authentication system should leverage the existing AD of the EhB.


# Threat model
*describe your threat model. One or more architectural diagram expected. Also a list of the principal threats and what you will do about them*
# Deployment
*minimally, this section contains a public URL of the app. A description of how your software is deployed is a bonus. Do you do this manually, or did you manage to automate? Have you taken into account the security of your deployment process?*
# *you may want further sections*
*especially if the use of your application is not self-evident*
