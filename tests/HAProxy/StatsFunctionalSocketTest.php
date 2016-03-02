<?php

namespace HAProxy;

class StatsFunctionalSocketTest extends StatsFunctionalHttpTest {

    protected $host = "/tmp/haproxy-stats";
    protected $type = Executor::SOCKET;
    protected $username = null;
    protected $password = null;

}
