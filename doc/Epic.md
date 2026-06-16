> Légende priorité : **P0** indispensable / **P1** important / **P2** bonus

---

# EPIC 1 - Authentification

## US1.1 - Inscription (P0)
**En tant que** visiteur, je veux créer un compte afin de publier et interagir.

### Scénario 1 - Inscription réussie
- **Given :** je suis sur la page "Inscription"
- **When :** je saisis un email, un pseudo et un mot de passe valides puis je valide
- **Then :** mon compte est créé avec un rôle utilisateur et je peux me connecter

### Scénario 2 - Email / pseudo déjà utilisé
- **Given :** l'email ou le pseudo existe déjà
- **When :** je valide l'inscription avec ces informations
- **Then :** un message d'erreur clair s'affiche

## US1.2 - Connexion / déconnexion (P0)
**En tant que** utilisateur, je veux gérer ma session afin d'accéder à mon espace.

### Scénario 1 - Connexion réussie
- **Given :** j'ai un compte actif
- **When :** je saisis des identifiants corrects
- **Then :** je suis connecté et redirigé vers l'accueil ou mon profil

### Scénario 2 - Déconnexion réussie
- **Given :** je suis connecté
- **When :** je clique sur "Déconnexion"
- **Then :** ma session est supprimée

---

# EPIC 2 - Profils utilisateurs

## US2.1 - Voir un profil (P0)
**En tant que** visiteur, je veux voir le profil d'un créateur afin de découvrir ses builds.

### Scénario 1 - Profil affiché
- **Given :** un utilisateur existe
- **When :** j'ouvre son profil public
- **Then :** je vois son pseudo, son avatar, sa bio et ses builds publics

## US2.2 - Modifier mon profil (P1)
**En tant que** utilisateur, je veux modifier mon profil afin de personnaliser mon compte.

### Scénario 1 - Modification réussie
- **Given :** je suis connecté
- **When :** je modifie pseudo, bio ou avatar puis je sauvegarde
- **Then :** mes informations sont mises à jour

---

# EPIC 3 - Publication d'un build

## US3.1 - Publier un build (P0)
**En tant que** utilisateur, je veux publier un build afin de partager ma construction.

**Champs principaux :** titre, description, images, tags, catégories, dimensions, difficulté, temps estimé, version, mode, statut moddé, matériaux et ressources.

### Scénario 1 - Création réussie
- **Given :** je suis connecté
- **When :** je remplis les champs obligatoires et je valide
- **Then :** le build est créé et visible selon sa visibilité

### Scénario 2 - Données invalides
- **Given :** je suis sur le formulaire
- **When :** je valide avec des champs obligatoires manquants ou incohérents
- **Then :** le build n'est pas créé et les erreurs s'affichent

## US3.2 - Gérer les images (P0)
**En tant que** auteur, je veux ajouter des images afin d'illustrer mon build.

### Scénario 1 - Upload accepté
- **Given :** je suis sur le formulaire
- **When :** j'ajoute des images valides
- **Then :** elles sont rattachées au build avec leur ordre d'affichage

## US3.3 - Gérer les matériaux (P0)
**En tant que** auteur, je veux gérer les matériaux afin d'aider les autres à reproduire mon build.

### Scénario 1 - Ajouter un matériau
- **Given :** je suis sur le formulaire
- **When :** j'ajoute un nom, une quantité et éventuellement une couleur
- **Then :** le matériau est enregistré sur le build

## US3.4 - Ajouter une ressource téléchargeable (P2)
**En tant que** auteur, je veux joindre un fichier afin que les visiteurs puissent reproduire mon build.

### Scénario 1 - Ressource ajoutée
- **Given :** je suis l'auteur du build
- **When :** j'ajoute un fichier valide
- **Then :** une ressource `BuildAsset` est créée avec nom, type, URL, taille et compteur de téléchargements

---

# EPIC 4 - Page détail d'un build

## US4.1 - Accéder à la page détail (P0)
**En tant que** visiteur, je veux une page dédiée afin de voir toutes les informations.

### Scénario 1 - Build visible
- **Given :** un build est public
- **When :** j'ouvre sa page
- **Then :** je vois galerie, description, catégories, tags, matériaux, stats, note, commentaires et ressources

### Scénario 2 - Build supprimé
- **Given :** un build est supprimé
- **When :** je ne suis ni auteur ni modérateur
- **Then :** le build n'est pas accessible publiquement

## US4.2 - Afficher le statut moddé (P1)
**En tant que** visiteur, je veux savoir si un build utilise des mods afin de vérifier sa compatibilité.

### Scénario 1 - Statut affiché
- **Given :** je suis sur la page détail
- **When :** la page charge
- **Then :** je vois si le build est vanilla ou moddé

---

# EPIC 5 - Interactions communautaires

## US5.1 - Liker / unliker un build (P0)
**En tant que** utilisateur, je veux liker un build afin de soutenir son créateur.

### Scénario 1 - Like
- **Given :** je suis connecté et je n'ai pas liké ce build
- **When :** je clique sur "Like"
- **Then :** un `BuildLike` est créé et le compteur se met à jour

### Scénario 2 - Unlike
- **Given :** j'ai déjà liké ce build
- **When :** je reclique sur "Like"
- **Then :** mon `BuildLike` est retiré

## US5.2 - Sauvegarder / retirer un build (P0)
**En tant que** utilisateur, je veux sauvegarder un build afin de le retrouver.

### Scénario 1 - Sauvegarde
- **Given :** je suis connecté
- **When :** je clique sur "Sauvegarder"
- **Then :** un `BuildSave` est créé

## US5.3 - Commenter un build (P0)
**En tant que** utilisateur, je veux commenter afin d'interagir.

### Scénario 1 - Commentaire publié
- **Given :** je suis connecté
- **When :** je publie un commentaire valide
- **Then :** le commentaire apparaît sous le build

## US5.4 - Liker un commentaire (P1)
**En tant que** utilisateur, je veux liker un commentaire afin de réagir simplement.

### Scénario 1 - Like de commentaire
- **Given :** je suis connecté
- **When :** je clique sur le like d'un commentaire
- **Then :** un `CommentLike` est créé

## US5.5 - Noter un build (P1)
**En tant que** utilisateur, je veux noter un build afin de donner une évaluation.

### Scénario 1 - Note enregistrée
- **Given :** je suis connecté
- **When :** je choisis une note de 1 à 5
- **Then :** un `BuildRating` est créé ou mis à jour et la moyenne est recalculée

## US5.6 - Télécharger une ressource (P2)
**En tant que** utilisateur, je veux télécharger une ressource afin de reproduire le build.

### Scénario 1 - Téléchargement enregistré
- **Given :** une ressource est disponible
- **When :** je clique sur "Télécharger"
- **Then :** le fichier est téléchargé, un `BuildDownload` est enregistré et les compteurs augmentent

---

# EPIC 6 - Système social

## US6.1 - Suivre / se désabonner d'un créateur (P1)
**En tant que** utilisateur, je veux suivre un créateur afin de retrouver ses nouveautés.

### Scénario 1 - Suivre
- **Given :** je suis connecté et je ne suis pas abonné
- **When :** je clique sur "Suivre"
- **Then :** un `UserFollow` est créé

### Scénario 2 - Se désabonner
- **Given :** je suis déjà abonné
- **When :** je clique sur "Suivi"
- **Then :** le `UserFollow` est retiré

---

# EPIC 7 - Recherche et découverte

## US7.1 - Rechercher, filtrer et trier (P0)
**En tant que** visiteur, je veux explorer les builds afin de trouver rapidement ce qui m'intéresse.

### Scénario 1 - Recherche
- **Given :** je suis sur l'accueil
- **When :** je saisis un mot-clé
- **Then :** je vois les builds correspondants

### Scénario 2 - Filtres appliqués
- **Given :** je suis sur l'accueil
- **When :** je filtre par catégorie, difficulté, version, mode, tag ou statut moddé
- **Then :** seuls les builds correspondants sont affichés

### Scénario 3 - Tri appliqué
- **Given :** je suis sur l'accueil
- **When :** je trie par récents, populaires, mieux notés, plus vus ou plus téléchargés
- **Then :** la liste est réordonnée

## US7.2 - Pagination (P0)
**En tant que** visiteur, je veux paginer afin de parcourir beaucoup de builds.

### Scénario 1 - Page suivante
- **Given :** il existe plus de builds que la limite par page
- **When :** je clique sur "Suivant"
- **Then :** je vois les résultats suivants

---

# EPIC 8 - Modération et signalements

## US8.1 - Signaler un contenu ou un utilisateur (P1)
**En tant que** utilisateur, je veux signaler un build, un commentaire **ou un utilisateur** afin d'aider la modération.

### Scénario 1 - Signalement créé
- **Given :** je suis connecté
- **When :** je signale un build, un commentaire ou un utilisateur, je choisis un motif et je valide
- **Then :** un `Report` est créé avec son statut

### Scénario 2 - Accès refusé (non connecté)
- **Given :** je ne suis pas connecté
- **When :** je tente de signaler un build, un commentaire ou un utilisateur
- **Then :** je suis redirigé vers “Connexion” (ou je vois une invite à me connecter)

## US8.2 - Modérer un build, un commentaire ou un utilisateur (P1)
**En tant que** modérateur/admin, je veux pouvoir modérer un build, un commentaire ou un utilisateur avec un motif afin de retirer les contenus non conformes et maintenir un espace sain.

### Scénario 1 - Build modéré
- **Given :** je suis modérateur/admin
- **When :** supprime un build avec une raison
- **Then :** le build n’est plus visible publiquement
- **And :** un avertissement est envoyé à l’auteur avec le motif

### Scénario 2 - Commentaire modéré
- **Given :** je suis modérateur/admin
- **When :** supprime un commentaire avec une raison
- **Then :** le commentaire n’est plus visible publiquement
- **And :** un avertissement est envoyé à l’auteur du commentaire avec le motif

### Scénario 3 - Utilisateur sanctionné
- **Given :** je suis modérateur/admin
- **When :** je sanctionne un utilisateur avec une raison
- **Then :** l’utilisateur reçoit un avertissement ou voit son compte désactivé selon la sanction choisie
- **And :** le motif est enregistré

### Scénario 4 - Motif obligatoire
- **Given :** je suis modérateur/admin
- **When :** je tente de modérer un build, un commentaire ou un utilisateur sans raison
- **Then :** l’action est refusée
- **And :** un message indique que le motif est obligatoire

## US8.3 - Historiser une action de modération (P1)
**En tant que** système, je veux conserver les actions de modération afin de garder une trace.

### Scénario 1 - Action enregistrée
- **Given :** un modérateur agit sur un build, un commentaire **ou un utilisateur**
- **When :** l'action est validée
- **Then :** une `ModerationAction` est créée

# EPIC 9 - Notifications

## US9.1 - Recevoir une notification (P2)
**En tant que** utilisateur, je veux recevoir des notifications afin de suivre l'activité.

### Scénario 1 - Notification créée
- **Given :** quelqu'un interagit avec mon contenu ou me suit
- **When :** l'action est validée
- **Then :** une `Notification` est créée avec un destinataire, un acteur, un type et un message

### Scénario 2 - Notification lue
- **Given :** j'ai une notification non lue
- **When :** je la marque comme lue
- **Then :** `read_at` est renseigné

---

# EPIC 10 - Responsive

## US10.1 - Site responsive (P0)
**En tant que** visiteur, je veux utiliser le site sur mobile, tablette et desktop.

### Scénario 1 - Mobile
- **Given :** je suis sur mobile
- **When :** je navigue sur l'accueil, un build, un profil ou un formulaire
- **Then :** l'interface reste lisible et utilisable
