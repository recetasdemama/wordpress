require 'vlad'
require 'renfield'
require 'bundler/vlad'

Vlad.load :app => :passenger,
          :scm => :git

Dir.glob(File.dirname(__FILE__) + '/lib/*') {|file| require file}

def prepare(stage)
  info "Pointing to #{stage[:name]}"
  set :domain,        stage[:domain]
  set :application,   stage[:application]
  set :deploy_to,     stage[:deploy_to]
  set :repository,    stage[:repository]
  set :revision,      stage[:revision]
  set :ssh_flags,     stage[:ssh_flags] || '-p 30262'
  set :shared_paths,  stage[:shared_paths] if stage[:shared_paths]
  set :mkdirs,        stage[:mkdirs] if stage[:mkdirs]
  set :app,           stage[:app] if stage[:app]
  set :web_command,   stage[:web_command] if stage[:web_command]
end
