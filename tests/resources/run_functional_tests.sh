#!/bin/sh -e

DIR="$( cd "$( dirname "$0" )" && pwd )"
HAPROXY_VERSIONS="1.4 1.5 1.6"
PHP_VERSIONS="php:5.6-cli php:7.0-cli diegomarangoni/hhvm:cli"

kill_and_remove() {
    CONTAINER="$1"
    docker kill $CONTAINER >/dev/null 2>&1 || /bin/true
    docker rm -vf $CONTAINER >/dev/null 2>&1 || /bin/true
}

for HAPROXY_VERSION in $HAPROXY_VERSIONS; do

    echo "Removing old test containers"
    CONFIG="$DIR/haproxy-$HAPROXY_VERSION.cfg"
    HAPROXY="haproxyapi-haproxy-$HAPROXY_VERSION"

    kill_and_remove "$HAPROXY"

    echo "Starting HAProxy $HAPROXY_VERSION"
    docker run \
        --name="$HAPROXY" \
        -d \
        -v $CONFIG:/usr/local/etc/haproxy/haproxy.cfg \
        -v /tmp \
        -p "10010" \
        -p "10000" \
        haproxy:$HAPROXY_VERSION

    for PHP_VERSION in $PHP_VERSIONS; do
        echo "Running unit tests in $PHP_VERSION against HAProxy $HAPROXY_VERSION"
        docker run \
            --rm \
            --link="$HAPROXY:haproxy" \
            --volumes-from="$HAPROXY" \
            --workdir="/u/apps/site" \
            -v "$DIR/../../:/u/apps/site:ro" \
            $PHP_VERSION \
            vendor/bin/phpunit -vvvv
    done

done

echo "Cleaning up containers"
kill_and_remove "$HAPROXY"
