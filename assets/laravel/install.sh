#!/bin/bash

#TODO: duplicate DB, permissions (check, set), migrate uploads, non-interactive mode, reinstall

set -e

RED='\033[0;31m'
GREEN='\033[0;32m'
NC='\033[0m' # no color

# server requirements
REQ_PHP=7.1
REQ_PHP_EXT=(openssl PDO mbstring tokenizer xml gd)
REQ_PROGRAMS=(php npm composer)

CONFIG_KEYS=(DB_CONNECTION DB_HOST DB_PORT DB_DATABASE DB_USERNAME DB_PASSWORD)

OPT_FORCE=false
OPT_CHECK=false
OPT_DEV=true
OPT_SQLFILE=false
OPT_USE_ENV=false
OPT_CREATE_DB=false
OPT_OPTIONS=""

function usage {
  cat <<- _EOF_
  SIMPLO CMS deployment script

  Usage: ${0##*/} -C | -h
  Usage: ${0##*/} [-fpe] [-s file] [-c database] [-o options]

  Options:

  -c    Create database and user with the specified name.
  -C    Check dependencies and exit.
  -e    Use existing .env file.
  -f    Skip dependency check.
  -h    Display this help message and exit.
  -o    Pass aditional options as environment variables. USE WITH CAUTION!
  -p    Install in a production environment.
  -s    Import SQL file into MySQL before running migrations.

_EOF_
}

while getopts "fCps:ec:o:h" o; do
    case "${o}" in
        f)
            OPT_FORCE=true;;
        C)
            OPT_CHECK=true;;
        p)
            OPT_DEV=false;;
        s)
            if [ ! -r $OPTARG ]; then
                echo -e "${RED}Could not read ${OPTARG}${NC}"
                exit
            fi
            OPT_SQLFILE=${OPTARG};;
        e)
            if [ -f .env ]; then
                OPT_USE_ENV=true
            fi;;
        c)
            if ! [[ $OPTARG =~ ^[A-Za-z0-9_]{1,}$ ]]; then
                echo -e "${RED}The specified database name is not valid, allowed characters are [A-Za-z0-9_]${NC}"
                exit
            fi
            OPT_CREATE_DB=${OPTARG};;
        o)
            if ! [[ $OPTARG =~ ^(([A-Z0-9_]*=[^;\n]*[; ]*?;[; ]*?)*([A-Z0-9_]*=[^;=\n]*[; ]*?))$ ]] || ! eval ${OPTARG}; then
                echo -e "${RED}You have a syntax error in your options [${OPTARG}]${NC}"
                exit
            fi
            OPT_OPTIONS=${OPTARG}
            eval $OPT_OPTIONS;;
        h)
            usage
            exit;;
        *)
            usage
            exit;;
     esac
done

function print_ok {
    echo -e "${GREEN}OK${NC}"
}

function print_failed {
    echo -e "${RED}FAILED${NC}"
}

function compare_versions {
    if [[ $1 == $2 ]]; then
        echo 0
    fi
    local IFS=.
    local i ver1=($1) ver2=($2)
    for ((i=${#ver1[@]}; i<${#ver2[@]}; i++)); do
        ver1[i]=0
    done
    for ((i=0; i<${#ver1[@]}; i++)); do
        if [[ -z ${ver2[i]} ]]; then
            ver2[i]=0
        fi
        if ((10#${ver1[i]} > 10#${ver2[i]})); then
            echo 1
        fi
        if ((10#${ver1[i]} < 10#${ver2[i]})); then
            echo 2
        fi
    done
    echo 0
}

function get_php_version {
    php -r "echo PHP_VERSION;"
}

function check_php_version {
    printf "Checking PHP version ... "
    local version=$(compare_versions $(get_php_version) $REQ_PHP)

    if [[ $version = 2 ]]; then
        print_failed
        echo -e "${RED}ERROR: Required PHP version is '$REQ_PHP', found '$(get_php_version)'${NC}"
    else
        print_ok
    fi
}

function check_program {
    if ! hash "$1" 2>/dev/null; then return 1; fi
}

function check_php_extension {
    if ! php -m|grep --quiet -Fx "$1"; then
        return 1;
    fi
}

function check_dependencies {
    local errors=()
    for p in ${REQ_PROGRAMS[@]}; do
        printf "Checking for $p ... "
        if check_program "$p"; then
            print_ok
        else
            print_failed
            errors+=("$p")
        fi
    done

    for e in ${REQ_PHP_EXT[@]}; do
        printf "Checking for PHP extension $e ... "
        if check_php_extension "$e"; then
            print_ok
        else
            print_failed
            errors+=("$e")
        fi
    done

    if [ ${#errors[@]} -ne 0 ]; then
        echo -e "${RED}Failed to locate the following dependencies: ${errors[@]}${NC}"
        return 1;
    fi
}

function create_db {
    local password="$(openssl rand -base64 12)"

    local new_db=$1

    if [ -f /root/.my.cnf ]; then

        mysql -e "CREATE DATABASE ${new_db} /*\!40100 DEFAULT CHARACTER SET utf8 */;"
        mysql -e "CREATE USER ${new_db}@localhost IDENTIFIED BY '${password}';"
        mysql -e "GRANT ALL PRIVILEGES ON ${new_db}.* TO '${new_db}'@'localhost';"
        mysql -e "FLUSH PRIVILEGES;"
    else
        read -p "Please enter root user MySQL password: " -s rootpasswd
        mysql -uroot -p${rootpasswd} -e "CREATE DATABASE ${new_db} /*\!40100 DEFAULT CHARACTER SET utf8 */;"
        mysql -uroot -p${rootpasswd} -e "CREATE USER ${new_db}@localhost IDENTIFIED BY '${password}';"
        mysql -uroot -p${rootpasswd} -e "GRANT ALL PRIVILEGES ON ${new_db}.* TO '${new_db}'@'localhost';"
        mysql -uroot -p${rootpasswd} -e "FLUSH PRIVILEGES;"
    fi

    DB_DATABASE=$new_db
    DB_USERNAME=$new_db
    DB_PASSWORD=$password
}

function install_theme {
    cd resources/themes
    git clone $1
    cd $(basename $1 .git)
    npm install

    if [[ $OPT_DEV ]]; then
        npm run dev
    else
        npm run production
    fi

    if [ -f composer.json ]; then
        composer update
    fi

    echo "Theme instalation finished. Please enable it after logging into your admin account."
}

function main {

    if [ "$OPT_FORCE" = false ] || [ "$OPT_CHECK" = true ]; then
        check_dependencies
        check_php_version
        if [ "$OPT_CHECK" = true ]; then
            exit
        fi
    fi

    if [ ! -f composer.json ]; then
        echo "File composer.json not found, aborting"
        exit
    elif [ ! -f artisan ]; then
        echo "File artisan not found, aborting"
        exit
    fi

    if [[ $OPT_CREATE_DB != false ]]; then
        create_db $OPT_CREATE_DB
    fi

    if [ -f .env ] && [ "$OPT_REINSTALL" = false ] && [ "$OPT_USE_ENV" = false ]; then
        read -p "Existing configuration file (.env) found. Would you like to use it? [Y/n]:" -n 1 -r
        echo
        if [[ ! $REPLY =~ ^[Nn]$ ]]; then
            OPT_USE_ENV=true
        fi
    fi

    if [ "$OPT_USE_ENV" = false ]; then
        printf "Creating new configuration file (.env) from .env.example ... "
        cp --backup=numbered .env.example .env
        print_ok
    fi


    local new_config_keys=()
    for k in ${CONFIG_KEYS[@]}; do
        if [ ! -z ${!k+x} ]; then
             sed -i -e "s/\($k *= *\).*/\1${!k}/" .env
        else
            new_config_keys+=($k)
        fi
    done
    CONFIG_KEYS=("${new_config_keys[@]}")


    if [ "$OPT_USE_ENV" = true ]; then
        printf "Loading variables from existing .env file ... "
        source .env
        print_ok
    else
        printf "Loading variables from .env.example ... "
        source .env.example
        print_ok
    fi




    for k in ${CONFIG_KEYS[@]}; do
        read -p "$k [${!k}]:" value
        value=${value:-${!k}}
        sed -i -e "s/\($k *= *\).*/\1$value/" .env
    done

    echo "Database configuration finished."
    cat .env
    read -p "Would you like make additional changes to your .env file? [y/N]:" -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        "${EDITOR:-vim}" .env
    fi

    source .env

    npm install

    if [[ $OPT_DEV ]]; then
        composer install --dev
        npm run dev
    else
        composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader
        php artisan config:cache
        npm run production
        route:cache
    fi
    php artisan key:generate

    if [ ! "$OPT_SQLFILE" = false ]; then
        mysql -u${$DB_USERNAME} -p${DB_PASSWORD} ${DB_DATABASE} < ${OPT_SQLFILE}
        php artisan migrate
    elif [ "$OPT_REINSTALL" = true ]; then
        php artisan migrate:fresh --seed
    else
        php artisan migrate --seed
    fi

    read -p "CMS configuration finished. Would you like to install a theme? [Y/n]:" -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Nn]$ ]]; then
        read -p "Please enter the URL of the theme's git repository:" theme_repo
        install_theme $theme_repo
    fi

}

main $@