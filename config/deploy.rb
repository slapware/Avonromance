# config valid only for current version of Capistrano
lock '3.4.0'

set :application, 'avonromance'
set :repo_url, 'git@github.com:HarperCollins/avonromance.git'

set :deploy_to, "/var/www/#{fetch(:application)}"

set :scm, :git

set :deploy_via, :remote_cache

set :linked_files, ['wp-config.php', '.htaccess']
set :linked_dirs, ['wp-content/uploads']

namespace :deploy do

    desc 'Download core and update plugins'
    task :plugin_core do
        on roles(:app) do
	    execute "cd #{release_path} && wp core update && wp plugin update --all"
        end
    end

    after 'updated', 'plugin_core'
end
