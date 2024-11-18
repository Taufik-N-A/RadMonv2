/**
 * Radius Monitor Change Ticket Config
 * Made by Maizil <https://github.com/maizil41>
 */
document.getElementById("changeConfig").addEventListener("submit",function(event){var messageElement=document.getElementById("message");if(messageElement){messageElement.style.display="none"}
if(this.checkValidity()){document.getElementById("loader").style.display="inline-block"}else{event.preventDefault()}})