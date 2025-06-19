# Script PowerShell pour remplacer le Makefile sur Windows

function Show-Help {
    Write-Host ""
    Write-Host "Usage:"
    Write-Host "  .\commands.ps1 <command>"
    Write-Host ""
    Write-Host "Commands:"
    Write-Host "  setup         Set up project with Docker (build, start, migrate, seed, storage-link)"
    Write-Host "  start         Start Docker containers"
    Write-Host "  stop          Stop Docker containers"
    Write-Host "  restart       Restart Docker containers"
    Write-Host "  build         Rebuild Docker containers"
    Write-Host "  migrate       Run database migrations"
    Write-Host "  seed          Seed the database"
    Write-Host "  test          Run tests"
    Write-Host "  cache-clear   Clear Laravel cache"
    Write-Host "  clean         Remove Docker containers and volumes"
    Write-Host "  logs          View Docker container logs"
    Write-Host "  shell         Open shell in PHP container"
    Write-Host "  npm-dev       Run npm dev server"
    Write-Host "  npm-build     Build frontend assets"
    Write-Host ""
}

function Setup {
    if (!(Test-Path ".env")) {
        Copy-Item ".env.example" ".env" -ErrorAction SilentlyContinue
    }
    docker-compose build
    docker-compose up -d
    docker-compose exec php php artisan key:generate
    docker-compose exec php php artisan migrate --force
    docker-compose exec php php artisan db:seed
    docker-compose exec php php artisan storage:link
}

function Start-Containers {
    docker-compose up -d
}

function Stop-Containers {
    docker-compose stop
}

function Restart-Containers {
    Stop-Containers
    Start-Containers
}

function Build-Containers {
    docker-compose build
}

function Run-Migrations {
    docker-compose exec php php artisan migrate
}

function Seed-Database {
    docker-compose exec php php artisan db:seed
}

function Run-Tests {
    docker-compose exec php php artisan test
}

function Clear-Cache {
    docker-compose exec php php artisan cache:clear
    docker-compose exec php php artisan config:clear
    docker-compose exec php php artisan route:clear
    docker-compose exec php php artisan view:clear
}

function Clean-Environment {
    docker-compose down -v
}

function Show-Logs {
    docker-compose logs -f
}

function Open-Shell {
    docker-compose exec php /bin/bash
}

function Start-NpmDev {
    npm run dev
}

function Build-NpmAssets {
    npm run build
}

# Traitement des arguments
$command = $args[0]

switch ($command) {
    "setup" { Setup }
    "start" { Start-Containers }
    "stop" { Stop-Containers }
    "restart" { Restart-Containers }
    "build" { Build-Containers }
    "migrate" { Run-Migrations }
    "seed" { Seed-Database }
    "test" { Run-Tests }
    "cache-clear" { Clear-Cache }
    "clean" { Clean-Environment }
    "logs" { Show-Logs }
    "shell" { Open-Shell }
    "npm-dev" { Start-NpmDev }
    "npm-build" { Build-NpmAssets }
    default { Show-Help }
} 