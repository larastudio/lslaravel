<?php
namespace Deployer;

require 'recipe/laravel.php';
require 'vendor/deployer/recipes/cachetool.php';

// Configuration
set('repository', 'git@github.com:Larastudio/lslaravel.git');
set('default_stage', 'production');
set('git_tty', true); // [Optional] Allocate tty for git on first deployment
set('ssh_type', 'native');
set('cachetool', '/var/run/php/php7.2-fpm.sock');
set('keep_releases', 10);

// Make sure uploads & published aren't overwritten by deploying
set('shared_dirs', [
    'public/uploads',
    'public/published',
    'storage/tls/sites.d'
]);
set('shared_files', [
    '.env',
]);
set('writable_dirs', [
    'public/uploads',
    'public/published',
    'storage/framework/cache/data',
    'storage/tls'
]);

// SMART CUSTOM DEPLOY COMMANDS
task('db:migrate', function () {
    run("cd {{release_path}} && php artisan migrate");
});
task('horizon:terminate', function () {
    run("cd {{release_path}} && php artisan horizon:terminate");
});

// Hosts
// dep deploy production
// dep deploy staging

   host('staging')
   ->hostname('staging.larastud.io')
   ->user('forge')
   ->forwardAgent()
   ->stage('staging')
   ->set('deploy_path', '/home/web/staging.larastud.io');

   host('production')
   ->hostname('larastud.io')
   ->user('forge')
   ->forwardAgent()
   ->stage('production')
   ->set('deploy_path', '/home/web/larastud.io');


// Run database migrations
after('deploy:symlink', 'db:migrate');

// Clear OPCache
after('db:migrate', 'cachetool:clear:opcache');
after('cachetool:clear:opcache', 'horizon:terminate');

