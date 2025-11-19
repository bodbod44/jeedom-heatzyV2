Heatzy
====

Description
=== 
Ce plugin permet de gérer vos modules pilote et flam de la marque Heatzy.

Les modules gérés sont :

* **Heatzy**
* **Flam/Plugzy**
* **Pilote seconde génération**
* **Pilote SoC**
* **Pilote SoC3**
* **Pilote_Pro**
* **Elec Pro (Acova version)**
* **Radiateur Glow**
* **Radiateur Shine**
* **Radiateur Onyx**
* **Radiateur Bloom**
* **Radiateur Flat**
* **Radiateur INEA**

Pré-requis
=== 
L'utilisation de ce plugin requiert de créer un compte avec l'application Heatzy disponible sur Android et iOS.

Il est nécessaire de suivre le guide d'installation et d'utilisation fourni pour mettre en service les modules Heatzy.

Configuration
==

Configuration du plugin
-------------
Après téléchargement du plugin, il vous suffit juste d'activer celui-ci et de saisir l'adresse mail et le mot de passe pour accéder au cloud Heatzy.

Puis cliquez sur synchroniser pour récupérer la liste des modules Heatzy pilotes associés à votre compte.

![heatzy1](../images/configuration4.png)

* **Email** : votre adresse email utilisé lors de votre enregistrement dans le cloud
* **Mot de passe** : votre mot de passe d'accès renseigné lors de votre enregistrement dans le cloud
* **Token** : votre token d'accès au cloud, après la synchronisation de vos modules
* **Expire** : la date d'expiration du token d'accès au cloud, après la synchronisation de vos modules
* **Timeout sur les connexions euapi.gizwits.com** (60sec par défaut) : Timeout sur les connexions euapi.gizwits.com. Une valeur trop basse peut provoquer des remontées d'erreurs en cas de surcharge des serveurs heatzy/gizwits
* **Fréquence de rafraichissement des commandes** (1min par défaut) : Fréquence des appels à l'API heatzy pour récupérer les informations type mode, température, activations ...
* **Fréquence de rafraichissement du statut du module** (5min par défaut) : Fréquence des appels à l'API heatzy pour récupérer le statut OnLine/Offline et pour l'ajout des nouveau xmodules rattachés au compte
* **Synchroniser** : permet de synchroniser vos modules Heatzy avec le cloud


Configuration des objets connectés Heatzy
-------------
La configuration des modules Heatzy est accessible à partir du menu 'Plugins' > 'Objets connectés'.

![heatzy2](../images/configuration_equipement_Gestion2.png)
![heatzy3](../images/configuration_equipement_ListeEqLogic.png)

Une fois que vous cliquez sur un équipement vous retrouvez la configuration de l'équipement :

![heatzy4](../images/detail_equipement3.png)

Vous retrouvez dans la section **Général**

* **Nom de l'équipement** : nom de votre module Heatzy, permet de renommer le module dans le cloud.
* **Objet parent** : objet parent auquel appartient votre équipement
* **Catégorie** : catégorie de l'équipement, par défaut chauffage
* **Activer** : permet de rendre votre équipement actif
* **Visible** : le rend visible sur le dashboard
* **MAC** : l'adresse MAC du module Heatzy
* **DID** : l'identifiant du module Heatzy

Vous retrouvez dans la section **Informations**

* **Création** : date et heure de création de l'équipement
* **Dernière communication** : date et heure de la dernière communication du module Heatzy dans le cloud
* **Dernière mise à jour** : date et heure de mise à jour de l'équipement
* **Type** : pilote ou flam
* **Produit** : le type de produit de la marque heatzy

Configuration des commandes des objets connectés Heatzy
-------------

### Wigdet dashboard desktop

![heatzy5](../images/WidgetDesktop_PiloteSoc.png)![heatzy6](../images/WidgetDesktop_PilotePro.png)![heatzy7](../images/WidgetDesktop_Glow.png)![heatzy8](../images/WidgetDesktop_Shine.png)![heatzy9](../images/WidgetDesktop_Flam.png)

### Wigdet dashboard mobile

![heatzy10](../images/WidgetMobile.png)


Chaque équipement dispose des commandes actions suivantes :

* **Off** : permet de passer en mode off
* **Confort** : permet de passer en mode confort
* **Eco** : permet de passer en mode eco
* **HorsGel** : permet de passer en mode hors-gel
* **refresh** : permet forcer le rafraichissement de l'etat de l'équipement
* **Activer Programmation** : permet d'activer la programmation, pré-définie par l'application Heatzy
* **Désactiver Programmation** : permet d'activer la programmation, pré-définie par l'application Heatzy
* **Activer le verrouillage** : permet d'activer le verrouillage et d'éteindre la led, pré-définie par l'application Heatzy
* **Désactiver le verrouillage** : permet d'activer le verrouillage et d'allumer la led, pré-définie par l'application Heatzy
* **Activer la détéction de fenetre ouverte** : permet d'activer la détéction de fenetre ouverte (le radiateur passe en mode HorsGel si la température chute de 2° en moins de 5min)
* **Désactiver  la détéction de fenetre ouverte** : permet d'activer la détéction de fenetre ouverte
* **Plugzy OFF** : Permet d'éteindre le plugzy *(seulement pour l'équipement de type flam)*
* **Plugzy ON** : Permet d'allumer le plugzy *(seulement pour l'équipement de type flam)*

D'une commande info **Etat Consigne** de type _numerique_ :

* **0** : Mode Confort
* **4** : Mode Confort-1 (uniquement pour les modules 6 ordres)
* **5** : Mode Confort-2 (uniquement pour les modules 6 ordres)
* **1** : Mode Eco
* **2** : Mode Hors-gel
* **3** : Mode Off

Et de commandes info de type _numerique_ :

* **Temp. confort** : la température de consigne du mode confort
* **Temp. eco**     : la température de consigne du mode eco
* **Temperature**   : la température relevée par le module
* **Taux Humidité** : le taux d'humidité relevé par le module

Et d'une commande info **Mode** de type _string_ :

* **Confort**
* **Confort-1** (uniquement pour les modules 6 ordres)
* **Confort-2** (uniquement pour les modules 6 ordres)
* **Eco**
* **HorsGel**
* **Off**

Et d'une commande info **Etat programmation** de type _binaire_ :

* **1** : La programmation est activée
* **0** : La programmation est désactivée

Et d'une commande info **Etat Verrouillage** de type _binaire_ :

* **1** : Le verrouillage est activé
* **0** : Le verrouillage est désactivé

Et d'une commande info **Etat Activation fenêtre ouverte** de type _binaire_ :

* **1** : La détéction est activée
* **0** : La détéction est désactivée

Et d'une commande info **OnLine** de type _binaire_ :

* **1** : Le module est Online (connecté à internet et fonctionnel)
* **0** : Le module est Offline (déconnecté ou détaché du compte heatzy. N'est plus pilotable)

Et d'une commande info **Mode dérogation** de type _numeric_ :

* **0** : pas de dérogation
* **1** : mode vacances
* **2** : mode boost
* **3** : détection de presence (selon type de module)

Et d'une commande info **Délai dérogation Vacances** de type _numerique_ :

* **xx** représente le nombre de jours demandé pour le mode vacances

Et d'une commande info **Délai dérogation Boost** de type _numerique_ :

* **xx** représente le nombre de minutes demandé pour le mode boost

Et d'une commande info **Détéction Présence** de type _binaire_ :

**EXPERIMENTAL**
Fonctionne uniquement en mode "détéction de présence" (pour les modules qui possèdent cette fonctionnalités)
* **1** : Le plugin a détécté qu'une fenetre était ouverte (détéction chute de température)
* **0** : Fenetre fermée (après hausse de température ou après 60 min)
**EXPERIMENTAL**

Et d'une commande info **Fenetre Ouverte** de type _binaire_ :

**EXPERIMENTAL**
* **1** : Le plugin a détécté qu'une fenetre était ouverte (détéction chute de température)
* **0** : Fenetre fermée (après hausse de température ou après 60 min)
**EXPERIMENTAL**
             
Et d'une commande info **Tendance Température** de type _numeric_ :

**EXPERIMENTAL**
* **-x** : La température à tendance à diminuer (à hauteur du coeficient par minute)
* **0** : La température est stable
* **+x** : La température à tendance à augmenter (à hauteur du coeficient par minute)
**EXPERIMENTAL**


>Vous pouvez tester les valeurs des commandes info dans vos scénarios.

**Parametres complémentaires par équipement**

* **Template** : Choix du template d'affichage
  * template **bodbod** : Nouveau template unique et commun à tous les modules *(les commandes non prises en charge par le module ne sont pas affichées)*
  * template **l3flo** : Template d'origine créé par l3flo (<span style="color: #FF0000">/!\ Ces templates n'est plus maintenu et disparaitra des une future version</span>)
  * template **jeedom** : Laisse jeedom créer les commandes et informations par défaut
* Capteur de température externe (facultatif)
  * Cela permet d'avoir la température pour les modules qui n'ont pas de capteur
  * Pour les modules qui possède un capteur interne, le capteur externe sera utilisé par le plugin
* Capteur d'humidité externe (facultatif)
  * Cela permet d'avoir le taux d'humidité pour les modules qui n'ont pas de capteur
  * Pour les modules qui possède un capteur interne, le capteur externe sera utilisé par le plugin
* Calcul de tendance pour la detection d'une fenetre ouverte avec :
  * **Nombre de degré (°c)**
  * **Durée (min)**
  * Pour détécter une chute de 2° en moins de 5min, mettre 2 (°) et 5 (min)
  * **Commande d'alerte** : Alerte en cas de detection de fenetre ouverte

**Tableau des commandes possible par modules :**

*(les futurs modules qui ne sont pas dans ce tableau seront naturellement pris en charg si l'utilisation de l'APi est la même)*
 | Commande | Type | Pilote_Soc | Pilote_Soc3 | Pilote_Pro | Elec_Pro | Glow | Shine | Flam_Week2 | INEA | Cool | Relais | 
 | ----- | ----- | ----- | ----- | ----- | ----- | ----- | ----- | ----- | ----- | ----- | ----- | 
 | Etat Consigne<br>4 ordres : 0/1/2/3<br>6 ordres : 0/1/2/3/4/5 | info | Oui | Oui | Oui | Oui | Oui | Oui | Oui | Oui | N/A | N/A | 
 | Mode<br>4 ordres : Confort/Eco/Hors Gel/Off<br>6 ordres : Confort/Confort-1/Confort-2/Eco/Hors Gel/Off | info | Oui | Oui | Oui | Oui | Oui | Oui | Oui | Oui | N/A | N/A | 
 | Confort | action | Oui | Oui | Oui | Oui | Oui | Oui | Oui | Oui | N/A | N/A | 
 | Confort-1 | action | N/A | N/A | Oui | N/A | N/A | N/A | N/A | N/A | N/A | N/A | 
 | Confort-2 | action | N/A | N/A | Oui | N/A | N/A | N/A | N/A | N/A | N/A | N/A | 
 | Eco | action | Oui | Oui | Oui | Oui | Oui | Oui | Oui | Oui | N/A | N/A | 
 | Hors Gel | action | Oui | Oui | Oui | Oui | Oui | Oui | Oui | Oui | N/A | N/A | 
 | Off | action | Oui | Oui | Oui | Oui | Oui | Oui | Oui | Oui | N/A | N/A | 
 | Température courante | info | N/A | N/A | Oui | N/A | Oui | Oui | Oui | N/A | N/A | N/A | 
 | Température Confort | info | N/A | N/A | Oui | N/A | Oui | Oui | Oui | N/A | N/A | N/A | 
 | Consigne Température Confort | action | N/A | N/A | Oui | ? | Oui | Oui | ? | ? | N/A | N/A | 
 | Température Eco | info | N/A | N/A | Oui | N/A | Oui | Oui | Oui | N/A | N/A | N/A | 
 | Consigne Température Eco | action | N/A | N/A | Oui | ? | Oui | Oui | ? | ? | N/A | N/A | 
 | Humidité courante | info | N/A | N/A | Oui | N/A | N/A | N/A | N/A | N/A | N/A | N/A | 
 | Etat Programmation | info | Oui | Oui | Oui | Oui | Oui | Oui | Oui | Oui | N/A | N/A | 
 | Programmation On | action | Oui | Oui | Oui | Oui | Oui | Oui | Oui | Oui | N/A | N/A | 
 | Programmation Off | action | Oui | Oui | Oui | Oui | Oui | Oui | Oui | Oui | N/A | N/A | 
 | Etat Vérouillage | info | Oui | Oui | Oui | Oui | N/A | N/A | Oui | Oui | N/A | N/A | 
 | Vérouillage On | action | Oui | Oui | Oui | Oui | N/A | N/A | Oui | Oui | N/A | N/A | 
 | Vérouillage Off | action | Oui | Oui | Oui | Oui | N/A | N/A | Oui | Oui | N/A | N/A | 
 | Etat Fenetre Ouverte | info | N/A | N/A | Oui | N/A | N/A | N/A | ? | ? | N/A | N/A | 
 | Fenetre Ouverte On | action | N/A | N/A | Oui | N/A | N/A | N/A | ? | ? | N/A | N/A | 
 | Fenetre Ouverte Off | action | N/A | N/A | Oui | N/A | N/A | N/A | ? | ? | N/A | N/A | 
 | Etat On_Off (plugzy ou OnOff) | info | N/A | N/A | N/A | N/A | Oui | Oui | Oui | N/A | N/A | N/A | 
 | On | action | N/A | N/A | N/A | N/A | Oui | Oui | Oui | N/A | N/A | N/A | 
 | Off | action | N/A | N/A | N/A | N/A | Oui | Oui | Oui | N/A | N/A | N/A | 
 | Mode dérogation | info | Oui | Oui | Oui | ? | Oui | Oui | ? | ? | N/A | N/A | 
 | Derogation OFF | action | Oui | Oui | Oui | ? | Oui | Oui | ? | ? | N/A | N/A | 
 | Derogation Vacances | action | Oui | Oui | Oui | ? | Oui | Oui | ? | ? | N/A | N/A | 
 | Délai Derogation Vacances (en jours) | info | Oui | Oui | Oui | ? | Oui | Oui | ? | ? | N/A | N/A | 
 | Derogation Boost | action | Oui | Oui | Oui | ? | Oui | Oui | ? | ? | N/A | N/A | 
 | Délai Derogation Boost (en min) | info | Oui | Oui | Oui | ? | Oui | Oui | ? | ? | N/A | N/A | 
 | Derogation Présence | action | N/A | N/A | Oui | ? | N/A | N/A | ? | ? | N/A | N/A | 
 | Détéction Présence | info | N/A | N/A | Oui | ? | N/A | N/A | ? | ? | N/A | N/A | 


**Utilisation des tâches planifiées Heatzy (Réservé à un publique averti !!!) **
Heatzy possède son propre système de tâches planifiées.
Les tâches sont envoyés aux serveurs Gizwitz qui se charge d'envoyer les consignes aux modules au moment voulu.
Cette fonctionnalité n'est utilisable que par l' API (l'application mobile ne se pas les gérer).

L'utilité de cette fonctionnalité reste marginale dans la mesure ou on peut le faire dans jeedom.
Neanmoins, j'ai décidé de l'implémenter dans le plugin si certains utilisateurs ont des besoins spécifiques.

Le plugin jeedom, permet de lire, créer, modifier ou supprimer des tâches.
En revanche, le coeur de la tâche est géré sous forme de JSON (donc réservé à un publique de connaisseurs.
Un lien a été ajouté vers la documentation officielle.
![heatzy11](../images/Scheduler.png)


FAQ
=== 

**Quelle est la fréquence de rafraîchissement ?**

Le système récupère l'état des modules **toutes les minutes**. L'état de la programmation est rafraîchie toutes les 30 minutes pour les modules Heatzy et Flam.

>Pour les modules Heatzy et Flam, il est possible de désactiver le rafraîchissement de l'état de la programmation en décochant la fonctionnalités **con30** depuis la page de configuration du plugin. Pour les autres modules la programmation est lu dans la commande **timer_switch**.

**Lorsque je désactive la programmation, l'état n'est pas mis à jour ?**

Le plugin met à jour les 336 tâches associées au module Heatzy ou Flam pour les désactiver/activer, soit 336 requêtes HTTP envoyé au cloud Heatzy. Il est donc nécessaire d'attendre quelques secondes que le plugin finisse d'activer/désactiver chacune des tâches. L’état de la programmation est mis à jour si tous ce passe bien.

**Quelle est la durée de vie du token utilisateur d'accès au cloud ?**

Le token d'accès au cloud change à chaque synchronisation avec une validité d'environ 6 mois.<br>
Un nouveau token est demandé au cloud automatiquement 24h avant que celui-ci expire.

**Peut-on commander les modules en local (sans cloud) ?**

Malheureusement, cette fonctionnalité n'existe pas sur les modules Heatzy.
Une requete a été formulée en ce sens aux équipes Heatzy

**Mon équipement n'est pas rafraîchi ?**

Vérifiez si l'équipement est bien activé.
                                               
**Je viens de mettre à jour le plugin, que dois-je faire ?**

Il est conseillé de synchroniser les modules via la page de configuration du plugin.                            
                                               
**J'ai un type de module qui n'est pas répertorié sur cette page, que faire ?**

Le plugin va créer de lui même toutes les commandes qui sont connues dans les autres modules.<br>
S'il manque des commandes, n'hésitez pas à faire un message sur le forum jeedom (avec le tag heatzy)

**Mon module n'est plus dans l'application Heatzy, que faire ?**

Dans un premier temps, fermer votre application mobile et revérifiez.
S'il a été supprimé de l'application, le plugin ne pourra rien faire.
Reportez vous aux manuels des produits : https://drive.google.com/drive/folders/1pbrZ7RRNZf8yzdbH-cd7Fk9ih2j7WZFd

**Le plugin renvoit des erreurs 8xxx ou 9xxx, a quoi correspondent-ils ?**

Les erreurs renvoyées par l'API gizwits sont repartoriés ici : https://docs.gizwits.com/en-us/AppDev/APICloudSDK.html#Error-codes
Ces sont des libellés tchniques et pas forcement compréhensibles par tout le monde.
N'hésitez pas à faire un message sur le forum jeedom (avec le tag heatzy).
En Octobre 2025 à l'approche de l'hiver, beaucoup d'utliateur ont réactivé leurs modules Heatzy provoquant des saturations des serveurs GizWits.
Cela s'est traduit par des temps de latence sur les commandes et une grosses remontés d'erreurs dan le plugin jeedom Heatzy (beaucoup d'appels KO). Une mécanique de réémission d'appel a été mise en place pour limiter les conséquences.