<?php

namespace DataCollector\Core;

use DataCollector\Core\Collector\DataCollectorInterface;
use DataRelay\Core\Initialization;

class DataCollectorInitialization extends Initialization
{
    protected const PLUGINS = [
        DataCollectorInterface::class => [
        ],
    ];
}
