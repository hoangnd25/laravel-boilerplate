# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure(2) do |config|
  # vm
  config.vm.box = "ubuntu/trusty64"

  # config variables
  project_name = "laravel-boilerplate"
  project_user = "vagrant"
  site_dir = '/vagrant'
  log_dir = '/vagrant/logs'
  vagrant_ip = '10.0.0.99'

  # port forwarding
  config.vm.network :forwarded_port, guest: 80, host: 9234

  # shared folders
  config.vm.synced_folder ".", "/vagrant", type: "nfs"
  apt_host = './.cache/apt'
  apt_vm = '/var/cache/apt/archives'
  apt_partial = "#{apt_host}/partial"
  FileUtils.mkpath apt_partial # because apt-get wants
  shared_folders = {
    apt_host => apt_vm
  }

  # provider
  config.vm.provider :virtualbox do |vb,override|
  	override.vm.network "private_network", ip: "#{vagrant_ip}"
    shared_folders.each do |source, destination|
      FileUtils.mkpath source
      config.vm.synced_folder source, destination
      vb.customize ['setextradata', :id, "VBoxInternal2/SharedFoldersEnableSymlinksCreate/#{destination}", '1']
    end

    vb.customize ['setextradata', :id, 'VBoxInternal2/SharedFoldersEnableSymlinksCreate/v-root', '1']
  end

  config.vm.provider :parallels do |v,override|	
	override.vm.box = "parallels/ubuntu-14.04"	
	override.vm.network "private_network", ip: "#{vagrant_ip}"
	v.memory = 1536
  	v.cpus = 3
  end

  # provision
  config.vm.provision "ansible" do |ansible|
    ansible.playbook = "ansible/main.yml"

    ansible.extra_vars = {
      project_name: project_name,
      project_user: project_user,
      server_name: 'localhost',
      site_dir: site_dir,
      log_dir: log_dir,
      vagrant_env: true
    }
  end
end
