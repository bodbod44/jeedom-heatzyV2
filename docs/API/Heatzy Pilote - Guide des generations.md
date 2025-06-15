# ![][image1]

# Heatzy Open API

## Distinguer les différentes générations de Heatzy Pilote

Pour éviter la confusion lors de création d'outils web et domotique, ce document a pour but de détailler les différences et les points communs entre chaque module Heatzy Pilote et Acova Elec'Pro.  
Tous ces modules partagent une base commune et sont compatibles avec l'app Heatzy (V8.0) ainsi que les Open API Heatzy.  
Néanmoins, certaines générations de modules disposent de fonctions qui leur sont propres.  
Ci-dessous, une liste exhaustive de toutes les générations de Heatzy Pilote et leurs fonctions associées.

### Pilote 1ʳᵉ génération

Il s'agit de la première itération de modules Heatzy, produits en 2016, jusqu'en 2018\. Ces modules disposent de moins de fonctions que les générations suivantes.

**Data points**

| Data point | Description |
| :---- | :---- |
| mode | Mode de chauffe (confort, éco, hors-gel, arrêt) |

\**pour plus de détails sur les data points, voir la [documentation API](https://documenter.getpostman.com/view/10809108/SzYXYfP6)*

**Particularités**  
La programmation présente dans l'app est entièrement gérée par le serveur. Il n'y a donc pas de data point dédié à la programmation.

### Pilote de 2ᵈᵉ et 3ᵉ génération

Ces deux générations sont communément appelées "Pilote v2" par la communauté Heatzy.  
Ces modules apportent beaucoup de nouvelles fonctions par rapport à la première génération :  
 \- Stockage de la programmation et de la date dans la mémoire du produit  
 \- Ajout des dérogations mode vacances et mode boost  
 \- Ajout de la fonctionnalité de verrouillage produit

**Data points**

| Data point | Description |
| :---- | :---- |
| mode | Mode de chauffe (confort, éco, hors-gel, arrêt) |
| pX\_dataY | Données de programmation |
| derog\_mode | Activation des dérogations (mode vacance, mode boost) |
| derog\_time | Temps restant à une dérogation |
| lock\_switch | Verrouillage du produit |
| time\_week | Jour actif |
| time\_hour | Heure de la journée |
| timer\_switch | Activation de la programmation |

\*pour plus de détails sur les data points, voir la [documentation API](https://documenter.getpostman.com/view/10809108/SzYXYfP6)\*

**Particularités**  
La programmation est enregistrée dans la puce du produit. Le produit enregistre également la date et l'heure. Cette mise à jour permet d'obtenir un suivi de la programmation plus stable.  
Les pilotes de 3eme génération sont identiques a la seconde. Ils disposent cependant d'une architecture SoC.

### Pilote de 4ᵉ génération

Il s'agit de la dernière génération de pilote (en 2024). Ce module a été produit en 2022 et apporte surtout des avancées techniques. Ses API sont très similaires aux précédentes générations.  
Il est facile à différencier grâce à son témoin de connexion de couleur verte (et non bleue).

**Data points**

| Data point | Description |
| :---- | :---- |
| mode | Mode de chauffe (confort, éco, hors-gel, arret) |
| pX\_dataY | Données de programmation |
| derog\_mode | Activation des dérogations (mode vacance, mode boost) |
| derog\_time | Temps restant à une dérogation |
| lock\_switch | Verrouillage du produit |
| time\_week | Jour actif |
| time\_hour | Heure de la journée |
| timer\_switch | Activation de la programmation |

\*pour plus de détails sur les data points, voir la [documentation API](https://documenter.getpostman.com/view/10809108/SzYXYfP6)\*

**Particularités**  
La puce Wifi du module a été mise à jour pour faciliter l'appairage et permettre une plus grande compatibilité avec les récentes normes Wifi. L'appairage se fait maintenant via Bluetooth.  
Les modules de 4eme génération permettent en plus de passer les radiateurs en mode confort-1 et confort-2.

### Liste complète de tous les modules "Pilote"

Pour connaitre la génération d'un module Heatzy Pilote ou Acova Elec' Pro, il suffit de le connecter à internet puis de passer par les [open API](https://documenter.getpostman.com/view/10809108/SzYXYfP6) pour obtenir les informations relatives au produit. "Product\_name" et "Product\_key" permettent d'identifier les différentes générations de module.

Les API des générations 2 et 3 sont 100% identiques  
Les API des générations 4 possèdent exactement les mêmes data points que les générations 2 et 3\. À la seule différence que les modes confort-1 et confort-2 sont supportés.

| Module | Date | Product Name | Product Key | Generation | Remarque |
| :---- | :---- | :---- | :---- | :---- | :---- |
| Pilote | 2016 | Heatzy | 9420ae048da545c88fc6274d204dd25f | 1 | Pas de mode boost, vacances, verrouillage. La programmation est enregistrée sur les serveurs Heatzy. Pas entièrement compatible avec la fonction groupe (mise à jour 7.0) |
| Pilote | 2018 | Pilote2 | 51d16c22a5f74280bc3cfe9ebcdc6402 | 2 | Mode boost, vacances et verrouillage. La programmation est enregistrée dans le produit. |
| Elec’ Pro | 2018 | Elec\_Pro | 4fc968a21e7243b390e9ede6f1c6465d | 2 | Idem. |
| Pilote | 2019 | Pilote\_Soc | b9a67b6ce24b437d9794103fd317e627 | 3 | Idem. |
| Elec’ Pro | 2019 | Elec\_Pro\_Soc | b8c6657b66c34148b4dee64d615cefc7 | 3 | Idem. |
| Pilote | 2022 | Pilote\_Soc\_C3 | 46409c7f29d4411c85a3a46e5ee3703e | 4 | Mise à jour de la puce Wifi. Appairage via Bluetooth. Le témoin de connexion est vert pour mieux faire la différence avant l’appairage. Ajout des modes confort-1 et confort-2. Plus grande compatibilité Wifi. |
| Elec’ Pro | 2023 | Elec\_Pro\_Ble | 9dacde7ef459421eaf8dc4bea9385634 | 4 | Idem. |

---

Copyright Heatzy 2024

[image1]: <data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAIwAAAAtCAYAAAB8gIN1AAAETElEQVR4Xu2aTYscVRSGO4qCoIimq4NhwE1SNUNP0l0ZEQT/gASCIEJWgkt/gaB/wLXgwpWudefCIC5cSLISdKsbQZQsA4KBJH7dQ6ZC+fS5dc89DiKp88C7qfu8b3fuFFF0FosgCIIgCIKHhGbb/Zny1xCez5l0Hz/E3YDxhcSl/JO4G4W4lDxxNwpxKXnibhTiUvLE3SjEpeSJu1GIS8kTd6OgXQqf8fzfwL2T3B7gbm5/dHaXZwL7DH1hte3u0DPmBrcGpj4vh6djQvnixSy3+19ypwQ3psKukVPcyWXZd2f5jGMCHS2eTi7pe10p7fFcw9Mxw/GacCsHe5Yst+cvcycHu55wU6CjxdOZSmmP5xqejhmOV+YW94jSMWfZt1e5R9jxhrsCHS2ezlRKezzX8HTMcBz5VJxVv/+aclb8MnSRr56+cOGZpm9fUc5M+4lH6I+z2rYfNtv2Yz7XwmGBTrPp3h6yWK8fpz9G/mxT2dnOfI/SuYanY4bjpQ+hl3PT89/p5dwBuqUOveN8QW8g/aDfVPzsZ1gcD9yd2rc4xNMxw/HSBzR995PFp5PzBtL59/Sneun5bXrpb5RX6ZG9l/aeYG/iM4pOLdwsbVu9MZ6OGY6nfECHsMNzgc6ZiweHdIQm8zdR7X7O02Av17U4NXDPslvjDng6ZjzjpQ7PrU4mv7Er0ON5CUvf4ljhlnWz1hc8HTOe8VKH54Oz6ts3+DwX+UcHd8fQ53kJS9/iWOBOzd5/1THjGS91eF4TbuXw9gYsfYtTghu1W56ep2PGM17q8NySqQ2eCaV+CUvf4kzBvmfH0/V0zHjGS51V371FJxd2BTqnN+2LJee5/uB5OlOwz3PB4uRg90HW6yfpTsE+zzU8HTOecUuHDvIz/TH0eS7QyXka7OW6FkcjuXfZlSz77gW6Jbhh+R61fhWecUuHjuT0pfMH9Ag7uX2B3pQ7QH+qZ3EIOzVdDe6UtuiW/Go849YOveP8QU9QvCG/0h1QXM/3yXboyP/GoDOmUf5j4nG+pVuDsif53OipfzY3nnFrp9m079OtDTcJfW+4K9Bhav1SuDdArzbcE0rnWSzjpKaTzu/Rt4ZbGs2l9Tn2POGukJ7/SI+Bv3Nem/HeGHo1sWzRyeIpnkSnkNvsl1A2qsK9AXpMjWvJeI/QtcayQyeLp+jpCOxpYaeG+7/OsLuJfCIun3NrDN2pHs9rwz0NdkphX4DzC8//l8jvhfDZSbHsuqfSRXzdbLpry81+y3Mh/TvWy822/YjPczx78dxe2vxGIr8jxPMRp8a/O2PJ4vXFoxzxYnlhguAB8cIEVcQLM3Nqf/jxwswY/vBTrtMZo/jxwswJ/vAlZ4/aJT1hdXh4hm68MDOj6bv3+AJoLwLPNCeYCXwJ7Nl/h1vBTNh9GQrpu8+4EcyMZtPd3HkxlCyOjh5jN5gx6aX4ji/Jcd6lGwRBEAQPAX8Do0LGKlDzOycAAAAASUVORK5CYII=>