# Base PHP Service
    
## Getting Started
### Prerequisites
Name | version
---- | -------
PHP |  \>= v7.1 
Composer | \>=1.9.0

### Installing
1. Composer install
2. Configure .env in root file using .env.sample
​
​
​
### Configuration
* On the root you need to have a file named **.env**, here you put your preferences <br /> *(if it doesn't exist you 

​

## Configuration for HTTPS petitions<br />(it's only necessary if you'll make requests from a secure site and you run it in local machine)

### In the local machine (out of the vagrant)
1. Modify ```/etc/hosts```: <br /> ``sudo vim /etc/hosts`` and add a new line with this: <br /> ```33.33.33.10    base-ms-local.bunkerdb.com```

2. Change ```Vagrantfile``` to map the new micro-service <br /> please add the new line <br />
```config.vm.synced_folder "~/bunker/base_php_ms", "/var/www/html/base_php_ms", type: "nfs"``` (it's an example, you must put the location of your consumer folder in the first param)

### Inside the Vagrant

1. Modify ```/etc/hosts```: <br /> ``sudo vim /etc/hosts`` and add a new line with this: <br /> ```127.0.0.1    base-ms-local.bunkerdb.com```
2. Create the following file in /etc/nginx/conf.d/base_php_ms.conf
```
server {
    listen 443;
    server_name  base-ms-local.bunkerdb.com;

    ssl on;
    ssl_certificate /etc/ssl/certs/bunkerdb.chain.crt;
    ssl_certificate_key /etc/ssl/certs/bunkerdb.key;

    root         /var/www/html/base_php_ms/public;
    access_log   /var/log/nginx/base_php_ms.access.log;
    error_log   /var/log/nginx/base_php_ms.error.log;

    location / {
        include /etc/nginx/fastcgi_params;
        try_files $uri /index.php$is_args$args;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_param SCRIPT_NAME $fastcgi_script_name;
        fastcgi_param SCRIPT_FILENAME /var/www/html/base_php_ms/public$fastcgi_script_name;
        fastcgi_read_timeout 300;
        fastcgi_index index.php;
        fastcgi_pass 127.0.0.1:9000;
        #fastcgi_pass unix:/run/php/php7.0-fpm.sock;
    }

}
```
3. Restart Nginx WebServer ```sudo service nginx restart```
