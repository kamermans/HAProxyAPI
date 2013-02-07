# HAProxyAPI #
## PHP API for HAProxy administration ##
[![Build Status](https://travis-ci.org/[YOUR_GITHUB_USERNAME]/[YOUR_PROJECT_NAME].png)](https://travis-ci.org/[YOUR_GITHUB_USERNAME]/[YOUR_PROJECT_NAME])


This PHP API lets you programatically access HAProxy for both admin and read-only commands like enabling/disabling servers, and checking the status of servers.  I can communicate via HAProxy over the builtin HTTP **stats web page**, using the built-in **UNIX domain socket**, or over **TCP** if, for example, you are proxying your domain socket to a TCP socket so you can access it remotely.

The HAProxy HTTP interface only supports `Stats`, `EnableServer` and `DisableServer`, so use of the domain or TCP socket is required for all other commands.

HAProxyAPI was written by Steve Kamerman and is distributed under the GNU GPLv3 license.

----------

# Getting Started #
Before you can use HAProxyAPI, you'll need to include the class loader.  The API is PSR-0 compliant and has builtin Composer support.  If you don't know what that means, you probably just want to use the included class loader, `autoload.php`:

```php
<?php
require_once 'autoload.php';
```

Then, you'll need to create an `HAProxy\Executor` which is used to run all the commands.

## Connecting to HAProxy via HTTP ##
Make sure you have the `stats` interface enabled in your HAProxy config.  You will also need `admin` privileges if you want to enable or disable servers.  Currently, authentication is required to use this method.

```php
<?php
// Create a Executor for HTTP
$exec = new HAProxy\Executor('http://hostname:port/haproxy_stats_url', HAProxy\Executor::HTTP);
// Set your HAProxy stats page credentials
$exec->setCredentials('username', 'password');
```

## Connecting to HAProxy via Socket ##
Socket communications requires the `stats` configuration option to be enabled, as well as the appropriate permission level to run the commands.  Here's and example config for enabling the stats socket:

```
global
    stats    socket /tmp/haproxy-stats    user haproxy group haproxy mode 700 level admin
```

To use the UNIX domain socket interface for HAProxy, you pass its full filename to the constructor:

```php
<?php
// Create a Executor for HTTP
$exec = new HAProxy\Executor('/tmp/haproxy-stats', HAProxy\Executor::SOCKET);
```

## Connecting to HAProxy via TCP/IP ##
HAProxy does not ship with TCP/IP support, but it does support UNIX domain sockets.  You can use something like `socat` or `netcat`/`nc` to make your domain socket accessible via TCP/IP.  This example exposes the socket to the localhost on port 10010:

```
socat TCP-LISTEN:10010,bind=127.0.0.1,reuseaddr,fork,su=haproxy,range=127.0.0.0/8 UNIX-CLIENT:/tmp/haproxy-stats
```

If you can access the socket via TCP/IP, you can use HAProxyAPI to connect to it as well (think `socat + ssh-port-forwarding = secure-remote-admin`).

For this setup, you use the same socket setup as above, but you pass a hostname and port instead:

```php
<?php
// Create a Executor for HTTP
$exec = new HAProxy\Executor('localhost:10010', HAProxy\Executor::SOCKET);
```

## Getting Statistics ##
To get a statistics object, use `HAProxy\Stats::get($exec)`:

```php
<?php
// Connect
$exec = new HAProxy\Executor('localhost:10010', HAProxy\Executor::SOCKET);
// Get stats
$stats = HAProxy\Stats::get($exec);
// Show a tree of the backends, frontends and servers
echo $stats->dumpServiceTree();
```

That will output something like this:

```
+- foo-service
|  +- FRONTEND (OPEN)
|
+- foo-nodes
|  +- node01.foobar.com (UP)
|  +- node02.foobar.com (MAINT)
|  +- node03.foobar.com (UP)
|  +- node04.foobar.com (UP)
|  +- BACKEND (UP)
|
+- stats
|  +- FRONTEND (OPEN)
|  +- BACKEND (UP)
```

Using this information, you can get statistics about individual servers:

```php
<?php
$server = $stats->getServiceStats('foo-nodes','node01.foobar.com');
echo "-------------------------------------\n";
echo "{$server->info->service_name}: {$server->health->status} ({$server->health->check_status} - {$server->health->check_duration}ms )\n";
echo "-------------------------------------\n";
echo $server->dump();
echo "-------------------------------------\n";
```

Output:

```
-------------------------------------
node01.foobar.com: UP (L7OK - 4ms )
-------------------------------------
Info Stats:
        proxy_name: foo-nodes
        service_name: node01.foobar.com
        weight: 1
        process_id: 1
        proxy_id: 2
        service_id: 1
        tracked:
        type: 2
Health Stats:
        status: UP
        active: 1
        backup: 0
        check_failed: 414
        up_down_transitions: 11
        status_change: 22700
        downtime: 7875
        throttle:
        selected_total: 1309606
        check_status: L7OK
        check_code: 200
        check_duration: 4
        check_fail_details: 0
Queue Stats:
        current: 0
        max: 0
        limit:
Session Stats:
        current: 0
        max: 26
        limit:
Bytes Stats:
        in: 697255098
        out: 598278314
Rate Stats:
        current: 0
        max: 49
        limit:
Denied Stats:
        requests:
        responses: 0
Error Stats:
        requests:
        responses: 0
        connections: 2
Warning Stats:
        retries: 151
        redispatches: 23
HttpResponseCode Stats:
        http_1xx: 0
        http_2xx: 1308874
        http_3xx: 0
        http_4xx: 537
        http_5xx: 0
-------------------------------------
```

## Enabling/Disabling Servers ##
You can put servers into maintanence mode (aka disabled mode) and bring them back up using the `HAProxy\Command\DisableServer` and `HAProxy\Command\EnableServer` HAProxyAPI commands.

Both commands take a **backend service name** (ex: `foo-nodes`) and a **server name** (ex: `node01.foobar.com`).

To execute commands, you pass them to the `HAProxy\Executor::execute($command)` method:

```php
<?php
// Create a Executor for HTTP
$exec = new HAProxy\Executor('http://hostname:port/haproxy_stats_url', HAProxy\Executor::HTTP);
// Set your HAProxy stats page credentials
$exec->setCredentials('username', 'password');

// Disable foo-nodes/node01.foobar.com in the load balancer
$exec->execute(new HAProxy\Command\DisableServer('foo-nodes', 'node01.foobar.com'));

// Show stats - you can see node01 is down
echo HAProxy\Stats::get($exec)->dumpServiceTree();
/*
+- foo-service
|  +- FRONTEND (OPEN)
|
+- foo-nodes
|  +- node01.foobar.com (MAINT)
|  +- node02.foobar.com (UP)
|  +- node03.foobar.com (UP)
|  +- node04.foobar.com (UP)
|  +- BACKEND (UP)
|
+- stats
|  +- FRONTEND (OPEN)
|  +- BACKEND (UP)
*/

// Enable foo-nodes/node01.foobar.com
$exec->execute(new HAProxy\Command\EnableServer('foo-nodes', 'node01.foobar.com'));

// Show stats - you can see node01 is coming up
echo HAProxy\Stats::get($exec)->dumpServiceTree();
/*
+- foo-service
|  +- FRONTEND (OPEN)
|
+- foo-nodes
|  +- node01.foobar.com (UP 1/3)
|  +- node02.foobar.com (UP)
|  +- node03.foobar.com (UP)
|  +- node04.foobar.com (UP)
|  +- BACKEND (UP)
|
+- stats
|  +- FRONTEND (OPEN)
|  +- BACKEND (UP)
*/
```