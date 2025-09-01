/community-connect
├── .github/                 # GitHub Actions for CI/CD
│   └── workflows/
│       └── main.yml
│
├── .vscode/                 # Recommended VS Code extensions/settings
│
├── src/                     # All application source code
│   ├── backend/             # PHP Backend (MVC)
│   │   ├── app/             # Application Core
│   │   │   ├── Controllers/ # Handle requests (e.g., ReportController.php)
│   │   │   ├── Models/      # Database interaction (e.g., Incident.php)
│   │   │   └── Views/       # HTML templates
│   │   ├── public/          # Publicly accessible files (index.php, .htaccess)
│   │   ├── config/          # Configuration files (database.php, settings.php)
│   │   └── vendor/          # Composer dependencies
│   │
│   ├── frontend/            # JS, CSS, and SASS
│   │   ├── js/
│   │   │   └── main.js
│   │   ├── css/
│   │   └── scss/
│   │
│   └── templates/           # Reusable HTML partials
│       └── dashboard.html
│
├── database/                # Database-related files
│   ├── migrations/          # Schema changes
│   └── seeders/             # Initial data for the database
│
├── .env.example             # Template for environment variables
├── .gitignore               # Files to ignore in Git
├── README.md                # Project overview, setup instructions, etc.
├── composer.json            # PHP dependencies
├── Dockerfile               # Instructions to build a Docker image
├── docker-compose.yml       # Define and run multi-container Docker applications
└── phpunit.xml 


php -S 0.0.0.0:8080 -t src/backend/public

git config --global --unset https.proxy

php database/migrations/migrations-runner.php