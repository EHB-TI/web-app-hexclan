# Goal
*describe how this web app will (evantually) earn money or make the world a better place*

- Idea1
We want to optimise and digitize the way parents approach a children's day care centre from the moment their child is born until the child goes to kindergarten.
For that, we foresee a website with some static webpages, but also a web page that allows the parents to raise questions, ask supplementary information on the website.
That information will have to be sent securely. The personal data entered on the form will have to be kept securely following GDPR requirements.

- Idea2
Imagine an association, chiro, scouts or petanque club that wishes to organise a fundraising event such as a BBQ or a party. Usually, we work with meal and drinking vouchers sold at the cash register at the entrance. We want to get rid of all the hassles due to working with cash money and the potential fraud.
Let's digitize this burdensome process during these fun events. 

- Idea3

The use case is that of a ticketing system used to improve the quality of the course material provided in the bachelor toegepaste informatica organised by the EhB. The objective is to enable users (students) to raise issues linked with the course material, such as typos in syllabi, outdated software components, dead links,...

# Acceptance criteria
*how do we know that the goals have been reached?*

- Idea1
The website is securely hosted on a cloud server.
Messages can be sent by visitors of the website. It should not be possible to send hundreds of messages at once (email bomb).
The personal data entered should be stored on a contacts database to be able to respond to the message.

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
