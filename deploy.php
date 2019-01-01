<?php
namespace Deployer;

require 'recipe/laravel.php';

// Configuration
set('repository', 'git@github.com:Larastudio/lslaravel.git');
set('default_stage', 'production');
set('git_tty', true); // [Optional] Allocate tty for git on first deployment
set('ssh_type', 'native');
set('keep_releases', 10);

// Make sure uploads & published aren't overwritten by deploying
set('shared_dirs', []);
set('shared_files', [
    '.env',
]);
set('writable_dirs', [
    'storage/framework/cache/data',
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
   ->user('web')
   ->forwardAgent()
   ->stage('staging')
   ->set('deploy_path', '/home/web/staging.larastud.io');

   host('production')
   ->hostname('larastud.io')
   ->user('web')
   ->forwardAgent()
   ->stage('production')
   ->set('deploy_path', '/home/web/laravel');


// Run database migrations
after('deploy:symlink', 'db:migrate');


