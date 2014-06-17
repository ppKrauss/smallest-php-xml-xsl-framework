# Make sure the user runs this as root
if [ `id -u` -ne 0 ]
then
    echo "You must run this script as root"
    exit 0
fi

# make sure we are in the scripts directory no matter what
DIR="$( cd "$( dirname "$0" )" && pwd )"
cd $DIR

CHECKOUT_DIR=`cd .. && pwd && cd scripts`
echo ""
echo -n "Use interactive deploy (RECOMMENDED)? (Y/n)"
read inter
if [ ""$inter != 'n' ]
then
    inter="Y"
fi

# function to test option to proceed
# confirm DEFAULT_VALUE MESSAGE
confirm() {
    pass=0
    if [ ""$1 != 'n' ]
    then
        echo -n "$2 "
        read testt

        if [ ""$testt != 'n' ]
        then
            pass=1
        fi
    else
        pass=1
    fi
    return $pass
}

# Deploy the VirtualHost on apache
confirm $inter "Configure the apache virtual host now? (Y/n) "
if [ $? != 0 ]
then
    echo -n "Provide a hostname for the project (will be used as ServerName and ServerAlias): "
    read hostname

    echo -n "Provide a port to listen in apache (default 80): "
    read port
    if [ -z "$port" ]
    then
        port=80
    fi

    echo -n "Provide the apache sites-available directory (default /etc/apache2/sites-available): "
    read available
    if [ -z "$available" ]
    then
        available="/etc/apache2/sites-available"
    fi

    awk_args="{
        sub(/PORT/,\"$port\");
        sub(/SERVERNAME/,\"$hostname\");
        sub(/SERVERALIAS/,\"$hostname\");
        sub(/DOCROOT/, \"$CHECKOUT_DIR\");
        print
    }"

    cat base.conf|awk "$awk_args" > "$available/$hostname.conf"
    grep $hostname /etc/hosts
    if [ $? -eq 0 ]
    then
        echo "Host may be already set. Ignoring..."
    else
        if [ "$port" != "80" ]
        then
            echo "127.0.0.1:$port $hostname" >> /etc/hosts
        else
            echo "127.0.0.1 $hostname" >> /etc/hosts
        fi
    fi

    a2ensite "$hostname.conf"
    echo "Reloading apache2..."
    /etc/init.d/apache2 reload

    echo "Your installation is now ready to use!"
fi

echo ""
echo "Setup completed!"
echo ""
