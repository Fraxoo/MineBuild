> Légende priorité : **P0** indispensable / **P1** important / **P2** bonus

---

# EPIC 1 — Authentification

## US1.1 — Inscription (P0)
**En tant que** visiteur, je veux créer un compte afin de publier et interagir.

### Scénario 1 — Inscription réussie
- **Given :** je suis sur la page “Inscription”
- **When :** je saisis un email, un pseudo et un mot de passe valides puis je valide
- **Then :** mon compte est créé et je suis connecté (ou redirigé vers “Connexion”)

### Scénario 2 — Email / pseudo déjà utilisé
- **Given :** l’email ou le pseudo existe déjà
- **And :** je suis sur la page “Inscription”
- **When :** je valide l’inscription avec ces informations
- **Then :** un message d’erreur clair s’affiche (“email/pseudo déjà utilisé”)

---

## US1.2 — Connexion (P0)
**En tant que** utilisateur, je veux me connecter afin d’accéder à mon espace.

### Scénario 1 — Connexion réussie
- **Given :** j’ai un compte existant
- **And :** je suis sur la page “Connexion”
- **When :** je saisis des identifiants corrects puis je valide
- **Then :** je suis connecté et redirigé vers l’accueil (ou mon profil)

### Scénario 2 — Identifiants incorrects
- **Given :** je suis sur la page “Connexion”
- **When :** je saisis un email ou mot de passe incorrect puis je valide
- **Then :** un message d’erreur s’affiche et je reste sur la page

---

## US1.3 — Déconnexion (P0)
**En tant que** utilisateur, je veux me déconnecter afin de sécuriser mon compte.

### Scénario 1 — Déconnexion réussie
- **Given :** je suis connecté
- **When :** je clique “Déconnexion”
- **Then :** ma session est supprimée et je redeviens visiteur

### Scénario 2 — Accès page privée refusé après déconnexion
- **Given :** je viens de me déconnecter
- **When :** j’essaie d’aller sur “Publier un build” ou “Mon profil”
- **Then :** je suis redirigé vers “Connexion”

---

# EPIC 2 — Profils utilisateurs

## US2.1 — Voir mon profil (P0)
**En tant que** utilisateur, je veux voir mon profil afin de gérer mes infos et mes builds.

### Scénario 1 — Profil affiché
- **Given :** je suis connecté
- **When :** j’ouvre “Mon profil”
- **Then :** je vois mes infos + la liste de mes builds + mes stats publiques

### Scénario 2 — Non connecté
- **Given :** je ne suis pas connecté
- **When :** j’ouvre “Mon profil”
- **Then :** je suis redirigé vers “Connexion”

---

## US2.2 — Modifier mon profil (P1)
**En tant que** utilisateur, je veux modifier mon profil afin de personnaliser mon compte.

### Scénario 1 — Modification réussie
- **Given :** je suis connecté
- **And :** je suis sur “Mon profil”
- **When :** je modifie bio / pseudo / avatar et je sauvegarde
- **Then :** mes infos sont mises à jour

### Scénario 2 — Données invalides
- **Given :** je suis sur “Mon profil”
- **When :** je saisis une valeur invalide (ex: pseudo trop long) et je sauvegarde
- **Then :** une erreur s’affiche et rien n’est enregistré

---

## US2.3 — Voir le profil d’un autre utilisateur (P0)
**En tant que** visiteur, je veux voir le profil d’un créateur afin de découvrir ses builds.

### Scénario 1 — Profil public accessible
- **Given :** un utilisateur existe
- **When :** j’ouvre son profil public
- **Then :** je vois sa bio + ses builds **PUBLIC** + ses stats publiques

### Scénario 2 — Profil introuvable
- **Given :** l’utilisateur n’existe pas
- **When :** j’ouvre l’URL du profil
- **Then :** je vois une page “Profil introuvable” (404)

---

# EPIC 3 — Publication d’un build (création + médias)

## US3.1 — Accéder au formulaire de publication (P0)
**En tant que** utilisateur, je veux accéder à un formulaire afin de publier un build.

### Scénario 1 — Accès autorisé
- **Given :** je suis connecté
- **When :** je clique “Publier un build”
- **Then :** j’arrive sur le formulaire

### Scénario 2 — Accès refusé
- **Given :** je ne suis pas connecté
- **When :** je clique “Publier un build”
- **Then :** je suis redirigé vers “Connexion”

---

## US3.2 — Publier un build (PUBLIC direct) (P0)
**En tant que** utilisateur, je veux publier un build afin de partager ma construction.

**Champs** : titre, description, images, tags, **catégories** (multi), dimensions, difficulté, temps estimé, version, mode (créatif/survie)

### Scénario 1 — Création réussie (PUBLIC)
- **Given :** je suis connecté et sur le formulaire
- **When :** je remplis les champs obligatoires + j’ajoute 1 à 5 images puis je valide
- **Then :** le build est créé avec la visibilité **PUBLIC**
- **And :** il apparaît sur l’accueil (explorer intégré)

### Scénario 2 — Champs obligatoires manquants
- **Given :** je suis sur le formulaire
- **When :** je valide avec un champ obligatoire vide
- **Then :** le build n’est pas créé
- **And :** une erreur s’affiche sur les champs concernés

---

## US3.3 — Upload d’images (1 à 5) (P0)
**En tant que** utilisateur, je veux ajouter des images afin d’illustrer mon build.

### Scénario 1 — Upload accepté
- **Given :** je suis sur le formulaire build
- **When :** j’ajoute 1 à 5 images valides
- **Then :** je vois un aperçu des images

### Scénario 2 — Upload refusé
- **Given :** je suis sur le formulaire build
- **When :** j’ajoute une 6e image ou un format non autorisé
- **Then :** le fichier est refusé avec un message (limite / formats)

---

# EPIC 4 — Matériaux requis (ajout libre + quantités + couleur)

## US4.1 — Ajouter un matériau manuellement (P0)
**En tant que** auteur, je veux ajouter des matériaux en texte libre afin de lister ce qu’il faut.

### Scénario 1 — Ajout d’un matériau
- **Given :** je suis dans “Matériaux requis” sur le formulaire / édition
- **When :** j’ajoute un matériau via texte (ex: “Stone Bricks”)
- **Then :** il apparaît dans la liste

### Scénario 2 — Nom invalide
- **Given :** je suis dans “Matériaux requis”
- **When :** j’ajoute un nom vide ou trop long
- **Then :** une erreur s’affiche et la ligne n’est pas ajoutée

---

## US4.2 — Associer une quantité à chaque matériau (P0)
**En tant que** auteur, je veux indiquer la quantité afin d’être précis.

### Scénario 1 — Quantité valide
- **Given :** un matériau existe dans la liste
- **When :** je saisis une quantité entière > 0
- **Then :** la quantité est enregistrée et affichée

### Scénario 2 — Quantité invalide
- **Given :** un matériau existe
- **When :** je saisis 0 / négatif / non numérique
- **Then :** une erreur empêche l’enregistrement

---

## US4.3 — Ajouter une couleur au matériau (P1)
**En tant que** auteur, je veux définir une couleur pour un matériau afin de mieux organiser la liste.

### Scénario 1 — Couleur renseignée
- **Given :** un matériau existe dans la liste
- **When :** je renseigne une couleur (ex: `#AABBCC` ou “gray”)
- **Then :** la couleur est enregistrée et affichée

### Scénario 2 — Couleur invalide
- **Given :** un matériau existe
- **When :** je renseigne une couleur invalide
- **Then :** une erreur s’affiche et la couleur n’est pas enregistrée

---

## US4.4 — Modifier / supprimer un matériau (P1)
**En tant que** auteur, je veux modifier/supprimer des matériaux afin de corriger ma liste.

### Scénario 1 — Modification
- **Given :** j’ai une liste de matériaux
- **When :** je modifie le nom, la quantité ou la couleur
- **Then :** la ligne est mise à jour

### Scénario 2 — Suppression
- **Given :** j’ai une liste de matériaux
- **When :** je supprime une ligne
- **Then :** elle disparaît de la liste

---

# EPIC 5 — Page détail d’un build (infos + stats + interactions)

## US5.1 — Accéder à la page détail (P0)
**En tant que** visiteur, je veux une page dédiée à chaque build afin de voir toutes les infos.

### Scénario 1 — Build PUBLIC visible
- **Given :** un build est **PUBLIC**
- **When :** j’ouvre l’URL du build
- **Then :** je vois la page détail complète (galerie, infos, matériaux, stats, commentaires, note)

### Scénario 2 — Build HIDDEN invisible au public
- **Given :** un build est **HIDDEN**
- **And :** je ne suis ni l’auteur ni modérateur/admin
- **When :** j’ouvre l’URL du build
- **Then :** je vois “Build indisponible” (ou 404)

### Scénario 3 — Build HIDDEN visible pour l’auteur
- **Given :** un build est **HIDDEN**
- **And :** je suis connecté et je suis l’auteur
- **When :** j’ouvre l’URL
- **Then :** je vois le build + le motif + les actions de correction

---

## US5.2 — Afficher galerie d’images (P0)
**En tant que** visiteur, je veux naviguer dans la galerie afin de voir le rendu.

### Scénario 1 — Plusieurs images
- **Given :** le build a plusieurs images
- **When :** je clique une miniature
- **Then :** l’image s’affiche en grand et je peux naviguer

### Scénario 2 — Une seule image
- **Given :** le build a 1 image
- **When :** j’ouvre la page
- **Then :** l’image s’affiche sans navigation “suivant/précédent”

---

## US5.3 — Afficher les informations complètes (P0)
**En tant que** visiteur, je veux voir toutes les infos du build afin de comprendre et reproduire.

### Scénario 1 — Infos affichées
- **Given :** je suis sur la page détail
- **When :** la page charge
- **Then :** je vois titre, description, tags, catégories, dimensions, difficulté, temps, version, mode

### Scénario 2 — Champs optionnels non remplis
- **Given :** certains champs sont optionnels
- **When :** la page charge
- **Then :** l’UI reste propre (pas de “null/undefined”)

---

## US5.4 — Afficher matériaux requis (P0)
**En tant que** visiteur, je veux voir les matériaux + quantités afin de reproduire le build.

### Scénario 1 — Matériaux présents
- **Given :** le build a des matériaux
- **When :** j’ouvre la section “Matériaux”
- **Then :** chaque matériau et sa quantité sont affichés

### Scénario 2 — Aucun matériau
- **Given :** aucun matériau n’est renseigné
- **When :** j’ouvre la section “Matériaux”
- **Then :** je vois “Aucun matériau renseigné”

---

# EPIC 6 — Interactions communautaires

## US6.1 — Liker / unliker un build (P0)
**En tant que** utilisateur, je veux liker afin de soutenir un build.

### Scénario 1 — Like
- **Given :** je suis connecté
- **And :** je n’ai pas liké ce build
- **When :** je clique “Like”
- **Then :** mon like est enregistré et les stats affichées se mettent à jour

### Scénario 2 — Unlike
- **Given :** je suis connecté
- **And :** j’ai déjà liké ce build
- **When :** je reclique “Like”
- **Then :** mon like est retiré et les stats affichées se mettent à jour

---

## US6.2 — Enregistrer / retirer un build (P0)
**En tant que** utilisateur, je veux sauvegarder un build afin de le retrouver.

### Scénario 1 — Sauvegarde
- **Given :** je suis connecté
- **And :** le build n’est pas enregistré
- **When :** je clique “Sauvegarder”
- **Then :** le build est ajouté à mes favoris

### Scénario 2 — Retrait
- **Given :** je suis connecté
- **And :** le build est déjà enregistré
- **When :** je reclique “Sauvegarder”
- **Then :** le build est retiré de mes favoris

---

## US6.3 — Commenter un build (P0)
**En tant que** utilisateur, je veux commenter afin d’interagir.

### Scénario 1 — Commentaire publié
- **Given :** je suis connecté
- **When :** je saisis un commentaire valide et je publie
- **Then :** le commentaire apparaît sous le build

### Scénario 2 — Commentaire invalide
- **Given :** je suis connecté
- **When :** je publie un commentaire vide / trop long
- **Then :** une erreur s’affiche et rien n’est posté

---

## US6.4 — Noter un build (1 à 5) (P1)
**En tant que** utilisateur, je veux noter un build afin de donner une évaluation.

### Scénario 1 — Première note
- **Given :** je suis connecté
- **And :** je n’ai jamais noté ce build
- **When :** je choisis une note 1..5
- **Then :** ma note est enregistrée et la moyenne affichée se met à jour

### Scénario 2 — Mise à jour de note
- **Given :** je suis connecté
- **And :** j’ai déjà noté ce build
- **When :** je change ma note
- **Then :** seule ma dernière note est prise en compte

---

## US6.5 — Partager un build (P1)
**En tant que** visiteur, je veux partager un build afin de le diffuser.

### Scénario 1 — Copier le lien
- **Given :** je suis sur un build
- **When :** je clique “Partager” puis “Copier le lien”
- **Then :** le lien est copié et une confirmation s’affiche

### Scénario 2 — Partage externe (option)
- **Given :** je suis sur un build
- **When :** je clique “Partager” puis un réseau
- **Then :** une fenêtre/intent de partage s’ouvre avec l’URL

---

# EPIC 7 — Système social (follow)

## US7.1 — Suivre / se désabonner d’un créateur (P1)
**En tant que** utilisateur, je veux suivre un créateur afin de voir ses nouveautés.

### Scénario 1 — Suivre
- **Given :** je suis connecté
- **And :** je ne suis pas abonné à ce créateur
- **When :** je clique “Suivre”
- **Then :** je suis abonné et les compteurs se mettent à jour

### Scénario 2 — Se désabonner
- **Given :** je suis connecté
- **And :** je suis déjà abonné
- **When :** je clique “Suivi” (désabonnement)
- **Then :** je ne le suis plus et les compteurs se mettent à jour

---

## US7.2 — Voir la liste des créateurs suivis (P1)
**En tant que** utilisateur, je veux voir mes abonnements afin de les gérer.

### Scénario 1 — Liste affichée
- **Given :** je suis connecté
- **When :** j’ouvre “Mes abonnements”
- **Then :** je vois la liste des créateurs suivis (paginée)

### Scénario 2 — Aucun abonnement
- **Given :** je suis connecté
- **And :** je ne suis personne
- **When :** j’ouvre “Mes abonnements”
- **Then :** je vois “Aucun abonnement”

---

# EPIC 8 — Recherche & Découverte (filtres, tri, pagination)

## US8.1 — Rechercher (P0)
**En tant que** visiteur, je veux rechercher un build afin de le trouver rapidement.

### Scénario 1 — Résultats trouvés
- **Given :** je suis sur l’accueil (explorer intégré)
- **When :** je saisis un mot-clé et je valide
- **Then :** je vois des builds correspondants (**PUBLIC uniquement**)

### Scénario 2 — Aucun résultat
- **Given :** je lance une recherche sans correspondance
- **When :** je valide
- **Then :** je vois “Aucun résultat”

---

## US8.2 — Filtrer (P0)
Filtres : catégories, difficulté, version, mode, tags  
**En tant que** visiteur, je veux filtrer afin d’affiner les résultats.

### Scénario 1 — Filtres appliqués
- **Given :** je suis sur l’accueil (explorer intégré)
- **When :** je sélectionne des filtres
- **Then :** seuls les builds correspondants (**PUBLIC**) sont affichés

### Scénario 2 — Réinitialiser filtres
- **Given :** des filtres sont actifs
- **When :** je clique “Réinitialiser”
- **Then :** la liste revient à l’état par défaut

---

## US8.3 — Trier (P0)
Tri : récents, populaires, mieux notés, plus vus, plus téléchargés  
**En tant que** visiteur, je veux trier afin de mieux explorer.

### Scénario 1 — Tri appliqué
- **Given :** je suis sur l’accueil (explorer intégré)
- **When :** je choisis un tri
- **Then :** la liste est réordonnée selon ce tri

### Scénario 2 — Tri conservé avec filtres/pagination
- **Given :** j’ai filtres + tri actifs
- **When :** je change de page
- **Then :** filtres + tri sont conservés

---

## US8.4 — Pagination (P0)
**En tant que** visiteur, je veux paginer afin de parcourir beaucoup de builds.

### Scénario 1 — Page suivante
- **Given :** il existe plus de builds que la limite par page
- **When :** je clique “Suivant”
- **Then :** je vois les résultats suivants

### Scénario 2 — Dernière page
- **Given :** je suis sur la dernière page
- **When :** je clique “Suivant”
- **Then :** le bouton est désactivé (ou aucun changement)

---

# EPIC 9 — Statistiques (vues, likes, saves, downloads)

## US9.1 — Compter les vues (P1)
**En tant que** système, je veux compter les vues afin de mesurer l’intérêt.

### Scénario 1 — Vue incrémentée
- **Given :** un visiteur ouvre un build **PUBLIC**
- **When :** la page détail charge
- **Then :** une vue est comptabilisée

### Scénario 2 — Build HIDDEN non accessible au public
- **Given :** un build est **HIDDEN**
- **And :** je suis visiteur
- **When :** j’essaye d’ouvrir le build
- **Then :** je ne vois pas le build et aucune vue n’est comptabilisée

---

## US9.2 — Afficher les stats sur la page build (P1)
**En tant que** visiteur, je veux voir les stats afin de comparer les builds.

### Scénario 1 — Stats visibles
- **Given :** je suis sur un build **PUBLIC**
- **When :** la page charge
- **Then :** je vois vues / likes / favoris / téléchargements

### Scénario 2 — Stats à zéro
- **Given :** le build n’a aucune interaction
- **When :** j’ouvre la page
- **Then :** les stats affichent 0 sans bug

---

# EPIC 10 — Gestion de ses builds (édition)

## US10.1 — Modifier mon build (P0)
**En tant que** auteur, je veux modifier mon build afin de corriger/mettre à jour.

### Scénario 1 — Modification autorisée
- **Given :** je suis connecté
- **And :** je suis l’auteur du build
- **When :** je modifie infos / matériaux / images et je sauvegarde
- **Then :** les changements sont enregistrés

### Scénario 2 — Modification interdite
- **Given :** je suis connecté
- **And :** je ne suis pas l’auteur
- **When :** j’essaie d’éditer le build
- **Then :** l’accès est refusé (403) ou l’action est cachée

---

## US10.2 — Supprimer mon build (P1)
**En tant que** auteur, je veux supprimer mon build afin de retirer un contenu.

### Scénario 1 — Suppression confirmée
- **Given :** je suis l’auteur
- **When :** je clique “Supprimer” puis je confirme
- **Then :** le build n’est plus visible

### Scénario 2 — Annulation
- **Given :** je clique “Supprimer”
- **When :** j’annule la confirmation
- **Then :** rien ne change

---

## US10.3 — Gérer mes images (ajouter/retirer) (P1)
**En tant que** auteur, je veux mettre à jour les images afin d’améliorer la galerie.

### Scénario 1 — Retirer une image
- **Given :** je suis l’auteur
- **When :** je supprime une image
- **Then :** l’image disparaît de la galerie

### Scénario 2 — Ajouter une image (max 5)
- **Given :** j’ai moins de 5 images
- **When :** j’ajoute une image valide
- **Then :** elle est ajoutée
- **Given :** j’ai déjà 5 images
- **When :** j’essaie d’en ajouter une
- **Then :** l’ajout est refusé avec un message

---

# EPIC 11 — Modération & Signalements (HIDDEN + motif)

## US11.1 — Signaler un build ou un commentaire (P1)
**En tant que** utilisateur, je veux signaler un contenu afin d’aider la modération.

### Scénario 1 — Signalement créé
- **Given :** je suis connecté
- **When :** je clique “Signaler”, je choisis un motif et je valide
- **Then :** un signalement est enregistré

### Scénario 2 — Non connecté
- **Given :** je ne suis pas connecté
- **When :** je clique “Signaler”
- **Then :** je suis redirigé vers “Connexion” (ou une invite à se connecter)

---

## US11.2 — Masquer (HIDE) un build avec motif (P1 / P0 si tu veux)
**En tant que** modérateur/admin, je veux masquer un build avec un motif afin de retirer un contenu non conforme.

### Scénario 1 — Build masqué avec motif
- **Given :** je suis modérateur/admin
- **And :** un build est **PUBLIC**
- **When :** je clique “Masquer”
- **And :** je saisis un motif puis je confirme
- **Then :** le build devient **HIDDEN**
- **And :** il disparaît de l’accueil (public)
- **And :** le motif est visible par l’auteur

### Scénario 2 — Motif obligatoire
- **Given :** je suis modérateur/admin
- **When :** je confirme l’action “Masquer” sans motif
- **Then :** l’action est refusée avec une erreur “Motif obligatoire”

---

## US11.3 — Rendre public (UNHIDE) un build (auteur) (P1)
**En tant que** auteur, je veux rendre public mon build après correction afin qu’il redevienne visible.

### Scénario 1 — Remise PUBLIC réussie
- **Given :** je suis connecté
- **And :** je suis l’auteur d’un build **HIDDEN**
- **When :** je clique “Rendre public”
- **Then :** le build repasse **PUBLIC** et redevient visible publiquement

### Scénario 2 — Remise PUBLIC refusée (données invalides)
- **Given :** je suis l’auteur d’un build **HIDDEN**
- **And :** il manque une image obligatoire ou un champ requis
- **When :** je clique “Rendre public”
- **Then :** l’action est refusée et les erreurs à corriger s’affichent

---

## US11.4 — Supprimer/modérer un commentaire (P1)
**En tant que** modérateur/admin, je veux supprimer un commentaire afin de maintenir un espace sain.

### Scénario 1 — Commentaire supprimé/masqué
- **Given :** je suis modérateur/admin
- **When :** je supprime (ou masque) un commentaire
- **Then :** il n’est plus visible publiquement

### Scénario 2 — Accès refusé
- **Given :** je suis connecté mais pas modérateur/admin
- **When :** j’essaie de supprimer (ou masquer) un commentaire
- **Then :** l’action est refusée (403) ou cachée

---

# EPIC 12 — Notifications + Responsive + Téléchargements (bonus utiles RNCP)

## US12.1 — Notifications (P2)
**En tant que** utilisateur, je veux recevoir des notifications (follow/like/comment/note/modération) afin de suivre l’activité.

### Scénario 1 — Notification créée
- **Given :** quelqu’un like/commente/suit/note mon contenu (ou un modo masque mon build)
- **When :** l’action est validée
- **Then :** une notification apparaît dans ma liste

### Scénario 2 — Marquer comme lues
- **Given :** j’ai des notifications non lues
- **When :** j’ouvre la cloche et je les marque comme lues
- **Then :** elles passent en “lues”

---

## US12.2 — Responsive (P0)
**En tant que** visiteur, je veux un site responsive afin de naviguer sur mobile/tablette/desktop.

### Scénario 1 — Mobile
- **Given :** je suis sur mobile
- **When :** je navigue (accueil, détail, publier, profil)
- **Then :** tout reste lisible et utilisable

### Scénario 2 — Desktop
- **Given :** je suis sur desktop
- **When :** je navigue
- **Then :** la mise en page est optimisée et cohérente

---

## US12.3 — Télécharger une ressource associée à un build (P2)
**En tant que** utilisateur, je veux télécharger une ressource (plan/fichier) afin de reproduire le build.

### Scénario 1 — Téléchargement disponible
- **Given :** le build a un fichier attaché
- **When :** je clique “Télécharger”
- **Then :** le fichier se télécharge et une statistique “downloads” se met à jour

### Scénario 2 — Pas de fichier
- **Given :** le build n’a pas de ressource
- **When :** je consulte la page détail
- **Then :** le bouton “Télécharger” n’apparaît pas