# Heatzy Pilote : Description des points de données

Ce document présente l’ensemble des points de données accessibles via les API du module Heatzy Pilote de seconde, troisième et quatrième génération. (voir [le guide des générations de pilote](https://docs.google.com/document/d/1pPtNXlcbZtI_iEK47yl-kQDyy95WR4gg8UwnVWcvv8M/edit#heading=h.6mbky5jn3693))

## Description des paramètres

* Paramètre : Le point de donnée accessible via l’API  
* Type (valeur) : Le type de donnée accepte par l’API (nombre entier, booléen, texte) ainsi que les valeurs min-max acceptées par l’API.   
* Écriture : La capacité d’écriture ou non de la valeur. (écriture ou lecture seule)

| Paramètre | Type | Écriture | Description |
| :---- | :---- | :---- | :---- |
| mode | Enum (0-5\*) | écriture | Modes de chauffe envoyé au radiateur. 0 : confort 1 : éco 2 : hors-gel 3 : arrêt 4 : confort-1 \* 5 : confort-2 \* \*Seulement les produits de 4ᵉ génération (Pilote\_Soc\_C3 et Elec\_Pro\_Ble) reçoivent les modes confort-1 et confort-2 |
| timer\_switch | Bool (0-1) | écriture | Activation du mode programmation. |
| derog\_mode | Enum (0-2) | écriture | Bascule entre les différentes dérogations. 0 : pas de dérogation 1 : mode vacances 2 : mode boost |
| derog\_time | Int (0-255) | écriture | Temps restant à la dérogation en cours. En mode vacances : Le temps est compté en jours En mode boost : Le temps est compté en minutes |
| boost\_switch | Bool (0-1) | écriture | Obsolète |
| boost\_time | Int (0-255) | écriture | Obsolète |
| lock\_switch | Bool (0-1) | écriture | Activation du verrouillage. |
| time\_week | Int (1-7) | écriture | Jour de la semaine actuel. |
| time\_hour | Int (0-24) | écriture | Heure de la journée actuelle, comptée en tranches de 30 minutes. 00 : 00h00 01 : 00h30 02 : 01h00 … 14 : 07h00 15 : 07h30 … |
| time\_min | Int (0-255) | écriture | Obsolète |
| data1 | Int (0-255) | écriture | Non utilisé |
| data2 | Int (0-255) | écriture | Non utilisé |
| pX\_dataY | Int (0-255) | écriture | Modes de chauffage définis dans le programme. X : Jour de la semaine (1-7 : lundi-dimanche) Y : Heure de la journée, par tranches de 2 heures (0-12) Codage de la valeur : L'appareil utilise 2 octets pour chaque mode enregistré dans le programme. 00 : confort 01 : éco 10 : hors-gel Il concatène ensuite 4 modes en une valeur de 8 octets. Les octets sont liés de droite à gauche, dans l'ordre chronologique. Exemple 1 : data1 \= 01 01 01 01 00h00=éco, 00h30=éco, 01h00=éco, 02h00=éco Exemple 2 : 01 01 00 00 00h00=confort, 00h30=confort, 01h00=éco, 02h00=éco Une fois que l'appareil a enregistré les données du programme dans une chaîne de 8 octets, il convertit la valeur en un nombre décimal. Exemple 1 : data1 \= 01 01 01 01 data1 \= 85 Exemple 2 : data1 \= 01 01 00 00 data1 \= 80 |

## 

## Hiérarchie de fonctionnement

Un module Heatzy peut suivre 3 modes de fonctionnement : 

* **Dérogation** : Ce mode de fonctionnement a la hiérarchie la plus élevée et force l'appareil à changer de mode en conséquence. En mode vacances, l'appareil sera réglé en mode hors-gel jusqu'à la fin de la dérogation. En mode boost, l'appareil sera réglé en mode confort jusqu'à la fin de la dérogation. Le mode Prog fonctionne toujours en arrière-plan, mais il est ignoré.  
* **Programmation** : L'appareil peut suivre un programme hebdomadaire. En fonction du programme, l'appareil peut être réglé en mode confort, éco ou antigel. Le mode programme a une priorité relative. Si l'utilisateur souhaite changer de mode manuellement alors que l'appareil suit un programme, l'appareil changera de mode en fonction de l'entrée de l'utilisateur, mais ce changement de mode sera temporaire.  
* **Manuel** : Dans cet état, l'appareil ne suit aucun automatisme. L'appareil ne changera de mode que si l'utilisateur interagit avec lui.