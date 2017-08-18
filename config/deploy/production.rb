set :stage, :production

SSHKit.config.command_map[:php] = "/usr/local/php/php-5.6/bin/php"
SSHKit.config.command_map[:composer] = "/usr/local/php/php-5.6/bin/php /home/host1604557/.bin/composer.phar"

server "serv16.hostland.ru",
    user: "host1604557",
    roles: %w{web app},
    ssh_options: {
        user: "host1604557",
        keys: %w(/home/host1604557/app-matrix.tk/.ssh/id_rsa),
        forward_agent: true,
        port: "1024"
    }
