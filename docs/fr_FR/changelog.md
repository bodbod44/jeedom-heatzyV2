# 29/08/2025 (stable)
- Ajout d'un bouton vers la documentation du plugin (version beta)
- Ajour de la gestion des dérogations (Mode Off/Vacances/Boost/Présence)
  - Le mode dérogation actif supprime l'affichage des modes de chauffage (Off, Confort, eco ...)
  - L'envoi de derog_vacances ou derog_boost à 0 provoque un passage en OFF
  - Les dérogations sont accessibles par scénario
- Prise en charge du changement de consigne éco/confort (widget + scnéario)
- Modification de la page de configuration des équipements avec suppression de certains boutons "afficher"
  - Concerne les commandes qui n'ont pas d'influence sur l'affichage (facilite la compréhension en lien avec ([ce sujet](https://community.jeedom.com/t/modules-heatzy-pro-nouvelles-fonctionnalites/141672/22)) )
  - Concerne uniquement le template commun (bodbod)
- Ajout des paramètres (niveau plugin) pour définir la fréquence de rafraichissement de :
  - Statut : Ajout des nouveaux modules rattachés et statut OnLine/Offline
  - Commande infos : Mode actifs, activation, températures ...
- Le bouton Synchroniser permet de forcer la recréation de certaines commandes si KO à la première création
- La mise à jour du plugin va retrier l'ordre des commandes (setOrder) pour être plus proche de celui du widget
- Renommage des commandes Mode (ex : Confort -> Mode Confort)
- Ajout d'un message pour les utilisateurs ayant le template l3flo sur des équipements (pour les inviter à changer car template plus maintenus)
- Passage du timeout des appels API en constante globale (et définie à 10s)
- Correction de warning PHP ([Signalement](https://community.jeedom.com/t/erreur-sur-cron-execution/141846/1))
- Correction d'un bug avec l'affichage sur les vues ([Signalement](https://community.jeedom.com/t/modules-heatzy-pro-nouvelles-fonctionnalites/141672/22))

# 28/07/2025 (stable)
- Ajout d’une détection d’une fenêtre ouverte (détection chute de température)
- Ajout d’un onglet Paramètres dans chaque équipement
- Prise en charge d’un capteur de température externe
- Prise en charge d’un capteur d’humidité externe
- Ajout d’une commande information sur la tendance de la température (non affichée)
- Ajout de la case historiser sur les commandes info/binaire
- Refonte de la regénération du token (si expiré ou invalide)
- Suppression du cron pour la regénération du token
- Optimisation de la fonction synchronize (suppression de l’appel datapoint)
- Ajout du detail_message lors des erreurs d’appels
- Ajout d’une commande info sur l’état OnLine/Offline
- Nouvelles cmd pour remonter le derog_mode et le derog_time (en lecture)
- Détéction et désactivation des équipements qui ne sont plus rattachés au compte heatzy (err 9017)
- Mise en place d’un garde fou pour limiter les appels gizwits en cas d’erreur 9xxx
- Préparation du mode dérogation

# 28/06/2025 (stable)
- Prise en charge des modules GLOW et SHINE
- Prise en charge des 5e et 6e ordre (pilote_pro)
- Ajout d'un bouton vers le forum community
- Ajout d'un bouton vers les manuels utilisateurs officiels Heatzy
- Ajout de la doc heatzy au format md
- Refonte de la création des commandes (dynamique à partir du retour API)
- Refonte de la fonction execute (lisibilité/maintenance) + Ajout info debug
- Récupération des infos pour la modal debug (utile pour débuguer les problèmes sur le forum)
- Ajout d'un message pour basculer sur le market (si installé par fichier/github)
- Affichage d'un message lorsque le token est modifié.
- Renommage de la commande EtatWindow -> WindowSwitch
- Correction du lien WindowSwitchOn/Off avec WindowSwitch
- Correction de l'affichage refresh sur template.
- Correction info.json
- Correction template mobile.
- Mise à jour de la documentation.

# 14/06/2025 (stable)
- Arrivée sur le market.
- Correction du cron Dayly-Daily.
- Mise à jour des templates communs.
  - Création du template commun mobile.
  - Ajout plugzy dans template commun + correction.
  - Modification du template commun pour les modules qui n'ont pas toutes les commandes (objet non pris en charge par certains modules).
  - Modification de tous les templates pour uniformiser les #variables# (LogicalId_xxx).
  - Correction du curseur sur une commande avec un historique.
  - Modification des popup pour afficher les CollecteDate et ValueDate de chaque commande.
- Possibilité de choisir le template d'affichage (bodbod / l3flo / jeedom).
- Affectation par défaut du template commun
- Modification dynamique des commandes dans ToHtml pour tous les templates.
- Ajout d'une modal debug (non utilisée à date).
- Renommage de la commande etat en EtatConsigne lors de l'update du plugin.

# 04/05/2025 (Stable)
- Remise en place du divisé par 10 à la lecture des températures (pilote_pro).
- Suppr de l'ancienne cmd window_switch lors de la mise à jour du plugin.
- Ajout config isTemplateCommun (mode debug pour tester un template commun).
- Suppression du ":" devant le taux d'humidité (pilote_pro).
- Affichage de la valeur dans la page des commandes.
- Correction pour Pilote_pro (template DashBoard + bug entre lock_switch et winodow_switch).
- Ajout d'une tempo (1s) entre l'action et l'update depuis l'API (pour être sûr que l'API renvoie la nouvelle valeur).
- Ajout d'une commande fenêtre ouverte (pilote_pro).
- Correction d'un bug affichage température.
- Correction de la température pour pilote_pro (suppression du /10)
- Correction de la resynchronisation automatique lors du code 9004.
- Correction de la temp pour pilote_pro (/10)
- Changement de l'icône pour l'humidité (pilote_pro).
- Désactivation possible du bouton Prog/Vérouillage (Pilote_Soc + Pilote_Pro).
- Désactivation possible de l'affichage de l'humidité (Pilote_Pro).
- Ajout de la doc sur les API.
- Ajout du template pilote pro (dashboard+mobile)
- Ajout de l'image du pilote_pro.
- Prise en charge du taux d'humidité pour le pilote_pro.
- Prise en charge de la lecture de cur_temp, com_temp, eco_temp, cft_temp
- Ajout logo pour le type INEA
- Ajout logo Flam-week2
- Ajout logo Elec_Pro
- Modification icône Heatzy ([Merci dragoon25](https://community.jeedom.com/u/dragoon25/summary))
- Ajout d'une personnalisation du logo équipement.
  - Si le logo n'est pas trouvé, il affiche l'éclair ou la flamme.
  - Logo : Pilote2, Pilote_Soc, flam
- Correction clic sur le filtre de gauche dans la configuration.
- Renommage de la commande Etat Lock -> Etat Vérouillage
- Correction displayExeption (ajout d'une condition sur la version jeedom) 
- Ajout de la fonctionnalité de verrouillage pour Pilote2 (dashboard+mobile+API) ([Signalement](https://community.jeedom.com/t/resurrection-du-plugin-heatzy-mise-a-jour/136824/2))
- Renommage de la commande lock en Verrouillage (dashboard + mobile + commandes actions)
- Correction affichage du logo qui était en 403 (affichage de l'éclair ou de la flamme)
- Ajout du mode Verrouillage dans la documentation.
- Ajout de la fonctionnalité de Verrouillage pour Pilote_Soc (dashboard+mobile+API) ([Signalement](https://community.jeedom.com/t/plugin-tiers-heatzy/54291/5))
  - Le mode verrouillage évite qu’une tiers personne modifie le fonctionnement du radiateur via le module physique (bloque toutes actions faites depuis le bouton de contrôle du module)
  - Le mode verrouillage permet également d'éteindre les leds sur le module (pratique si ceux-ci sont placés dans une chambre)
- Modification de l'icône du plugin pour améliorer la visibilité en mode sombre ([Signalement](https://community.jeedom.com/t/plugin-tiers-heatzy/54291/3))
- Correction "ERROR : heatzy::Login : impossible de se connecter" ([Signalement](https://community.jeedom.com/t/error-heatzy-login-impossible-de-se-connecter/129877))
- Correction du problème token sur GetConsigne ([Signalement](https://community.jeedom.com/t/probleme-token-depuis-le-passage-a-2024/118282))
- Call to undefined displayExeption ([Signalement](https://community.jeedom.com/t/lors-de-la-synchronisation-erreur-500-internal-server-error/134687/3) / [Détail1](https://community.jeedom.com/t/correction-ajax-displayexeption-e-a-remplacer-par-displayexception-e-et-doc-absente-en-beta/105525) / [Détail2](https://community.jeedom.com/t/correction-ajax-displayexeption-e-a-remplacer-par-displayexception-e/105523))
- Affichage équipement Pilote_Soc_C3.html (Dashboard+Mobile) ([Signalement](https://community.jeedom.com/t/affichage-equipement/95357/4))



---
---
Reprise du code de l3flo
---
---



# 20/10/2019
- Affichage compatible pour jeedom v4.
– Chaque module dispose d'un template.
- La commande timer_switch est utilisée pour activer/désactiver la programmation des modules seconde génération et INEA

# 06/01/2019
- Ajout du module INEA. Le module est géré de la même manière que le module FLAM.

# 18/11/2018
- Synchronisation automatique si le code retour Gizwits est 9004.
- Ajout de la clé du produit.

# 22/10/2018
- Prise en charge des modules pilotes version 2018.

# 06/05/2018
- Intégration et prise en charge des modules Flam et Plugzy.
- Mise en alerte des modules lorsqu'ils ne sont plus connectés au cloud Heatzy.

# 10/01/2018
- Correction affichage des tuiles.

# 12/12/2017
- Activation / désactivation de la programmation des modules.

# 04/12/2017
- Correction de bug d'affichage.

# 26/11/2017
- Possibilité d'historiser la commande info Etat.
- Possibilité de mettre à jour le nom du module heatzy à la sauvegarde de l'équipement si celui-ci a changé.
- Ajout d'un toast contenant le nombre de modules synchronisés. Je trouvais qu'il manquait une information lorsque la synchronisation était terminée.
- Ajout d'un textarea commentaire dans le détail de l'équipement.

# 22/11/2017
– Première version du plugin Heatzy, contrôle le module pilote.
