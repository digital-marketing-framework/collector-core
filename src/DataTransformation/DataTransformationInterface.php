<?php

namespace DigitalMarketingFramework\Collector\Core\DataTransformation;

use DigitalMarketingFramework\Core\Model\Data\DataInterface;

interface DataTransformationInterface
{
    public const DEFAULT_VISIBILITY = 'disabled';

    public const VISIBILITY_DISABLED = 'disabled';

    public const VISIBILITY_PRIVATE = 'private';

    public const VISIBILITY_PUBLIC = 'public';

    public function getVisibility(): string;

    public function allowed(): bool;

    public function transform(DataInterface $data): DataInterface;
}
