# Vision IA Community

Plugin WordPress pour créer une communauté style Skool sur Vision IA.

## Installation

1. Télécharger le dossier `vision-ia-community`
2. Uploader dans `/wp-content/plugins/`
3. Activer le plugin dans WordPress
4. Le plugin créera automatiquement les 4 catégories

## Utilisation

### Shortcode

Ajouter sur une page Elementor :

```
[community_feed]
```

Options disponibles :
```
[community_feed posts_per_page="15"]
```

### Catégories créées automatiquement

- Discussion générale 💬
- Besoin d'aide ❗
- Victoires 🌟
- Annonces 📢 (admin uniquement)

### Intégration MasterStudy

Un onglet "Communauté" est automatiquement ajouté aux profils MasterStudy avec :
- Liste des posts de l'utilisateur
- Liste des commentaires

## Fonctionnalités

✅ Custom Post Type "community_post"
✅ Taxonomie pour catégories
✅ Feed avec filtres par catégorie
✅ Formulaire de création de post (membres avec abonnement PMP)
✅ Système de likes (AJAX)
✅ Embed YouTube automatique
✅ Épingler des posts (admin)
✅ Intégration profil MasterStudy
✅ Design responsive style Skool
✅ **Pièces jointes** : Images, Vidéos, Audio, PDF
✅ **Champ URL** pour ajouter des liens
✅ **Preview des fichiers** avant publication

## Types de fichiers supportés

- **Images** : JPG, PNG, GIF, WebP
- **Vidéos** : MP4, WebM, MOV, OGG
- **Audio** : MP3, WAV, OGG
- **Documents** : PDF

Taille max par fichier : 10 MB

## Permissions

- **Poster** : Membres avec abonnement Paid Memberships Pro actif (ou tous les membres si PMP non installé)
- **Annonces** : Administrateurs uniquement
- **Épingler** : Administrateurs uniquement

## Configuration serveur recommandée

Pour les uploads de vidéos, vérifiez ces valeurs dans php.ini :

```
upload_max_filesize = 64M
post_max_size = 64M
max_execution_time = 300
```

## Personnalisation CSS

Les variables CSS sont dans `/assets/css/community.css` :

```css
:root {
    --vic-primary: #4F46E5;
    --vic-border-radius: 12px;
    /* etc. */
}
```

## Support

Plugin développé par Vision IA.
