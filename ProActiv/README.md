# ProActiv - Plateforme Auto-Entrepreneur

ProActiv est une plateforme web complète dédiée à la gestion des auto-entrepreneurs français. Elle propose des outils de calcul, de génération de documents, et une communauté d'entraide.

## 🚀 Fonctionnalités

### 📊 **Calculateurs intelligents**
- Calcul des charges sociales en temps réel
- Estimation des impôts selon le type d'activité
- Calcul des revenus nets
- Aide à la tarification

### 📄 **Gestion documentaire**
- Génération de factures professionnelles
- Création de devis personnalisés
- Attestations et certificats
- Export PDF automatique

### 👥 **Communauté active**
- Forum de discussion par thématiques
- Questions/réponses entre entrepreneurs
- Partage d'expériences et conseils
- Modération active

### 🔒 **Sécurité et conformité**
- Chiffrement des données sensibles
- Protection CSRF
- Logs d'activité
- Respect RGPD

## 🛠️ Installation

### Prérequis
- PHP 8.1+ 
- MySQL 5.7+ ou MariaDB 10.3+
- Apache avec mod_rewrite
- Extensions PHP : PDO, PDO_MySQL, JSON, OpenSSL, mbstring

### Installation rapide

1. **Clonez le projet**
   ```bash
   git clone https://github.com/votre-compte/proactiv.git
   cd proactiv
   ```

2. **Configurez la base de données**
   ```bash
   mysql -u username -p < database/schema.sql
   ```

3. **Configurez l'application**
   - Éditez `config/app.php`
   - Modifiez les paramètres de base de données
   - Générez une clé de chiffrement sécurisée

4. **Configurez votre serveur web**
   - Pointez le DocumentRoot vers le dossier `public/`
   - Activez mod_rewrite
   - Configurez SSL (recommandé)

### Configuration O2Switch

Pour un déploiement sur O2Switch :

1. **Uploadez les fichiers** dans `/public_html/ProActiv/`
2. **Importez la base de données** via phpMyAdmin
3. **Ajustez la configuration** dans `config/app.php`
4. **Testez l'installation** sur `https://votre-domaine.com/ProActiv/`

## ⚙️ Configuration

### Base de données

```php
// config/app.php
'database' => [
    'host' => 'localhost',
    'port' => 3306,
    'name' => 'kahu8902_proactiv',
    'username' => 'kahu8902_proactiv',
    'password' => 'votre-mot-de-passe',
    'charset' => 'utf8mb4',
]
```

### Sécurité

```php
// Générez une clé unique pour votre installation
'security' => [
    'encryption_key' => 'votre-cle-256-bits-unique',
    'session_name' => 'ProActiv_Session',
    'csrf_protection' => true,
]
```

## 📁 Structure du projet

```
ProActiv/
├── .github/              # Instructions GitHub Copilot
├── assets/               # CSS, JS, images
│   ├── css/
│   └── js/
├── config/               # Fichiers de configuration
├── database/             # Scripts SQL
├── public/               # Point d'entrée web
├── src/                  # Code source PHP
│   └── Controllers/      # Contrôleurs MVC
├── templates/            # Vues et templates
│   ├── layouts/
│   ├── home/
│   └── errors/
├── storage/              # Logs et fichiers temporaires
├── .htaccess            # Configuration Apache
└── README.md
```

## 🎯 Utilisation

### Interface utilisateur

1. **Inscription** : Créez votre compte auto-entrepreneur
2. **Profil** : Renseignez vos informations d'activité
3. **Calculateurs** : Utilisez les outils de calcul
4. **Documents** : Générez vos factures et devis
5. **Forum** : Participez à la communauté

### Calculateurs disponibles

- **Charges sociales** : Selon votre type d'activité (libéral, commercial, artisanal)
- **Impôts** : Avec abattements forfaitaires
- **Revenus nets** : Calcul après charges et impôts
- **Tarification** : Aide au calcul de vos tarifs

## 🔧 Développement

### Architecture

ProActiv utilise une architecture MVC simple et efficace :

- **Modèle** : Accès aux données via PDO
- **Vue** : Templates PHP avec Bootstrap 5
- **Contrôleur** : Logique métier et routage

### Standards de code

- **PSR-12** : Style de codage PHP
- **Sécurité** : Validation et échappement systématiques
- **Performance** : Cache et optimisations
- **Accessibilité** : Interface responsive

### Contribution

1. Fork le projet
2. Créez une branche feature (`git checkout -b feature/nouvelle-fonctionnalite`)
3. Committez vos changements (`git commit -am 'Ajout nouvelle fonctionnalité'`)
4. Push vers la branche (`git push origin feature/nouvelle-fonctionnalite`)
5. Créez une Pull Request

## 📊 Base de données

### Tables principales

- **users** : Utilisateurs et authentification
- **user_profiles** : Profils détaillés
- **documents** : Documents générés
- **charge_calculations** : Historique des calculs
- **forum_categories** : Catégories du forum
- **forum_topics** : Sujets de discussion
- **forum_posts** : Messages du forum
- **notifications** : Système de notifications

## 🔒 Sécurité

### Mesures implémentées

- Chiffrement des mots de passe (bcrypt)
- Protection CSRF sur tous les formulaires
- Validation des données côté serveur
- Échappement XSS automatique
- Logs d'activité détaillés
- Rate limiting sur les connexions

### Recommandations

- Utilisez HTTPS en production
- Configurez des sauvegardes régulières
- Surveillez les logs d'erreur
- Maintenez le système à jour

## 📞 Support

### Documentation

- [Guide utilisateur](docs/user-guide.md)
- [API Documentation](docs/api.md)
- [FAQ](docs/faq.md)

### Contact

- **Email** : support@proactiv.fr
- **Forum** : [forum.proactiv.fr](https://forum.proactiv.fr)
- **Issues** : [GitHub Issues](https://github.com/votre-compte/proactiv/issues)

## 📝 Licence

Ce projet est sous licence MIT. Voir le fichier [LICENSE](LICENSE) pour plus de détails.

## 🙏 Remerciements

- Bootstrap pour l'interface utilisateur
- Font Awesome pour les icônes
- La communauté auto-entrepreneur pour les retours
- Tous les contributeurs du projet

---

**ProActiv** - Simplifiez votre gestion d'auto-entrepreneur
Version 1.0.0 - Hébergé avec ❤️ en France
