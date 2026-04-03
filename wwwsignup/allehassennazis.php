<?php
$name = $_GET['name'];
?>
<html>
    <head>
        <title>Alle Hassen Nazis!</title>
        <style>

body{
  font-family: 'Nunito', sans-serif;
}

.a{color: #E7484F}
.b{color: #F68B1D}
.c{color: #FCED00}
.d{color: #009E4F}
.e{color: #00AAC3}
.f{color:  #732982}


.container{
  margin-top: 150px;
}

.text-center {
  text-align: center;
}

.rainbow{
  background-color: #343A40;
  border-radius: 4px;  
  color: #fff;
  cursor: pointer;
  padding: 8px 16px;
  
}

.rainbow-1:hover{
   background-image: linear-gradient(90deg, #00C0FF 0%, #FFCF00 49%, #FC4F4F 80%, #00C0FF 100%);
   animation:slidebg 5s linear infinite;
}

.rainbow-2:hover{
  background-image: linear-gradient(to right, red, orange, yellow, green, blue, indigo, violet, red);
  animation:slidebg 2s linear infinite;
}

.rainbow-3:hover{
  background-image: linear-gradient(to right, red, orange, yellow, green, blue, indigo, red);
  animation:slidebg 2s linear infinite;
}

.rainbow-4:hover{
   background-image:     linear-gradient(
      to right, 
      #E7484F,
      #F68B1D, 
      #FCED00,
      #009E4F,
      #00AAC3,
      #732982
    );
  animation:slidebg 2s linear infinite;
}


.rainbow-5:hover{
   background-image:     linear-gradient(
      to right, 
      #E7484F,
      #E7484F 16.65%,
      #F68B1D 16.65%,
      #F68B1D 33.3%,
      #FCED00 33.3%,
      #FCED00 49.95%,
      #009E4F 49.95%,
      #009E4F 66.6%,
      #00AAC3 66.6%,
      #00AAC3 83.25%,
      #732982 83.25%,
      #732982 100%,
      #E7484F 100%
     
     
     
    );
  animation:slidebg 2s linear infinite;
}


@keyframes slidebg {
  to {
    background-position:20vw;
  }
}

.follow{
  margin-top: 40px;
}

.follow a{
  color: black;
  padding: 8px 16px;
  text-decoration: none;
}


        </style>
    </head>
    <body>
        <div class="container text-center">
  <h1 class=""> Danke für dein Interesse <?php echo $name; ?>!<br />Aber 
    <span class="a">1</span><span class="b">6</span><span class="c">1</span><span class="d">h</span><span class="e">o</span><span class="f">s</span><span class="a">t</span>.NET stellt nur Services bereit für Leute die Nazis hassen! </h1>
  <h3>Finde heraus warum auch du Nazis hassen solltest.</h3>
  <a href="https://www.exit-deutschland.de" class="rainbow rainbow-1">EXIT-Deutschland<a/>
  <a href="https://www.bpb.de/themen/rechtsextremismus/dossier-rechtsextremismus/214239/wo-demokraten-gefaehrlich-leben/" class="rainbow rainbow-2">bpb Infos<a/>
  <a href="https://www.amadeu-antonio-stiftung.de/alle-12-minuten-eine-rechte-straftat-hass-und-gewalt-sind-zum-flaechenbrand-geworden-137603/" class="rainbow rainbow-3">Amadeu Antonio Stiftung<a/>
  <a href="https://rote-hilfe.de" class="rainbow rainbow-4">Rote Hilfe<a/>
  <a href="https://www.kas.de/de/web/extremismus/rechtsextremismus" class="rainbow rainbow-5">Konrad Adenauer Stiftung<a/>
    
    <div class="follow">
    <a class="rainbow-4" href="https://161host.net">161host.NET<a/>
</div>
    
    </body>
</html>
