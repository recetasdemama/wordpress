@defaults_recetasdemama = {
  :deploy_to    =>  '/home/recetasdemama/www',
  :repository   =>  'git://github.com/recetasdemama/wordpress.git',
  :revision     =>  'remotes/origin/master',
  :shared_paths => {  'wp-content/backup-db'        => 'wp-content/backup-db',
                      'wp-content/booster_cache'    => 'wp-content/booster_cache',
                      'wp-content/cache'            => 'wp-content/cache',
                      'wp-content/ewww'             => 'wp-content/ewww',
                      'wp-content/upgrade'          => 'wp-content/upgrade',
                      'wp-content/uploads'          => 'wp-content/uploads',
                      'config/wp-config.php'        => 'wp-config.php',
                      'config/advanced-cache.php'   => 'wp-content/advanced-cache.php',
                      'config/wp-cache-config.php'  => 'wp-content/wp-cache-config.php'
                    },
  :mkdirs       =>  [],
  :app          =>  '',
  :web_command  =>  'sudo service apache2'
}

@production_recetasdemama = {
  :name => 'recetasdemama production',
  :application => 'recetasdemama.es',
  :domain => 'wp_rdm'
}

namespace :recetasdemama do
  desc "Points vlad to the production server"
  task :production do
    prepare(@production_recetasdemama.merge(@defaults_recetasdemama))
    Rake::Task['vlad:confirm'].invoke
  end

  namespace :production do
    desc "Updates production with the latest version"
    remote_task :deploy => %w[
      recetasdemama:production
      recetasdemama:private:deploy
    ]
  end

  namespace :private do
    desc "deploy to server"
    task :deploy => %w[
      vlad:update
      vlad:cleanup
    ]
  end
end
