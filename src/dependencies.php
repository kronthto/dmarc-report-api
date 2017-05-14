<?php

// DIC configuration

use Psr\Container\ContainerInterface;

$container = $app->getContainer();

$container['dmarcparser'] = function (ContainerInterface $c) {
    $dbSettings = $c->get('settings')['db'];

    $parser = new Solaris\DmarcAggregateParser($dbSettings['host'], $dbSettings['user'], $dbSettings['pass'],
        $dbSettings['db']);

    return $parser;
};
