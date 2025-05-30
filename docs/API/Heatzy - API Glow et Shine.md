# ![][image1]

# Heatzy Glow et Shine : Description des points de données

Ce document présente l’ensemble des points de données accessibles via les API des radiateurs Heatzy Glow et Heatzy Shine.

## Description des paramètres

* Paramètre : Le point de donnée accessible via l’API  
* Type (valeur) : Le type de donnée accepte par l’API (nombre entier, booléen, texte) ainsi que les valeurs min-max acceptées par l’API.   
* Écriture : La capacité d’écriture ou non de la valeur. (écriture ou lecture seule)

| Paramètre | Type | Écriture | Description |
| :---- | :---- | :---- | :---- |
| on\_off | bool (0-1) | écriture | Allumage du radiateur. |
| mode | Enum (0-5) | écriture | Modes de chauffe. (mode manuel) 0 : confort 1 : éco 2 : hors-gel 3 : arrêt 4 : confort-1 5 : confort-2 |
| cur\_mode | Enum (0-5) | lecture seule | Modes de chauffe actuel. 0 : confort 1 : éco 2 : hors-gel 3 : arrêt 4 : confort-1 5 : confort-2 |
| timer\_switch | Bool (0-1) | écriture | Activation du mode programmation. |
| derog\_mode | Enum (0-2) | écriture | Bascule entre les différentes dérogations. 0 : pas de dérogation 1 : mode vacances 2 : mode boost |
| derog\_time | Int (0-255) | écriture | Temps restant à la dérogation en cours. En mode vacances : Le temps est compté en jours En mode boost : Le temps est compté en minutes |
| cur\_temp\_L | Int (0-255) | lecture seule | Température de la pièce, lue par le capteur du radiateur. (bit bas) La température est exprimée en dixièmes de degrés. |
| cur\_temp\_H | Int (0-255) | lecture seule | Température de la pièce, lue par le capteur du radiateur. (bit haut) La température est exprimée en dizaines de degrés. La température totale s’obtient en ajoutant le bit haut et le bit bas. |
| cft\_temp\_L | Int (0-255) | écriture | Consigne de température du mode confort. (bit bas) La température est exprimée en dixièmes de degrés. |
| cft\_temp\_H | Int (0-1) | écriture | Consigne de température du mode confort. (bit bas). La température est exprimée en dizaines de degrés. La température totale s’obtient en ajoutant le bit haut et le bit bas. |
| eco\_temp\_L | Int (0-255) | écriture | Consigne de température du mode éco. La température est exprimée en dixièmes de degrés. |
| eco\_temp\_H | Int (0-1) | écriture | Consigne de température du mode éco. (bit bas) La température est exprimée en dizaine de degrés. La température totale s’obtient en ajoutant le bit haut et le bit bas. |
| com\_temp | Int (0-100) | écriture | Étalonnage de la température du capteur. La température est exprimée en dixièmes de degrés. 0 : \-5°C,  50 : pas de changement 100 : \+5°C |
| boost\_switch | Bool (0-1) | écriture | Obsolète |
| boost\_time | Int (0-255) | écriture | Obsolète |
| lock\_c | Bool (0-1) | écriture | Activation du verrouillage. |
| week | Int (1-7) | écriture | Jour de la semaine actuel. |
| hour | Int (0-24) | écriture | Heure de la journée actuelle, comptée en tranches de 30 minutes. 00 : 00h00 01 : 00h30 02 : 01h00 … 14 : 07h00 15 : 07h30 … |
| min | Int (0-255) | écriture | Obsolète |
| data1 | Int (0-255) | écriture | Non utilisé |
| data2 | Int (0-255) | écriture | Non utilisé |
| power\_dataL | Int (0-255) | écriture | Obsolète |
| power\_dataH | Int (0-255) | écriture | Obsolète |
| power\_cnt | Enum (0-10) | écriture | Obsolète |
| pX\_dataY | Int (0-255) | écriture | Modes de chauffage définis dans le programme. X : Jour de la semaine (1-7 : lundi-dimanche) Y : Heure de la journée, par tranches de 2 heures (0-12) Codage de la valeur : L'appareil utilise 2 octets pour chaque mode enregistré dans le programme. 00 : confort 01 : éco 10 : hors-gel Il concatène ensuite 4 modes en une valeur de 8 octets. Les octets sont liés de droite à gauche, dans l'ordre chronologique. Exemple 1 : data1 \= 01 01 01 01 00h00=éco, 00h30=éco, 01h00=éco, 02h00=éco Exemple 2 : 01 01 00 00 00h00=confort, 00h30=confort, 01h00=éco, 02h00=éco Une fois que l'appareil a enregistré les données du programme dans une chaîne de 8 octets, il convertit la valeur en un nombre décimal. Exemple 1 : data1 \= 01 01 01 01 data1 \= 85 Exemple 2 : data1 \= 01 01 00 00 data1 \= 80 |

## 

## Hiérarchie de fonctionnement

Un radiateur Heatzy peut suivre 3 modes de fonctionnement : 

* **Dérogation** : Ce mode de fonctionnement a la hiérarchie la plus élevée et force l'appareil à changer de mode en conséquence. En mode vacances, l'appareil sera réglé en mode hors-gel jusqu'à la fin de la dérogation. En mode boost, l'appareil sera réglé en mode confort jusqu'à la fin de la dérogation. Le mode Prog fonctionne toujours en arrière-plan, mais il est ignoré.  
* **Programmation** : L'appareil peut suivre un programme hebdomadaire. En fonction du programme, l'appareil peut être réglé en mode confort, éco ou antigel. Le mode programme a une priorité relative. Si l'utilisateur souhaite changer de mode manuellement alors que l'appareil suit un programme, l'appareil changera de mode en fonction de l'entrée de l'utilisateur, mais ce changement de mode sera temporaire.  
* **Manuel** : Dans cet état, l'appareil ne suit aucun automatisme. L'appareil ne changera de mode que si l'utilisateur interagit avec lui.

---

Copyright Heatzy 2024

[image1]: <data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAIwAAAAtCAYAAAB8gIN1AAAETElEQVR4Xu2aTYscVRSGO4qCoIimq4NhwE1SNUNP0l0ZEQT/gASCIEJWgkt/gaB/wLXgwpWudefCIC5cSLISdKsbQZQsA4KBJH7dQ6ZC+fS5dc89DiKp88C7qfu8b3fuFFF0FosgCIIgCIKHhGbb/Zny1xCez5l0Hz/E3YDxhcSl/JO4G4W4lDxxNwpxKXnibhTiUvLE3SjEpeSJu1GIS8kTd6OgXQqf8fzfwL2T3B7gbm5/dHaXZwL7DH1hte3u0DPmBrcGpj4vh6djQvnixSy3+19ypwQ3psKukVPcyWXZd2f5jGMCHS2eTi7pe10p7fFcw9Mxw/GacCsHe5Yst+cvcycHu55wU6CjxdOZSmmP5xqejhmOV+YW94jSMWfZt1e5R9jxhrsCHS2ezlRKezzX8HTMcBz5VJxVv/+aclb8MnSRr56+cOGZpm9fUc5M+4lH6I+z2rYfNtv2Yz7XwmGBTrPp3h6yWK8fpz9G/mxT2dnOfI/SuYanY4bjpQ+hl3PT89/p5dwBuqUOveN8QW8g/aDfVPzsZ1gcD9yd2rc4xNMxw/HSBzR995PFp5PzBtL59/Sneun5bXrpb5RX6ZG9l/aeYG/iM4pOLdwsbVu9MZ6OGY6nfECHsMNzgc6ZiweHdIQm8zdR7X7O02Av17U4NXDPslvjDng6ZjzjpQ7PrU4mv7Er0ON5CUvf4ljhlnWz1hc8HTOe8VKH54Oz6ts3+DwX+UcHd8fQ53kJS9/iWOBOzd5/1THjGS91eF4TbuXw9gYsfYtTghu1W56ep2PGM17q8NySqQ2eCaV+CUvf4kzBvmfH0/V0zHjGS51V371FJxd2BTqnN+2LJee5/uB5OlOwz3PB4uRg90HW6yfpTsE+zzU8HTOecUuHDvIz/TH0eS7QyXka7OW6FkcjuXfZlSz77gW6Jbhh+R61fhWecUuHjuT0pfMH9Ag7uX2B3pQ7QH+qZ3EIOzVdDe6UtuiW/Go849YOveP8QU9QvCG/0h1QXM/3yXboyP/GoDOmUf5j4nG+pVuDsif53OipfzY3nnFrp9m079OtDTcJfW+4K9Bhav1SuDdArzbcE0rnWSzjpKaTzu/Rt4ZbGs2l9Tn2POGukJ7/SI+Bv3Nem/HeGHo1sWzRyeIpnkSnkNvsl1A2qsK9AXpMjWvJeI/QtcayQyeLp+jpCOxpYaeG+7/OsLuJfCIun3NrDN2pHs9rwz0NdkphX4DzC8//l8jvhfDZSbHsuqfSRXzdbLpry81+y3Mh/TvWy822/YjPczx78dxe2vxGIr8jxPMRp8a/O2PJ4vXFoxzxYnlhguAB8cIEVcQLM3Nqf/jxwswY/vBTrtMZo/jxwswJ/vAlZ4/aJT1hdXh4hm68MDOj6bv3+AJoLwLPNCeYCXwJ7Nl/h1vBTNh9GQrpu8+4EcyMZtPd3HkxlCyOjh5jN5gx6aX4ji/Jcd6lGwRBEAQPAX8Do0LGKlDzOycAAAAASUVORK5CYII=>