# 23/04/2025

- Suppression du ":" devant le taux d'humidité (pilote_pro)
- Affichage de la valeur dans la page des commandes

# 22/04/2025

- Correction pour Pilote_pro (template DashBoard + bug entre lock_switch et winodow_switch)
- Ajout d'une tempo (1s) ente l'action et l'update depuis l'API (pour être sure que l'API renvoit la nouvelle valeur)
  
# 21/04/2025

- Ajout d'une commande fenêtre ouverte (pilote_pro)
- Correction d'un bug affichage température

# 01/04/2025

- Correction de la température pour pilote_pro (suppression du /10)
- Correction de la resynchronisation automatique lors du code 9004
  
# 22/03/2025

- Correction de la temp pour pilote_pro (/10)
- Changement de l'icone pour l'humidité (pilote_pro)
- Désactivation possible du bouton Prog/Vérouillage (Pilote_Soc + Pilote_Pro)
- Désactivation possible de l'affichage de l'humidité (Pilote_Pro)
  
# 21/03/2025

- Ajout de la doc sur les API
- Ajout du template pilote pro (dashboard+mobile)
- Ajout de l'image du pilote_pro
- Prise en charge du taux d'humidité pour le pilote_pro
- Prise en charge de la lecture de cur_temp, com_temp, eco_temp, cft_temp

# 26/01/2025

- Mise à jour du changelog
  
# 25/01/2025

- Ajout logo pour le type INEA
- Ajout logo Flam-week2
- Ajout logo Elec_Pro

# 24/01/2025

- Modification icone Heatzy ([Merci dragoon25](https://community.jeedom.com/u/dragoon25/summary))

# 23/01/2025

- Ajout d'une personalisation du logo équipement
  - Si le logo n'est pas trouvé, il affiche l'éclaur ou la flamme
  - Logo : Pilote2, Pilote_Soc, flam
- Correction clic sur le filtre de gauche dans la configuration
- Renommage de la commande Etat Lock -> Etat Vérouillage
- Correction displayExeption (ajout d'une condition sur la version jeedom) 

# 22/01/2025

- Ajout de la fonctionnalité de Verrouillage pour Pilote2 (dashboard+mobile+API) ([Signalement](https://community.jeedom.com/t/resurrection-du-plugin-heatzy-mise-a-jour/136824/2?u=bodbod))

# 19/01/2025

- Renommage de la commande lock en Verouillage (dashboard + mobile + commandes actions)
- Correction affichage du logo qui était en 403 (affichage de l'éclair ou de la flamme)
- Ajout du mode Verrouillage dans la documentation
  
# 18/01/2025

- Ajout de la fonctionnalité de Verrouillage pour Pilote_Soc (dashboard+mobile+API) ([Signalement](https://community.jeedom.com/t/plugin-tiers-heatzy/54291/5?u=bodbod))
  - Le mode verouillage évite qu’une tiers personne modifie le fonctionnement du radiateur via le module physique (blque toutes actions faîtes depuis le bouton de contrôle du module)
  - Le mode verrouillage permet également d'éteindre les leds sur le module (pratique si ceux-ci sont placés dans une chambre)
- Modification de l'icone du plugin pour améliorer la visibilité en mode sombre ([Signalement](https://community.jeedom.com/t/plugin-tiers-heatzy/54291/3?u=bodbod))

# 10/01/2025

- Correction "ERROR : heatzy::Login : impossible de se connecter" ([Signalement](https://community.jeedom.com/t/error-heatzy-login-impossible-de-se-connecter/129877))
- Correction du problème token sur GetConsigne ([Signalement](https://community.jeedom.com/t/probleme-token-depuis-le-passage-a-2024/118282))
- Call to undefined displayExeption ([Signalement](https://community.jeedom.com/t/lors-de-la-synchronisation-erreur-500-internal-server-error/134687/3?u=bodbod) / [Détail1](https://community.jeedom.com/t/correction-ajax-displayexeption-e-a-remplacer-par-displayexception-e-et-doc-absente-en-beta/105525) / [Détail2](https://community.jeedom.com/t/correction-ajax-displayexeption-e-a-remplacer-par-displayexception-e/105523))
- Affichage équipement Pilote_Soc_C3.html (Dashboard+Mobile) ([Signalement](https://community.jeedom.com/t/affichage-equipement/95357/4?u=bodbod))



---
---
Reprise du code de l3flo
---
---



# 20/10/2019

- Affichage compatible pour jeedom v4
- Chaque modules disposent d'un template
- La commande timer_switch est utilisé pour activer/désactiver la programmation des modules seconde génération et INEA
  
# 06/01/2019

- Ajout du module INEA. Le module est géré de la même maniere que le module FLAM

# 18/11/2018

- Synchronisation automatique si le code retour Gizwits est 9004
- Ajout de la clé du produit

# 22/10/2018

- Prise en charge des modules pilotes version 2018

# 06/05/2018

- Intégration et prise en charge des modules Flam et Plugzy
- Mise en alerte des modules lorsqu'ils ne sont plus connectés au cloud Heatzy

# 10/01/2018

- Correction affichage des tuilles

# 12/12/2017

- Activation / désactivation de la programmation des modules

# 04/12/2017

- Correction de bug d'affichage

# 26/11/2017

- Possibilité d'historiser la commande info Etat
- Possibilité de mettre à jour le nom du module heatzy à la sauvegarde de l'équipement si celui a changé
- Ajout d'un toast contenant le nombre de module synchronisé, je trouvais qu'il manquait une information lorsque la synchronisation était terminée
- Ajout d'un textarea commentaire dans le détail de l'équipement

# 22/11/2017

- Premiere version du plugin Heatzy, contrôle le module pilote
