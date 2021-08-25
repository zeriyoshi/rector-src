<?php

namespace Rector\Tests\CodingStyle\Rector\Namespace_\ImportFullyQualifiedNamesRector\FixtureNameImportingOnChangedFiles;

class DoAutoImportNamesOnChangedFile
{
    /**
     * @var \Rector\Tests\CodingStyle\Rector\Namespace_\ImportFullyQualifiedNamesRector\Source\NormalParamClass
     */
    private $some;

    public function __construct(\Rector\Tests\CodingStyle\Rector\Namespace_\ImportFullyQualifiedNamesRector\Source\NormalParamClass $some)
    {
        $this->some = $some;
    }
}
?>
-----
<?php

namespace Rector\Tests\CodingStyle\Rector\Namespace_\ImportFullyQualifiedNamesRector\FixtureNameImportingOnChangedFiles;

use Rector\Tests\CodingStyle\Rector\Namespace_\ImportFullyQualifiedNamesRector\Source\NormalReturnClass;
class DoAutoImportNamesOnChangedFile
{
    /**
     * @var NormalReturnClass
     */
    private $some;

    public function __construct(NormalReturnClass $some)
    {
        $this->some = $some;
    }
}
?>
