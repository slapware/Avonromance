server 'hcpetgwp.cloudapp.net', user: 'deploy', roles: %w{app db web}
set :ssh_options, {
    keys: %w(~/.ssh/id_rsa),
    forward_agent: true, 
    auth_methods: %w(publickey password),
    user: 'deploy'
}

on fetch(:dev) do |host|
  as 'www-data' do
    puts capture(:whoami)
  end
end
