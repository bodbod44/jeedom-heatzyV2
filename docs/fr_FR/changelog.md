# 15/06/2025 (dev)
- Correction du lien WindowSwitchOn/Off avec WindowSwitch
- Correction des erreurs 9025 sur les glow/shine pour larecherche des ordres
- Récupération des infos pour la modal debug (utile pour debuger les problèmes sur le forum)

# 12/06/2025 (dev)
- Ajout d'un message pour basculer sur le market

# 31/05/2025 (dev)
- Ajout d'info dans les logs execute

# 31/05/2025 (dev)
- Ajout d'un bouton vers le forum community
- Mise à jour de la documentation

# 30/05/2025 (dev)
- Affichage d'un message lorsque le token est modifié
- Ajout du lien officiel vers les manuels utilisateurs Heatzy
- Ajout de la doc au format md
- Renommage de la commande EtatWindow -> WindowSwitch

# 29/05/2025 (dev)
- Modification de la fonction execute (lisibilité/maintenance)
- Prise en charge update+execute+template de LOCK_C pour Shine/Glow

# 28/05/2025 (dev)
- Correction de l'affichage refresh sur template

# 27/05/2025 (dev)
- Correction info.json
- Correction template mobile

# 25/05/2025 (dev)
- Modification du template commun pour la prise en charge des modules 6 ordres
- Mise à jour de la documentation

# 24/05/2025 (dev)
- Prise en charge des modules GLOW et SHINE
- Prise en charge des 5e et 6e ordre (pilote_pro)
- Refonte de la création des commande (à partir du retour API)

# 14/06/2025 (stable)
- Arrivée sur le market
- Correction du cron Dayly-Daily
- Mise à jour des templates commun
  - Création du template commun mobile
  - Ajout plugzy dans template commun + correction
  - Modification du template commun pour les modules qui n'ont pas toutes les commandes (objet non pris en charge par certains modules)
  - Modification de tous les templates pour uniformiser les #variables# (LogicalId_xxx)
  - Correction du curseur sur une commande avec un historique
  - Modification des popup pour afficher les CollecteDate et ValueDate de chaque commande
- Possibilité de choisir le template d'affichage (bodbod / l3flo / jeedom)
- Affectation par défaut du template commun
- Modification dynamique des commandes dans ToHtml pour tous les templates
- Ajout d'une modal debug (non utilisée à date)
- Renommage de la commande etat en EtatConsigne lors de l'update du plugin

# 04/05/2025 (Stable)
- Remise en place du divisé par 10 à la lecture des températures (pilote_pro)
- Suppr de l'ancienne cmd window_switch lors de la mise à jour du plugin
- Ajout config isTemplateCommun (mode debug pour tester un template commun)
- Suppression du ":" devant le taux d'humidité (pilote_pro)
- Affichage de la valeur dans la page des commandes
- Correction pour Pilote_pro (template DashBoard + bug entre lock_switch et winodow_switch)
- Ajout d'une tempo (1s) ente l'action et l'update depuis l'API (pour être sure que l'API renvoit la nouvelle valeur)
- Ajout d'une commande fenêtre ouverte (pilote_pro)
- Correction d'un bug affichage température
- Correction de la température pour pilote_pro (suppression du /10)
- Correction de la resynchronisation automatique lors du code 9004
- Correction de la temp pour pilote_pro (/10)
- Changement de l'icone pour l'humidité (pilote_pro)
- Désactivation possible du bouton Prog/Vérouillage (Pilote_Soc + Pilote_Pro)
- Désactivation possible de l'affichage de l'humidité (Pilote_Pro)
- Ajout de la doc sur les API
- Ajout du template pilote pro (dashboard+mobile)
- Ajout de l'image du pilote_pro
- Prise en charge du taux d'humidité pour le pilote_pro
- Prise en charge de la lecture de cur_temp, com_temp, eco_temp, cft_temp
- Ajout logo pour le type INEA
- Ajout logo Flam-week2
- Ajout logo Elec_Pro
- Modification icone Heatzy ([Merci dragoon25](https://community.jeedom.com/u/dragoon25/summary))
- Ajout d'une personalisation du logo équipement
  - Si le logo n'est pas trouvé, il affiche l'éclaur ou la flamme
  - Logo : Pilote2, Pilote_Soc, flam
- Correction clic sur le filtre de gauche dans la configuration
- Renommage de la commande Etat Lock -> Etat Vérouillage
- Correction displayExeption (ajout d'une condition sur la version jeedom) 
- Ajout de la fonctionnalité de Verrouillage pour Pilote2 (dashboard+mobile+API) ([Signalement](https://community.jeedom.com/t/resurrection-du-plugin-heatzy-mise-a-jour/136824/2?u=bodbod))
- Renommage de la commande lock en Verouillage (dashboard + mobile + commandes actions)
- Correction affichage du logo qui était en 403 (affichage de l'éclair ou de la flamme)
- Ajout du mode Verrouillage dans la documentation
- Ajout de la fonctionnalité de Verrouillage pour Pilote_Soc (dashboard+mobile+API) ([Signalement](https://community.jeedom.com/t/plugin-tiers-heatzy/54291/5?u=bodbod))
  - Le mode verouillage évite qu’une tiers personne modifie le fonctionnement du radiateur via le module physique (blque toutes actions faîtes depuis le bouton de contrôle du module)
  - Le mode verrouillage permet également d'éteindre les leds sur le module (pratique si ceux-ci sont placés dans une chambre)
- Modification de l'icone du plugin pour améliorer la visibilité en mode sombre ([Signalement](https://community.jeedom.com/t/plugin-tiers-heatzy/54291/3?u=bodbod))
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
