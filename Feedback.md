# Feedback hexclan security testing

De registratieprocudere is niet gebruiksvriendelijk en het doorlopen ervan is moeizaam verlopen.

Nadat we erin geslaagd waren de registratie te volbrengen, hebben we eenmalig een token ontvangen, bij een volgende poging kregen we geen token meer wat nodig was om sommige calls te testen.


## Bijkomende testen 
### HSTS
![HSTS](https://github.com/dmtwood/pizzeria/blob/main/hexcan_hsts.jpg)
### DNS CAA
![DNS](https://github.com/dmtwood/pizzeria/blob/main/hexclan_dns.jpg)
### SSL
![SSL](https://github.com/dmtwood/pizzeria/blob/main/hexclan_ssl.jpg)
We geven ze mee voor de volledigheid, maar kunnen zelf onvoldoende inschatten in welke mate ze relevant zijn in combinatie met een API architectuur.


# Evaluatiecriteria

Het application/json media type wordt steeds ondersteund, zowel voor request als response bodies. De API mag ook andere media types aanvaarden, zoals bv. application/x-www-form-urlencoded, maar dat hoeft niet.

![image](https://user-images.githubusercontent.com/49392867/146678538-86a444a8-9e14-43bc-aa70-4ff12806625d.png)


Succesvolle requests worden beantwoord met status codes 200, 201 (in geval van een POST), of 204 (in geval er geen respons body is).

![image](https://user-images.githubusercontent.com/49392867/146678673-ba91422b-04b0-41c0-9b1f-6c23ec75d7b0.png)

![image](https://user-images.githubusercontent.com/49392867/146678833-af2b1c22-655f-46b3-9981-c2f9a95e65a5.png)

Response 405 zou hier aangewezen zijn, maar we zien een 400 bad request.
![image](https://user-images.githubusercontent.com/49392867/146679461-8e3478ec-dcd2-429e-98f0-e2707f30d212.png)
![image](https://user-images.githubusercontent.com/49392867/146679495-76ed62f5-ef06-4417-ae31-f495d0c6c7b0.png)
![image](https://user-images.githubusercontent.com/49392867/146679560-55cd912e-b367-4d53-ab36-dedbbee411fc.png)


Een request die authenticatie vereist, maar geen access token bevat, geeft aanleiding tot status code 401.
![image](https://user-images.githubusercontent.com/49392867/146679059-5b1bfa87-5c61-429d-9399-af842785e85f.png)


Indien de Accept request header een niet ondersteund media type bevat, dan resulteert een request in status code 406
> Dit is niet testbaar aangezien er geen file upload feature zichtbaar is in de API.

Responses met een body bevatten zowel een correcte Content-Type header als X-Content-Type-Options: nosniff om MIME sniffing tegen te gaan.

![image](https://user-images.githubusercontent.com/49392867/146679917-dae63cca-ff56-403d-9975-3d7658534780.png)

> Geen X-Content-Type-Options: nosniff om MIME sniffing tegen te gaan.

Een collectie ondersteunt de methodes PUT, PATCH en DELETE niet. Ze worden dus beantwoord met status code 405.

![image](https://user-images.githubusercontent.com/49392867/146680050-77650f25-e1ab-4f08-804c-e4a1166cf2eb.png)


POST wordt nooit ondersteund op een element.

![image](https://user-images.githubusercontent.com/49392867/146680180-00bc60e4-61cf-4211-801e-aaaab8265989.png)
> Hieraan werd niet voldaan. 



Indien de REST API bereikbaar is van op het publieke netwerk en de origin verschilt van de API consumers (aangeraden), dan zijn ook volgende voorwaarden van toepassing.

> Dit hebben we niet kunnen testen omdat we enkel met Postman moesten werken, er was geen frontend. Cors-headers hebben geen effect op Postman. 


De lijst van toegelaten origins worden gedocumenteerd in de README van je git repo.
> Deze lijst was niet beschikbaar.


> GET, PUT, en OPTIONS requests op niet-bestaande resources resulteren zoals verwacht in status code 404.

![image](https://user-images.githubusercontent.com/49392867/146681124-7cd412fb-22d9-49fb-8ae5-05c3463df6f9.png)

![image](https://user-images.githubusercontent.com/49392867/146681167-53bbc225-a4d4-4e4f-affc-e4894c5086dd.png)

Put  op niet bestaande resource resulteert in een 405, geen 404.

![image](https://user-images.githubusercontent.com/49392867/146681093-a596c5c9-b51c-4037-ad53-600dd62382cd.png)

Options op niet bestaande resource resulteert in een 200, geen 404.


> Elke URL beantwoordt OPTIONS requests zoals verwacht.
> 
![image](https://user-images.githubusercontent.com/49392867/146681071-99acf812-2bd6-479b-9f54-24ad44951846.png)


> De Access-Control-Allow-Methods response header bevat op correcte wijze de verzameling van ondersteunde methoden en enkel de ondersteunde methoden.
> 
![image](https://user-images.githubusercontent.com/49392867/146681267-00a5d873-7765-42e3-9429-5d467abe096e.png)



# Conclusie
 Door de registratieprocedure,  de registratiemoeilijkheden en doordat we enkel met postman konden testen, is de test-scope enigszins beperkt. 
 Het is niet gebruiksvriendelijk om eerst een registratie aan te moeten vragen en dan pas te registreren. Het zou kunnen geinspireerd zijn vanuit een veiligheidsoverwegen, maar de lasten wegen ons insziens niet helemaal op tegen de baten.
 
 Op de login en registratie is een get method beschikbaar, dat lijkt ons niet het opzet en zou best beperkt worden tot het toelaten van post method.
 
 OPTIONS requests op niet-bestaande resources resulteren ook in een 200, waar het beter is ze te laten resulteren in een status code 404.
 Put  op niet bestaande resource resulteren nu in een 405, waar het beter is ze te laten resulteren in een status code 404.
 
 Tot slot raden we ook aan om X-Content-Type-Options:nosniff te implementeren, om MIME sniffing tegen te gaan.
 
 We hebben elementen gevonden die een post method toestaan, onze aanbeveling is om deze updates enkel via PUT te laten verlopen. 
 
Het overige mag als conform en veilig beschouwd worden.

