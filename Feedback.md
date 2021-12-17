# Feedback hexclan security testing

De registratieprocudere is niet met succes voltooid geworden.

 De pincode scheen telkens meteen vervallen, ook bij instant inloggen.

# De request die we wel hebben kunnen testen

> GET, PUT, en OPTIONS requests op niet-bestaande resources resulteren zoals verwacht in status code 404.

> Elke URL beantwoordt OPTIONS requests zoals verwacht.

> De Access-Control-Allow-Methods response header bevat op correcte wijze de verzameling van ondersteunde methoden 
en enkel de ondersteunde methoden.

# Conclusie
 Door de registratiemoeilijkheden is de test-scope eerder beperkt. 
 Wat wel getest kon worden, mag als conform en veilig beschouwd worden.
