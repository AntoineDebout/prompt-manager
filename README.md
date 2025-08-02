# Gestionnaire de Prompts

Une application construite avec Laravel, Vue.js et Tailwind CSS pour gérer et organiser vos prompts d'IA de manière efficace.

<img width="1427" height="866" alt="Capture d’écran 2025-08-03 à 01 42 38" src="https://github.com/user-attachments/assets/0fa2b261-0c33-483d-b56c-669ce4b30ed3" />

<img width="1427" height="867" alt="Capture d’écran 2025-08-03 à 01 42 55" src="https://github.com/user-attachments/assets/545c10f9-8367-4e56-b7a7-323f0c06d3d2" />

## 🌟 Fonctionnalités

- **Gestion Multi-prompts**
  - Organisation de plusieurs prompts via un sélecteur intuitif
  - Chaque prompt peut contenir plusieurs messages
  - Prise en charge des rôles différents (system, user, assistant)

- **Édition Interactive**
  - Interface drag-and-drop pour réorganiser les messages
  - Édition en temps réel avec auto-redimensionnement des zones de texte
  - Boutons de déplacement haut/bas pour une navigation facile

- **Variables Dynamiques**
  - Insertion facile de variables prédéfinies
  - Protection des variables insérées contre les modifications partielles
  - Panneau latéral avec liste organisée des variables disponibles

- **Export et Partage**
  - Export au format JSON avec mise en forme propre
  - Copie rapide vers le presse-papiers
  - Prévisualisation du prompt formaté

## 🚀 Technologies

- **Frontend**
  - Vue.js 3 avec Composition API
  - Tailwind CSS pour le style
  - Animations fluides et transitions
  - Interface responsive et moderne

- **Backend**
  - Laravel
  - API RESTful
  - Gestion efficace des états

## 💎 Design

- **Interface Utilisateur**
  - Thème clair et professionnel
  - Composants modulaires et réutilisables
  - Animations subtiles pour une meilleure expérience utilisateur
  - Boutons stylisés et cohérents

- **Organisation**
  - Layout à deux colonnes (messages + sidebar)
  - Sidebar sticky avec sélecteur de prompt et variables
  - Modal d'export élégant et fonctionnel

## 🛠️ Installation

1. Cloner le repository :
```bash
git clone [url-du-repo]
cd prompt-manager
```

2. Installer les dépendances :
```bash
composer install
npm install
```

3. Configurer l'environnement :
```bash
cp .env.example .env
php artisan key:generate
```

4. Lancer le serveur de développement :
```bash
php artisan serve
npm run dev
```

## 📦 Structure des Composants

- **Index.vue** : Composant principal
  - Gestion de l'état global
  - Organisation du layout
  - Coordination des sous-composants

- **Composants**
  - **PromptItem** : Gestion individuelle des messages
  - **PromptSelector** : Sélection du prompt actif
  - **VariablesList** : Affichage et insertion des variables
  - **ExportModal** : Export et copie du prompt

## 🎨 Personnalisation

L'application utilise Tailwind CSS avec une configuration personnalisée incluant :
- Palette de couleurs primaires
- Composants de boutons réutilisables
- Animations et transitions
- Police système optimisée

## 📝 Contribution

Les contributions sont les bienvenues ! N'hésitez pas à :
1. Fork le projet
2. Créer une branche pour votre fonctionnalité
3. Commit vos changements
4. Push sur votre fork
5. Créer une Pull Request

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## 📄 Licence

Ce projet est sous licence MIT - voir le fichier [LICENSE.md](LICENSE.md) pour plus de détails.
